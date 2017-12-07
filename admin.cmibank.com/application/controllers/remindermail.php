<?php
/**
 * 清算-催债
 * * */
class remindermail extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '清算-催债') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_product_backmoney_model','product_backmoney');
        $this->load->model('admin_contract_model','contract');
        $this->load->model('admin_remindermail_model','remindermail');
        $this->load->model('admin_emailmanage_model','emailmanage');
        $this->load->model('admin_stock_product_model','stock_product');
        $this->load->model('admin_longmoney_income_log_model','longmoney_income_log');
        $this->load->model('admin_product_remit_model','product_remit');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
            
            $now=time();//开始的日期
            $i=7;
            $aa =  file_get_contents("http://static1.cmibank.com/tpl/specialdays.json");
            $cc = json_decode($aa, true);
            foreach($cc['workdays'] as $key=>$val){
            	$rtnworddays[] = strtotime($val['date']);
            }
            foreach($cc['holidays'] as $key=>$val){
            	$rtnholidays[] = strtotime($val['date']);
            }
            $working = $rtnworddays;
            $holiday = $rtnholidays;
            $day=date("d",$now);
            $w=$i;
            while(true){
                $newdate=mktime(0, 0, 0,date("m",$now),$day+$w,date("Y",$now));
                if(in_array($newdate, $holiday)){
                    $w++;
                }else{
                    if(date("w",$newdate)!=0&&date("w",$newdate)!=6)
            		{
            			break;
                    }else
            		{
            			if(in_array($newdate, $working))
            			{
            				break;
            			}
            		 	$w++;
            		}
                }
            }
        	$page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
            $searchParam=array();
            $backmoneylist=array();
            if($this->input->request('op') == "search"){
	            $searchcorname = $this->input->post('searchcorname');
	            $searchcon_number = $this->input->post('searchcon_number');
	            $stime = $this->input->post('stime');
	            $etime = $this->input->post('etime');
	            
	            if(!empty($searchcon_number)){
	            	$searchParam['searchcon_number']=$searchcon_number;
	            	$data['searchcon_number'] = $searchcon_number;
	            }
	            if(!empty($searchcorname)){
	            	$searchParam['searchcorname']=$searchcorname;
	            	$data['searchcorname'] = $searchcorname;
	            }
	            $searchParam['ismail']=0;
            }else{
            	$searchParam['ismail']=0;
            }
            $searchParam['today']=$newdate;
            $backmoneylist = $this->getProductBackMoney($searchParam,array($psize, $offset));
            $count=0;
            if(!empty($backmoneylist)){
            	$count = count($this->product_backmoney->getBackmoneyByCondition($searchParam));
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'notice/index?page=' . $page;
                 
            }else{
                $data['count'] = 0;
                $data['pageNum']    = 0;
                $data['numPerPage'] = 0;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1104');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $data['list'] = $backmoneylist;
            $this->load->view('/remindermail/v_index',$data);
        }
    }
    public function noRemindermail(){
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        } else {
        	$page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
            $searchParam=array();
            $backmoneylist=array();
            if($this->input->request('op') == "search"){
	            $searchcorname = $this->input->post('searchcorname');
	            $searchcon_number = $this->input->post('searchcon_number');
	            $stime = $this->input->post('stime');
	            $etime = $this->input->post('etime');
	            
	            if(!empty($searchcon_number)){
	            	$searchParam['searchcon_number']=$searchcon_number;
	            	$data['searchcon_number'] = $searchcon_number;
	            }
	            if(!empty($searchcorname)){
	            	$searchParam['searchcorname']=$searchcorname;
	            	$data['searchcorname'] = $searchcorname;
	            }
	            if(!empty($stime)){
	            	$searchParam['stime']=$stime;
	            	$data['stime'] = $stime;
	            }
	            if(!empty($etime)){
	            	$searchParam['etime']=$etime;
	            	$data['etime'] = $etime;
	            }
	            $searchParam['ismail']=0;
            }else{
            	$searchParam['ismail']=0;
            }
            $backmoneylist = $this->getProductBackMoney($searchParam,array($psize, $offset));
            $count=0;
            if(!empty($backmoneylist)){
            	$count = count($this->product_backmoney->getBackmoneyByCondition($searchParam));
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'notice/index?page=' . $page;
                 
            }else{
                $data['count'] = 0;
                $data['pageNum']    = 0;
                $data['numPerPage'] = 0;
            }
            $data['list'] = $backmoneylist;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1106');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $this->load->view('/remindermail/v_noRemindermail',$data);
        }
    }
    
    public function alreadyRemindermail(){
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
            $searchParam=array();
            $backmoneylist=array();
            if($this->input->request('op') == "search"){
	            $searchcorname = $this->input->post('searchcorname');
	            $searchcon_number = $this->input->post('searchcon_number');
	            $stime = $this->input->post('stime');
	            $etime = $this->input->post('etime');
	            
	            if(!empty($searchcon_number)){
	            	$searchParam['searchcon_number']=$searchcon_number;
	            	$data['searchcon_number'] = $searchcon_number;
	            }
	            if(!empty($searchcorname)){
	            	$searchParam['searchcorname']=$searchcorname;
	            	$data['searchcorname'] = $searchcorname;
	            }
	            if(!empty($stime)){
	            	$searchParam['stime']=$stime;
	            	$data['stime'] = $stime;
	            }
	            if(!empty($etime)){
	            	$searchParam['etime']=$etime;
	            	$data['etime'] = $etime;
	            }
	            $searchParam['ismail']=1;
            }else{
            	$searchParam['ismail']=1;
            }
            $backmoneylist = $this->getProductBackMoney($searchParam,array($psize, $offset));
            $count=0;
            if(!empty($backmoneylist)){
            	$count = count($this->product_backmoney->getBackmoneyByCondition($searchParam));
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'notice/index?page=' . $page;
                 
            }else{
                $data['count'] = 0;
                $data['pageNum']    = 0;
                $data['numPerPage'] = 0;
            }
            $data['list'] = $backmoneylist;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1107');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $data['longProductIncome'] = $this->longmoney_income_log->getLongIncome();
            $this->load->view('/remindermail/v_alreadyRemindermail',$data);
        }
    }
    
		public function getProductBackMoney($params,$limit=''){
			$backmoneylist = array();
			$backmoneylist = $this->product_backmoney->getBackmoneyByCondition($params,$limit);
			if(!empty($backmoneylist)){
				foreach ($backmoneylist as $key=>$val){
					$productList = $this->product->getProductList(array('cid'=>$val['cid']), '', '');
					$totalProfit = 0;
					if(!empty($productList)){
						foreach ($productList as $product){
							if($product['sellmoney']>0){
								$days = $this->diff_days($product['uistime'],$product['uietime']);
								$totalProfit=$totalProfit+$this->countProductProfit($days,$product['sellmoney'],$product['income']);
							}
						}
					}
					$backmoneylist[$key]['totalProfit'] = $totalProfit;
				}
			}
			return $backmoneylist;
		}
       public function infoemial($bid){
           error_reporting(0);
           $product_backmoney = $this->product_backmoney->getProductBackmoneyByBid($bid);
           $backmoney_plus_profit = 0;
           $BackMoneyServer = 0;
           $contract =  $this->contract->getContractByCid($product_backmoney['cid']);
           $emailmanage = $this->emailmanage->getEmailmanageBycorid($contract['corid']);
           $longmoney = 0;
           if($product_backmoney['is_stock'] == 1){
				$stock_product = $this->stock_product->getStockProductList(array('cid'=>$product_backmoney['cid']));
				$longmoney=$stock_product[0]['stockmoney'];
           }
           if(!empty($product_backmoney['pids'])){
	           	$remit = $this->product_remit->getctimebycid($product_backmoney['cid']);
               $product = $this->product->getProductInPid($product_backmoney['pids']);
               foreach ($product as $key=>$_val){
                   $backmoney_plus_profit += round($this->getBackMoney_plus_Profit($_val['sellmoney'],$_val['income'],$_val['uistime'],$_val['uietime']),2);
               }
           }
           $backmoney = round($this->getBackMoney($contract['con_money'],$contract['con_income'],date('Y-m-d',$contract['remittime']),$contract['repaymenttime']),2);
           $longprofit = $this->countProductProfit( $this->diff_days(date('Y-m-d',$contract['remittime']), $contract['repaymenttime']),$longmoney,9);
           $money_plus_profit = round($longmoney+$backmoney_plus_profit+$longprofit,2);
           $moneyServer = round($backmoney-$money_plus_profit,2);
           $myfile = fopen("send_email_info.txt", "r") or die("Unable to open file!");
           $emailcontent =  fread($myfile,filesize("send_email_info.txt"));
           fclose($myfile);
           
           $big_money_plus_profit = $this->changbig($money_plus_profit);
           $big_moneyServer = $this->changbig($moneyServer);
           $cietime = date("Y年m月d日 ",strtotime($product_backmoney['cietime']));
           $con_number = $contract['con_number'];
           $bjx_zhanghao = ""; //金运通账号
           $fwf_zhanghao = "50131000620276073"; //万米账号
           $bigbackmoney = $this->changbig($backmoney);
  
           $emailcontent = str_replace("(*本加息小写金额*)",$money_plus_profit,$emailcontent);
           $emailcontent = str_replace("(*服务费小写金额*)",$moneyServer,$emailcontent);
           $emailcontent = str_replace("(*本加息大写金额*)",$big_money_plus_profit,$emailcontent);
           $emailcontent = str_replace("(*服务费大写金额*)",$big_moneyServer,$emailcontent);
           $emailcontent = str_replace("(*截止日*)",$cietime,$emailcontent);
           $emailcontent = str_replace("(*回款小写金额*)",$backmoney,$emailcontent);
           $emailcontent = str_replace("(*回款大写金额*）",$bigbackmoney,$emailcontent);
           $emailcontent = str_replace("(*合同编号*)",$con_number,$emailcontent);
           $emailcontent = str_replace("(*富友账号*)",$bjx_zhanghao,$emailcontent);
           $emailcontent = str_replace("(*万米账号*)",$fwf_zhanghao,$emailcontent);
           
           $data['emailcontent'] = $emailcontent;
           $data['bid'] = $bid;
           $data['emailmanage'] = $emailmanage;
           $this->load->view('/remindermail/v_infoemail',$data);
       }
       
       public function getBackMoney($con_money,$income,$start,$end){
       	$num = $this->diff_days($start, $end);
       	return $con_money+($con_money*$income*$num/36000);
       }
       
       private function countProductProfit($days, $money, $income){
       	$profit = $income/100/365 * $money * $days;
       	return $profit;
       }
       
       public function getBackMoney_plus_Profit($sellmoney,$income,$start,$end){
           $num = $this->diff_days($start, $end);
           return $sellmoney+($sellmoney*$income*$num/36500);
       
       }
       public function getBackMoneyServer($sellmoney,$con_income,$income,$start,$end){
           $num = $this->diff_days($start, $end);
           return $sellmoney*($con_income-$income)*$num/36000;
       }
       public function diff_days($start, $end){
           list($a_year, $a_month, $a_day) = explode('-', $start);
           list($b_year, $b_month, $b_day) = explode('-', $end);
           $a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
           $b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
           return abs(($a_new-$b_new)/86400) + 1;
       }
//     public function infoemial($bid){
//         error_reporting(0);
//         $myfile = fopen("send_email_info.txt", "r") or die("Unable to open file!");
//         $emailcontent =  fread($myfile,filesize("send_email_info.txt"));
//         fclose($myfile);
        
//         $product_backmoney = $this->product_backmoney->getProductBackmoneyByBid(array('bid' => $bid));
           
//         $contract =  $this->contract->getContractByCid($product_backmoney['cid']);
//         $emailmanage = $this->emailmanage->getEmailmanageBycorid($contract['corid']);
       
       
//         $con_number = $contract['con_number'];
//         $cietime = date("Y年m月d日 ",strtotime($product_backmoney['cietime']));
//         $backmoney = $product_backmoney['backmoney']-($product_backmoney['remitmoney']*$contract['con_bzjbl']/100);
//         $bigbackmoney = $this->changbig($backmoney);
        
//         $bjx_zhanghao = "695097684"; //金运通账号
//         $fwf_zhanghao = "121916234710901"; //上海万米账号
 

//         $emailcontent = str_replace("(*姓名*)","王丹",$emailcontent);
//         $emailcontent = str_replace("(*截止日*)",$cietime,$emailcontent);
//         $emailcontent = str_replace("(*小写金额*)",$backmoney,$emailcontent);
//         $emailcontent = str_replace("(*大写金额*）",$bigbackmoney,$emailcontent);
//         $emailcontent = str_replace("(*合同编号*)",$con_number,$emailcontent);
//         $emailcontent = str_replace("(*金运账号*)",$bjx_zhanghao,$emailcontent);
//         $emailcontent = str_replace("(*万米账号*)",$fwf_zhanghao,$emailcontent);
        
//         $data['emailcontent'] = $emailcontent;
//         $data['emailmanage'] = $emailmanage;
//         $data['bid'] = $bid;
//         $this->load->view('/remindermail/v_infoemail',$data);
//     }
    
    public function changbig($ns){
        static $cnums = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖"), 
        $cnyunits = array("圆","角","分"), 
        $grees = array("拾","佰","仟","万","拾","佰","仟","亿"); 
        list($ns1,$ns2) = explode(".",$ns,2); 
        $ns2 = array_filter(array($ns2[1],$ns2[0])); 
        $ret = array_merge($ns2,array(implode("", $this->_cny_map_unit(str_split($ns1), $grees)), "")); 
        $ret = implode("",array_reverse($this->_cny_map_unit($ret,$cnyunits))); 
        return str_replace(array_keys($cnums), $cnums,$ret); 
    }
    function _cny_map_unit($list,$units){
        $ul = count($units);
        $xs = array();
        foreach (array_reverse($list) as $x){
            $l = count($xs);
            if($x!="0" || !($l%4)){
                $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
            }
            else{
                $n=is_numeric($xs[0][0]) ? $x : '';
            }
            array_unshift($xs, $n);
        }
        return $xs;
    }
    public function sendemail(){
        if($this->input->request('op') == 'sendemail'){
            $email = trim($this->input->post('address'));
            $cc = trim($this->input->post('copyaddress'));
            $vars = trim($this->input->post('content'));
            $bid = trim($this->input->post('bid'));
            $subject = trim($this->input->post('subject'));
            $email_tpl_num = 'VKgdW2';
            $ret=$this->remindermail->send_email($email,$cc,$subject, array('content'=>$vars), $email_tpl_num);
            $rn = array();
            $rn =json_decode($ret,true);
            if($rn['status'] == 'success'){
                $data['ismail'] = '1';
                $product_backmoney = $this->product_backmoney->updatePorductBackmoney($bid, $data);
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发送邮件成功', array(), '清算-催债 ', 'no', OP_DOMAIN.'/remindermail/alreadyRemindermail'));
            }else{
                exit($this->ajaxDataReturn(self::AJ_RET_FAIL,  '发送邮件失败', array(), '清算-催债 ', 'no', OP_DOMAIN.'/remindermail/alreadyRemindermail'));
            }
        }
    }
    
    public function issendmail($bid){
        $data['ismail'] = '1';
        $product_backmoney = $this->product_backmoney->updatePorductBackmoney($bid, $data);
        $log = $this->op->actionData($this->getSession('name'), '清算-催债', '', '设为已催款（已发送邮件）', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '设为已催款', array(), '清算-催债 ', 'no', OP_DOMAIN.'/remindermail/noRemindermail'));
    }
}