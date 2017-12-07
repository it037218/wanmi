<?php

/**
 * 权限管理
 * * */
class Onlineproduct extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '产品发布中心') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model', 'product');
    }
    
    public function tomorrow(){
        
        $odate = date('Y-m-d',strtotime("+1 day"));
        $this->index($odate);
    }

    public function index($odate = '') {
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        } 
        $this->load->model('admin_ptype_model', 'ptype');
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        $function_name = "1025";
        //$odate = $this->uri->segment(3);
        if(empty($odate)){
            $odate = date("Y-m-d");
	        $function_name = "1027";
        }
        $ptypeList = $this->ptype->getPtypeList();      
        $rtn = array();
        $yugao = array();
        foreach ($ptypeList as $_ptypeinfo){
            $ptypeproduct = $this->ptype_product->getPtypeProductList($_ptypeinfo['ptid'], $odate);
            $_ptypeinfo['type'] = 'changping';
            $rtn[$_ptypeinfo['ptid']] = $_ptypeinfo;
            if($ptypeproduct){
                $count = 0;
                foreach ($ptypeproduct as $_tp){
                    if($count >= 2){     //就取2个
                        break;
                    }
                    $rtn[$_ptypeinfo['ptid']]['plist'][$count] = $this->product->getProductByPid($_tp['pid']);
                    $count++;
                }
            }
            $rtn[$_ptypeinfo['ptid'] . '_yugao'] = array('name' => $_ptypeinfo['name'] . '_预告', 'type' => 'yugao', 'ptid' => $_ptypeinfo['ptid']);
            $ptypeproduct = $this->ptype_product->getPtypeProductList($_ptypeinfo['ptid'], $odate, true);
            if($ptypeproduct){
                $count = 0;
                foreach ($ptypeproduct as $_tp){
                    if($count >= 2){     //就取2个
                        break;
                    }
                    $rtn[$_ptypeinfo['ptid'] . '_yugao']['plist'][$count] = $this->product->getProductByPid($_tp['pid']);
                    $count++;
                }
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '定期产品发布', $this->getIP(), $this->getSession('uid'));
        $rtn_data['list'] = $rtn;
        $rtn_data['odate'] = $odate;
        $edatable = $this->op->getEditable($this->getSession('uid'),$function_name);
        if(!empty($edatable)){
        	$rtn_data['editable'] = $edatable[0]['editable'];
        }else{
        	$rtn_data['editable']=0;
        }
        $this->load->view('/onlineproduct/v_index', $rtn_data);
    }
    
    public function Soldout($ptid, $pid,$odate){
        $ptid = $this->uri->segment(3);
        $pid = $this->uri->segment(4);
        $odate = $this->uri->segment(5);
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        //从缓存中去掉
        $this->product->moveOnlineProduct($ptid, $pid, $odate);
        //更改产品状态   2下架
        $product = $this->product->getProductDetail($pid);
        // 如果sellmoney ==0 就设置为已经打款
        if($product['sellmoney'] == 0){
            $update_data = array('status' => 2, 'downtime' => time(),'is_upload'=>1);
        }else{
            $update_data = array('status' => 2, 'downtime' => time());
        }
        
        $this->product->updateProductStatus($pid, $update_data);
        $this->ptype_product->updatePorductByPid($pid,array('status' =>1));
        
        if($odate == date('Y-m-d')){
            //加入到售罄队列缓存
            $this->product->addProductToSellOutList($pid);
        }
        
        
        $this->load->model('admin_stock_product_model', 'stock_product');
        $stock_product = $this->stock_product->getSumStockMoney($product['cid']);
        $back_money = $product['money'] - $product['sellmoney'];
        
        $sell_money = $this->product->countSellMoneyByCid($product['cid']);
        $online_money = $this->product->countOnlineProductMoneyByCid($product['cid']);
        $bk = $sell_money + $online_money + $stock_product[0]['SUM(stockmoney)'];
        $this->load->model('admin_contract_model', 'contract');
        $this->contract->backMoneytoContract($product['cid'], $bk);
        
        //记个文本日志
        $log = array();
        $log['status'] = '3';
        $log['pid'] = $pid;
        $log['cid'] = $product['cid'];
        $log['ptid'] = $ptid;
        $log['back_money'] = $back_money;
        $this->load->model('base/log_base', 'log_base');
        $this->log_base->back_contract_log($log);
        $log = $this->op->actionData($this->getSession('name'), '售罄:'. $pid, '', '定期产品发布', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward',OP_DOMAIN.'/onlineproduct'));
    }
    
	public function downtoline($ptid, $pid,$odate = ''){

	    $ptid = $this->uri->segment(3);
	    $pid = $this->uri->segment(4);
	    $this->load->model('admin_ptype_product_model', 'ptype_product');
	    $product = $this->product->getProductDetail($pid);
	    
	    //从缓存中去掉
	    $this->product->moveOnlineProduct($ptid, $pid,$odate);
	    //更改产品状态   2下架
 	    $update_data = array('status' => 2, 'downtime' => time());
	    
 	    $this->product->updateProductStatus($pid, $update_data);
	    $this->ptype_product->updatePorductByPid($pid,array('status' =>1));
	    
	    $product = $this->product->getProductDetail($pid);
	    $back_money = $product['money'] - $product['sellmoney'];
	    
	    $this->load->model('admin_stock_product_model', 'stock_product');
	    $stock_product = $this->stock_product->getSumStockMoney($product['cid']);
	    
	    $sell_money = $this->product->countSellMoneyByCid($product['cid']);
	    $online_money = $this->product->countOnlineProductMoneyByCid($product['cid']);
	    $contract_money = $sell_money + $online_money+$stock_product[0]['SUM(stockmoney)'];
	    
	    $this->load->model('admin_contract_model', 'contract');
	    $this->contract->backMoneytoContract($product['cid'], $contract_money);
	    if($product['sellmoney'] == 0){
	        //删除 product 和 ptype_product 两张表
	        $this->product->delProductByPid($pid);
	        $this->ptype_product->delPtypeProduct($pid);
	    }
	    else{
            //加入到售罄队列缓存
            $this->product->addProductToSellOutList($pid);
	    }
	    //记个文本日志
	    $log = array();
	    $log['status'] = '2';
	    $log['pid'] = $pid;
	    $log['cid'] = $product['cid'];
	    $log['ptid'] = $ptid;
	    $log['back_money'] = $back_money;
	    $this->load->model('base/log_base', 'log_base');
	    $this->log_base->back_contract_log($log);
	    
	    $log = $this->op->actionData($this->getSession('name'), '下架:'.$pid, '', '定期产品下架', $this->getIP(), $this->getSession('uid'));
	    exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '设置成功', array(), '修改产品信息 ', 'forward',OP_DOMAIN.'/onlineproduct'));

	}
    public function changeindex(){
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $this->load->model('admin_ptype_model', 'ptype');
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $ptype = $this->ptype->getPtypeList();
        $ptype_list = array();
        foreach ($ptype as $_val){
           $ptype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($ptype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        
        $ptypeproduct = $this->ptype_product->getPtypeProductList($ptid, $odate);
        foreach ($ptypeproduct as $_index => $_tp){
            $ptypeproduct[$_index]['detail'] = $this->product->getProductByPid($_tp['pid']);
        }
        if(empty($ptypeproduct)){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'项目中没有产品')));
        }
        $rtn_data['list'] = $ptypeproduct;
        $rtn_data['odate'] = $odate;
        $rtn_data['ptname'] = $ptype_list[$ptid];
//         echo json_encode($rtn_data, true);exit;
        $this->load->view('/onlineproduct/v_changeindex', $rtn_data);
    }
	
    public function yugao(){
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $this->load->model('admin_ptype_model', 'ptype');
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $ptype = $this->ptype->getPtypeList();
        $ptype_list = array();
        foreach ($ptype as $_val){
           $ptype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($ptype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        
        $ptypeproduct = $this->ptype_product->getPtypeProductList($ptid, $odate,true);
        foreach ($ptypeproduct as $_index => $_tp){
            $ptypeproduct[$_index]['detail'] = $this->product->getProductByPid($_tp['pid']);
        }
        if(empty($ptypeproduct)){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'项目中没有产品')));
        }
        $rtn_data['list'] = $ptypeproduct;
        $rtn_data['odate'] = $odate;
        $rtn_data['ptname'] = $ptype_list[$ptid];
//         echo json_encode($rtn_data, true);exit;
        $this->load->view('/onlineproduct/v_yugao', $rtn_data);
    }
    
    public function tiaoxu(){
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $pid = $this->input->get('pid');
        $action = $this->input->get('action');
        $this->load->model('admin_ptype_model', 'ptype');
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $ptype = $this->ptype->getPtypeList();
        $ptype_list = array();
        foreach ($ptype as $_val){
            $ptype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($ptype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        $where = array('ptid' => $ptid , 'pid' => $pid, 'odate' => $odate);
        $c_ptype_product_info = $this->ptype_product->getPtypeProduct($where);
        $minrindex = $this->ptype_product->getminrindex($ptid, $odate);
        
        if($c_ptype_product_info['rindex'] == $minrindex){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'第一名不能调序!!')));
        }
        if($action == 'up'){
            $n_ptype_product_info = $this->ptype_product->getupproduct($c_ptype_product_info['rindex'], $ptid, $odate, $minrindex);
            
        }else if($action == 'down'){
            $n_ptype_product_info = $this->ptype_product->getdownproduct($c_ptype_product_info['rindex'], $ptid, $odate);
        }else {
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'错误的请求!!')));
        }
        if(!$n_ptype_product_info){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'前面已没有产品或第1名产品不能替换')));
        }
        $c_rindex = $c_ptype_product_info['rindex'];
        $n_rindex = $n_ptype_product_info['rindex'];
        $c_ptype_product_info['rindex'] = $n_rindex;
        $n_ptype_product_info['rindex'] = $c_rindex;
        $ret1 = $this->ptype_product->updatePtypePorduct($c_ptype_product_info);
        if(!$ret1){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!')));
        }
        $ret2 = $this->ptype_product->updatePtypePorduct($n_ptype_product_info);
        if(!$ret2){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!!')));
        }
        $ret2 = $this->ptype_product->rebuildPtypeProductListRedisCache($ptid, $odate);
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '调整' . $ptype_list[$_val['ptid']] . '顺序', $this->getIP(), $this->getSession('uid'));
//         echo OP_DOMAIN.'/onlineproduct/changeindex?ptid='.$ptid.'&odate="'.$odate.'"';exit;
        exit($this->ajaxDataReturnParams(self::AJ_RET_SUCC,  '调序成功', array(), '调整' . $ptype_list[$_val['ptid']] . '顺序', 'forward' , OP_DOMAIN.'/onlineproduct/changeindex?odate='.$odate.'&ptid='.$ptid ));
    }
    
    public function totop(){
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $pid = $this->input->get('pid');
        $action = $this->input->get('action');
        $this->load->model('admin_ptype_model', 'ptype');
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $ptype = $this->ptype->getPtypeList();
        $ptype_list = array();
        foreach ($ptype as $_val){
            $ptype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($ptype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        $minrindex = $this->ptype_product->getminrindex($ptid, $odate);
        $the2_min = $this->ptype_product->get_the2_min_rindex($ptid, $odate, $minrindex);
        
        $where = array('ptid' => $ptid , 'pid' => $pid, 'odate' => $odate, 'status' => 0);
        $c_ptype_product_info = $this->ptype_product->getPtypeProduct($where);
        $c_ptype_product_info['rindex'] = $the2_min;
        $ret1 = $this->ptype_product->updateAllPtypePorductRindex($odate, $ptid, $the2_min);
        if(!$ret1){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!!')));
        }
        $ret2 = $this->ptype_product->updatePtypePorduct($c_ptype_product_info);
        
        if(!$ret2){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!')));
        }
        $ret2 = $this->ptype_product->rebuildPtypeProductListRedisCache($ptid, $odate);
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '调整' . $ptype_list[$_val['ptid']] . '顺序', $this->getIP(), $this->getSession('uid'));
//      echo OP_DOMAIN.'/onlineproduct/changeindex?ptid='.$ptid.'&odate="'.$odate.'"';exit;
        exit($this->ajaxDataReturnParams(self::AJ_RET_SUCC,  '调序成功', array(), '调整' . $ptype_list[$_val['ptid']] . '顺序', 'forward' , OP_DOMAIN.'/onlineproduct/changeindex?odate='.$odate.'&ptid='.$ptid ));
    }
    
    
    
    
    
}