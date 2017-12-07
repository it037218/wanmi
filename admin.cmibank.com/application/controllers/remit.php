<?php

class remit extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '清算-打款') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_contract_model', 'contract');
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_ptype_model','ptype');
    }
    /**
     * 今日打款列表
     */
    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('今日打款列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            $searchcorname = trim($this->input->post('searchcorname'));
            $searchcon_number = trim($this->input->post('searchcon_number'));
            $searchpname = trim($this->input->post('searchpname'));
            
            if($this->input->request('op') == "search"){
                $contractinfo = $this->contract->getContractlistWhere($searchcorname,$searchcon_number,'请输入开始日期','请输入结束日期');
                foreach ($contractinfo as $key=>$val){
                    $rtncontractcid[] = $val['cid'];
                    $rtncontractcorid[] = $val['corid'];
                }
                $cid = implode(',', $rtncontractcid);
                $corcid = implode(',', $rtncontractcorid);
                $product = $this->product->getTodayRemitProductWhere($cid,$corcid,$searchpname);
                $data['searchcorname'] = $searchcorname;
                $data['searchcon_number'] = $searchcon_number;
                $data['searchpname'] = $searchpname;
            }else{
                
                $product = $this->product->getTodayRemitProduct();
            }
            if(!$product){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'今日暂无打款产品')));
            }
            $cids = array();
            foreach ($product as $key=>$_p){
                if(!isset($rtnproductlist['count_sellmoney'])){
                    $rtnproductlist['count_sellmoney'] = 0;
                }
                $ptype = $this->ptype->getPtypeByPtid($_p['ptid']);
                if(!isset($rtnproductlist[$ptype['name']]['sellmoney'])){
                    $rtnproductlist[$ptype['name']]['sellmoney'] = 0;
                }
                $rtnproductlist[$ptype['name']]['sellmoney'] += $_p['sellmoney'];
                $rtnproductlist['count_sellmoney'] += $_p['sellmoney'];
                
                if(!in_array($_p['cid'], $cids)){
                    $cids[] = $_p['cid'];
                }
            }
            $contract = $this->contract->getContractList(array('cid' => $cids), NULL, NULL);
            $contractList = array();
            foreach ($contract as $_c){
                $contractList[$_c['cid']] = $_c;
            }
            
            $data['rtnproduct'] = $rtnproductlist;
            $data['contract'] = $contractList;
            $data['list'] = $product;
            $data['to'] = '/remit/v_today';
            $edatable = $this->op->getEditable($this->getSession('uid'),'1101');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '清算-打款', '', '今日打款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/remit/v_today', $data);
        }
    }
    
    public function doremit($pid = '', $ptype = ''){
        $flag = $this->op->checkUserAuthority('今日打款列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            if($this->input->request('op') == 'doremit'){
                $is_upload = trim($this->input->post('is_upload'));
                $pid = trim($this->input->request('pid'));
                $warrant_img = trim($this->input->request('warrant_img'));
                $des = trim($this->input->request('des'));
                $ctime = trim($this->input->request('ctime'));
                $product = $this->product->getProductByPid($pid);
                if($product['status'] == 6){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'产品已有还款凭证')));
                }
                if($product['remitid']){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'产品已有打款凭证')));
                }
                $to_params = trim($this->input->request('to'));
                $to = '/remit';
                $to_name = '今日打款列表';
                if($to_params == 'no_remit'){
                    $to = '/remit/no_remit';
                    $to_name = '末打款列表';
                }
                $data = array('pid' => $pid, 'warrant_img' => $warrant_img, 'des' => $des ,'ctime'=>strtotime($ctime));
                $this->load->model('admin_product_remit_model', 'product_remit');
                $lastid = $this->product_remit->addPorductRemit($data);
                $this->product->updatePorduct($pid, array('remitid' => $lastid));
                //按回款日  生成   回款单
                $this->load->model('admin_product_backmoney_model', 'product_backmoney');
                $backmoney_info = $this->product_backmoney->getProductBackmoney(array('cid'=> $product['cid'], 'cietime' => $product['cietime']));
                $contract = $this->contract->getContractByCid($product['cid']);
                if($backmoney_info){
                    $update_data = array();
                    if(!empty($backmoney_info['pids'])){
	                    $update_data['pids'] = $backmoney_info['pids'] . ',' . $product['pid'];
                    }else{
                    	$update_data['pids'] = $product['pid'];
                    }
                    $profit = $this->countProductProfit(strtotime($product['cistime']), strtotime($product['cietime']), $contract['con_income'], $product['sellmoney']);
                    $update_data['remitmoney'] =$product['sellmoney'] + $backmoney_info['remitmoney'];
                    $update_data['backmoney'] = $profit + $product['sellmoney'] + $backmoney_info['backmoney'];
                    $this->product_backmoney->updatePorductBackmoney($backmoney_info['bid'], $update_data);
                }else{
                    $inster_data = array();
                    $inster_data['cid'] = $product['cid'];
                    $inster_data['pids'] = $pid;
                    $inster_data['remitmoney'] = $product['sellmoney'];
                    $inster_data['cietime'] = $product['cietime'];
                    $profit = $this->countProductProfit(strtotime($product['cistime']), strtotime($product['cietime']), $contract['con_income'], $product['sellmoney']);
                    $inster_data['backmoney'] = $product['sellmoney'] + $profit;
                    $this->product_backmoney->addPorductBackmoney($inster_data);
                }
                $update_data = array('status' => 4,'is_upload'=>$is_upload);
                $this->product->updatePorduct($pid, $update_data);
                
                $cid = trim($this->input->post('cid'));
                $con_dkje = trim($this->input->post('con_dkje'));
                
                $contract = $this->contract->getContractByCid($cid);
                $datacontent['con_dkje'] = $con_dkje +$contract['con_dkje'];
                $datacontent['remittime']=strtotime($ctime);
                $contract = $this->contract->updateContract($cid,$datacontent);
                
                $log = $this->op->actionData($this->getSession('name'), '给合作方打款', '', '添加打款凭证', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加打款凭证成功', array(), $to_name, 'closeCurrent', OP_DOMAIN . $to));
            } else {
                $product = $this->product->getProductDetail($pid);
                $contract = $this->contract->getContractByCid($product['cid']);
                $this->load->model('admin_corporation_model', 'corporation');
                $corporation = $this->corporation->getCorporationByCid($contract['corid']);
                $data['corporation'] = $corporation;
                $data['contract'] = $contract;
                $data['product'] = $product;
                $data['to'] = $ptype;
                $log = $this->op->actionData($this->getSession('name'), '清算-还款', '', '今日应打款列表', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/remit/v_doremit', $data);
            }
        }
    }
    
    private function countProductProfit($start, $end, $money, $income){
        $days = (($end - $start)/86400) + 1;
        $profit = $income/100/360 * $money * $days;
        return $profit;
    }

    public function editRemit($pid = ''){
        if($this->input->request('op') == 'editremit'){
            $rid = trim($this->input->request('rid'));
            $warrant_img = trim($this->input->request('warrant_img'));
            $des = trim($this->input->request('des'));
            $pid = trim($this->input->post('pid'));
            $ctime = trim($this->input->post('ctime'));
            $is_upload = trim($this->input->post('is_upload'));
            $product_data = array('is_upload'=>$is_upload);
            $data = array('warrant_img' => $warrant_img, 'des' => $des,'ctime'=>strtotime($ctime));
            $this->load->model('admin_product_remit_model', 'product_remit');
            $this->product_remit->updatePorductRemit($rid, $data);
            $this->product->updatePorduct($pid, $product_data);
            
            $log = $this->op->actionData($this->getSession('name'), '修改打款凭证', '', '修改打款凭证:'. $rid, $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '修改打款凭证成功', array(), '修改打款凭证 ', 'no', OP_DOMAIN . '/remit'));
        } else {
            $product = $this->product->getProductByPid($pid);
            $contract = $this->contract->getContractByCid($product['cid']);
            $this->load->model('admin_corporation_model', 'corporation');
            $corporation = $this->corporation->getCorporationByCid($contract['corid']);
            $this->load->model('admin_product_remit_model', 'product_remit');
            $remit = $this->product_remit->getProductRemitByRid($product['remitid']);
            $data['corporation'] = $corporation;
            $data['contract'] = $contract;
            $data['product'] = $product;
            $data['remit'] = $remit;
            $log = $this->op->actionData($this->getSession('name'), '修改打款凭证', '', '修改打款凭证', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/remit/v_editremit', $data);
        }
    }
    
    
    public function showremit($pid){
        $product = $this->product->getProductByPid($pid);
        $contract = $this->contract->getContractByCid($product['cid']);
        $this->load->model('admin_corporation_model', 'corporation');
        $corporation = $this->corporation->getCorporationByCid($contract['corid']);
        $this->load->model('admin_product_remit_model', 'product_remit');
  
        $remit = $this->product_remit->getProductRemitByRid($product['remitid']);

        $data['corporation'] = $corporation;
        $data['contract'] = $contract;
        $data['product'] = $product;
        $data['remit'] = $remit;
        $log = $this->op->actionData($this->getSession('name'), '清算-还款', '', '今日应打款列表', $this->getIP(), $this->getSession('uid'));
        $this->load->view('/remit/v_showdoremit', $data);
    }
    
    public function no_remit(){
        $flag = $this->op->checkUserAuthority('今日打款列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $pname = trim($this->input->post('searchpname'));
            $corname = trim($this->input->post('searchcorname'));
            $con_number = trim($this->input->post('searchcon_number'));
            $startcistime = trim($this->input->post('startcistime'));
            $endcistime = trim($this->input->post('endcistime'));
            $startcietime = trim($this->input->post('startcietime'));
            $endcietime = trim($this->input->post('endcietime'));
            if($this->input->request('op') == "search"){
                $productList = $this->product->getNoRemitProductWhere($pname,$startcistime,$endcistime,$startcietime,$endcietime,$corname,$con_number);
                if(!$productList){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有内容')));
                }
                $cids = array();
                foreach ($productList as $_p){
                    if(!in_array($_p['cid'], $cids)){
                        $cids[] = $_p['cid'];
                    }
                }
                //$contract = $this->contract->getContractList(array('cid' => $cids),'',array(100000,0));
                $contract = $this->contract->getContractList(array('cid' => $cids),'',NULL);
                $contractList = array();
                foreach ($contract as $_c){
                    $contractList[$_c['cid']] = $_c;
                }
                $count = count($productList);
                $data['contract'] = $contractList;
                $data['list'] = $productList;
                
                foreach ($productList as $key=>$val){
                    if(!isset($rtnproductlist['count_sellmoney'])){
                        $rtnproductlist['count_sellmoney'] = 0;
                    }
                    $ptype = $this->ptype->getPtypeByPtid($val['ptid']);
                    if(!isset($rtnproductlist[$ptype['name']]['sellmoney'])){
                        $rtnproductlist[$ptype['name']]['sellmoney'] = 0;
                    }
                    $rtnproductlist[$ptype['name']]['sellmoney'] += $val['sellmoney'];
                    $rtnproductlist['count_sellmoney'] += $val['sellmoney'];
                }
                
                $data['rtnproduct'] = $rtnproductlist;
                $data['searchcorname'] = $corname;
                $data['searchcon_number'] = $con_number;
                $data['searchpname'] = $pname;
                $data['none'] = 'none';
            }else{
                $productList = $this->product->getNoRemitProduct($psize, $offset);
                if(!$productList){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有末打款产品')));
                }
                $cids = array();
                foreach ($productList as $_p){
                    if(!in_array($_p['cid'], $cids)){
                        $cids[] = $_p['cid'];
                    }
                }
                $contract = $this->contract->getContractList(array('cid' => $cids), NULL, NULL);
                $contractList = array();
                foreach ($contract as $_c){
                    $contractList[$_c['cid']] = $_c;
                }
                $count = $this->product->countNoRemitProduct();
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['contract'] = $contractList;
                $data['list'] = $productList;
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                 
            }else{
                $data['numPerPage'] =$data['pageNum'];
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1102');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '清算-打款', '', '今日打款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/remit/v_no_remit', $data);
           
        }
    }

    public function remited(){
        $flag = $this->op->checkUserAuthority('已打款列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            if($this->input->request('op') == "search"){
                $pname = trim($this->input->post('searchpname'));
                $corname = trim($this->input->post('searchcorname'));
                $con_number = trim($this->input->post('searchcon_number'));
                $startcistime = trim($this->input->post('startcistime'));
                $endcistime = trim($this->input->post('endcistime'));
                $startcietime = trim($this->input->post('startcietime'));
                $endcietime = trim($this->input->post('endcietime'));
                $productList = $this->product->getRemitedProductWhere($pname,$startcistime,$endcistime,$startcietime,$endcietime,$corname,$con_number);
                if(!$productList){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有已打款产品')));
                }
                $cids = array();
                foreach ($productList as $_p){
                    if(!in_array($_p['cid'], $cids)){
                        $cids[] = $_p['cid'];
                    }
                }
                //$contract = $this->contract->getContractList(array('cid' => $cids),'',array(100000,0));
                $contract = $this->contract->getContractList(array('cid' => $cids),'',NULL);
                $contractList = array();
                foreach ($contract as $_c){
                    $contractList[$_c['cid']] = $_c;
                }
                $count = count($productList);
                $data['contract'] = $contractList;
                $data['list'] = $productList;
                
                foreach ($productList as $key=>$val){
                    if(!isset($rtnproductlist['count_sellmoney'])){
                        $rtnproductlist['count_sellmoney'] = 0;
                    }
                    $ptype = $this->ptype->getPtypeByPtid($val['ptid']);
                    if(!isset($rtnproductlist[$ptype['name']]['sellmoney'])){
                        $rtnproductlist[$ptype['name']]['sellmoney'] = 0;
                    }
                    $rtnproductlist[$ptype['name']]['sellmoney'] += $val['sellmoney'];
                    $rtnproductlist['count_sellmoney'] += $val['sellmoney'];
                }
                
                $data['rtnproduct'] = $rtnproductlist;
                $data['searchcorname'] = $corname;
                $data['searchcon_number'] = $con_number;
                $data['searchpname'] = $pname;
                $data['none'] = 'none';
            }else{
                $productList = $this->product->getRemitedProduct($psize, $offset);
                if(!$productList){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有已打款产品')));
                }
                $cids = array();
                foreach ($productList as $_p){
                    if(!in_array($_p['cid'], $cids)){
                        $cids[] = $_p['cid'];
                    }
                }
                $contract = $this->contract->getContractList(array('cid' => $cids), NULL, NULL);
                $contractList = array();
                foreach ($contract as $_c){
                    $contractList[$_c['cid']] = $_c;
                }
                $count = $this->product->countRemitedProduct();
                $data['contract'] = $contractList;
                $data['list'] = $productList;
                $data['to'] = '/remit/v_no_remit';
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
   
            }else{
                 $data['numPerPage'] =$data['pageNum'] = $data['count'] = $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1103');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '清算-打款', '', '今日打款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/remit/v_remited', $data);
        }
    }
}