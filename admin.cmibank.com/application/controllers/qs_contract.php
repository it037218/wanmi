<?php
/**
 * 合同管理
 * * */
class qs_contract extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '清算-合同管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_contract_model','contract');
        $this->load->model('admin_stock_product_model','stock_product');
    }
    
    public function index(){
        $flag = $this->op->checkUserAuthority('清算-合同管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'清算-合同管理');
        }else{
           $page = max(1, intval($this->input->request('pageNum')));
           $psize = max(20, intval($this->input->request('numPerPage')));
           $data = array();
           $offset = ($page - 1) * $psize;
           $total = 0;
            if($this->input->request('op') == "search"){
            	$searchnumber = trim($this->input->post('searchnumber'));
            	$searchcorname = trim($this->input->post('searchcorname'));
            	$end = trim($this->input->post('end'));
            	$star = trim($this->input->post('star'));
            	$is_null = trim($this->input->post('is_null'));
            	$weidakuang = trim($this->input->request('is_weidakuang'));
            	$params = array();
            	if(!empty($searchcorname)){
            		$params['corname']=$searchcorname;
            		$data['searchcorname'] = $searchcorname;
            	}
            	if(!empty($searchnumber)){
            		$params['con_number']=$searchnumber;
            		$data['searchnumber'] = $searchnumber;
            	}
            	if(!empty($star)){
            		$params['repaymentstime']=$star;
            		$data['star'] = $star;
            	}
            	if(!empty($end)){
            		$params['repaymentetime']=$end;
            		$data['end'] = $end;
            	}
            	if(!empty($is_null)){
            		$params['is_null']=$is_null;
            		$data['is_null'] = $is_null;
            	}
            	if(!empty($weidakuang)){
            		$params['weidakuang']=$weidakuang;
            		$data['weidakuang'] = $weidakuang;
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
	            $contract = $this->contract->getContractlistsql($params,array($psize, $offset));
                
                if(empty($contract)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'没有内容')));
                }else{
                	$contractList = $this->contract->getContractlistsql($params);
                	foreach ($contractList as $_c){
                		$total += $_c['con_money'];
                	}
                	$count = count($contractList);
                }
                $where = array();
                foreach ($contract as $_val){
                    $where[] = $_val['cid'];
                }
                $rtnstatus =array();
                $cidList = array();
                $product = $this->product->getProductList(array('cid'=>$where), '','');
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        $rtnstatus[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                if(empty($contract)){
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'没有内容')));
                }
                $data['rtnstatus'] = $rtnstatus;
                $data['count_stock_money'] = $count_stock_money;
                $data['list'] =  $contract;
                if(empty($data['list'])){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
            }else{
                $rtn_stock_product = array();
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                $params = array();
                $params['weidakuang']=1;
                $data['weidakuang'] = 1;
                $contract = $this->contract->getContractlistsql($params,array($psize, $offset));
                $where = array();
                foreach ($contract as $_val){
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
                $data['count_stock_money'] = $count_stock_money;
                $data['list'] = $contract;
                $contractList = $this->contract->getContractlistsql($params);
                foreach ($contractList as $_c){
                	$total += $_c['con_money'];
                }
                $count = count($contractList);
            }
            $data['total']    = $total;
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
            }else{
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1091');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '清算-合同管理', '', '清算-合同管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/qs_contract/v_index',$data);
        }
    }
    public function index2(){
        $flag = $this->op->checkUserAuthority('清算-合同管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'清算-合同管理');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $searchnumber = trim($this->input->post('searchnumber'));
            $searchcorname = trim($this->input->post('searchcorname'));
            if($searchnumber && $searchnumber != '请输入搜索内容' && $this->input->request('op') == "search"){
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
                $contract = $this->contract->getContractLikeCon_number($searchnumber,array($psize, $offset));
                if(empty($contract)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'没有内容')));
                }
                $where = array();
                foreach ($contract as $_val){
                    $where[] = $_val['cid'];
                }
                $rtnstatus =array();
                $product = $this->product->getProductList(array('cid'=>$where), '',array($psize, $offset));
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        $rtnstatus[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                $data['rtnstatus'] = $rtnstatus;
                $data['count_stock_money'] = $count_stock_money;
                $data['list'] =  $this->contract->getContractLikeCon_number($searchnumber, array($psize, $offset));
                $data['searchnumber'] = $searchnumber;
                if(empty($data['list'])){
                      exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                $count = count($this->contract->getContractLikeCon_number($searchnumber));
            }else if($searchcorname && $searchcorname != '请输入搜索内容' && $this->input->request('op') == "search"){
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
                $contract = $this->contract->getContractLikeCorname($searchcorname,array($psize, $offset));
                if(empty($contract)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'没有内容')));
                }
                $where = array();
                foreach ($contract as $_val){
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
                $data['count_stock_money'] = $count_stock_money;
                $data['list'] = $contract;
                $data['searchcorname'] = $searchcorname;
                $count = count($this->contract->getContractLikeCorname($searchcorname));
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
                $contract = $this->contract->getContractList('', 'interesttime desc', array($psize, $offset));
                $where = array();
                foreach ($contract as $_val){
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
                $data['count_stock_money'] = $count_stock_money;
                $data['list'] = $contract;
                $count = count($this->contract->getContractList('', '',''));
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
            $log = $this->op->actionData($this->getSession('name'), '清算-合同管理', '', '清算-合同管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/qs_contract/v_index',$data);
        }
        
    }
    
    public function showproduct(){
        $cid = $this->uri->segment(3);
        $searchpname = trim($this->input->post('searchpname'));
        $searchstart = trim($this->input->post('searchstart')); 
        $searchend = trim($this->input->post('searchend'));
        if($searchpname && $searchpname != '请输入搜索内容' && $this->input->request('op') == "search"){
            $cid = trim($this->input->post('cid'));
            $product = $this->product->getProductListByCid($cid,$searchpname,$searchstart,$searchend);
        }else if($searchstart && $searchstart != '请输入搜索内容' && $this->input->request('op') == "search"){
            $cid = trim($this->input->post('cid'));
            $product = $this->product->getProductListByCid($cid,$searchpname,$searchstart,$searchend);
        }else{
            $product = $this->product->getProductList(array('cid'=>$cid), 'uistime desc', '');
        }
        
        $contract = $this->contract->getContractByCid($cid);
        $data['contract'] = $contract;
        $data['list'] = $product;
        $data['cid'] = $cid;
        $this->load->view('/qs_contract/v_productlist',$data);
    }
    
    public function export(){
    	$cid = $this->uri->segment(3);
    	$searchpname = trim($this->input->post('searchpname'));
    	$searchstart = trim($this->input->post('searchstart'));
    	$searchend = trim($this->input->post('searchend'));
    	if($searchpname && $searchpname != '请输入搜索内容' && $this->input->request('op') == "search"){
    		$cid = trim($this->input->post('cid'));
    		$product = $this->product->getProductListByCid($cid,$searchpname,$searchstart,$searchend);
    	}else if($searchstart && $searchstart != '请输入搜索内容' && $this->input->request('op') == "search"){
    		$cid = trim($this->input->post('cid'));
    		$product = $this->product->getProductListByCid($cid,$searchpname,$searchstart,$searchend);
    	}else{
    		$product = $this->product->getProductList(array('cid'=>$cid), 'uistime desc', '');
    	}
    
    	$contract = $this->contract->getContractByCid($cid);
    	$data['contract'] = $contract;
    	$data['list'] = $product;
    	$data['cid'] = $cid;
    	$this->load->view('/qs_contract/v_export',$data);
    }
    
    public function downtoline(){
        $this->load->model('admin_ptype_product_model','ptype_product');
        $cid = $this->uri->segment(3);
        //总产品pid
        $rtnproduct = array();
        $product = $this->product->getProductList(array('cid'=>$cid), '', '');
        $ptype_product = array();
        foreach ($product as $key=>$val){
            if($val['status'] < 2){
                $rtnproduct[]=$val['pid'];
                //更改产品状态   2下架
                $update_data = array('status' => 2, 'downtime' => time());
                $this->product->updateProductStatus($val['pid'], $update_data);
                $this->ptype_product->updatePorductByPid($val['pid'],array('status' =>2));
                $this->product->moveOnlineProduct($val['ptid'], $val['pid']);
            }
        } 
        $backmoney = $this->product->backmoney($cid);
        $ret = $this->contract->updateContract($cid, array('status'=>'2'));
        $ret = $this->contract->backMoneytoContract($cid, $backmoney);
        if($ret){
            $log = $this->op->actionData($this->getSession('name'), '定期产品采购', '', '下架', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '定期产品采购', 'forward',OP_DOMAIN.'/qs_contract'));
        }
    }
    public function uptoline($cid){
        //$status 设置为1  即为正常
        $ret = $this->contract->updateContractstatus($cid,1);
        if($ret){
            $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '开启', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '开启成功', array(), '合同管理', 'forward',OP_DOMAIN.'/qs_contract'));
        }
    }
}