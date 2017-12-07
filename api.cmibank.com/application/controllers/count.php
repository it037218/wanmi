<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
*/
class count extends Controller {

    public function __construct()
    {
        parent::__construct();
        if(!$this->input->is_cli_request()){
            die('only cli model can run!');
        }
    }
    
    public function check_all(){
    	$this->load->model('base/user_identity_base' , 'user_identity_base');
    	$psize = 100;
    	$count = $this->user_identity_base->getUseridentityCount(null);
    	echo 'size:'.$count;
    	$max_page = ceil($count/$psize);
    	for($page = 1; $page <= $max_page; $page++){
    		$offset = ($page - 1) * $psize;
    		$userList = $this->user_identity_base->getUseridentityList(null,null,array($psize, $offset));
    		if(!empty($userList)){
    			foreach ($userList as $val){
    				$this->check_withdraw_money($val['uid']);
    			}
    		}
    	}
    }
    
    
    public function resetCdBase(){
    	$this->load->model('base/user_log_base' , 'user_log_base');
    	$this->load->model('logic/cd_logic', 'cd_logic');
    	for ($index=1;$index<91528;$index++){
    		$cd_data = $this->cd_logic->getUserCd($index);
    		$times = $this->user_log_base->getWithdrawTimes($index);
    		if($times){
    			$left = $times>=10?0:(10-$times);
    			$cd_data['free_withDraw'] =$left;
    		}
    		$this->cd_logic->setUserCd($index, $cd_data);
    	}
    }
    
    public function resetCtime(){
    	$this->load->model('base/user_identity_base' , 'user_identity_base');
    	$this->load->model('base/user_base','user_base');
    	$this->load->model('base/user_expmoney_base','user_expmoney_base');
    	$this->load->model('base/user_notice_base', 'user_notice_base');
    	$tt = array();
    	for ($index=33993;$index<96296;$index++){
    		$account = $this->user_base->getAccountInfo($index);
    		if(!empty($account)){
    			$user = $this->user_identity_base->getUserIdentity($index);
    			if(empty($user)){
    				$expList = $this->user_expmoney_base->getUserAllExpmoneyList($index);
    				if(empty($expList)){
    					$data['id']= date('Ymd') . mt_rand(1000000000,9999999999);
    					$data['uid']=$index;
    					$data['name']='系统活动';
    					$data['type'] = 3;
    					$data['money'] = 1288;
    					$data['ctime'] = NOW;
    					$data['etime'] = strtotime(date('Y-m-d',time()))+7*86400+86399;
    					$data['uietime'] = strtotime(date('Y-m-d',time()))+7*86400+86399;
    					$data['days'] = 7;
    					$data['income'] = 8;
    					$this->user_expmoney_base->addExpmoney($index,$data);
    					$notice_data = array(
    							'uid' => $index,
    							'title' => '体验金获得提醒',
    							'content' => "恭喜您获得了 1288元体验金，赶快去【我的资产-体验金券】看看吧！",
    							'ctime' => NOW
    					);
    					$this->user_notice_base->addNotice($index,$notice_data);
    					$this->crontab_run(json_encode($account['account']), 'luser101');
    				}
    			}
    		}
    	}
    }
    
    public function check_userful(){
    	$this->load->model('base/user_identity_base' , 'user_identity_base');
    	$this->load->model('base/userproduct_base','userproduct');
    	$this->load->model('base/user_base','user_base');
    	$this->load->model('base/userlongproduct_base','userlongproduct_base');
    	$this->load->model('base/longmoney_base','longmoney_base');
    	$tt = array();
			for ($index=0;$index<91528;$index++){
				$account = $this->user_base->getAccountInfo($index);
				if(!empty($account)){
					if(true){
						$user = $this->user_identity_base->getUserIdentity($index);
						if(!empty($user)){
			    			$total = $this->userproduct->getAllMoney($index);
			    			$totalmoney = $total[0]['totalmoney']?$total[0]['totalmoney']:0;
			    			$totalcount = $total[0]['totalcount']?$total[0]['totalcount']:0;
			    			
			    			$ltotal = $this->userlongproduct_base->getAllMoney($index);
			    			$ltotalmoney = $ltotal[0]['totalmoney']?$ltotal[0]['totalmoney']:0;
			    			$ltotalcount = $ltotal[0]['totalcount']?$ltotal[0]['totalcount']:0;
			    			
			    			$longmoney = $this->longmoney_base->getUserLongMoney($index);
			    			$temp_total = $totalmoney+$longmoney;
			    			if($temp_total>200000){
				    			$tt["$temp_total"] = $user['phone'].' '.$user['realname'].'  '.$temp_total;
			    			}
						}
					}
				}
			}
			ksort($tt);
			print_r($tt);
    }
    
    public function check_luserful(){
    	$this->load->model('base/user_identity_base' , 'user_identity_base');
    	$this->load->model('base/userlongproduct_base','userlongproduct_base');
    	$psize = 100;
    	$count = $this->user_identity_base->getUseridentityCount(null);
    	echo 'size:'.$count;
    	$max_page = ceil($count/$psize);
    	for($page = 1; $page <= $max_page; $page++){
    		$offset = ($page - 1) * $psize;
    		$userList = $this->user_identity_base->getUseridentityList(null,null,array($psize, $offset));
    		if(!empty($userList)){
    			foreach ($userList as $val){
    				$totalmoney = $this->userlongproduct_base->getAllMoney($val['uid']);
    				$log = array(
    						'phone' => $val['phone'],
    						'productmoney' => $totalmoney
    				);
    				if($totalmoney<=1000){
    					$this->crontab_run(json_encode($log), 'luser_1000.log');
    				}else{
    					$this->crontab_run(json_encode($log), 'luser_0000.log');
    				}
    			}
    		}
    	}
    }
    
    public function checksxf(){
    	$this->load->model('base/withdraw_sxf_log_base', 'withdraw_sxf');
    	$this->load->model('base/user_log_base', 'user_log_base');
    	$sxfList = $this->withdraw_sxf->getAll();
    	echo count($sxfList);
    	foreach ($sxfList as $sxf){
    		$log = $this->user_log_base->getuserlog($sxf['uid'],$sxf['orderid']);
    		if(empty($log)){
    			echo $sxf['id'].'is wrong';
    		}
    	}
    }
    
    public function temp_data(){
    	$odate = date('Y-m-d', strtotime('-1 day'));
    	//$odate = date('Y-m-d');
    	//活期--售出（昨日）
    	$this->load->model('base/longproduct_base', 'longproduct_base');
    	$lp_sellmoney = $this->longproduct_base->getLongProductSellmoney($odate);
    	$insert_data['lp_sellout'] =  $lp_sellmoney;
    	echo '活期产品售出:' . $lp_sellmoney . "<br />==============<br />";
    	
    	//活期用户购买
    	$this->load->model('base/userlongproduct_base', 'userlongproduct_base');
    	$lp_buy = $this->userlongproduct_base->getSumUserLongProduct($odate);
    	$insert_data['lp_buy'] =  $lp_buy;
    	echo '活期用户购买:' . $lp_buy . "<br />==============<br />";
    	
    	//活期--转出（昨日）
    	$this->load->model('base/longtobalance_log_base', 'longtobalance_log_base');
    	$ltob = $this->longtobalance_log_base->getLogListByCtime($odate);
    	$insert_data['ltob'] =  $ltob ? $ltob : 0;
    	echo '活期转出金额:' . $ltob . "<br />==============<br />";
    	
    	//当日手续费
    	$this->load->model('base/withdraw_sxf_log_base', 'withdraw_sxf');
    	$sxf = $this->withdraw_sxf->getSxfByDay($odate);
    	$insert_data['sxf'] = $sxf ? $sxf : 0;
    	echo '手续费:' . $sxf . "<br />==============<br />";
    	
    	$this->load->model('base/buy_log_base', 'buy_log_base');
    	$buy_product = $this->buy_log_base->getLogListByCtime($odate);
    	$insert_data['p_buy_log'] =  $buy_product? $buy_product :0;
    	echo '定期购买:' . $insert_data['p_buy_log'] . "<br />==============<br />";
    	
    	$this->load->model('base/redbag_log_base','redbag_log_base');
    	$hongbao = $this->redbag_log_base->count_by_date($odate);
    	$insert_data['hongbao'] =  $hongbao ? $hongbao : 0;
    	echo '红包:' . $hongbao . "<br />==============<br />";
    	
    	$this->load->model('base/user_coupon_base','user_coupon_base');
    	$coupon = $this->user_coupon_base->getUserCouponUsedbyDate($odate);
    	$insert_data['coupon'] =  $coupon ? $coupon : 0;
    	echo '抵用券:' . $coupon . "<br />==============<br />";
    	
    	$this->load->model('base/buchang_base','buchang_base');
    	$buchang = $this->buchang_base->getBuchangByDay($odate);
    	$insert_data['buchang'] =  $buchang ? $buchang : 0;
    	echo '补偿:' . $buchang . "<br />==============<br />";
    	
    	$this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
    	$withhold = $this->weehours_withdraw_log->getYesWithhold($odate);
    	$insert_data['withhold'] =  $withhold ? $withhold : 0;
    	echo '出款了的:' . $withhold . "<br />==============<br />";
    	
    	$this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
    	$yesnotWithhold = $this->weehours_withdraw_log->getYesnotWithhold($odate);
    	$insert_data['notwithhold'] =  $yesnotWithhold ? $yesnotWithhold : 0;
    	echo '没有出款的:' . $yesnotWithhold . "<br />==============<br />";
    }

    public function crontab_run($msg, $filename = 'crontab_run_log.'){
    	if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
    		$logFile = './'. $filename .date("Y-m-d");
    	}else{
    		$logFile = '/tmp/'. $filename .date("Y-m-d");
    	}
    	$fp = fopen($logFile, 'a');
    	$isNewFile = !file_exists($logFile);
    	if (flock($fp, LOCK_EX)) {
    		if ($isNewFile) {
    			chmod($logFile, 0666);
    		}
    		fwrite($fp, $msg . "\n");
    		flock($fp, LOCK_UN);
    	}
    	fclose($fp);
    }
    
    public function check_withdraw_money($uid){
    	echo "uid:" .  $uid . '<br />';
    	$this->load->model('base/longmoney_base', 'longmoney_base');
    	$longmoney = $this->longmoney_base->getUserLongMoney($uid);
    	echo "活期:" .  $longmoney . '<br />';
    	$this->load->model('base/balance_base', 'balance_base');
    	$balance = $this->balance_base->get_user_balance($uid);
    	echo "余额:" .  $balance . '<br />';
    	//定期
    	$this->load->model('logic/user_logic', 'user_logic');
    	$sum_product = $this->user_logic->get_not_finished_product($uid);
    	echo "定期:" . $sum_product . '<br />';
    	//充值
    	$this->load->model('base/user_log_base', 'user_log_base');
    	$pay_money = $this->user_log_base->sum_money_by_action($uid, 0);
    	echo "充值:" .  $pay_money . '<br />';
    	//取现
    	$withdraw_money = $this->user_log_base->sum_money_by_action($uid, 2);
    	echo "取现:" .  $withdraw_money . '<br />';
    	//活期利息
    	$this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
    	$lprofit = $this->ulp_profit_log_base->sum_user_longproduct_profit($uid);
    	echo "活期利息:" . $lprofit . '<br />';
    	//定期利息
    	$sum_product_profit = $this->user_logic->get_finished_product_profit($uid);
    	echo "定期利息:" . $sum_product_profit . '<br />';
    	//活动奖励
    	$activity_money = $this->user_log_base->sum_money_by_action($uid, 5);
    	echo "活动奖励:" . $activity_money . '<br />';
    	//邀请奖励
    	$invite_reward_money = $this->user_log_base->sum_money_by_action($uid, 6);
    	echo "邀请奖励:" . $invite_reward_money . '<br />';
    	//体验金
    	$tiyangjing_money = $this->user_log_base->sum_money_by_action($uid, 7);
    	echo "体验金奖励发放:" . $tiyangjing_money . '<br />';
    
    	$withdraw_failed = $this->user_log_base->sum_money_by_action($uid, 20);
    	echo "取现失败:" . $withdraw_failed . '<br />';
    	$withdraw_back = $this->user_log_base->sum_money_by_action($uid, 21);
    	echo "取现退回:" . $withdraw_back . '<br />';
    
    	//加的钱
    	$add_money = $pay_money + $activity_money + $invite_reward_money + $tiyangjing_money + $sum_product_profit + $lprofit;
    	//拥有的钱
    	$has_money = $balance + $sum_product + $longmoney;
    
    	$diff = $has_money - ($add_money - $withdraw_money);
    	echo "diff:" . $diff . '<br />';
    	if(abs($diff) > 0){
    		//记录
    		$log = array(
    				'uid' => $uid,
    				'do_withdraw' => $do_withdraw_money,
    				'diff' => round($diff,3)
    		);
    		$this->crontab_run(json_encode($log), 'do_withdraw_check.');
    		return false;
    	}else{
    		return true;
    	}
    }
    
    public function count_plat_money(){
        
        //$odate = $this->input->request('odate');
        $odate = date('Y-m-d', strtotime('-1 day'));
        $insert_data = array();
        $p_odate = date('Y-m-d');
        echo $odate . "<br />==============<br />";
        $insert_data['odate'] = $odate;
        //用户余额
        $this->load->model('base/balance_base', 'balance_base');
        $sum_balance = $this->balance_base->count_balance();
        $insert_data['balance'] = $sum_balance;
        echo '用户余额:' . $sum_balance . "<br />==============<br />";
        
        //当日手续费
        $this->load->model('base/withdraw_sxf_log_base', 'withdraw_sxf');
        $sxf = $this->withdraw_sxf->getSxfByDay($odate);
        $insert_data['sxf'] = $sxf ? $sxf : 0;
        echo '手续费:' . $sxf . "<br />==============<br />";
        
        //定期产品售出金额
        $this->load->model('base/pay_log_base', 'pay_log');
        $pay_money = $this->pay_log->getLogListByCtime($odate);
        $insert_data['pay'] = $pay_money ? $pay_money : 0;
        echo '充值:' . $pay_money . "<br />==============<br />";
        
        $plat_money = $this->pay_log->getLogListForPlatByCtime($odate);
        foreach ($plat_money as $plat){
        	if($plat['platform']=='jytpay'){
	        	$insert_data['pay_jyt'] = $plat['sum_amt'];
		        echo '金运通充值:' . $plat['sum_amt'] . "<br />==============<br />";
        	}else if($plat['platform']=='baofoo'){
        		$insert_data['pay_baofoo'] = $plat['sum_amt'];
        		echo '宝付充值:' . $plat['sum_amt'] . "<br />==============<br />";
        	}else if($plat['platform']=='fuiou'){
        		$insert_data['pay_fuiou'] = $plat['sum_amt'];
        		echo '富友充值:' . $plat['sum_amt'] . "<br />==============<br />";
        	}
        }
        
        
        //取现
        $this->load->model('base/withdraw_log_base', 'withdraw_log');
        $withdraw_money = $this->withdraw_log->getLogListBySucctime($odate);
        $insert_data['withdraw'] =  $withdraw_money;
        echo '取现:' . $withdraw_money . "<br />==============<br />";
        //定期定单金额购买
        $this->load->model('base/buy_log_base', 'buy_log_base');
        $buy_product = $this->buy_log_base->getLogListByCtime($odate);
        $insert_data['p_buy_log'] =  $buy_product ? $buy_product : 0;
        echo '定期购买:' . $buy_product . "<br />==============<br />";
        

        $this->load->model('base/product_repayment_log_base','product_repayment_log_base');
        $repayment = $this->product_repayment_log_base->getLogListByCtime($odate);
        $insert_data['repayment'] =  $repayment['money'];
        echo '定期还款本金:' . $repayment['money'] . "<br />==============<br />";
        $insert_data['repayment_profit'] =  $repayment['profit'];
        echo '定期还款利息:' . $repayment['profit'] . "<br />==============<br />";
        //定期用户购买金额
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $userbuy_product = $this->userproduct_base->getSumUserProduct($odate);
        $insert_data['p_userbuy'] =  $userbuy_product;
        echo '定期用户购买金额:' . $userbuy_product . "<br />==============<br />";
        
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $userbuy_all_product = $this->userproduct_base->getAllSumUserProduct($odate);
        $insert_data['p_all_userbuy'] =  $userbuy_all_product;
        echo '当前定期用户购买总额:' . $userbuy_all_product . "<br />==============<br />";
        
        $this->load->model('base/product_base', 'product_base');
        $product_sellmoney = $this->product_base->sum_sellout_product($p_odate);
        $insert_data['p_product_sellmoney'] =  $product_sellmoney? $product_sellmoney : 0;
        echo '定期产品售出金额:' . $product_sellmoney . "<br />==============<br />";
        
        //定期利息
        $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
        $profit = $this->up_profit_log_base->get_all_profit_with_odate($odate);
        $insert_data['p_profit'] =  $profit;
        echo '昨日定期利息:' . $profit . "<br />==============<br />";
        
        //活期--总额（总额）
        $this->load->model('base/longmoney_base', 'longmoney_base');
        $longmoney = $this->longmoney_base->sumlongmoney($odate);
        $insert_data['longmoney'] =  $longmoney;
        echo '活期总额:' . $longmoney . "<br />==============<br />";
        
        //活期--售出（昨日）
        $this->load->model('base/longproduct_base', 'longproduct_base');
        $lp_sellmoney = $this->longproduct_base->getLongProductSellmoney($odate);
        $insert_data['lp_sellout'] =  $lp_sellmoney;
        echo '活期产品售出:' . $lp_sellmoney . "<br />==============<br />";
        
        //活期用户购买
        $this->load->model('base/userlongproduct_base', 'userlongproduct_base');
        $lp_buy = $this->userlongproduct_base->getSumUserLongProduct($odate);
        $insert_data['lp_buy'] =  $lp_buy;
        echo '活期用户购买:' . $lp_buy . "<br />==============<br />";
        
        //活期--转出（昨日）
        $this->load->model('base/longtobalance_log_base', 'longtobalance_log_base');
        $ltob = $this->longtobalance_log_base->getLogListByCtime($odate);
        $insert_data['ltob'] =  $ltob ? $ltob : 0;
        echo '活期转出金额:' . $ltob . "<br />==============<br />";
        
        $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
        $real_longmoney = $this->ulp_profit_log_base->get_all_longmoney_with_odate($p_odate);
        $insert_data['real_longmoney'] =  $real_longmoney;
        echo '昨日活期:' . $real_longmoney . "<br />==============<br />";
        
        //活期利息
        $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
        $l_profit = $this->ulp_profit_log_base->get_all_profit_with_odate($p_odate);
        $insert_data['l_profit'] =  $l_profit;
        echo '昨日活期利息:' . $l_profit . "<br />==============<br />";
        
        //==========================================小活期结算 start========================================//
        //小活期--总额（总额）
//         $this->load->model('base/klmoney_base', 'klmoney_base');
//         $klmoney = $this->klmoney_base->sumklmoney($odate);
        $insert_data['klmoney'] =   0;
//         echo '小活期总额:' . $klmoney . "<br />==============<br />";
        
        //小活期活期--售出（昨日）
//         $this->load->model('base/klproduct_base', 'klproduct_base');
//         $klp_sellmoney = $this->klproduct_base->getKlProductSellmoney($odate);
        $insert_data['klp_sellout'] =  0;
//         echo '小活期产品售出:' . $klp_sellmoney . "<br />==============<br />";
        
        //小活期用户购买
//         $this->load->model('base/userklproduct_base', 'userklproduct_base');
//         $klp_buy = $this->userklproduct_base->getSumUserKlProduct($odate);
        $insert_data['klp_buy'] =  0 ;
//         echo '小活期用户购买:' . $klp_buy . "<br />==============<br />";
        
        //活期--转出（昨日）
//         $this->load->model('base/kltobalance_log_base', 'kltobalance_log_base');
//         $kltob = $this->kltobalance_log_base->getLogListByCtime($odate);
        $insert_data['kltob'] =  0;
//         echo '小活期转出金额:' . $kltob . "<br />==============<br />";
        
//         $this->load->model('base/uklp_profit_log_base', 'uklp_profit_log_base');
//         $real_uklmoney = $this->uklp_profit_log_base->get_all_klmoney_with_odate($odate);
        $insert_data['real_klmoney'] =   0 ;
//         echo '昨日活期结息金额:' . $real_uklmoney . "<br />==============<br />";
        
        //定期利息
//         $kl_profit = $this->uklp_profit_log_base->get_all_profit_with_odate($odate);
        $insert_data['kl_profit'] = 0;
//         echo '昨日活期利息:' . $kl_profit . "<br />==============<br />";
        
        //==========================================小活期结算 end========================================//
        
        $this->load->model('base/invite_base', 'invite_base');
        $invite_reward = $this->invite_base->sum_invite_rewardmoney_with_buytime($odate);
        $insert_data['invite_reward'] =  $invite_reward ? $invite_reward : 0;
        echo '邀请奖励:' . $invite_reward . "<br />==============<br />";
        
        $this->load->model('base/user_invitereward_base', 'user_invitereward_base');
        $invite_user_reward = $this->user_invitereward_base->sum_rewardmoney($odate);
        $insert_data['invite_user_reward'] =  $invite_user_reward ? $invite_user_reward : 0;
        echo '佣金奖励:' . $invite_user_reward . "<br />==============<br />";
        
        $this->load->model('base/activity_log_base','activity_log');
        $activity_reward = $this->activity_log->getLogListByCtime($odate);
        $insert_data['activity_reward'] =  $activity_reward ? $activity_reward : 0;
        echo '活动1%奖励:' . $activity_reward . "<br />==============<br />";

        $this->load->model('base/invite_first_buy_log_base','invite_first_buy_log');
        $invite_first_buy_reward = $this->invite_first_buy_log->getLogListByCtime($odate);
        $insert_data['i_first_buy'] =  $invite_first_buy_reward ? $invite_first_buy_reward : 0;
        echo '邀请首次购买:' . $invite_first_buy_reward . "<br />==============<br />";
        
       	$this->load->model('base/redbag_log_base','redbag_log_base');
    	$hongbao = $this->redbag_log_base->count_by_date($odate);
    	$insert_data['hongbao'] =  $hongbao ? $hongbao : 0;
    	echo '红包:' . $hongbao . "<br />==============<br />";
    	
    	$this->load->model('base/user_coupon_base','user_coupon_base');
    	$coupon = $this->user_coupon_base->getUserCouponUsedbyDate($odate);
    	$insert_data['coupon'] =  $coupon ? $coupon : 0;
    	echo '抵用券:' . $coupon . "<br />==============<br />";
        
    	$this->load->model('base/buchang_base','buchang_base');
    	$buchang = $this->buchang_base->getBuchangByDay($odate);
    	$insert_data['buchang'] =  $buchang ? $buchang : 0;
    	echo '补偿:' . $buchang . "<br />==============<br />";
    	
    	$this->load->model('base/user_expmoney_base','user_expmoney_base');
    	$exp_profit = $this->user_expmoney_base->sumUserExpProfitByDate($odate);
    	$insert_data['exp_profit'] =  $exp_profit ? $exp_profit : 0;
    	echo '体验金利息发放:' . $exp_profit . "<br />==============<br />";
    	
    	$this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
    	$withhold = $this->weehours_withdraw_log->getYesWithhold($odate);
    	$insert_data['withhold'] =  $withhold ? $withhold : 0;
    	echo '出款了的:' . $withhold . "<br />==============<br />";
    	
    	$this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
    	$yesnotWithhold = $this->weehours_withdraw_log->getYesnotWithhold($odate);
    	$insert_data['notwithhold'] =  $yesnotWithhold ? $yesnotWithhold : 0;
    	echo '没有出款的:' . $yesnotWithhold . "<br />==============<br />";
    	
    	$this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
    	$yesWithdraw = $this->withdraw_failed_log_base->getYesWithdraw($odate);
    	$insert_data['fall_withdraw'] =  $yesWithdraw ? $yesWithdraw : 0;
    	echo '退款了的:' . $yesWithdraw . "<br />==============<br />";
    	 
    	$this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
    	$yestnotWithdraw = $this->withdraw_failed_log_base->getYestnotWithdraw($odate);
    	$insert_data['fall_notwithdraw'] =  $yestnotWithdraw ? $yestnotWithdraw : 0;
    	echo '没有退款的:' . $yestnotWithdraw . "<br />==============<br />";
    	
    	$this->load->model('base/luckybag_accepted_base', 'luckybag_accepted_base');
    	$luckybag = $this->luckybag_accepted_base->getYesLuckybag($odate);
    	$insert_data['luckybag'] =  $luckybag ? $luckybag : 0;
    	echo '邀请红包:' . $luckybag . "<br />==============<br />";
    	$this->load->model('logic/product_logic', 'product_logic');
    	$stockinfo = $this->product_logic->getTotalStockMoneyByRepaymentDate($odate);
    	$insert_data['long_repayment'] = $stockinfo['totalStockMoney'];
    	$insert_data['long_repayment_profit'] = $stockinfo['totalStockProfit'];
    	echo '还款活期:' . $insert_data['long_repayment'] . "<br />==============<br />";
    	echo '还款活期利息:' . $insert_data['long_repayment_profit'] . "<br />==============<br />";
    	
        $this->load->model('base/qs_log_base','qs_log_base');
        $ret = $this->qs_log_base->add($insert_data);
        if($ret){
            echo 'OK';
        }else{
            echo 'faild';
        }
    }
    
    
//     public function count_invite_first(){
//         error_reporting(E_ALL);
//         $this->load->model('base/invite_first_buy_log_base','invite_first_buy_log');
//         $this->load->model('base/qs_log_base','qs_log_base');
//         for($i = 1; $i <= 98; $i++){
//             $odate = date('Y-m-d', strtotime('-'.$i.' day'));
            
//             $invite_first_buy_reward = $this->invite_first_buy_log->getLogListByCtime($odate);
//             $insert_data['i_first_buy'] =  $invite_first_buy_reward;
//             if($insert_data['i_first_buy']){
//                 $this->qs_log_base->update(array('i_first_buy' => $insert_data['i_first_buy']), array('odate' => $odate));
//             }
//             echo $odate . ':' . $invite_first_buy_reward . "<br />==============<br />";
//             //         echo '邀请首次购买:' . $invite_first_buy_reward . "<br />==============<br />";
            
            
//         }
        
//     }
  
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */