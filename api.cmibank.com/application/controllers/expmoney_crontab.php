<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class expmoney_crontab extends Controller {

    public function __construct()
    {
        parent::__construct();
        if(!$this->input->is_cli_request()){
            die('only cli model can run!');
        }
        $this->load->model('base/expmoney_using_base', 'expmoney_using_base');
        $this->load->model('base/user_expproduct_base', 'user_expproduct_base');
        $this->load->model('base/balance_base', 'balance_base');
        $this->load->model('base/user_log_base', 'user_log_base');
    }
    
    //计算每日用户活期利息
    public function countExpMoneyUsingProfit(){
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'start';
        $this->crontab_run(json_encode($msg));
        
        $todaytime = mktime(0,0,0);
        $psize = 3000;
        $count = $this->expmoney_using_base->count_expmoney_using();
        $max_page = ceil($count/$psize);
        $runcount = 0;
//         $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
//         $longcontract = $this->longproductcontract_base->getContractByCid(LONGPRODUCT_CID);
        $odate = date('Y-m-d', strtotime('-1 day'));        //昨天
        $today_odate = date('Y-m-d');                       //今天
        for($page = 1; $page <= $max_page; $page++){
            $offset = ($page - 1) * $psize;
            $data = $this->expmoney_using_base->getExpMoneyUsingList($todaytime, $offset, $psize);
            //处理业务逻辑
            foreach ($data as $user_expmoney_using){
                if($user_expmoney_using['counttime'] == $todaytime){
                    continue;
                }
                $uid = $user_expmoney_using['uid'];
                $remove_money = 0;
                if($user_expmoney_using['updatetime'] > $todaytime){
                    $remove_money = $this->user_expproduct_base->count_expproduct_money($uid, $todaytime);
                }
                $count_money = $user_expmoney_using['money'] - $remove_money;
                if($count_money == 0){
                    continue;
                }
                $user_expproduct = $this->user_expproduct_base->_get_db_userExpProduct($uid);
                //print_r($user_expproduct);
                $this->load->model('base/exp_profit_log_base', 'exp_profit_log_base');
                $income = 8;
                foreach ($user_expproduct as $_user_exp_p){
                    $income = $_user_exp_p['income'];
                    $profit = round($_user_exp_p['money'] * $_user_exp_p['income'] / 365 / 100, 2);
                    $up_profit_data = array();
                    $up_profit_data['uid'] = $uid;
                    $up_profit_data['profit'] = $profit;
                    $up_profit_data['money'] = $_user_exp_p['money'];
                    $up_profit_data['odate'] = $odate;
                    $up_profit_data['time'] = $todaytime;
                    $up_profit_data['trxId'] = $_user_exp_p['trxId'];
                    $up_profit_data['ue_id'] = $_user_exp_p['id'];
                    $this->exp_profit_log_base->add_exp_profit_log($uid, $up_profit_data);
                }
                //修改用户过期体验金产品
                $end_expmoney = $this->user_expproduct_base->sumUserExpProductMoneyWithOdate($uid, $odate);
                $this->user_expproduct_base->updateUserExpProductStatus($uid, $odate);
                
                if($end_expmoney > 0){
                    //减去体验金
                    //echo $end_expmoney;
                    $this->expmoney_using_base->cost_user_expmoney_using($uid, $end_expmoney);
                    //结算到期金额利息 
                    $add_profit = round($end_expmoney * $income / 365 / 100, 2) * 7;
                    //加到余额
                    $this->balance_base->add_user_balance($uid, $add_profit);
                    //添加用户日志
                    $log_data = array(
                        'uid' => $uid,
                        'pid' => 0,
                        'paytime' => NOW,
                        'pname' => '体验金利息发放',
                        'money' => $add_profit,
                        'balance' => $this->balance_base->get_user_balance($uid),
                        'action' => USER_ACTION_EXPMONEY
                    );
                    $this->user_log_base->addUserLog($uid, $log_data);
                    //体验金结算日志
                    $exp_log_data = array(
                        'uid' => $uid,
                        'ctime' => NOW,
                        'log_desc' => '体验金收回',
                        'money' => $end_expmoney,
                        'action' => EXPMONEY_LOG_END,
                        'balance'  => 0
                    );
                    $this->load->model('logic/expmoney_logic', 'expmoney_logic');
                    $log_data = $this->expmoney_logic->addLog($uid, $exp_log_data);
                    //体验金到期运营日志
                    $exp_end_log = array(
                        'uid' => $uid,
                        'end_money' => $end_expmoney,
                        'end_date' => date('Y-m-d'),
                        'ctime' => NOW,
                    );
                    $this->expmoney_logic->addExpEndLog($exp_end_log);
                }
                //更新用户体验金金额信息
                $updateInfo = array();
                $updateInfo['counttime'] = $todaytime;
                $where = array();
                $where['uid'] = $uid;
                $this->expmoney_using_base->updateUserExpMoney($updateInfo, $where);
                $runcount++;
            }
            sleep(1);
        }
//         $this->load->model('base/longmoney_income_log', 'longmoney_income_log');
//         $data = $this->longmoney_income_log->add($income, $todaytime);
        $msg = array();
        $msg['date'] = date("Y-m-d H:i:s",time());
        $msg['type'] = 'end';
        $msg['countnum'] = $count;
        $msg['runnum'] = $runcount;
        $this->crontab_run(json_encode($msg));
    }
    
    public function countExpMoneyProfit(){
    	$msg = array();
    	$msg['date'] = date("Y-m-d H:i:s",time());
    	$msg['type'] = 'start countExpMoneyProfit';
    	$this->crontab_run(json_encode($msg));
    	$todaytime = strtotime(date('Y-m-d'));
    	$today_uietime = $todaytime+86399;
    	$psize = 30;
    	$odate = date('Y-m-d', strtotime('-1 day')); 
    	echo 'odate:'.$odate;
    	$this->load->model('base/user_expmoney_base', 'expmoney_base');
    	$this->load->model('base/expmoney_profit_base', 'expmoney_profit_base');
    	$count=0;
    	$runcount=0;
    	for ($tableindex=0;$tableindex<16;$tableindex++){
    		$flag=true;
    		$page=1;
    		$orrset = 0;//偏移量纠正
    		while ($flag){
    			$offset = ($page - 1) * $psize-$orrset;
    			$expmoneyList = $this->expmoney_base->getUsingExpmoneyList($tableindex,$offset,$psize);
    			if(!empty($expmoneyList)){
    				foreach ($expmoneyList as $expmoney){
    					if($expmoney['odate']!=$odate){
    						$count++;
	    					$profit = round($expmoney['money'] * $expmoney['income'] / 36500, 2);
	    					$uid = $expmoney['uid'];
	    					$profit_data = array();
	    					$profit_data['uid'] = $uid;
	    					$profit_data['profit'] = $profit;
	    					$profit_data['money'] = $expmoney['money'];
	    					$profit_data['income'] = $expmoney['income'];
	    					$profit_data['odate'] = $odate;
	    					$profit_data['pname'] = $expmoney['name'];
	    					$profit_data['ctime'] = $todaytime;
	    					$profit_data['eid'] = $expmoney['id'];
	    					$add_ret = $this->expmoney_profit_base->add_exp_profit_log($uid, $profit_data);
	    					if($add_ret){
	    						$_up['odate']=$odate;
	    						$this->expmoney_base->updateExpmoney($_up,$expmoney['id'],$uid);
	    					}
	    					if($expmoney['uietime']==$today_uietime){
	    						$runcount++;
	    						$total_profit = round($expmoney['money'] * $expmoney['income']* $expmoney['days']/ 36500, 2);
	    						$balance_ret = $this->balance_base->add_user_balance($uid, $total_profit);
	    						if($balance_ret){
		    						$user_log_data = array(
		    								'uid' => $uid,
		    								'pid' => $expmoney['id'],
		    								'paytime' => NOW,
		    								'pname' => '体验金利息发放',
		    								'money' => $total_profit,
		    								'balance' => $this->balance_base->get_user_balance($uid),
		    								'action' => USER_ACTION_EXPMONEY
		    						);
		    						$this->user_log_base->addUserLog($uid, $user_log_data);
		    						$expmoney_update=array();
		    						$expmoney_update['status']=2;
		    						$expmoney_update['profit']=$total_profit;
		    						$this->expmoney_base->updateExpmoney($expmoney_update,$expmoney['id'],$uid);
		    						$orrset++;
	    						}
	    					}
    					}
    				}
    				$page++;
    			}else{
    				$flag=false;
    			}
    		}
    	}
    	$msg = array();
    	$msg['date'] = date("Y-m-d H:i:s",time());
    	$msg['type'] = 'end countExpMoneyProfit';
    	$msg['countnum'] = $count;
    	$msg['runnum'] = $runcount;
    	$this->crontab_run(json_encode($msg));
    	$this->expmoney_profit_base->cleanCache();
    }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */