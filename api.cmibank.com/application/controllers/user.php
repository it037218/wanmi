<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 用户购买产品（资产）信息
 */
class user extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_login();
    }
    
    public function getUserTgInfo(){
        $this->load->model('logic/tg_user_identity_logic', 'identity_logic');
        $identityinfo = $this->identity_logic->getUserIdentity($this->uid);
        if($identityinfo){
            unset($identityinfo['uid']);
            $idcard = $identityinfo['idCardNo'];
            $identityinfo['idCard'] = substr($idcard, 0, 6) . '********' . substr($idcard, -4);
            unset($identityinfo['idCardNo']);
            unset($identityinfo['requestNo']);
            unset($identityinfo['backRequestNo']);
            $identityinfo['realname'] = '*' . mb_substr($identityinfo['realname'], 1);
        }else{
            $identityinfo = array();
        }
        $response = array('error'=> 0, 'data'=> $identityinfo);
        $this->out_print($response);
    }
    
    //余额 
    public function userBlance(){
        $this->load->model('logic/balance_logic', 'balance_logic');
        $balance = $this->balance_logic->get_balance($this->uid);
        $this->load->model('base/redbag_base' , 'redbag_base');
        $res = $this->redbag_base->get_user_redbag_money($this->account);
		if($res){
			$balance = $balance+$res['money'];
		}
        $data = array('balance'=> $balance);
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
//     //用户操作日志  一次性取出来
//     public function userActionLog(){
//         $type = $this->input->post('type');
//         $all_type = array('all','in','out','product','longproduct','longtobalance');
//         if(!in_array($type, $all_type)){
//             $response = array('error'=> 1404, 'msg' => '错误的类型');
//             $this->out_print($response);
//         }
//         $page = max(1, intval($this->input->post('page')));
//         $psize = 20;
//         $start = ($page - 1) * $psize;
//         $end = $start + $psize - 1;
//         $this->load->model('logic/user_logic', 'user_logic');
//         $data['actionlog'] = $this->user_logic->getUserLog($this->uid,  $type, $start, $end);
//         $response = array('error'=> 0, 'data'=> $data);
//         $this->out_print($response);
//     }
    
    //用户操作日志    分段取出来
    public function userActionLog(){
        $type = $this->input->post('type');
        $all_type = array('all','in','out','product','longproduct','longtobalance','klproduct', 'kltobalance','cashout','longall');
        if(!in_array($type, $all_type)){
            $response = array('error'=> 1404, 'msg' => '错误的类型');
            $this->out_print($response);
        }
        $page = max(1, intval($this->input->post('page')));
        $psize = 20;
        $start = ($page - 1) * $psize;
        $end = $start + $psize - 1;
        $this->load->model('logic/user_logic', 'user_logic');
        $data['actionlog'] = $this->user_logic->getNewUserLog($this->uid,  $type, $start, $end);
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function userActionLogDetail(){
    	$orderid = trim($this->input->post('orderid'));
    	$this->load->model('logic/user_logic', 'user_logic');
    	$data['logDetail'] = $this->user_logic->getUserLogDetail($this->uid,  $orderid);
    	$response = array('error'=> 0, 'data'=> $data);
    	$this->out_print($response);
    }
    
    //用户信息
    public function getUserInfo(){
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $data['identity'] = $this->user_identity_logic->getPublicUserIdentity($this->uid);
        $this->load->model('base/balance_base' , 'balance_base');
        $data['balance'] = $this->balance_base->get_user_balance($this->uid);
        $data['server_time'] = NOW;
        
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $product_money = $this->userproduct_base->getUserSumProductMoney($this->uid);
        $max_long_product_buy = $product_money + LONGPRODUCT_LIMIT_DEFAULT;
        $this->load->model('base/longmoney_base', 'longmoney_base');
        $longmoney = $this->longmoney_base->getUserLongMoney($this->uid);
        $data['canbuyLongProduct'] = ($max_long_product_buy - $longmoney) > 0 ? floor($max_long_product_buy - $longmoney) : 0;
        $this->load->model('base/user_base', 'user_base');
        $user_account_info = $this->user_base->getAccountInfo($this->uid);
        $data['top_uid'] = $user_account_info['top_uid'];
        $data['top_pwd'] = $user_account_info['top_pwd'];
        $this->load->model('base/user_jifeng_base' , 'user_jifeng_base');
        $daycount = $this->user_jifeng_base->getDay($this->uid);
        $data['qiandao'] = empty($daycount)?0:1;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    //用户活期+定期收益   
    public function getUserProfitDetailList(){
        $page = $this->input->post('page');
        $days = $this->input->post('days');
        //1所有  0末结算
        $type = 1;
        if(!$page && $days == 0){
            $response = array('error'=> 1404, 'msg' => '错误的参数');
            $this->out_print($response);
        }
        
        if($days){
            $endtime = mktime(0, 0, 0) - ($days * 86400);
            $start = mktime(0, 0, 0);
            $offset = $endtime;
        }else{
            $page = max(1, $page);
            $start = ($page - 1) * 20;
            $start = mktime(0, 0, 0) - $start * 86400;
            $offset = $start - 19 * 86400;
            
        }
        $this->load->model('logic/user_logic', 'user_logic');
        $product_data = $this->user_logic->getUserProductProfitDetail($this->uid, $type, $start, $offset, true);
        $pname = '易米宝';
        $income_array = $this->getALLLongMoneyIncomeLog();
        if($days){
            $endtime = mktime(0, 0, 0);
            $start = mktime(0, 0, 0) - (($days - 1) * 86400);
        }else{
            $page = max(1, $page);
            $endtime = mktime(0,0,0) - ($page - 1) * 19 * 86400;
            $start = $endtime - 18 * 86400;
        }
        $this->load->model('logic/user_logic', 'user_logic');
        $long_data = $this->user_logic->getUserLongProductProfitDetail($this->uid, $start, $endtime);
        $profitlist = array();
        $longProduct_data = array();
        if($long_data){
            foreach ($long_data as $value){
                unset($value['id']);
                unset($value['uid']);
                unset($value['b_longmoney']);
                $value['pname'] = $pname;
                $value['income'] = $income_array[$value['time']];
                $value['money'] = $value['f_longmoney'];
                unset($value['f_longmoney']);
                $profitlist[date('Y-m-d', $value['time'] - 86400)][110] = $value;
            }
            $longProduct_data = $profitlist;
        }
        //易米宝
        $kl_data = $this->user_logic->getUserKlProductProfitDetail($this->uid, $start, $endtime);
        $profitlist = array();
        $klProduct_data = array();
        $pname = '易米宝';
        $income_array = $this->getALLKlMoneyIncomeLog();
        if($kl_data){
            foreach ($kl_data as $value){
                unset($value['id']);
                unset($value['uid']);
                unset($value['b_longmoney']);
                $value['pname'] = $pname;
                $value['income'] = $income_array[$value['time']];
                $value['money'] = $value['f_klmoney'];
                unset($value['f_longmoney']);
                $profitlist[date('Y-m-d', $value['time'] - 86400)][120] = $value;
            }
            $klProduct_data = $profitlist;
        }
        $expproduct_data = $this->__getUserExpProductProfitDetailList($page, $days);
        $expmoney_data = $this->__getUserExpMoneyProfitDetailList($page, $days);
        $rtn['product'] = $product_data;
        $rtn['longProduct'] = $longProduct_data;
        $rtn['expProduct'] = $expproduct_data;
        $rtn['expmoney'] = $expmoney_data;
        $rtn['klPorduct'] = $klProduct_data;
        $type = $this->input->post('type');
        if($type == 'h5'){
            $zhenghe = $product_data;
            foreach ($longProduct_data as $lp_date => $lp_array){
                if(isset($product_data[$lp_date])){
                    $zhenghe[$lp_date] = array_merge($product_data[$lp_date], $lp_array);
                }else{
                    $zhenghe[$lp_date] = $lp_array;
                }
            }
            foreach ($expproduct_data as $ep_date => $ep_array){
                if(isset($product_data[$ep_date])){
                    $zhenghe[$ep_array] = array_merge($product_data[$ep_date], $ep_array);
                }else{
                    $zhenghe[$ep_array] = $ep_array;
                }
            }
            $response = array('error'=> 0, 'data'=> $zhenghe);
            $this->out_print($response);
        }else{
            $response = array('error'=> 0, 'data'=> $rtn);
            $this->out_print($response);
        }
    }
    
    /******************************************************************************************************
     ******************************************** 定期信息start***********************************************
     ******************************************************************************************************/
    //用户定期
    public function userProduct(){
        $this->load->model('logic/user_logic', 'user_logic');
        $user_product = $this->user_logic->getUserProductInfo($this->uid);
        $product = array();
        if($user_product){
            $pids = array();
            foreach ($user_product as $_up){
                if(!in_array($_up['pid'], $pids)){
                    $pids[] = $_up['pid'];
                }
            }
            $product_prof = $this->user_logic->get_profit_buy_uid_and_pid($this->uid, $pids);
            $format_profit = array();
            foreach ($product_prof as $_prof){
                if(!isset($format_profit[$_prof['trxid']]['profit'])){
                    $format_profit[$_prof['trxid']]['profit'] = 0;
                }
                $format_profit[$_prof['trxid']]['profit'] += $_prof['profit'];
            }
            foreach ($user_product as $up){
                if(!isset($product[$up['pid']])){
                    $product[$up['pid']]['pname'] = $up['pname'];
                    $product[$up['pid']]['profit'] = 0;
                    $product[$up['pid']]['money'] = 0;
                }
                $up['profit'] = isset($format_profit[$up['trxId']]['profit']) ? $format_profit[$up['trxId']]['profit'] : 0;
                $product[$up['pid']]['profit'] += $up['profit'];
                $product[$up['pid']]['money'] += $up['money'];
                $product[$up['pid']]['product_list'][] = $up;
            }
        }
        $data['product'] = $product;
        $data['countmoney'] = 0;
        $data['countprofit'] = 0;
        if($data['product']){
            foreach ($data['product'] as $pid => $_val){
                $data['countmoney'] += $_val['money'];
                $data['countprofit'] += $_val['profit'];
            }
        }
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    
    /**
     * {
          "error": 0,
          "data": {
        	    "buynum": 26,                  //投资笔数
        	    "countProfit": 63.62,          //累计收益
        	    "yesterday_profit": 0,         //昨日收益
                "longmoneyCountprofit": 132	   //活期累计收益
          },
     */
    public function userProfitInfo(){
        $this->load->model('logic/user_logic', 'user_logic');
        $yesterday_profit = $this->user_logic->get_product_yesterday_profit($this->uid);
        $data['yesterday_profit'] = $yesterday_profit;
        $product_count_profit = $this->user_logic->get_product_count_profit($this->uid);
        $data['countProfit'] = $product_count_profit ? $product_count_profit : 0;   //定期累计
        //定期在笔数
        $data['buynum'] = count($this->user_logic->getUserProductInfo($this->uid));
        //活期
        $this->load->model('logic/longproduct_logic', 'longproduct_logic');
        $data['longmoney'] = $this->longproduct_logic->getLongmoney($this->uid);
        $data['longmoneyCountprofit'] = $this->user_logic->countUserLongProduct($this->uid);
        //小活期
        $this->load->model('logic/klproduct_logic', 'klproduct_logic');
        $data['klmoney'] = $this->klproduct_logic->getKlmoney($this->uid);
        $data['klmoneyCountprofit'] = $this->user_logic->countUserKlProduct($this->uid);
        //体验金
        $this->load->model('logic/expmoney_logic', 'expmoney_logic');
        $expmoney = $this->expmoney_logic->get_expmoney($this->uid);
        $data['expmoney'] = $expmoney;
        $data['expmoneyCountprofit'] = $this->user_logic->countUserExpProductProfit($this->uid, array());
        $data['expmoneyTotalprofit'] = $this->user_logic->countUserExpmoneyProfit($this->uid, array());

        $yesterday = $this->user_logic->get_expproduct_yesterday_profit($this->uid);
        $data['expmoney_yesterday_profit'] = $yesterday;
        $data['exp_current_profit'] = 0;
        $current_profit = $this->expmoney_logic->getuserExpProduct($this->uid);
        $ue_ids = array();
        if($current_profit){
            foreach ($current_profit as $_d){
                $ue_ids[] = $_d['id'];
            }
            if($ue_ids){
                $data['exp_current_profit'] = $this->user_logic->countUserExpProductProfit($this->uid, $ue_ids);
            }
        }
        
        $new_yesterday = $this->user_logic->get_expmoney_yesterday_profit($this->uid);
        $data['new_expmoney_yesterday_profit'] = $new_yesterday;
        $data['expmoney_current_profit'] = 0;
        $this->load->model('base/user_expmoney_base' , 'user_expmoney_base');
        $current_expmoney = $this->user_expmoney_base->get_user_expmoney_list($this->uid);
        $eids = array();
        if($current_expmoney){
        	foreach ($current_expmoney as $_d){
        		if($_d['status']==1){
	        		$eids[] = $_d['id'];
        		}
        	}
        	if($eids){
        		$data['expmoney_current_profit'] = $this->user_logic->countUserExpmoneyProfit($this->uid, $eids);
        	}
        }
        
        //邀请
        $this->load->model('logic/invite_logic', 'invite_logic');
        $data['invite'] = $this->invite_logic->getinvitemoney($this->uid);
        $data['transaction'] = $this->invite_logic->getinvitereward($this->uid);
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    //用户定期累计收益
    public function getUserProductProfitDetailList(){
        $page = $this->input->post('page');
        $days = $this->input->post('days');
        $type = $this->input->post('type');   //1所有  0末结算
        if($days){
            $type = 1;   //取天数 强制为所有
            $endtime = mktime(0, 0, 0) - ($days * 86400);
            $start = mktime(0, 0, 0);
            $offset = $endtime;
        }else{
            $page = max(1, $page);
            $start = ($page - 1) * 20;
            $offset = $start + 19;
        }
        $this->load->model('logic/user_logic', 'user_logic');
        $withScore = false;
        if($days){
            $withScore = true;
        }
        $data = $this->user_logic->getUserProductProfitDetail($this->uid, $type, $start, $offset, $withScore);
        $rtn = array();
        
        $response = array('error'=> 0, 'data'=> array('product' => $data));
        $this->out_print($response);
    }
    
    
    
    /******************************************************************************************************
     ******************************************** 活期信息start***********************************************
     ******************************************************************************************************/
    
    //用户活期产品
    public function userLongProduct(){
        $this->load->model('logic/user_logic', 'user_logic');
        $longproduct = $this->user_logic->getUserLongProductInfo($this->uid);
        if($longproduct == null){
            $data['longproduct'] = array();
        }else{
            $data['longproduct'] = $longproduct;
        }
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    
    
    
    public function getUserExpProductProfitDetailList(){
        $page = $this->input->post('page');
//         $days = $this->input->post('days');
        $type = $this->input->post('type');   //1累计  0当前未结算
        $this->load->model('logic/expmoney_logic', 'expmoney_logic');
        $ue_ids = array();
        if($type == 0){
            //找到所有末结算的体验金产品
            $data = $this->expmoney_logic->getuserExpProduct($this->uid);
            if($data){
                foreach ($data as $_d){
                    $ue_ids[] = $_d['id'];
                }
            }else{
                $rtn['profit_list'] = array();
                $rtn['count_profit'] = 0;
                $response = array('error'=> 0, 'data'=> $rtn);
                $this->out_print($response);
            }
        }
        $rtn['profit_list'] = $this->__getUserExpProductProfitDetailList($page, 0, $ue_ids);
        if($page == 1){
            $rtn['count_profit'] = $this->user_logic->countUserExpProductProfit($this->uid, $ue_ids);
        }
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    private function __getUserExpProductProfitDetailList($page, $days = 0, $ue_ids = array()){
        if(!$page && $days == 0){
            $page = 1;
        }
        $pname = '体验金收益';
        $income = 8;
        if($days){
            $endtime = mktime(0, 0, 0);
            $start = mktime(0, 0, 0) - (($days - 1) * 86400);
        }else{
            $psize = 19;
            $page = max(1, $page);
            $endtime = mktime(0,0,0) - ($page - 1) * $psize * 86400;
            $start = $endtime - ($psize -1) * 86400;
        }
        $this->load->model('logic/user_logic', 'user_logic');
        $data = $this->user_logic->getUserExpProductProfitDetail($this->uid, $start, $endtime, $ue_ids);
        //print_r($data);
        $pid = 120;
        $profitlist = array();
        if($data){
            foreach ($data as $value){
                $date_key = date('Y-m-d', $value['time'] - 86400);
                unset($value['id']);
                unset($value['uid']);
                unset($value['trxId']);
                unset($value['ue_id']);
                if(isset($profitlist[$date_key][$pid])){
                    $profitlist[$date_key][$pid]['money'] += $value['money'];
                    $profitlist[$date_key][$pid]['profit'] += $value['profit'];
                }else{
                    $value['pname'] = $pname;
                    $value['income'] = $income;
                    $value['money'] = $value['money'];
                    $profitlist[$date_key][$pid] = $value;
                }
            }
        }
        return $profitlist;
    }
    
    public function getUserExpMoneyProfitDetailList(){
    	$page = $this->input->post('page');
    	$type = $this->input->post('type');   //1累计  0当前未结算
    	$this->load->model('base/user_expmoney_base', 'user_expmoney_base');
    	$ue_ids = array();
    	if($type == 0){
    		//找到所有末结算的体验金产品
    		$data = $this->user_expmoney_base->get_user_expmoney_list($this->uid);
    		if($data){
    			foreach ($data as $_d){
    				if($_d['status']==1){
	    				$ue_ids[] = $_d['id'];
    				}
    			}
    		}else{
    			$rtn['profit_list'] = array();
    			$rtn['count_profit'] = 0;
    			$response = array('error'=> 0, 'data'=> $rtn);
    			$this->out_print($response);
    		}
    	}
    	$rtn['profit_list'] = $this->__getUserExpMoneyProfitDetailList($page, 0, $ue_ids);
    	if($page == 1){
    		$rtn['count_profit'] = $this->user_logic->countUserExpmoneyProfit($this->uid, $ue_ids);
    	}
    	$response = array('error'=> 0, 'data'=> $rtn);
    	$this->out_print($response);
    }
    
    private function __getUserExpMoneyProfitDetailList($page, $days = 0, $ue_ids = array()){
    	if(!$page && $days == 0){
    		$page = 1;
    	}
    	$pname = '体验金收益';
    	$income = 8;
    	if($days){
    		$endtime = mktime(0, 0, 0);
    		$start = mktime(0, 0, 0) - (($days - 1) * 86400);
    	}else{
    		$psize = 19;
    		$page = max(1, $page);
    		$endtime = mktime(0,0,0) - ($page - 1) * $psize * 86400;
    		$start = $endtime - ($psize -1) * 86400;
    	}
    	$this->load->model('logic/user_logic', 'user_logic');
    	$data = $this->user_logic->getUserExpMoneyProfitDetail($this->uid, $start, $endtime, $ue_ids);
        $profitlist = array();
        if($data){
            foreach ($data as $value){
            	$profitlist[$value['odate']][$value['id']] = $value;
            }
        }
        return $profitlist;
    }
    
    //用户小活期收益
    public function getUserKlProductProfitDetailList(){
        $page = $this->input->post('page');
        $days = $this->input->post('days');
        $rtn['profit_list'] =  $this->__getUserKlProductProfitDetailList($page, $days);
        if($page == 1){
            $rtn['count_profit'] = $this->user_logic->countUserKlProduct($this->uid);
        }
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    //用户活期收益
    public function getUserLongProductProfitDetailList(){
        $page = $this->input->post('page');
        $days = $this->input->post('days');
        $rtn['profit_list'] =  $this->__getUserLongProductProfitDetailList($page, $days);
        if($page == 1){
            $rtn['count_profit'] = $this->user_logic->countUserLongProduct($this->uid);
        }
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    private function getALLKlMoneyIncomeLog(){
        if(mktime(1,0,0) > NOW){
            $odata = date('Y-m-d', strtotime('-1 day'));
            $this->load->model('base/klmoney_income_log', 'klmoney_income_log');
            $income_array = $this->klmoney_income_log->getALLKlMoneyIncomeLog($odata);
            $this->load->model('base/klproductcontract_base', 'klproductcontract_base');
            $contract = $this->klproductcontract_base->getContractByCid(KLPRODUCT_CID);
            $income = $contract['income'];
            $income_array[mktime(0,0,0)] = $income;
        }else{
            $this->load->model('base/klmoney_income_log', 'klmoney_income_log');
            $income_array = $this->klmoney_income_log->getALLKlMoneyIncomeLog();
        }
        return $income_array;
    }
    
    
    private function getALLLongMoneyIncomeLog(){
        if(mktime(1,0,0) > NOW){
            $odata = date('Y-m-d', strtotime('-1 day'));
            $this->load->model('base/longmoney_income_log', 'longmoney_income_log');
            $income_array = $this->longmoney_income_log->getALLLongMoneyIncomeLog($odata);
            $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
            $contract = $this->longproductcontract_base->getContractByCid(LONGPRODUCT_CID);
            $income = $contract['income'];
            $income_array[mktime(0,0,0)] = $income;
        }else{
            $this->load->model('base/longmoney_income_log', 'longmoney_income_log');
            $income_array = $this->longmoney_income_log->getALLLongMoneyIncomeLog();
        }
        return $income_array;
    }
    
    
    private function __getUserKlProductProfitDetailList($page, $days = 0){
        if(!$page && $days == 0){
            $page = 1;
        }
        $pname = '快乐宝';
        //         $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
        //         $contract = $this->longproductcontract_base->getContractByCid(LONGPRODUCT_CID);
        //         $income = $contract['income'];
        $income_array = $this->getALLKlMoneyIncomeLog();
        if($days){
            $endtime = mktime(0, 0, 0);
            $start = mktime(0, 0, 0) - (($days - 1) * 86400);
        }else{
            $psize = 20;
            $page = max(1, $page);
            $endtime = mktime(0,0,0) - ($page - 1) * $psize * 86400;
            $start = $endtime - ($psize -1) * 86400;
        }
        $this->load->model('logic/user_logic', 'user_logic');
        $data = $this->user_logic->getUserKlProductProfitDetail($this->uid, $start, $endtime);
        $profitlist = array();
        if($data){
            foreach ($data as $value){
                unset($value['id']);
                unset($value['uid']);
                unset($value['b_klmoney']);
                $value['pname'] = $pname;
                $value['income'] = $income_array[$value['time']];
                $value['money'] = $value['f_klmoney'];
                unset($value['f_klmoney']);
                $profitlist[date('Y-m-d', $value['time'] - 86400)][110] = $value;
            }
        }
        return $profitlist;
    }
    
    
    private function __getUserLongProductProfitDetailList($page, $days = 0){
        if(!$page && $days == 0){
            $page = 1;
        }
        $pname = '易米宝';
//         $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
//         $contract = $this->longproductcontract_base->getContractByCid(LONGPRODUCT_CID);
//         $income = $contract['income'];
        $income_array = $this->getALLLongMoneyIncomeLog();
        if($days){
            $endtime = mktime(0, 0, 0);
            $start = mktime(0, 0, 0) - (($days - 1) * 86400);
        }else{
            $psize = 20;
            $page = max(1, $page);
            $endtime = mktime(0,0,0) - ($page - 1) * $psize * 86400;
            $start = $endtime - ($psize -1) * 86400;
        }
        $this->load->model('logic/user_logic', 'user_logic');
        $data = $this->user_logic->getUserLongProductProfitDetail($this->uid, $start, $endtime);
        $profitlist = array();
        if($data){
            foreach ($data as $value){
                unset($value['id']);
                unset($value['uid']);
                unset($value['b_longmoney']);
                $value['pname'] = $pname;
                $value['income'] = $income_array[$value['time']];
                $value['money'] = $value['f_longmoney'];
                unset($value['f_longmoney']);
                $profitlist[date('Y-m-d', $value['time'] - 86400)][110] = $value;
            }
        }
        return $profitlist;
    }
    
    //用户活期产品信息首页
    public function userLongProductInfo(){
        $this->load->model('logic/user_logic', 'user_logic');
        $rtn['count_profit'] = $this->user_logic->countUserLongProduct($this->uid);
        $start = $endtime = mktime(0, 0, 0);
        $yesterday = $this->user_logic->getUserLongProductProfitDetail($this->uid, $start, $endtime);
        $rtn['yesterday'] = isset($yesterday[0]['profit']) ? $yesterday[0]['profit'] : 0;
        $start = mktime(0, 0, 0) - (7 * 86400);
        $day_7 = $this->user_logic->getUserLongProductProfitDetail($this->uid, $start, $endtime);
        $count_7 = 0;
        if($day_7){
            foreach ($day_7 as $one_day){
                $count_7 += $one_day['profit'];
            }
        }
        $rtn['day_7'] = $count_7;
        $start = mktime(0, 0, 0) - (30 * 86400);
        $day_30 = $this->user_logic->getUserLongProductProfitDetail($this->uid, $start, $endtime);
        $count_30 = 0;
        if($day_30){
            foreach ($day_30 as $one_day){
                $count_30 += $one_day['profit'];
            }
        }
        $rtn['day_30'] = $count_30;
        $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
        $longcontract = $this->longproductcontract_base->getContractByCid(LONGPRODUCT_CID);
        $income = $longcontract['income'];
        $this->load->model('logic/longproduct_logic', 'longproduct_logic');
        $rtn['longmoney'] = $this->longproduct_logic->getLongmoney($this->uid);
        $wan_profit = 10000 * $income / 360 / 100;
        $rtn['wan'] = sprintf("%.2f",substr(sprintf("%.3f", $wan_profit), 0, -1));
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    //用户活期产品信息首页
    public function userKlProductInfo(){
        $this->load->model('logic/user_logic', 'user_logic');
        $start = $endtime = mktime(0, 0, 0);
        $yesterday = $this->user_logic->getUserKlProductProfitDetail($this->uid, $start, $endtime);
        $rtn['yesterday'] = isset($yesterday[0]['profit']) ? $yesterday[0]['profit'] : 0;
        $start = mktime(0, 0, 0) - (7 * 86400);
        $day_7 = $this->user_logic->getUserKlProductProfitDetail($this->uid, $start, $endtime);
        $count_7 = 0;
        if($day_7){
            foreach ($day_7 as $one_day){
                $count_7 += $one_day['profit'];
            }
        }
        $rtn['day_7'] = $count_7;
        $start = mktime(0, 0, 0) - (30 * 86400);
        $day_30 = $this->user_logic->getUserKlProductProfitDetail($this->uid, $start, $endtime);
        $count_30 = 0;
        if($day_30){
            foreach ($day_30 as $one_day){
                $count_30 += $one_day['profit'];
            }
        }
        $rtn['day_30'] = $count_30;
        $this->load->model('base/klproductcontract_base', 'klproductcontract_base');
        $klcontract = $this->klproductcontract_base->getContractByCid(KLPRODUCT_CID);
        $income = $klcontract['income'];
        $this->load->model('logic/klproduct_logic', 'klproduct_logic');
        $rtn['klmoney'] = $this->klproduct_logic->getKlmoney($this->uid);
        $wan_profit = 10000 * $income / 360 / 100;
        $rtn['wan'] = sprintf("%.2f",substr(sprintf("%.3f", $wan_profit), 0, -1));
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    
    public function longmoney_seven(){
        $days = 7;
        $this->load->model('base/longmoney_income_log', 'longmoney_income_log');
        $data = $this->longmoney_income_log->getLongmoneyIncomeLogCache($days);
        $rtn = array();
        foreach ($data as $_data){
            $key = date('Y-m-d', $_data['ctime'] - 86400);
            $rtn[$key]['income'] = $_data['income'];
            $rtn[$key]['profit'] = round($_data['income'] / 360 / 100 * 10000, 2);
            //$rtn[$key]['profit'] = number_format($_data['income'] / 360 / 100 * 10000, 2, '.','');
        }
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    public function klmoney_seven(){
        $days = 7;
        $this->load->model('base/klmoney_income_log', 'klmoney_income_log');
        $data = $this->klmoney_income_log->getKlmoneyIncomeLogCache($days);
        $rtn = array();
        foreach ($data as $_data){
            $key = date('Y-m-d', $_data['ctime'] - 86400);
            $rtn[$key]['income'] = $_data['income'];
            $rtn[$key]['profit'] = round($_data['income'] / 360 / 100 * 10000, 2);
            //$rtn[$key]['profit'] = number_format($_data['income'] / 360 / 100 * 10000, 2, '.','');
        }
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    public function getUserActivity(){
        $this->load->model('logic/activity_logic', 'activity_logic');
        $a = $this->activity_logic->getUserActivity($this->uid, ACTIVITY_GIVE_MONEY);
        $data = array();
        $data['content'] = 1;
        echo $this->activity_logic->addtUserActivity($this->uid, ACTIVITY_GIVE_MONEY, $data);
    }
    
    public function user_push_tag(){
        $tpl_tag = array('isnew0', 'isnew1');
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity = $this->user_identity_logic->getPublicUserIdentity($this->uid);
        $user_tag = array();
        if($identity['isnew']){
            $user_tag[] = 'isnew1';
        }else{
            $user_tag[] = 'isnew0';
        }
        $rtn = array();
        $rtn['tpl_tag'] = $tpl_tag;
        $rtn['user_tag'] = $user_tag;
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    public function getUserConponList(){
    	$this->load->model('base/user_coupon_base' , 'user_coupon_base');
    	$conponList = $this->user_coupon_base->get_user_coupon_list($this->uid);
    	if(empty($conponList)){
	    	$response = array('error'=> 0, 'data'=> array());
	    	$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $conponList);
    		$this->out_print($response);
    	}
    }
    
    public function getUserUsedConponList(){
    	$page = $this->input->post('page');
    	$this->load->model('base/user_coupon_base' , 'user_coupon_base');
    	$conponList = $this->user_coupon_base->get_user_used_coupon_list($this->uid,$page);
    	if(empty($conponList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $conponList);
    		$this->out_print($response);
    	}
    }
    
    public function getUserExpiredConponList(){
    	$page = $this->input->post('page');
    	$this->load->model('base/user_coupon_base' , 'user_coupon_base');
    	$conponList = $this->user_coupon_base->get_user_expired_coupon_list($this->uid,$page);
    	if(empty($conponList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $conponList);
    		$this->out_print($response);
    	}
    }
    
    public function getUserNoticeList(){
    	$this->load->model('base/user_notice_base' , 'user_notice_base');
    	$noticeList = $this->user_notice_base->get_user_notice_list($this->uid);
    	if(empty($noticeList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $noticeList);
    		$this->out_print($response);
    	}
    }
    public function getUserExpmoneyList(){
    	$this->load->model('base/user_expmoney_base' , 'user_expmoney_base');
    	$expmoneyList = $this->user_expmoney_base->get_user_expmoney_list($this->uid);
    	if(empty($expmoneyList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $expmoneyList);
    		$this->out_print($response);
    	}
    }
    
    public function getUserLuckybagList(){
    	$this->load->model('logic/luckybag_logic' , 'luckybag_logic');
    	$list = $this->luckybag_logic->getUserLuckybagList($this->uid,$this->account);
    	if(empty($list)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $list);
    		$this->out_print($response);
    	}
    }
    
    public function getUserAcceptedList(){
    	$this->load->model('base/luckybag_accepted_base' , 'luckybag_accepted_base');
    	$list = $this->luckybag_accepted_base->get_user_accepted_luckybag_list($this->uid);
    	if(empty($list)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $list);
    		$this->out_print($response);
    	}
    }
    
    public function getUserJifengLog(){
    	$page = $this->input->post('page');
    	$this->load->model('base/user_jifeng_base' , 'user_jifeng_base');
    	$jifengList = $this->user_jifeng_base->get_user_jifeng_list($this->uid,$page);
    	if(empty($jifengList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $jifengList);
    		$this->out_print($response);
    	}
    }
    
    public function getUserQiandaoLog(){
    	$page = $this->input->post('page');
    	$this->load->model('base/user_jifeng_base' , 'user_jifeng_base');
    	$jifengList = $this->user_jifeng_base->get_user_qiandao_list($this->uid,$page);
    	$qiandao = $this->user_jifeng_base->get_total_Qiandao($this->uid);
    	if(empty($jifengList)){
    		$response = array('error'=> 0, 'data'=> array(),'jifeng'=>0);
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $jifengList,'jifeng'=>$qiandao);
    		$this->out_print($response);
    	}
    }
    
    public function qiandao(){
    	$this->load->model('base/user_jifeng_base' , 'user_jifeng_base');
    	$daycount = $this->user_jifeng_base->incrDay($this->uid);
    	if($daycount>1){
    		$total_count = $this->user_jifeng_base->getCount($this->uid);
    		$tomo = $total_count>=4?5:$total_count+1;
	    	$monthcount = $this->user_jifeng_base->getMonth($this->uid);
    		if($monthcount==19){
    			$tomo = $tomo +20;
    		}
    		$response = array('error'=> 1, 'msg'=> '您今天已签到','lianxuqiandao'=>$total_count,'tomorrow'=>$tomo);
    		$this->out_print($response);
    	}
    	$count = $this->user_jifeng_base->incr($this->uid);
    	$inte = $count>=5?5:$count;
    	$this->load->model('logic/activity_logic', 'activity_logic');
    	$ret = $this->activity_logic->addUserIntegral($this->account,$inte,$this->uid,JIFENG_QIANDAO);
    	$this->user_jifeng_base->set_total_Qiandao($this->uid,$inte);
    	$tomo = $count>=4?5:$count+1;
    	$monthcount = $this->user_jifeng_base->incrMonth($this->uid);
    	if($monthcount==20){
	    	$_inte = 20;
	    	$ret = $this->activity_logic->addUserIntegral($this->account,$_inte,$this->uid,JIFENG_LEIJI_QIANDAO);
	    	$this->user_jifeng_base->set_total_Qiandao($this->uid,$_inte);
    	}else if($monthcount==19){
    		$tomo = $tomo +20;
    	}
    	if($ret){
    		$response = array('error'=> 0, 'msg'=> '恭喜获得'.$inte.'个积分，明天继续哦！','lianxuqiandao'=>$count,'tomorrow'=>$tomo,'jifen'=>$inte);
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 1, 'msg'=> '签到失败，请稍后再试','lianxuqiandao'=>$count,'tomorrow'=>$inte);
    		$this->out_print($response);
    	}
    }
    
    public function getUserTotalJifeng(){
    	$this->load->model('logic/activity_logic', 'activity_logic');
    	$info_data = $this->activity_base->get_activity_rank_with_actid_phone(2, $this->account);
    	$info_data = $info_data ? $info_data : 0;
    	if($info_data){
    		$response = array('error'=> 0, 'data'=> $info_data);
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> 0);
    		$this->out_print($response);
    	}
    }
    
    public function getUserQiandaoByMonth(){
    	$this->load->model('base/user_jifeng_base' , 'user_jifeng_base');
    	$year = trim($this->input->post('year'));
    	$month = trim($this->input->post('month'));
    	$stime = mktime(0, 0, 0, $month, 1, $year);
    	$etime = mktime(0, 0, 0, $month+1, 1, $year);
    	$jifengList = $this->user_jifeng_base->get_user_qiandao_list_by_month($this->uid,$stime,$etime);
    	if(empty($jifengList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $jifengList);
    		$this->out_print($response);
    	}
    }
    
    public function getUserProductContract(){
    	$bid = trim($this->input->post('bid'));
    	$pid = trim($this->input->post('pid'));
    	$data = array();
    	if(empty($bid)){
    		$this->load->model('base/product_base', 'product_base');
    		$productDetail = $this->product_base->getProductDetail($pid);
    		if($productDetail['ucid']=='1011'){
	    		$this->load->view('zqzr_template');
    		}else{
    			$this->load->view('jkxy_template');
    		}
    	}else{//bid not empty
	    	$this->load->model('base/userproduct_base' , 'userproduct_base');
	    	$userProductDetail = $this->userproduct_base->getUserProductByid($this->uid,$bid);
	    	
	    	$this->load->model('base/product_base', 'product_base');
	    	$productDetail = $this->product_base->getProductDetail($userProductDetail['pid']);
	    	
	    	$this->load->model('base/user_identity_base', 'identity');
	        $identity = $this->identity->getUserIdentity($this->uid);
	    	
	    	$this->load->model('base/contract_base' , 'contract_base');
	    	$contract = $this->contract_base->getContractByCid($productDetail['cid']);
	    	
	    	$this->load->model('base/corporation_base' , 'corporation_base');
	    	$corporation = $this->corporation_base->getCorpByCid($contract['corid']);
	    	
	    	$data['contract'] = $contract;
	    	$data['userproduct'] = $userProductDetail;
	    	$data['productDetail'] = $productDetail;
	    	$data['identity'] = $identity;
	    	$data['corp'] = $corporation;
                
                $this->load->view('zqzr', $data);
                
//	    	if($productDetail['ucid']=='1011'){
//		    	$this->load->view('zqzr', $data);
//	    	}else{
//	    		$this->load->view('jkxy', $data);
//	    	}
    	}
    	
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */