<?php

class test extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('admin_account_model', 'account');
        $this->load->model('admin_userproduct_model', 'userproduct');
        $this->load->model('admin_userlongproduct_model', 'userlongproduct');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_test_model','test');
        $this->load->model('admin_buy_log_model','buy_log');
        $this->load->model('admin_expmoney_log_model','expmoney_log');
        $this->load->model('admin_invite_first_buy_log_model','invite_first_buy_log');
        $this->load->model('admin_longproduct_buy_info_model','longproduct_buy_info');
    }
    
    public function regForTop(){
    	require_once(APPPATH."/libraries/top-sdk/TopSdk.php");
    	$c = new TopClient;
    	$c->appkey = '';//cmibank todo 
    	$c->secretKey = '';
    	for ($index=1;$index<47878;$index++){
    		$account = $this->account->getAccountByUid($index);
    		if(!empty($account)){
    			$user = $this->useridentity->getUseridentityByUid($index);
    			$nick = $account['account'];
    			if(!empty($user)){
    				$nick = $user['realname'];
    			}
		    	$req = new OpenimUsersUpdateRequest;
		    	$t_uid = 'cmibank'.$account['account'];
		    	$req->setUserinfos("{'userid':'".$t_uid."','mobile':'".$account['account']."','nick':'".$nick."'}");
		    	$resp = $c->execute($req);
    		}
    	}
    }
    public function send_tiqianhuikuang_msg(){
        exit;
        $sends = array();   //已发过的手机号
        include(APPPATH . 'libraries/submail.lib.php');
        $submail = new submail();
        $this->load->model('admin_product_model', 'product');
        $data = $this->product->getProductWithWhere(array('cid' => array(8800036,8800101)));
        $pids = array();
        foreach ($data as $_data){
            $pids[] = $_data['pid'];
        }
        $this->load->model('admin_account_model', 'account');
        $this->load->model('admin_userproduct_model', 'userproduct');
        for($i = 0; $i < 16; $i++){
            $up_array = $this->userproduct->getUserProductByPid($i, $pids);
            foreach ($up_array as $_up){
                $account = $this->account->getAccountByUid($_up['uid']);
                $phone = $account['account'];
                $pname = $_up['pname'];
                echo $_up['uid'] . '=>' . $phone . '=>' . $pname . '<br />'; 
                $submail->send_msg($phone,array('pname' => $pname),'xOeWc2');
            }
        }
    }
    
    //每日平台结算
    public function getOneDayBusiness(){
        error_reporting(E_ALL);
        $this->load->model('admin_balance_model','balance');
        $this->load->model('admin_longmoney_model','longmoney');
        $this->load->model('admin_up_profit_log_model','up_profit_log');
        $this->load->model('admin_ulpprofitlog_model','ulpprofitlog');
        $this->load->model('admin_exp_profit_log_model','exp_profit_log');
        $this->load->model('admin_user_expmoney_model','user_expmoney');
        $this->load->model('admin_expmoney_model','expmoney');
        //总资产                
       // 账户余额
       $balance = $this->balance->getSumBalanceMoney();
       $Tota_balance = ($balance[0]['sum(balance)'] ? $balance[0]['sum(balance)'] : 0);
       $data['Tota_balance'] = $Tota_balance;
       
       //定期投资总额
       $Tota_productmoney = 0;
       for($_table_index=0;$_table_index<16;$_table_index++){
           $productmoney = $this->userproduct->getSumProductMoney($_table_index);
           $Tota_productmoney += $productmoney[0]['SUM(money)'];
            
       }
       $data['Tota_productmoney'] = $Tota_productmoney;
       
       //活期总资产
       $longmoney = $this->longmoney->getSumLongMoney();
       $Tota_longmoney = ($longmoney[0]['sum(money)'] ? $longmoney[0]['sum(money)'] : 0);
       $data['Tota_longmoney'] = $Tota_longmoney;
       
       
       //昨日收益  = 昨日定期收益 + 昨日活期收益
       
       //昨日定期收益
       $TotalYest_up_proift = $this->up_profit_log->TotalYest_up_proift();
       $data['TotalYest_up_proift'] = $TotalYest_up_proift;
       
       //昨日活期收益
       $total_yesy_ulp_profit = $this->ulpprofitlog->total_yesy_ulp_profit();
       $data['total_yesy_ulp_profit'] = $total_yesy_ulp_profit;
       //累计收益 = 累计定期收益 + 累计活期收益
       
       //累计定期收益
       $total_up_profit = $this->up_profit_log->TotalCount_up_profit();
       $data['total_up_profit'] = $total_up_profit;
       
       //累计活期收益
       $Total_ulp_profit = $this->ulpprofitlog->Total_Count_ulp_profit();
       $data['Total_ulp_profit'] = $Total_ulp_profit;
       
       //当前定期收益
       $totalnow_profit = 0;
       $pid = array();
       for($_table_index=0;$_table_index<16;$_table_index++){
           $ProductPid = $this->userproduct->getProductPid($_table_index);
           if($ProductPid){
               $pid[] = $ProductPid;
           }
       }
       $pids = implode(",", $pid);
       if($pid){
           for($index = 0;$index<32;$index++){
                $up_profit_log = $this->up_profit_log->getUpProfitLog($index,$pids);
                $totalnow_profit += $up_profit_log[0]['sum(profit)'];
            }
       }
       $data['totalnow_profit'] = $totalnow_profit;
       
       //昨日体验金收益
       $TotalYestExpproift = 0;
       for($index = 0;$index<16;$index++){
           $exp_profit_log = $this->exp_profit_log->getTotalYestExpProfit($index);
           $TotalYestExpproift += $exp_profit_log[0]['sum(profit)'];
       }
       $data['TotalYestExpproift'] = $TotalYestExpproift;
       
       //累计体验金收益
       $totalexpprofit = 0;
       for($index = 0;$index<16;$index++){
           $exp_profit_log = $this->exp_profit_log->geTotalExpProfit($index,'');
           $totalexpprofit += $exp_profit_log[0]['sum(profit)'];
       }
       $data['totalexpprofit'] = $totalexpprofit;
       
       //当前体验金收益
//        $totalnowexp = 0;
//        for($index = 0;$index<16;$index++){
//            $ids[] = $this->user_expproduct->getUserExpProduct($index);
//        }
//        $ids = implode(",", $ids);
//        for($index = 0;$index<16;$index++){
//            $countProfit[] = $this->exp_profit_log->geTotalExpProfit($index,$ids);
//        }
//        foreach($countProfit as $val){
//            $totalnowexp += $val[0]['sum(profit)'];
//        }
       $data['totalnowexp'] = 0;
       
       //可投体验金
       $expmoney = $this->user_expmoney->sumUnusedExpmoney();
       $data['totalexpmoney'] = $expmoney;
            
       $data['odate'] = date('Y-m-d H:i:s',time());
       $this->load->model('admin_business_model','business');
       
       $ret = $this->business->addBusiness($data);
       if($ret){
           echo "getOneDayBusiness_success".$data['odate']."\r\n";          
       }
        
    }
    public function testbuyinfo(){
        // 100--500
        // 500-1000
        // 1000---2000
        // 2000---5000
        // 5000--8000
        // 8000
        $cfg = array('100~500' => 500, '500-1000' => 1000, '1000---2000' => 2000, '2000---5000' => 5000, '5000--8000' => 8000, '8000+' => 500000);
        $print_data = array();
        $uids = array(187664,187676,187677,187687,187689,187693,187698,187700,187710,187757,187781,187794,187802,187804,187805,187817,187830,187858,187887,187916,187943,187950,187952,187954,187957,187967,188032,188041,188047,188056,188058,188069,188071,188077,188081,188088,188123,188134,188149,188150,188193,188199,188219,188231,188234,188236,188240,188246,188269,188290,188291,188301,188316,188318,188330,188346,188353,188357,188363,188401,188409,188415,188504,188506,188508,188518,188572,188621,188622,188732,188736,188765,188768,188793,188812,188852,188854,188863,188865,188884,188922,188941,188945,188952,188957,188959,188961,188984,188985,188998,189014,189016,189018,189019,189021,189022,189024,189026,189028,189031,189075,189296,189325,189344,189347,189350,189356,189358,189359,189360,189364,189365,189366,189374,189377,189381,189384,189385,189387,189389,189392,189403,189410,189425,189426,189429,189438,189445,189450);
        foreach ($uids as $_uid){
            $this->load->model('base/admin_userproduct_model', 'userproduct');
            $data = $this->userproduct->getUserFirstProductByUid($_uid);
            $money = $data[0]['money'];
            foreach ($cfg as $key => $value){
                if($money < $value){
                    $print_data[$key]['num']++;
                    $print_data[$key]['money'][] = $money;
                    break;
                }
            }
            
        }
        print_r($print_data);
    }
    
    public function reset_product_remitId(){
        $this->load->model('admin_product_remit_model','product_remit');
        $this->load->model('admin_product_model', 'product');
        $data = $this->product_remit->getAllReimit();
        foreach ($data as $_data){
            $update = array();
            $update['status'] = 4;
            $update['remitid'] = $_data['rid'];
            $this->product->updatePorduct($_data['pid'], $update);
            echo "<br />";
        }
        
    }
    
    private function countProductProfit($start, $end, $money, $income){
        $days = (($end - $start)/86400) + 1;
        $profit = $income/100/360 * $money * $days;
        return $profit;
    }
    
    public function index(){
        $start = $this->input->post('start');
        $end = $this->input->post('end');
        $pt = $this->input->post('pt');
        //$start = '2015-08-17 00;00;00';
        //$end = '2015-08-18 00;00;00';
        $p= array();
        $uids = array();
        $account = $this->account->getAccountlist($start,$end,$pt);
        $count_reg = count($account); //注册用户数
        echo '注册用户'.$count_reg."<br/>";
        
        
        foreach ($account as $key=>$val){
            $uids[] = $val['uid'];
            echo '注册手机号码：'.$val['account'].'<br/>';
        }
        $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$uids),'',array(1000,0));
        $count_banka =count($useridentity); //绑定卡用户
        echo '绑卡用户'.$count_banka."<br/>";
        
        foreach ($useridentity as $val){
            $test['longmoney'] = $this->test->getuserlongproductby($val['uid'],$start,$end);
            if(empty($test['longmoney'][0]['money'])){
                $test['longmoney'][0]['money'] = 0;
            }
            $test['money'] = $this->test->getuserproductby($val['uid'],$start,$end);
            echo $val['phone']."--0--定期：".$test['money'][0]['sum(money)']."----活期：".$test['longmoney'][0]['sum(money)']."<br/>";
            
        }
    }
    function lastNWeek($ts, $n, $format = '%Y-%m-%d') {
        $ts = intval($ts);
        $n  = abs(intval($n));
     
        // 周一到周日分别为1-7
        $dayOfWeek = date('w', $ts);
        if (0 == $dayOfWeek)
        {
            $dayOfWeek = 7;
        }
        $lastNMonday = 7 * $n + $dayOfWeek - 1;
        $lastNSunday = 7 * ($n - 1) + $dayOfWeek;
        return array(
            strftime($format, strtotime("-{$lastNMonday} day", $ts)),
            strftime($format, strtotime("-{$lastNSunday} day", $ts))
        );
    }
    
    public function getms(){
        
        echo date('Y-m-01', strtotime('-2 month'));
        echo "<br/>";
        echo date('Y-m-t', strtotime('-2 month'));
        echo "<br/>";
    }
    

    
    public function xunhuang(){
        $odate = $this->input->post('odate');
        $this->bitongji($odate);
    }
    
    public function bitongjiweek(){
        error_reporting(E_ALL);
        $odate = date('Y-m-d');
        $cur = $this->lastNWeek(strtotime($odate), 0);
        $Bireportweek = $this->test->getBireport($cur[0],$cur[1]);
        $rtnBiweek = array();
        
        if(empty($Bireportweek)){
            exit('周没有用户购买记录 周数据统计停止计算');
        }
        
        foreach ($Bireportweek as $key=>$val){
           $rtnBiweek[$val['plat']]['activate'] += $val['activate'];
           $rtnBiweek[$val['plat']]['register'] += $val['register'];
           $rtnBiweek[$val['plat']]['daydeal'] += $val['daydeal'];
           $rtnBiweek[$val['plat']]['olddeal'] += $val['olddeal'];
           $rtnBiweek[$val['plat']]['countdeal'] += $val['countdeal'];
           $rtnBiweek[$val['plat']]['dealmoney'] += $val['dealmoney'];
           $rtnBiweek[$val['plat']]['daybuyuser'] += $val['daybuyuser'];
           $rtnBiweek[$val['plat']]['daymoney'] += $val['daymoney'];
           $rtnBiweek[$val['plat']]['daymoney_d'] += $val['daymoney_d'];
           $rtnBiweek[$val['plat']]['daymoney_h'] += $val['daymoney_h'];
           $rtnBiweek[$val['plat']]['daynumber'] += $val['daynumber'];
           $rtnBiweek[$val['plat']]['bangkashu'] += $val['bangkashu'];
           $rtnBiweek[$val['plat']]['newuid'] += $val['newuid'];
           $rtnBiweek[$val['plat']]['oldnum'] += $val['oldnum'];
           $rtnBiweek[$val['plat']]['oldmoney'] += $val['oldmoney'];
           $rtnBiweek[$val['plat']]['cdate'] = $cur[0].'--'.$cur[1];
        }
        $Nextnextweek = $this->buy_log->getBuylogUidsNextnextWeek($odate);
        $Nextweek = $this->buy_log->getBuylogUidsNextWeek($odate);
        
        $Nextnextweekbuyuser = $this->test->getdaybuyuser($Nextnextweek);
        foreach ($Nextnextweekbuyuser as $val){
            $rtnNextnext[$val['plat']][] = $val['uid'];
             
        }
        $Nextweekbuyuser = $this->test->getdaybuyuser($Nextweek);
        foreach ($Nextweekbuyuser as $val){
            $rtnNext[$val['plat']][] = $val['uid'];
             
        }
        foreach ($rtnNext as $key => $val){
            if(!empty($rtnNext[$key])){
                $fugou[$key] = array_intersect_assoc($val, $rtnNextnext[$key]);
            }
        }
        foreach ($fugou as $key=>$val){
            $trnfugou[$key] = count($val);
            $rtnBiweek[$key]['fugou'] = count($val);
            $rtnBiweek[$key]['cdate'] = $cur[0].'--'.$cur[1];
        }
        foreach ($rtnNextnext as $key=>$val){
            $rtnBiweek[$key]['qt'] = count($val);
        }
          
        foreach ($rtnBiweek as $key=>$val){
            $data['qt'] = $val['qt']?$val['qt'] :0 ;
            $data['fugou'] = $val['fugou']?$val['fugou'] :0 ;
            $data['fugoulv'] = ($val['fugou']/$val['qt'])*100 ;
            $data['olddeal'] = $val['olddeal']?$val['olddeal'] :0 ;
            $data['daymoney'] = $val['daymoney']?$val['daymoney'] :0 ;
            $data['daymoney_h'] = $val['daymoney_h']?$val['daymoney_h'] :0 ;
            $data['daymoney_d'] = $val['daymoney_d']?$val['daymoney_d'] :0 ;
            $data['daynumber'] = $val['daynumber']?$val['daynumber'] :0 ;
            $data['daybuyuser'] = $val['daybuyuser']?$val['daybuyuser'] : 0 ;
            $data['countdeal'] = $val['daydeal'] + $val['olddeal'];
            $data['cdate'] = $val['cdate'];
            $data['plat'] = $key;
            $data['register'] = $val['register'] ? $val['register'] : 0 ;
            $data['dealmoney'] = $val['dealmoney'] ? $val['dealmoney'] : 0;
            $data['daydeal'] = $val['daydeal'] ? $val['daydeal'] : 0 ;
            $data['bangkashu'] = $val['bangkashu'] ? $val['bangkashu'] : 0;
            $data['newuid'] = $val['newuid'] ? $val['newuid'] : 0;
            $data['oldnum'] = $val['oldnum'] ? $val['oldnum'] : 0;
            $data['oldmoney'] = $val['oldmoney'] ? $val['oldmoney'] : 0;
               
             
            $data['arpu'] = ($val['daymoney']/$val['daybuyuser']);
            $data['deal_reg'] = ($val['newuid']/$val['register'])*100;
            $data['daydeal_bangka'] = ($val['newuid']/$val['bangkashu'])*100;
            $data['bangka_reg'] = ($val['bangkashu']/$val['register'])*100;
            $this->test->insertbiWeek($data);
            echo "bitongjiweek_success".$data['odate']."\r\n";
        }
        
    }
    //运营日报
    public function bitongji() {
        error_reporting(E_ALL);
        $odate = date('Y-m-d');
        //$odate = $this->uri->segment(3);
        $bi = array();
        $uids = array();
        $reg = array();
        $platinfo = $this->account->getGROUPplat($odate);
        $plat = array();
        $data = array();
        $yestodate = date('Y-m-d', strtotime($odate) - 86400);
        if(empty($platinfo)){
            exit('运营日报 指定范围没有用户 终止计算');
        }
        foreach ($platinfo as $key => $val) {
            $bi[$val['plat']] = $this->account->getGroupByplat($val['plat'], $odate);
            $reg[$val['plat']] = $this->useridentity->searchbk($bi[$val['plat']]);
            $plat[$val['plat']]['bangkashu'] = $this->useridentity->bangkashu($bi[$val['plat']]);
            $plat[$val['plat']]['register'] = $val['register'];
        }

        // 新注册用户
        foreach ($reg as $plat_name => $val) {
            $money_d = 0;
            $count_d = 0;
            $money_h = 0;
            $count_h = 0;
            $c = 0;
            $newuid_d = array();
            $newuid_h = array();
            foreach ($val as $index => $_val) {
                $_d = $this->userproduct->getUserProductInUid($index, implode(',', $_val), $odate);
                $_h = $this->userlongproduct->getUserLongProductInUid($index, implode(',', $_val), $odate);
                $bishu_d = count($_d);
                if ($bishu_d) {
                    $count_d += $bishu_d;
                    foreach ($_d as $k => $v) {
                        $money_d += $v['money'];
                    }
                }
                $bishu_h = count($_h);
                if ($bishu_h) {
                    $count_h += $bishu_h;
                    foreach ($_h as $k => $v) {
                        $money_h += $v['money'];
                    }
                }

                $d_number = $this->userproduct->getNewUserNumber($index, implode(',', $_val), $odate);
                $number_d = count($d_number);
                if ($number_d) {
                    foreach ($d_number as $val) {
                        $newuid_d[] = $val['uid'];
                    }
                }

                $h_number = $this->userlongproduct->getNewLongUserNumber($index, implode(',', $_val), $odate);
                $number_h = count($h_number);
                if ($number_h) {
                    foreach ($h_number as $val) {
                        $newuid_h[] = $val['uid'];
                    }
                }
            }
            $plat[$plat_name]['newuid'] = count(array_unique(array_merge($newuid_d, $newuid_h))); //当日新增交易用户
            $plat[$plat_name]['dealmoney'] = $money_d + $money_h; //单日新增用户总交易额
            $plat[$plat_name]['dealmoney_h'] = $money_h; //单日新增用户活期总交易额
            $plat[$plat_name]['dealmoney_d'] = $money_d; //单日新增用户定期总交易额
            $plat[$plat_name]['daydeal'] = $count_d + $count_h; //单日新增用户总交易必输
            $plat[$plat_name]['cdate'] = $yestodate;
        }

        //单日购买笔数
        for ($index = 0; $index < 16; $index++) {
            if (!isset($daynumber_d['count'])) {
                $daynumber_d['count'] = 0;
            }
            $dBuyuser = $this->test->daybuyuser($index, 'd', $odate);
            $d_n = array();
            if ($dBuyuser) {
                $d_n[] = implode(',', $dBuyuser);
            }
        }
        for ($index = 0; $index < 16; $index++) {
            if (!isset($daynumber_h['count'])) {
                $daynumber_h['count'] = 0;
            }
            $hBuyuser = $this->test->daybuyuser($index, 'h', $odate);
            if ($hBuyuser) {
                $h_n[] = implode(',', $hBuyuser);
            }
        }
        $uids = implode(',', array_merge(array_filter($d_n), array_filter($h_n)));

        $daybuyuser = $this->test->getdaybuyuser($uids);
        $daybuyusers = $this->test->getdaybuyusers($uids);
        foreach ($daybuyuser as $val) {
            $index = $val['uid'] % 16;
            $daypeople[$val['plat']][$index][] = $val['uid'];
        }
        foreach ($daybuyusers as $key => $val) {
            $plat[$val['plat']]['daybuyuser'] = $val['daybuyuser'];
        }

        foreach ($daypeople as $plat_name => $val) {
            $money_d = 0;
            $count_d = 0;
            $money_h = 0;
            $count_h = 0;
            foreach ($val as $index => $_val) {
                $_d = $this->userproduct->getUserProductInUid($index, implode(',', $_val), $odate);
                $_h = $this->userlongproduct->getUserLongProductInUid($index, implode(',', $_val), $odate);
                $bishu_d = count($_d);
                if ($bishu_d) {
                    $count_d += $bishu_d;
                    foreach ($_d as $k => $v) {
                        $money_d += $v['money'];
                    }
                }
                $bishu_h = count($_h);
                if ($bishu_h) {
                    $count_h += $bishu_h;
                    foreach ($_h as $k => $v) {
                        $money_h += $v['money'];
                    }
                }
            }
            $plat[$plat_name]['daymoney'] = $money_d + $money_h;
            $plat[$plat_name]['daymoney_h'] = $money_h;
            $plat[$plat_name]['daymoney_d'] = $money_d;
            $plat[$plat_name]['daynumber'] = $count_d + $count_h;
            $plat[$plat_name]['cdate'] = $yestodate;
        }

        //未交易老用户
        $olduser = $this->test->getolduser($odate);
        $oldusers = $this->test->getoldusers($odate);
        foreach ($oldusers as $key => $val) {
            $plat[$val['plat']]['oldnum'] = $val['oldnum'];
        }
        foreach ($olduser as $val) {
            $index = $val['uid'] % 16;
            $oldpeople[$val['plat']][$index][] = $val['uid'];
        }
        foreach ($oldpeople as $plat_name => $val) {
            $money_d = 0;
            $count_d = 0;
            $money_h = 0;
            $count_h = 0;
            foreach ($val as $index => $_val) {
                $_d = $this->userproduct->getUserProductInUid($index, implode(',', $_val), $odate);
                $_h = $this->userlongproduct->getUserLongProductInUid($index, implode(',', $_val), $odate);
                $bishu_d = count($_d);
                if ($bishu_d) {
                    $count_d += $bishu_d;
                    foreach ($_d as $k => $v) {
                        $money_d += $v['money'];
                    }
                }
                $bishu_h = count($_h);
                if ($bishu_h) {
                    $count_h += $bishu_h;
                    foreach ($_h as $k => $v) {
                        $money_h += $v['money'];
                    }
                }
            }
            $plat[$plat_name]['oldmoney'] = $money_d + $money_h;
            $plat[$plat_name]['oldmoney_h'] = $money_h;
            $plat[$plat_name]['oldmoney_d'] = $money_d;
            $plat[$plat_name]['olddeal'] = $count_d + $count_h;
            $plat[$plat_name]['cdate'] = $yestodate;
        }
        $qtuids = $this->buy_log->getBulylogUidsbyQiantian($odate);
        $ztuids = $this->buy_log->getBulylogUidsbyZuotian($odate);
        $qtbuyuser = $this->test->getdaybuyuser($qtuids);
        foreach ($qtbuyuser as $val) {
            $rtnqt[$val['plat']][] = $val['uid'];
        }

        $ztybuyuser = $this->test->getdaybuyuser($ztuids);
        foreach ($ztybuyuser as $val) {
            $rtnzt[$val['plat']][] = $val['uid'];
        }

        foreach ($rtnzt as $key => $val) {
            if (!empty($rtnqt[$key])) {
                $fugou[$key] = array_intersect_assoc($val, $rtnqt[$key]);
            }
        }

        foreach ($fugou as $key => $val) {
            $trnfugou[$key] = count($val);
            $plat[$key]['fugou'] = count($val);
            $plat[$key]['cdate'] = $yestodate;
        }

        foreach ($rtnqt as $key => $val) {
            $plat[$key]['qt'] = count($val);
        }
        foreach ($plat as $key => $val) {
            $data['qt'] = $val['qt'] ? $val['qt'] : 0;
            $data['fugou'] = $val['fugou'] ? $val['fugou'] : 0;
            $data['fugoulv'] = ($val['fugou'] / $val['qt']) * 100;
            $data['olddeal'] = $val['olddeal'] ? $val['olddeal'] : 0;
            $data['daymoney'] = $val['daymoney'] ? $val['daymoney'] : 0;
            $data['daymoney_h'] = $val['daymoney_h'] ? $val['daymoney_h'] : 0;
            $data['daymoney_d'] = $val['daymoney_d'] ? $val['daymoney_d'] : 0;
            $data['daynumber'] = $val['daynumber'] ? $val['daynumber'] : 0;
            $data['daybuyuser'] = $val['daybuyuser'] ? $val['daybuyuser'] : 0;
            $data['countdeal'] = $val['daydeal'] + $val['olddeal'];
            $data['cdate'] = $val['cdate'];
            $data['plat'] = $key;
            $data['register'] = $val['register'] ? $val['register'] : 0;
            $data['dealmoney'] = $val['dealmoney'] ? $val['dealmoney'] : 0;
            $data['dealmoney_d'] = $val['dealmoney_d'] ? $val['dealmoney_d'] : 0;
            $data['dealmoney_h'] = $val['dealmoney_h'] ? $val['dealmoney_h'] : 0;
            $data['daydeal'] = $val['daydeal'] ? $val['daydeal'] : 0;
            $data['bangkashu'] = $val['bangkashu'] ? $val['bangkashu'] : 0;
            $data['newuid'] = $val['newuid'] ? $val['newuid'] : 0;
            $data['oldnum'] = $val['oldnum'] ? $val['oldnum'] : 0;
            $data['oldmoney'] = $val['oldmoney'] ? $val['oldmoney'] : 0;
            $data['oldmoney_d'] = $val['oldmoney_d'] ? $val['oldmoney_d'] : 0;
            $data['oldmoney_h'] = $val['oldmoney_h'] ? $val['oldmoney_h'] : 0;

            $data['arpu'] = $val['daymoney'] / $val['daybuyuser'];
            $data['deal_reg'] = ($val['newuid'] / $val['register']) * 100;
            $data['daydeal_bangka'] = ($val['newuid'] / $val['bangkashu']) * 100;
            $data['bangka_reg'] = ($val['bangkashu'] / $val['register']) * 100;
            $this->test->insertbi($data);
        }
        echo "bitongji_success" . $data['odate'] . "\r\n";
    }

    public function getPayGroupbyUid(){
           for($index=29;$index<=46;$index++){
               $user[] = $this->test->getPay_log($index);
           }
           foreach ($user as $key=>$val){
               if($key == 0){
                   $first_arr = $val;
               }else{
                  foreach ($val as $_uid => $_money) {
                      if(isset($first_arr[$_uid])){
                          $first_arr[$_uid] += $_money;
                      }else{
                          $first_arr[$_uid] = $_money;
                      }
                  }
               }
               
           }
           $yb = 0;
           $wb = 0;
           $lq = 0;
           $yw = 0;
           $lw = 0;
           $ww = 0;
           $other = 0;
          foreach ($first_arr as $money){
              if($money<100){
                  $yb +=1;
              }else if($money>=100 && $money<500){
                  $wb +=1;
              }else if($money>=500 && $money<2000){
                  $lq +=1;                    
              }else if($money>=2000 && $money<10000){
                  $yw +=1;
              }else if($money>=10000 && $money<20000){
                  $lw +=1;
              }else if($money>=20000 && $money<50000){
                  $ww +=1;
              }else{
                  $other +=1;
              }

               
          }
          echo "一百以下共计".$yb."人"."<br/>";;
          echo "一百到五百之间共计".$wb."人"."<br/>";
          echo "五百到两千之间共计".$lq."人"."<br/>";
          echo "两千到一万之间共计".$yw."人"."<br/>";
          echo "一万到两万之间共计".$lw."人"."<br/>";
          echo "两万到五万之间共计".$ww."人"."<br/>";
          echo "五万以上共计".$other."人"."<br/>";
 
       } 
//     public function getBuyLongproductUser(){
//         $index = 0;
//         for($index;$index<=15;$index++){
//             $test[] = $this->test->getUserLongProduct($index);
//         }
//         $uids = implode(',', $test);
//         $user = $this->test->search_pz_user_identity($uids);
//         foreach ($user as $key=>$val){
//             echo $val['phone'].' '.$val['realname'].'<br/>';
//         }
//     }
       
       //某个时间段发送体验金
       public function sum_expmoney(){
           $where = array();
           $expmoney = 0;
           $num = 0;
           for ($index=0;$index<16;$index++){
               $expmoney += $this->expmoney_log->getexpmoney_log_list($index,$where,$order='',$limit='');
               $num += $this->expmoney_log->getexpmoney_log_num($index,$where,$order,$limit='');
           }
           print_r("总消耗体验金".$expmoney."人数".$num);
           
       }
       //每个月首投费用
       public function sum_invite_first_buy(){
           $invite_first_buy_log = 0;
           for ($index=44;$index<50;$index++){
                $invite_first_buy_log += $this->invite_first_buy_log->get_invite_first_buy_list($index,$where='',$order='',$limit='');
           }
          print_r($invite_first_buy_log);
       }
       //11月前注册的用户
       public function sum_product_money(){
           $money = 0;
           $uids = $this->test->get_11_yue_berfor();
           for($index=0;$index<16;$index++){
              $money +=  $this->test->get_product_buy_info_by_uids($index,$uids);
           }
           echo $money;

       }
       
       //总ios购买定期金额
       public function sumProductByIos(){
           $moneyiso = 0;
           $uids = $this->account->getUidsByIos();
           for ($index=0;$index<16;$index++){
              $moneyiso += $this->test->sumProductByIos($index,$uids);
           }
           echo "定期ios购买金额".$moneyiso;
          
       }
       //总ios购买活期金额
       public function sumLongProductByIos(){
           $longmoneyiso = 0;
           $uids = $this->account->getUidsByIos();
           for ($index=0;$index<16;$index++){
               $longmoneyiso += $this->test->sumLongProductByIos($index,$uids);
           }
           echo "活期ios购买金额".$longmoneyiso;
       }
       //总购买用户
       public function countBuyNum(){
         $uids = $this->account->getUidsByIos();
         $this->test->countProductByNum(1,$uids);
       }
       
       //邀请人数中，首投超过2000元的用户多少
       public function invitedayu(){
            $uids = $this->test->invitetime();
            $num = 0;
            for($index=49;$index<=53;$index++){
               $num += $this->test->moneydayu($index,$uids);
            }
        
       }

       public function buyProductNumber(){
           $start = trim($this->input->post('start'));
           $end = trim($this->input->post('end'));
           $num = 0;
           for ($index=0;$index<16;$index++){
               $num += $this->test->buyProductNumber($index,$start,$end);
           }
           print_r($num);

         
       }
       
       public function buyProductUId(){
           $num = 0;
           for ($index=0;$index<16;$index++){
               $this->test->buyProductUId($index);
           }

       }
       public function buyLongProductUId(){
           $num = 0;
           for ($index=0;$index<16;$index++){
               $this->test->buyLongProductUid($index);
           }
       
       }
       public function buyLongproductNumber(){
           $start = trim($this->input->post('start'));
           $end = trim($this->input->post('end'));
           $num = 0;
           for ($index=0;$index<16;$index++){
               $num += $this->test->buyLongProductNumber($index,$start,$end);
           }
           print_r($num);
       }
       
       public function buyProductNumber2(){
           $num = 0;
           for ($index=0;$index<16;$index++){
               $num += $this->test->buyProductNumber2($index);
           }
           print_r($num);
       }
       public function buyLongproductNumber2(){
            $num = 0;
           for ($index=0;$index<16;$index++){
               $num += $this->test->buyLongProductNumber2($index);
           }
           print_r($num);
       }
       
      
       
    
}