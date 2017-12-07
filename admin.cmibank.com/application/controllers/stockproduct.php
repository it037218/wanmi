<?php
/**
 * 定期采购
 * * */
class stockproduct extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '定期产品采购'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_contract_model','contract');
        $this->load->model('admin_stock_product_model','stock_product');
        $this->load->model('admin_longmoney_income_log_model','longmoney_income_log');
        $this->load->model('admin_corporation_model', 'corporation');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('定期产品采购',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期产品采购');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $searchnumber = trim($this->input->post('searchnumber'));
            $is_stock = trim($this->input->post('is_stock'));
            if($this->input->request('op') == "search"){
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $count = count($this->contract->getContractIstock($searchnumber,$is_stock));
                $contract=  $this->contract->getContractIstock($searchnumber, $is_stock,array($psize, $offset));
                if(empty($count)){
                	exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                $data['list'] =$contract;
                $data['searchnumber'] = $searchnumber;
                $where = array();
                foreach ($contract as $_val){
                	$rtncontract[] = $_val;
                	$where[] = $_val['cid'];
                }
                $rtnstatus =array();
                $product = $this->product->getProductList(array('cid'=>$where), '', '');
                foreach ($product as $key=>$__val){
                	if($__val['status']<2){
                		$rtnstatus[$__val['cid']][$key] =$__val['pid'];
                	}
                }
                $data['rtnstatus'] = $rtnstatus;
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                $data['count_stock_money'] = $count_stock_money;
                if(empty($data['list'])){

                      exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
            }else{
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $rtn_stock_product = array();
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                $contract = $this->contract->getContracttoStock();
                $where = array();
                foreach ($contract as $_val){
                    //if($_val['con_money']-$_val['money'] == 0){
                     //   continue;
                    //}
                    $rtncontract[] = $_val;
                    $where[] = $_val['cid'];
                }

                $rtnstatus =array();
                $rtnptid = array();
                $product = $this->product->getProductList(array('cid'=>$where), '', '');
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        $rtnstatus[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                $data['rtnstatus'] = $rtnstatus;
                $data['rtnptid'] = $rtnptid;
                $data['count_stock_money'] = $count_stock_money;
                $data['list'] = $rtncontract;
                $count = count($rtncontract);
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'product/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1123');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '定期产品采购', '', '定期产品采购', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/stockproduct/v_index',$data);
        }
    }
    public function StockLongmoney(){
        if($this->input->request('op') == 'stock'){
            $money = trim($this->input->post('money'));
            $stockmoney = trim($this->input->post('stockmoney'));
            $warrant_img = trim($this->input->post('warrant_img'));
			$can_user_money = trim($this->input->post('can_user_money'));
// 			if($can_user_money-$stockmoney <= 0){
// 				 exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'采购金额不能大于活期可使用金额')));
// 			}
            if(empty($warrant_img)){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'凭证不能为空')));
            }
            $des = trim($this->input->post('des'));
            if($stockmoney<=$money){
                //按回款日  生成   回款单
                $cid = trim($this->input->post('cid'));
                $contract = $this->contract->getContractByCid($cid);
                $this->load->model('admin_product_backmoney_model', 'product_backmoney');
                $backmoney_info = $this->product_backmoney->getProductBackmoney(array('cid'=> $contract['cid'], 'cietime' => $contract['repaymenttime']));
                if($backmoney_info){
                    $update_data = array();
                    $profit = $this->countStockLongProductProfit(strtotime($contract['interesttime']), strtotime($contract['repaymenttime']), $contract['con_income'], $stockmoney);
                    $update_data['remitmoney'] =$stockmoney + $backmoney_info['remitmoney'];
                    $update_data['backmoney'] = $profit + $stockmoney + $backmoney_info['backmoney'];
                    $update_data['is_stock'] = 1;
                    $this->product_backmoney->updatePorductBackmoney($backmoney_info['bid'], $update_data);
                }else{
                    $inster_data = array();
                    $inster_data['cid'] = $contract['cid'];
                    $inster_data['is_stock'] = 1;
                    $inster_data['remitmoney'] = $stockmoney;
                    $inster_data['cietime'] = $contract['repaymenttime'];
                    $profit = $this->countStockLongProductProfit(strtotime($contract['interesttime']), strtotime($contract['repaymenttime']), $contract['con_income'], $stockmoney);
                    $inster_data['backmoney'] = $stockmoney + $profit;
                    $this->product_backmoney->addPorductBackmoney($inster_data);
                }
                
                //合同总共下分金额  = 合同下分金额+活期采购金额
                $totalmoney = $contract['money']+$stockmoney; 
                //总打款金额 = 合同原打款金额  + 活期采购-保证金的钱//不用减掉保证金20160615
                $totalcon_dkje = $contract['con_dkje']+$stockmoney;
                $data_content['is_stock'] = '1';
                $data_content['money'] = $totalmoney;
                $data_content['con_dkje'] =$totalcon_dkje; 
                $data_content['remittime']=time();
                //采购之后 更新合同下分金额
                $ret = $this->contract->updateContract($cid, $data_content);
                $data['cid'] = $cid;
                $data['status'] = 0;
                $data['stockmoney'] = $stockmoney;
                $data['des'] = $des;
                $data['warrant_img'] = $warrant_img;
                $data['ctime'] = time();
                //更新打款金额
                $ret = $this->stock_product->addStockProduct($data);
                if($ret){
                    $log = $this->op->actionData($this->getSession('name'), '定期产品采购', '', '定期产品采购', $this->getIP(), $this->getSession('uid'));
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '采购成功', array(), '定期产品采购 ', 'forward', OP_DOMAIN.'/stockproduct/'));
                }
                
                
                
            }else{
                 exit($this->ajaxDataReturn(self::AJ_RET_FAIL,'采购失败',array(),'定期产品采购'));
            }
            
        }else{
            $this->load->model('admin_longmoney_model','longmoney');
            $longmoney = $this->longmoney->getLongMoneyList();
            $data = array();

            $rtnstockmoney = array();
            $stockproduct = $this->stock_product->getStockProductList(array('status'=>'0'));
            foreach ($stockproduct as $val){
                $rtnstockmoney[] = $val['stockmoney'];
            }
            
            $this->load->model('qs_log','qs_log');
            $ret = $this->qs_log->getHistoryStockMoney();
            
            $p_date = date('Y-m-d');
            $this->load->model('longtobalance_log', 'longtobalance_log');
            $ltob = $this->longtobalance_log->getLogListByCtime($p_date);
            
            $this->load->model('admin_chongzhi_model', 'admin_chongzhi_model');
            $chongzhi = $this->admin_chongzhi_model->sumStockMoneyInclued();
            
            $cid = $this->uri->segment(3);
            $shengyu_money = $this->uri->segment(4);
            $repaymenttime = $this->uri->segment(5);
            $data['cid'] = $cid;
            $data['repaymenttime'] = $repaymenttime;
            $data['shengyu_money'] = $shengyu_money;
            $a = array_sum($rtnstockmoney);
            $data['longmoney'] = $ret-array_sum($rtnstockmoney)-$ltob+$chongzhi;
            $this->load->view('/stockproduct/v_StockProduct',$data);
        }
        
    }
    private function countStockLongProductProfit($start, $end, $money, $income){
        $days = (($end - $start)/86400) + 1;
        $profit = $income/100/360 * $money * $days;
        return $profit;
    }
    
    public function downtoline(){
        $this->load->model('admin_ptype_product_model','ptype_product');
        $cid = $this->uri->segment(3);
        //总产品pid
        $rtnproduct = array();
        $product = $this->product->getProductList(array('cid'=>$cid), '', '');
        $ptype_product = array();
        foreach ($product as $key=>$val){
            if($val['status']<2){
                $rtnproduct[]=$val['pid'];
                //更改产品状态   2下架
                $update_data = array('status' => 2, 'downtime' => time());
                $this->product->updateProductStatus($val['pid'], $update_data);
                $this->ptype_product->updatePorductByPid($val['pid'],array('status' =>2));
                $this->product->moveOnlineProduct($val['ptid'], $val['pid']);
            }
        }
       $backmoney = $this->product->backmoney($cid);
       $ret = $this->contract->backMoneytoContract($cid, $backmoney);
       if($ret){
           $log = $this->op->actionData($this->getSession('name'), '定期产品采购', '', '下架', $this->getIP(), $this->getSession('uid'));
           exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '定期产品采购', 'forward',OP_DOMAIN.'/stockproduct'));
       }
       

    }
    public function showproduct(){
       $cid = $this->uri->segment(3);
       $product = $this->product->getProductList(array('cid'=>$cid), '', '');
       $contract = $this->contract->getContractByCid($cid);
       $data['contract'] = $contract;
       $data['list'] = $product;
       $this->load->view('/stockproduct/v_detail',$data);
    }
    public function getLongProductStock(){
    	$s =$this->input->request('pageNum');
        $page = max(1, intval($this->input->request('pageNum')));
        $psize = max(20, intval($this->input->request('numPerPage')));
        $data = array();
        $offset = ($page - 1) * $psize;
        $searchParam=array();
        $data['sum']=array('sum_money'=>0,'sum_stockmoney'=>0);
        if($this->input->request('op') == "search"){
        	$type = trim($this->input->post('type'));
        	$con_number=trim($this->input->post('con_number'));
        	$stime = trim($this->input->post('stime'));
        	$etime = trim($this->input->post('etime'));
        	$data['type'] = $type;
        	if(!empty($type)){
        		if($type==1){
	        		$searchParam['type']=0;
        		}else if($type==2){
        			$searchParam['type']=1;
        		}
        	}
        	if(!empty($con_number)){
        		$searchParam['con_number']=$con_number;
        		$data['con_number'] = $con_number;
        	}
        	if(!empty($stime)){
        		$searchParam['stime']=$stime;
        		$data['stime'] = $stime;
        	}
        	if(!empty($etime)){
        		$searchParam['etime']=$etime;
        		$data['etime'] = $etime;
        	}
        	$stock_product = $this->stock_product->getStockProductByCondition($searchParam,array($psize, $offset));
        	$count = count( $this->stock_product->getStockProductByCondition($searchParam,''));
        	$sum = $this->stock_product->sumStockProductByCondition($searchParam);
        	$data['sum'] = $sum;
        }else{
        	$where = array();
        	$where['type'] = 0;
        	$data['type'] = 1;
	        $stock_product = $this->stock_product->getStockProductByCondition($where,array($psize, $offset));
	        $count = count( $this->stock_product->getStockProductByCondition($where,''));
	        $sum = $this->stock_product->sumStockProductByCondition($where);
	        $data['sum'] = $sum;
        }
        foreach ($stock_product as $key=>$val){
            $contract = $this->contract->getContractByCid($val['cid']);
            $longmoney_income = $this->longmoney_income_log->getLongMoneyIncomeLogList(strtotime(date('Y-m-d',$val['ctime'])));
            $stock_money = $this->stock_product->getAllSumStockMoney($val['cid']);
            $stock_product[$key]['corname'] = $contract['corname'];
            $stock_product[$key]['money'] = $contract['money'];
            $stock_product[$key]['con_money'] = $contract['con_money'];
            $stock_product[$key]['stock_money'] = $stock_money;
            $stock_product[$key]['con_number'] = $contract['con_number'];
            $stock_product[$key]['repaymenttime'] = $contract['repaymenttime'];
            $stock_product[$key]['income'] = $longmoney_income;
            
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
            $stock_product[$key]['totalProfit'] = $totalProfit;
        }
        $data['list'] = $stock_product;
        
        $data['pageNum']    = $page;
        $data['numPerPage'] = $psize;
        if($count>0){
            $data['count'] = $count;
            $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
            if(!empty($aboutustitle)){
                $data['rel'] .= '&title=' . $aboutustitle;
            }
        }else{
            $data['list'] = $data['page'] = '';
        }
        $this->load->view('stockproduct/v_longprodutstock',$data);
    }
    public function showlongremit($sid){
       $stock_product = $this->stock_product->getStockProductList(array('sid'=>$sid));
       $contract = $this->contract->getContractByCid($stock_product[0]['cid']);
       $corporation = $this->corporation->getCorporationByCid($contract['corid']);
       $data['contract'] = $contract;
       $data['stock_product'] = $stock_product[0];
       $data['corporation'] = $corporation;
       $this->load->view('/stockproduct/v_showlongremit',$data);
    }
    
    private function countProductProfit($days, $money, $income){
    	$profit = $income/100/365 * $money * $days;
    	return $profit;
    }
    
    function diff_days($start, $end){
    	list($a_year, $a_month, $a_day) = explode('-', $start);
    	list($b_year, $b_month, $b_day) = explode('-', $end);
    	$a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    	$b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    	$d = abs(($b_new-$a_new)/86400)+1;
    	return $d;
    }
    
}