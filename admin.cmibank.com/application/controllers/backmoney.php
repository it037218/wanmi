<?php
/**
 * 用户账户信息管理
 * * */
class backmoney extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '清算-回款'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_product_backmoney_model','product_backmoney');
        $this->load->model('admin_contract_model','contract');
        $this->load->model('admin_stock_product_model','stock_product');
        $this->load->model('admin_longmoney_income_log_model','longmoney_income_log');
        $this->load->model('admin_product_remit_model','product_remit');
     
    }
    
    public function noback(){
        $flag = $this->op->checkUserAuthority('未回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
        
            $searchParam=array();
            $backmoneylist=array();
            if($this->input->request('op') == "search"){
                $searchcorname = trim($this->input->post('searchcorname'));
                $searchcon_number =trim($this->input->post('searchcon_number')) ;
                $stime =trim($this->input->post('stime')) ;
                $etime = trim($this->input->post('etime'));
                 
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
            }
            $searchParam['status']=0;
            $backmoneylist = $this->getProductBackMoney($searchParam,array($psize, $offset),' contract.repaymenttime asc, contract.cid desc');
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
            
            $edatable = $this->op->getEditable($this->getSession('uid'),'1109');
            if(!empty($edatable)){
                $data['editable'] = $edatable[0]['editable'];
            }else{
                $data['editable']=0;
            }
            $data['longProductIncome'] = $this->longmoney_income_log->getLongIncome();
            $this->load->view('/backmoney/v_index',$data);
        }
    }
		public function getProductBackMoney($params,$limit='',$orderby=''){
			$backmoneylist = array();
			$backmoneylist = $this->product_backmoney->getBackmoneyByCondition($params,$limit,$orderby);
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
    
    public function todayback(){
          $flag = $this->op->checkUserAuthority('今日待回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
        
            $searchParam=array();
            $backmoneylist=array();
            if($this->input->request('op') == "search"){
                $searchcorname = trim($this->input->post('searchcorname'));
                $searchcon_number =trim($this->input->post('searchcon_number'));
                 
                if(!empty($searchcon_number)){
                    $searchParam['searchcon_number']=$searchcon_number;
                    $data['searchcon_number'] = $searchcon_number;
                }
                if(!empty($searchcorname)){
                    $searchParam['searchcorname']=$searchcorname;
                    $data['searchcorname'] = $searchcorname;
                }
            }
            $searchParam['cietime']=date('Y-m-d');
            $searchParam['status']=0;
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
            $edatable = $this->op->getEditable($this->getSession('uid'),'1108');
            if(!empty($edatable)){
                $data['editable'] = $edatable[0]['editable'];
            }else{
                $data['editable']=0;
            }
        
        }
       $this->load->view('/backmoney/v_todayback',$data);
  }
    public function Alreadyback(){
       $flag = $this->op->checkUserAuthority('已回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $count_remitmoney = array();
            $offset = ($page - 1) * $psize;
        
            $searchParam=array();
            $backmoneylist=array();
            if($this->input->request('op') == "search"){
             $searchcorname = trim($this->input->post('searchcorname'));
                $searchcon_number =trim($this->input->post('searchcon_number')) ;
                $stime =trim($this->input->post('stime')) ;
                $etime = trim($this->input->post('etime'));
                 
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
            }
            $searchParam['status']=3;
             
            $backmoneylist = $this->getProductBackMoney($searchParam,array($psize, $offset),' contract.repaymenttime desc, contract.cid desc');
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
            $edatable = $this->op->getEditable($this->getSession('uid'),'1110');
            if(!empty($edatable)){
                $data['editable'] = $edatable[0]['editable'];
            }else{
                $data['editable']=0;
            }
            
           $data['longProductIncome'] = $this->longmoney_income_log->getLongIncome();
            $this->load->view('/backmoney/v_Alreadyback',$data);
           
        }
    }
    
    public function detail($bid){
        $flag = $this->op->checkUserAuthority('今日待回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $product_backmoney = $this->populateBackMoney($bid);
            $data['detail'] = $product_backmoney;
            $this->load->view('/backmoney/v_detail',$data);
        }
       
    }
//     老方法计算
//     public function getBackMoney_plus_Profit($sellmoney,$income,$start,$end,$con_bzjbl){
//         $num = $this->diff_days($start, $end);
//         return ($sellmoney-($sellmoney*$con_bzjbl/100))+($sellmoney*$income*$num/36000);
        
//     }
    
    //新方法计算
    public function getBackMoney_plus_Profit($sellmoney,$income,$start,$end){
        $num = $this->diff_days($start, $end);
        return $sellmoney+$sellmoney*$income*$num/36500;
    
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
    public function updateback($bid=''){
        $flag = $this->op->checkUserAuthority('今日待回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            if($this->input->request('op') == "update"){
                $bid = trim($this->input->post('bid'));
                $warrant_img = trim($this->input->post('warrant_img'));
                $data['warrant_img'] = $warrant_img;
                
                $service_image = trim($this->input->post('service_image'));
                $data['service_image'] = $service_image;
                
                $service_money = trim($this->input->post('service_money'));
                $data['service_money'] = $service_money;
                
                $service_note = trim($this->input->post('service_note'));
                $data['service_note'] = $service_note;
                
                $ret = $this->product_backmoney->updatePorductBackmoney($bid,$data);
                if($ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'修改成功')));
                }
            }else{
                $product_backmoney = $this->populateBackMoney($bid);
                $data['detail'] = $product_backmoney;
                $data['bid'] = $bid;
                $this->load->view('/backmoney/v_updateback',$data);
            }
        
        }
    }
    public function setback($bid=''){
        $flag = $this->op->checkUserAuthority('今日待回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            if($this->input->request('op') == "update"){
                $num = array();
                $bid = trim($this->input->post('bid'));
                $info = $this->product_backmoney->getProductBackmoneyByBid($bid);
                $warrant_img = trim($this->input->post('warrant_img'));
                $service_image = trim($this->input->post('service_image'));
                $service_money = trim($this->input->post('service_money'));
                $service_note = trim($this->input->post('service_note'));
                //如果 只有活期走一下面
                if(empty($info['pids']) && $info['is_stock'] == 1){
                    $Stockdata['status'] = '1';
                    $ret = $this->stock_product->updateStockProduct($info['cid'], $Stockdata  );
                    $data['warrant_img'] = $warrant_img;
                    $data['service_image'] = $service_image;
                    $data['service_money'] = $service_money;
                    $data['service_note'] = $service_note;
                    $data['status'] = '3';
                    $product_backmoney = $this->product_backmoney->updatePorductBackmoney($bid,$data);
                    
                    $log = $this->op->actionData($this->getSession('name'), '清算-回款', '', '今日待回款列表', $this->getIP(), $this->getSession('uid'));
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'审核成功')));
                }
                
                //如果有活期走下面。
                if($info['is_stock'] == 1){
                    $Stockdata['status'] = '1';
                    $ret = $this->stock_product->updateStockProduct($info['cid'], $Stockdata  );
                }
               
                $repaymenttime = trim($this->input->post('repaymenttime'));
                $cietime = trim($this->input->post('cietime'));
                
                $time = (strtotime($repaymenttime) - strtotime($cietime))/86400;
                $isback = true;
                if($time<7){
                     $isback = false;
                }
     
                $pids = explode(',', $info['pids']);
                $product_data = array('status' => 5);
                $this->product->updatePorduct($pids, $product_data);
                if($info['status'] == 3){
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'已回库',array(),'回库');
                    exit;
                }
                $data['warrant_img'] = $warrant_img;
				$data['service_image'] = $service_image;
                $data['service_money'] = $service_money;
                $data['service_note'] = $service_note;
                $data['status'] = '3';
                $product_backmoney = $this->product_backmoney->updatePorductBackmoney($bid,$data);
                if($isback){
                    $this->minusmoney($bid);
                }else{
                	$log = $this->op->actionData($this->getSession('name'), '清算-回款', '', '今日待回款列表', $this->getIP(), $this->getSession('uid'));
                	exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'审核成功')));
                }
            }else{
                $product_backmoney = $this->populateBackMoney($bid);
                $data['detail'] = $product_backmoney;
                $data['bid'] = $bid;
                $this->load->view('/backmoney/v_setback',$data);
            }
            
        }
    }

    public function populateBackMoney($bid){
    	$product_backmoney = $this->product_backmoney->getProductBackmoneyByBid($bid);
    	$backmoney_plus_profit = 0;
    	$BackMoneyServer = 0;
    	$contract =  $this->contract->getContractByCid($product_backmoney['cid']);
    	$product_backmoney['corname'] = $contract['corname'];
    	$product_backmoney['con_number'] = $contract['con_number'];
    	$product_backmoney['repaymenttime'] = $contract['repaymenttime'];
    	$product_backmoney['interesttime'] = $contract['interesttime'];
    	$product_backmoney['con_money'] = $contract['con_money'];
    	$product_backmoney['con_bzjbl'] = $contract['con_bzjbl'];
    	$product_backmoney['con_income'] = $contract['con_income'];
    	$product_backmoney['remittime'] = $contract['remittime'];
    	$longmoney = 0;
    	if($product_backmoney['is_stock'] == 1){
    		$stock_product = $this->stock_product->getStockProductList(array('cid'=>$product_backmoney['cid']));
    		$product_backmoney['longproduct'] = $stock_product;
    		$product_backmoney['stockmoney'] = $stock_product[0]['stockmoney'];
                foreach ($stock_product as $stock_productkey => $stock_productvalue) {
                    $longmoney += $stock_productvalue['stockmoney'];
                }
    	}
        
    	if(!empty($product_backmoney['pids'])){
    		$remit = $this->product_remit->getctimebycid($product_backmoney['cid']);
    		$product = $this->product->getProductInPid($product_backmoney['pids']);
    		$product_backmoney['product'] = $product;
    		foreach ($product as $key=>$_val){
    			$backmoney_plus_profit += round($this->getBackMoney_plus_Profit($_val['sellmoney'],$_val['income'],$_val['uistime'],$_val['uietime']),2);
    		}
    	}
            
    	$totalmoney = round($this->getBackMoney($contract['con_money'],$contract['con_income'],date('Y-m-d',$contract['remittime']),$contract['repaymenttime']),2);
    	$money_plus_profit = $backmoney_plus_profit;
    	$moneyServer = bcsub($totalmoney,$money_plus_profit,2);
        $longProductIncome = $this->longmoney_income_log->getLongIncome();
    	$longmoneyProfit = $this->countProductProfit($this->diff_days(date('Y-m-d',$contract['remittime']),$contract['repaymenttime']),$longmoney,$longProductIncome);
    	$product_backmoney['totalmoney']=$totalmoney;
    	$product_backmoney['money_plus_profit']=$money_plus_profit;
    	$product_backmoney['longmoney']=$longmoney;
    	$product_backmoney['longmoneyProfit']=$longmoneyProfit;
    	$product_backmoney['moneyServer']=$moneyServer-$longmoney-$longmoneyProfit;
        $product_backmoney['longProductIncome']=$longProductIncome;
    	return $product_backmoney;
    }
    public function minusmoney($bid=''){
       $product_backmoney = $this->product_backmoney->getProductBackmoney(array('bid'=>$bid));
       $cid = $product_backmoney['cid'];
       $contract = $this->contract->getContractList(array('cid' =>$cid),'','');
       $money = $contract[0]['con_money'] + $product_backmoney['remitmoney'];

       $data['con_money'] = $money;
       $ret = $this->contract->updateContract($cid, $data);
       $log = $this->op->actionData($this->getSession('name'), '清算-回款', '', '今日待回款列表', $this->getIP(), $this->getSession('uid'));
       exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '审核成功', array(), '今日待回款列表','forward', OP_DOMAIN.'/backmoney/Alreadyback'));
    }
    
    public function getBackMoney($con_money,$income,$start,$end){
    	$num = $this->diff_days($start, $end);
    	return $con_money+($con_money*$income*$num/36000);
    }
    
/*     private function countProductProfit($start, $end, $money, $income){
        $days = (($end - $start)/86400) + 1;//等于
        $profit = $income/100/360 * $money * $days;//这个是活期每天的利息
        return $profit;
    } */

    private function countProductProfit($days, $money, $income){
        $profit = $income/100/365 * $money * $days;
        return $profit;
    }
    
    public function serviceMoney(){
        $flag = $this->op->checkUserAuthority('未回款列表',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'设置服务费');
        }else{
            $data = array();
            if($this->input->request('op') == "addServiceInfo"){
                
                $bid = trim($this->input->post('bid'));
                $service_money = trim($this->input->post('service_money'));
                $service_image = trim($this->input->post('service_image'));
                $service_note = trim($this->input->post('service_note'));
                
                $data['service_money'] = $service_money;
                $data['service_image'] = $service_image;
                $data['service_note'] = $service_note;
                
                $ret = $this->product_backmoney->updatePorductBackmoney($bid,$data);
                if($ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'设置成功')));
                }
            }else{
                $bid = $this->uri->segment(3);
                $product_backmoney = $this->product_backmoney->getProductBackmoneyList(array('bid' => $bid),'cietime desc','');
                $data['detail'] = $product_backmoney[0];
            }
            $this->load->view('/backmoney/v_servicemoney',$data);
        }
        
    }
    
    
    
}