<?php
class qs_duizhang extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == 'qs_duizhang'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_contract_model','contract');
        $this->load->model('admin_stock_product_model','stock_product');
        $this->load->model('admin_longmoney_income_log_model','longmoney_income_log');
        $this->load->model('admin_corporation_model', 'corporation');
        $this->load->model('admin_product_remit_model','product_remit');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('清算-合同账目核对',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'清算-合同账目核对');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
	        $psize = max(20, intval($this->input->request('numPerPage')));
	        $data = array();
	        $offset = ($page - 1) * $psize;
	        $searchParam=array();
	        $data['sum']=array('sum_money'=>0,'sum_stockmoney'=>0);
	        if($this->input->request('op') == "search"){
	        	$type = trim($this->input->post('type'));
	        	$con_number=trim($this->input->post('con_number'));
	        	$corname=trim($this->input->post('corname'));
	        	$stime = trim($this->input->post('stime'));
	        	$etime = trim($this->input->post('etime'));
	        	$sjtime = trim($this->input->post('sjtime'));
	        	$ejtime = trim($this->input->post('ejtime'));
	        	$data['type'] = $type;
	        	if(!empty($type)){
	        		$searchParam['type']=$type;
	        	}
	        	if(!empty($con_number)){
	        		$searchParam['con_number']=$con_number;
	        		$data['con_number'] = $con_number;
	        	}
	        	if(!empty($corname)){
	        		$searchParam['corname']=$corname;
	        		$data['corname'] = $corname;
	        	}
	        	if(!empty($stime)){
	        		$searchParam['stime']=$stime;
	        		$data['stime'] = $stime;
	        	}
	        	if(!empty($etime)){
	        		$searchParam['etime']=$etime;
	        		$data['etime'] = $etime;
	        	}
	        	if(!empty($sjtime)){
	        		$searchParam['sjtime']=$sjtime;
	        		$data['sjtime'] = $sjtime;
	        	}
	        	if(!empty($ejtime)){
	        		$searchParam['ejtime']=$ejtime;
	        		$data['ejtime'] = $ejtime;
	        	}
	        	$contractList = $this->stock_product->getStockContractByCondition($searchParam,array($psize, $offset));
	        	$count = count( $this->stock_product->getStockContractByCondition($searchParam,''));
	        }else{
	        	$data['type']=0;
	        	$where = array();
		        $contractList = $this->stock_product->getStockContractByCondition($where,array($psize, $offset));
		        $count = count( $this->stock_product->getStockContractByCondition($where,''));
	        }
	        $sum_money = $this->stock_product->sumStockContractByCondition($searchParam);
	        $data['sum_money']    = $sum_money;
	        foreach ($contractList as $key=>$val){
	            $contract = $this->contract->getContractByCid($val['cid']);
	            $longmoney_income = $this->longmoney_income_log->getLongMoneyIncomeLogList(strtotime(date('Y-m-d',$val['stocktime'])));
	            $contractList[$key]['income'] = $longmoney_income;
	            
	            $productList = $this->product->getProductList(array('cid'=>$val['cid']), '', '');
	            $totalProfit = 0;
	            $remitTotalProfit = 0;
	            if(!empty($productList)){
	            	$contractList[$key]['remitTotalProfit'] = $remitTotalProfit;
	            	foreach ($productList as $product){
	            		if($product['sellmoney']>0){
	            			$days = $this->diff_days($product['uistime'],$product['uietime']);
	            			$totalProfit=$totalProfit+$this->countProductProfit($days,$product['sellmoney'],$product['income']);
	            		}
	            	}
	            }
	            $contractList[$key]['totalProfit'] = $totalProfit;
	        }
	        $data['list'] = $contractList;
	        
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
	        $this->load->view('qs_duizhang/v_index',$data);
        }
    }
    
    public function baobiao(){
    	$flag = $this->op->checkUserAuthority('清算-合同账目核对',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'清算-合同账目核对');
    	}else{
    		$page = max(1, intval($this->input->request('pageNum')));
    		$psize = max(20, intval($this->input->request('numPerPage')));
    		$data = array();
    		$offset = ($page - 1) * $psize;
    		$searchParam=array();
    		$data['sum']=array('sum_money'=>0,'sum_stockmoney'=>0);
    		if($this->input->request('op') == "search"){
    			$type = trim($this->input->post('type'));
    			$con_number=trim($this->input->post('con_number'));
    			$corname=trim($this->input->post('corname'));
    			$stime = trim($this->input->post('stime'));
    			$etime = trim($this->input->post('etime'));
    			$sjtime = trim($this->input->post('sjtime'));
    			$ejtime = trim($this->input->post('ejtime'));
    			$dkstime = trim($this->input->post('dkstime'));
    			$dketime = trim($this->input->post('dketime'));
    			$corid = trim($this->input->post('corid'));
    			$data['type'] = $type;
    			$data['corid'] = $corid;
    			if(!empty($type)){
    				$searchParam['type']=$type;
    			}
    			if(!empty($corid)){
    				$searchParam['corid']=$corid;
    			}
    			if(!empty($con_number)){
    				$searchParam['con_number']=$con_number;
    				$data['con_number'] = $con_number;
    			}
    			if(!empty($corname)){
    				$searchParam['corname']=$corname;
    				$data['corname'] = $corname;
    			}
    			if(!empty($dkstime)){
    				$searchParam['dkstime']=$dkstime;
    				$data['dkstime'] = $dkstime;
    			}
    			if(!empty($dketime)){
    				$searchParam['dketime']=strtotime($dketime)+86400;
    				$data['dketime'] = $dketime;
    			}
    			if(!empty($stime)){
    				$searchParam['stime']=$stime;
    				$data['stime'] = $stime;
    			}
    			if(!empty($etime)){
    				$searchParam['etime']=$etime;
    				$data['etime'] = $etime;
    			}
    			if(!empty($sjtime)){
    				$searchParam['sjtime']=$sjtime;
    				$data['sjtime'] = $sjtime;
    			}
    			if(!empty($ejtime)){
    				$searchParam['ejtime']=$ejtime;
    				$data['ejtime'] = $ejtime;
    			}
    			$contractList = $this->stock_product->getStockContractByCondition($searchParam,array($psize, $offset),' corname desc,remittime desc ');
    			
    			$allList = $this->stock_product->getStockContractByCondition($searchParam,'');
    			$count = count($allList);
    		}else{
    			$data['type']=0;
    			$data['corid']=0;
    			$where = array();
    			$contractList = $this->stock_product->getStockContractByCondition($where,array($psize, $offset),' remittime desc ');
    			$allList = $this->stock_product->getStockContractByCondition($searchParam,'');
    			$count = count($allList);
    		}
    		$sum_money = $this->stock_product->sumStockContractByCondition($searchParam);
    		$data['sum_money']    = $sum_money;
    		foreach ($contractList as $key=>$val){
    			$contract = $this->contract->getContractByCid($val['cid']);
    			$longmoney_income = $this->longmoney_income_log->getLongMoneyIncomeLogList(strtotime(date('Y-m-d',$val['stocktime'])));
    			$contractList[$key]['income'] = $longmoney_income;
    			 
    			$productList = $this->product->getProductList(array('cid'=>$val['cid']), '', '');
    			$contractTotalProfit = 0;
    			if(!empty($productList)){
    				foreach ($productList as $product){
    					if($product['sellmoney']>0){
    						$days = $this->diff_days($product['uistime'],$product['uietime']);
    						$contractTotalProfit=$contractTotalProfit+$this->countProductProfit($days,$product['sellmoney'],$product['income']);
    					}
    				}
    			}
    			$contractList[$key]['totalProfit'] = $contractTotalProfit;
    		}
    		
    		$totalService = 0;
    		$totalServiceWithoutLong = 0;
    		$totalStocklixi = 0;
    		$totalRemitlixi =0;
    		$totallixi = 0;
    		foreach ($allList as $key=>$val){
    			if(!empty($val['remittime'])){
	    			$contract = $this->contract->getContractByCid($val['cid']);
	    			$longmoney_income = $this->longmoney_income_log->getLongMoneyIncomeLogList(strtotime(date('Y-m-d',$val['stocktime'])));
	    		
	    			$productList = $this->product->getProductList(array('cid'=>$val['cid']), '', '');
	    			$totalProfit = 0;
	    			$remitTotalProfit = 0;
	    			if(!empty($productList)){
	    				foreach ($productList as $product){
	    					if($product['sellmoney']>0){
	    						$days = $this->diff_days($product['uistime'],$product['uietime']);
	    						$_profit = $this->countProductProfit($days,$product['sellmoney'],$product['income']);
	    						$totalProfit=$totalProfit+$_profit;
	    						$totallixi = $totallixi+$_profit;
	    					}
	    				}
	    			}
	    			$dingqibenxi = $val['money']-$val['stockmoney']+round($totalProfit,2);
	    			$remitdays = '';
	    			$remitlixi=0;
	    			$remitbenxi=0;
	    			$stocklixi = 0;
    				$remitdays=$this->diff_days(date("Y-m-d",$val['remittime']), $val['repaymenttime']);
    				$remitlixi = $remitdays*$val['con_money']*$val['con_income']/36000;
    				$remitbenxi = $remitlixi+$val['con_money'];
    				$stocklixi=empty($val['stockmoney'])?'0':round($remitdays*$val['stockmoney']*9/36500,2);
    				$totalStocklixi = $totalStocklixi+$stocklixi;
    				$totalRemitlixi =$totalRemitlixi+$remitlixi;
	    			$totalService =$totalService + round(bcsub($remitbenxi,$dingqibenxi,2)-$val['stockmoney'],2);
    				$totalServiceWithoutLong = $totalServiceWithoutLong + round(bcsub($remitbenxi,$dingqibenxi,2)-$val['stockmoney']-$stocklixi,2);
    			}
    		}
    		$data['list'] = $contractList;
    		$data['totalServiceWithoutLong'] = $totalServiceWithoutLong;
    		$data['totalService'] = $totalService;
    		$data['totalStocklixi'] = $totalStocklixi;
    		$data['totallixi'] = $totallixi;
    		$data['totalRemitlixi'] = $totalRemitlixi;
    		$edatable = $this->op->getEditable($this->getSession('uid'),'9483');
    		if(!empty($edatable)){
    			$data['editable'] = $edatable[0]['editable'];
    		}else{
    			$data['editable']=0;
    		}
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
    		$this->load->view('qs_duizhang/v_baobiao',$data);
    	}
    }
    
    public function export(){
    			$type = trim($this->input->post('type'));
    			$con_number=trim($this->input->post('con_number'));
    			$corname=trim($this->input->post('corname'));
    			$stime = trim($this->input->post('stime'));
    			$etime = trim($this->input->post('etime'));
    			$sjtime = trim($this->input->post('sjtime'));
    			$ejtime = trim($this->input->post('ejtime'));
    			$dkstime = trim($this->input->post('dkstime'));
    			$dketime = trim($this->input->post('dketime'));
    			$corid = trim($this->input->post('corid'));
    			$data=array();
    			if(!empty($type)){
    				$searchParam['type']=$type;
    			}
    			if(!empty($corid)){
    				$searchParam['corid']=$corid;
    			}
    			if(!empty($con_number)){
    				$searchParam['con_number']=$con_number;
    			}
    			if(!empty($corname)){
    				$searchParam['corname']=$corname;
    			}
    			if(!empty($dkstime)){
    				$searchParam['dkstime']=$dkstime;
    			}
    			if(!empty($dketime)){
    				$searchParam['dketime']=strtotime($dketime)+86400;
    			}
    			if(!empty($stime)){
    				$searchParam['stime']=$stime;
    			}
    			if(!empty($etime)){
    				$searchParam['etime']=$etime;
    			}
    			if(!empty($sjtime)){
    				$searchParam['sjtime']=$sjtime;
    			}
    			if(!empty($ejtime)){
    				$searchParam['ejtime']=$ejtime;
    			}
    			$contractList = $this->stock_product->getStockContractByCondition($searchParam,'',' corname desc,remittime desc ');
    			foreach ($contractList as $key=>$val){
    				$contract = $this->contract->getContractByCid($val['cid']);
    				$longmoney_income = $this->longmoney_income_log->getLongMoneyIncomeLogList(strtotime(date('Y-m-d',$val['stocktime'])));
    				$contractList[$key]['income'] = $longmoney_income;
    			
    				$productList = $this->product->getProductList(array('cid'=>$val['cid']), '', '');
    				$totalProfit = 0;
    				$remitTotalProfit = 0;
    				if(!empty($productList)){
    					$contractList[$key]['remitTotalProfit'] = $remitTotalProfit;
    					foreach ($productList as $product){
    						if($product['sellmoney']>0){
    							$days = $this->diff_days($product['uistime'],$product['uietime']);
    							$totalProfit=$totalProfit+$this->countProductProfit($days,$product['sellmoney'],$product['income']);
    						}
    					}
    				}
    				$contractList[$key]['totalProfit'] = $totalProfit;
    			}
    			
    			$data['list'] = $contractList;
    			$this->load->view('qs_duizhang/v_export',$data);
    		}
    
    private function countStockLongProductProfit($start, $end, $money, $income){
        $days = (($end - $start)/86400) + 1;
        $profit = $income/100/360 * $money * $days;
        return $profit;
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
        	$where['status'] = 0;
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