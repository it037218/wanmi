<?php

/**
 * 权限管理
 * * */
class Product extends Controller {

    private $map;
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '产品已发布中心') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->map = array('0' => OP_DOMAIN . '/product/Unpublished', 1 => OP_DOMAIN . '/product');
        $this->qx =array('0' => '未发布定期产品','1' => '已发布定期产品');
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_contract_model', 'contract');
        $this->load->model('admin_ptype_product_model', 'ptype_product');
        $this->load->model('admin_product_buy_model','product_buy');
        $this->load->model('admin_useridentity_model','useridentity');
        $this->load->model('admin_userproduct_model','userproduct');
        $this->load->model('admin_pay_log_model','pay_log');
        $this->load->model('admin_buy_log_model','buy_log');
        $this->load->model('admin_ptype_model','ptype');
    }
    function Unpublished(){
        $status=0;
        $func_name = '/product/'.__FUNCTION__;
        $this->index($status, $func_name);
    }
    public function index($status=1, $func_name = 'product') {
        $flag = $this->op->checkUserAuthority($this->qx[$status], $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        } else {
            $countSellmoney = 0;
            $this->load->model('admin_ptype_model', 'ptype');
            $ptype = $this->ptype->getPtypeList();
            $ptype_list = array();
            foreach ($ptype as $_val){
               $ptype_list[$_val['ptid']] = $_val['name'];
            }
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            
            $searchpname = trim($this->input->post('searchpname'));
            $searchstart = trim($this->input->post('searchstart'));
            $searchend = trim($this->input->post('searchend'));
            $searchuietime = trim($this->input->post('searchuietime'));
            $searchenduietime = trim($this->input->post('searchenduietime'));
            
            if($this->input->request('op') == "search"){
                if($searchstart != '请输入开始日期' && $searchend != '请输入结束日期'){
                   $type = 1;
                }else if($searchuietime != '请输入开始日期' && $searchenduietime != '请输入结束日期'){              
                   $type = 2;
                  
                }else if($searchpname != "请输入搜索内容"){
                    $type = 3;
                } 
                $product = $this->product->getproduct($searchstart,$searchend,$searchpname,$type);
                if(empty($product)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'没有内容')));
                }
                $rtnproduct = array();
                foreach ($product as $key=>$val){
                    if(!isset($rtnproduct['count_sellmoney'])){
                        $rtnproduct['count_sellmoney'] = 0;
                    }
                    $ptype = $this->ptype->getPtypeByPtid($val['ptid']);
                    if(!isset($rtnproduct[$ptype['name']]['sellmoney'])){
                        $rtnproduct[$ptype['name']]['sellmoney'] = 0;
                    }
                    $rtnproduct[$ptype['name']]['sellmoney'] += $val['sellmoney'];
                    $rtnproduct['count_sellmoney'] += $val['sellmoney'];
                    $product[$key]['count_people'] = count($this->product_buy->getProductBuyInfoByPid(array('pid'=>$val['pid']),''));
                }
                $count = count($this->product->getproduct($searchstart,$searchend,$searchpname,$type));
                $data['list'] = $product;
                $data['searchstart'] = $searchstart;
                $data['searchend'] = $searchend;
                $data['ptype_list'] = $ptype_list;
                $data['func_name'] = $func_name;
                $data['status'] = $status;
                $data['rtnproduct'] = $rtnproduct;
                $data['searchpname'] = $searchpname;
            }else{
                if($status=='0'){
                    $product = $this->product->getProductList(array('status'=>$status), 'ctime desc',array($psize, $offset));
                    $count = count($this->product->getProductList(array('status'=>$status), 'ctime desc',''));
                }
                if($status=='1'){
                    //$where = array('uid' => array(11,22,33))
                    //array('status'=>array(2,3,4))
                    $product = $this->product->getProductList(array('status'=>array(1,2,3,4,5,6,7)), 'ctime desc', array($psize, $offset));
                    foreach ($product as $key=>$val){

                        $product[$key]['count_people'] = count($this->product_buy->getProductBuyInfoByPid(array('pid'=>$val['pid']),''));
                    }
                    $count = $this->product->getProductCount();
                }
                $contract = array();
                foreach ($product as $key=>$val){
                    $contract[] = $this->contract->getContractByCid($val['cid']);
                }
                $rtncontent =  array();
                foreach ($contract as $key=>$val){
                    $rtncontent[$val['cid']] = $val['con_income'];
                }
                $data['contract'] = $rtncontent;
                $data['list'] = $product;
                $data['ptype_list'] = $ptype_list;
                $data['func_name'] = $func_name;
                $data['status'] = $status;
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = '';
                $data['pageNum'] = $data['page'] = $data['numPerPage'] = $data['count'] = 0;
            }
            
            $function_name = "1023";
            if($func_name == 'product'){
            	$function_name = "1029";
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),$function_name);
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '产品已发布中心', '', '查看', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/product/v_index', $data);
        }
    }
    
    public function autotianchong(){
        $pname = $this->input->post('pname');
        $data = $this->product->autotianchong($pname);
        echo json_encode($data[0]);
        exit;
    }
    public function addproduct($status=1){
        $flag = $this->op->checkUserAuthority('已发布定期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加产品产品');
        } else {
            if($this->input->request('op') == 'addproduct'){
                $ptid = trim($this->input->post('ptid'));
                $corcid = trim($this->input->post('corcid'));
                $cid = trim($this->input->post('cid'));
                $contract = $this->contract->getContractByCid($cid);
                $ucid = trim($this->input->post('ucid'));
                $pname = trim($this->input->post('pname'));
                $income = trim($this->input->post('income'));
                $uistime = trim($this->input->post('uistime'));     //用户起息时间
                $uietime = trim($this->input->post('uietime'));     //用户结息时间
                $cistime = trim($this->input->post('cistime'));     //合作方起息时间
                $cietime = trim($this->input->post('cietime'));     //合作方结息时间
                
                $interesttime = $contract['interesttime'];      //合同起息时间
                $repaymenttime = $contract['repaymenttime'];    //合同还款时间
                
                if($uistime<$interesttime){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'产品起息时间必须大于于合同起息时间')));
                }
                if($uietime>$repaymenttime){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'产品结息时间必须小于合同结息时间')));
                }
                
                if($cistime<$interesttime){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'合作方起息时间必须大于于合同起息时间')));
                }
                if($cietime>$repaymenttime){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'合作方结息时间必须小于合同结息时间')));
                }
                $repaymode = trim($this->input->post('repaymode'));
                $money = trim($this->input->post('money'));
                $startmoney = trim($this->input->post('startmoney'));
                $money_limit = trim($this->input->post('money_limit'));
                $money_max = trim($this->input->post('money_max'));
                if($money_max<=0){
                    $money_max = $money;
                }
                $operation_tag = trim($this->input->post('operation_tag'));
                $standard_tag = trim($this->input->post('standard_tag'));
                $standard_text = trim($this->input->post('standard_text'));
                $standard_icon = trim($this->input->post('standard_icon'));
                $text_text = trim($this->input->post('text_text'));
                $text_url = trim($this->input->post('text_url'));
                $online_time = trim($this->input->post('online_time'));
                //$yugaotime = trim($this->input->post('yugaotime'));
                $canbuyuser = trim($this->input->post('canbuyuser'));
                $cancm = trim($this->input->post('cancm'));
                $recommend = trim($this->input->post('recommend'));
                
                $exp_buy = trim($this->input->post('exp_buy'));
                $exp_send = trim($this->input->post('exp_send'));
                
                
                $data['ptid'] = $ptid;      //项目类型
                $data['corcid'] = $corcid;  //债权公司Id
                $data['cid'] = $cid;        //合同编号ID
                $data['ucid'] = $ucid;      //用户合同ID
                $ret = $this->product->getProductList(array('pname'=>$pname),'');
                if(!empty($ret)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'产品名字已经存在了')));
                }
                $data['pname'] = $pname;
                $data['income'] = $income;
                $data['uistime'] = $uistime;
                $data['uietime'] = $uietime;
                $data['cistime'] = $cistime;
                $data['cietime'] = $cietime;
                $data['repaymode'] = $repaymode;
                $data['money'] = $money;
                $data['startmoney'] = $startmoney;
                $data['money_limit'] = $money_limit;
                $data['money_max'] = $money_max;
                $data['operation_tag'] = $operation_tag;
                $data['standard_tag'] = $standard_tag;
                $data['standard_text'] = $standard_text;
                $data['text_text'] = $text_text;
                $data['text_url'] = $text_url;
                $data['standard_icon'] = $standard_icon;
                $data['canbuyuser'] = $canbuyuser;
                $data['online_time'] = $online_time;
                //$data['yugaotime'] = $yugaotime;
                $data['cancm'] = $cancm;
                $data['recommend'] = $recommend;
                $data['ctime'] = time();                        //创建时间
                $data['exp_buy'] = $exp_buy;                    //买多少金额
                $data['exp_send'] = $exp_send;                  //送多少体验金
                
                //扣除合同金额
                $contract_data = $this->contract->getContractByCid($cid);
                $remain_money = $contract_data['con_money'] - $contract_data['money'];
                //判断上线时间
                if(!empty($online_time)){
                    if(strtotime($online_time)<$data['ctime']){
                        exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'预告时间必须大于现在时间')));
                    }    
                }
                if(empty($ucid)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请选择业务类型')));
                }
                if($ptid==0){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请选择项目')));
                }
                if($income>20){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'收益率不能大于20%')));
                }
                if($remain_money < $money){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'库存不足')));
                }
                $insertid = $this->product->addPorduct($data);
                if(!$insertid){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'上传产品信息失败!')));
                }
                $ret = $this->contract->updateContractMoney($cid, $money);
                if(!$ret){
                    $this->product->delProductByPid($insertid);
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新合同信息失败!')));
                }
                $log = $this->op->actionData($this->getSession('name'), '已发布定期产品', '', '上传产品信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '上传产品信息成功', array(), '上传产品信息 ', 'forward', OP_DOMAIN . '/product/Unpublished'));
            }else{
                $this->load->model('admin_ptype_model', 'ptype');
                $ptype = $this->ptype->getPtypeList();
                
                $ptype_list = array();
                foreach ($ptype as $_val){
                    $ptype_list[$_val['ptid']] = $_val['name'];
                }
                //最小日期
                $mindate =date("Y-m-d",time()+86400);
                
                $data['mindate'] = $mindate;
                $data['ptype_list'] = $ptype_list;
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getcanUseUsercontract();
                $this->load->model('admin_corporation_model', 'corporation');

                $corids = $this->contract->getRepaymenttimeBigNow();              
                $data['corporation'] = $this->corporation->getCorporationInCorid($corids);
                
                //$data['corporation'] = $this->corporation->getAllCorporation();
                $this->load->view('/product/v_addProduct', $data);
            }
        }
    }
    
    
    public function editproduct($status=1) {
        $flag = $this->op->checkUserAuthority('已发布定期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            if($this->input->request('op') == 'saveedit'){
               $pid = $this->input->post('pid');
               if(!$pid){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               }
               $ptid = trim($this->input->post('ptid'));
               $ucid = trim($this->input->post('ucid'));
               $pname = trim($this->input->post('pname'));
               $income = trim($this->input->post('income'));
               $uistime = trim($this->input->post('uistime'));     //用户起息时间
               $uietime = trim($this->input->post('uietime'));     //用户结息时间
               $cistime = trim($this->input->post('cistime'));     //合作方起息时间
               $cietime = trim($this->input->post('cietime'));     //合作方结息时间
               $repaymode = trim($this->input->post('repaymode'));
               $money = trim($this->input->post('money'));
               $startmoney = trim($this->input->post('startmoney'));
               $money_limit = trim($this->input->post('money_limit'));
               $money_max = trim($this->input->post('money_max'));
               $operation_tag = trim($this->input->post('operation_tag'));
               $standard_tag = trim($this->input->post('standard_tag'));
               $standard_text = trim($this->input->post('standard_text'));
               $standard_icon = trim($this->input->post('standard_icon'));
               
               $text_text = trim($this->input->post('text_text'));
               $text_url = trim($this->input->post('text_url'));
               $exp_buy = trim($this->input->post('exp_buy'));
               $exp_send = trim($this->input->post('exp_send'));
               
               $canbuyuser = trim($this->input->post('canbuyuser'));
               //$yugaotime = trim($this->input->post('yugaotime'));
               $online_time = trim($this->input->post('online_time'));
               $cancm = trim($this->input->post('cancm'));
               
               $data['ptid'] = $ptid;      //项目类型
               $data['ucid'] = $ucid;      //用户合同ID
               $data['pname'] = $pname;
               $data['income'] = $income;
               $data['uistime'] = $uistime;
               $data['uietime'] = $uietime;
               $data['cistime'] = $cistime;
               $data['cietime'] = $cietime;
               $data['repaymode'] = $repaymode;
               $data['money'] = $money;
               $data['startmoney'] = $startmoney;
               $data['money_limit'] = $money_limit;
               $data['money_max'] = $money_max;
               $data['operation_tag'] = $operation_tag;
               $data['standard_tag'] = $standard_tag;
               $data['standard_text'] = $standard_text;
               $data['standard_icon'] = $standard_icon;
               $data['text_text'] = $text_text;
               $data['text_url'] = $text_url;
               $data['exp_buy'] = $exp_buy;
               $data['exp_send'] = $exp_send;
               $data['canbuyuser'] = $canbuyuser;
               $data['cancm'] = $cancm;
               $data['online_time'] = $online_time;
               //$data['yugaotime'] = $yugaotime;
               $data['ctime'] = time();                 //创建时间
               
               $detail = $this->product->getProductByPid($pid);
               $cid = $detail['cid'];
               if($detail['money'] != $money){
                   $diff_money = $money - $detail['money'];
                   $contractInfo = $this->contract->getContractByCid($cid);
                   if(empty($contractInfo)){
                       exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的合同ID')));
                   }
                   if($contractInfo['money'] + $diff_money >= $contractInfo['money']){       //可售卖金额超过需要金额
                       exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'可售卖金额超过需要金额')));
                   }
                   if($contractInfo['money'] + $diff_money <= 0){
                       exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'错误的金额')));
                   }
                   $contractInfo['money'] += $diff_money;
                   $ret = $this->contract->updateContract($contractInfo['cid'], $contractInfo);
               }
               $ret = $this->product->updatePorduct($pid, $data);
               $ptype_product = $this->ptype_product->getPtypeProduct(array('pid'=>$pid));
               if(!isset($ptype_product)){
                   $ret = $this->ptype_product->rebuildPtypeProductListRedisCache($ptid, $ptype_product['odate']);
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '已发布定期产品', '', '修改产品信息', $this->getIP(), $this->getSession('uid'));
//                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward', OP_DOMAIN.'/product'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward',$this->map[$status]));
            }else{
                $pid = $this->uri->segment(3);
                if($pid < 0 || !is_numeric($pid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->product->getProductByPid($pid);
//                 if($data['detail']['status'] == 1){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已上架物品不能修改')));
//                 }
                $this->load->model('admin_ptype_model', 'ptype');
                $ptype = $this->ptype->getPtypeList();
                $ptype_list = array();
                foreach ($ptype as $_val){
                    $ptype_list[$_val['ptid']] = $_val['name'];
                }
                $data['ptype_list'] = $ptype_list;
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getcanUseUsercontract();
                $this->load->model('admin_corporation_model', 'corporation');
                $data['corporation'] = $this->corporation->getAllCorporation();
                $this->load->view('/product/v_editProduct', $data);
            }
        }
    }

	public function detail(){
		$flag = $this->op->checkUserAuthority('已发布定期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '编辑产品');
        } else {
                $pid = $this->input->get('pid');
                $data['detail'] = $this->product->getProductByPid($pid);
                $this->load->model('admin_ptype_model', 'ptype');
                $data['ptype'] = $this->ptype->getPtypeByPtid($data['detail']['ptid']);
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getUsercontractByUcid($data['detail']['ucid']);
                $data['contract'] = $this->contract->getContractByCid($data['detail']['cid']);
		}
		$this->load->view('/product/v_detail',$data);
	}
	
    //产品上线
    public function uptoline(){
        $flag = $this->op->checkUserAuthority('已发布定期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '产品上架');
        } else {
            if($this->input->request('op') == 'online'){
                $ptid = $this->input->request('ptid');
                $pid = $this->input->request('pid');
                $odate = $this->input->request('odate');
                $time = time();
                $detail = $this->product->getProductByPid($pid);
                if(empty($detail)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'产品不存在')));
                }
                $stype = 0;
                if($odate == 1){
                    $odate = date("Y-m-d");
                } else {
                    $odate = date('Y-m-d',strtotime("+1 day"));
                }
                //上线和预告时间if($detail['online_time'] && $detail['yugaotime']){
                if($detail['online_time']){
                    //添加到预告队列中
                    $stype = 1;
                    $maxRindex = 0;
                }else{
                    $maxRindex = $this->ptype_product->getMaxRindxByPtid($ptid, $odate);
                    $maxRindex++;
                }
                $ptype_product_info = array();
                $ptype_product_info['ptid'] = $ptid;
                $ptype_product_info['pid'] = $pid;
                $ptype_product_info['rindex'] = $maxRindex;
                $ptype_product_info['odate'] = $odate;
                $ptype_product_info['stype'] = $stype;
                $ret = $this->ptype_product->addptypeproduct($ptype_product_info);
                $update_data = array('status' => 1, 'uptime' => $time);
                $ret = $this->product->updatePorduct($pid, $update_data);
                $ret = $this->product->_flushProductDetailRedisCache($pid);
                if($ret){
                    $log = $this->op->actionData($this->getSession('name'), '产品上架:'.$pid, '', '已发布定期产品', $this->getIP(), $this->getSession('uid'));
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '添加成功', array(), '产品上架 ', 'forward', OP_DOMAIN.'/product'));
                }
            }else{
                $pid = $this->uri->segment(3);
                $ptid = $this->uri->segment(4);
                $data['pid'] = $pid;
                $data['ptid'] = $ptid;
                $this->load->view('/product/v_uptoline', $data);
            }
        }
    }
    public function delproduct($pid=''){
        $flag = $this->op->checkUserAuthority('未发布定期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '未发布定期产品');
        } else {
            $product = $this->product->getProductDetail($pid);
            //从缓存中去掉
    	    $this->product->moveYugaoProduct($product['ptid'], $pid);
    	    $back_money = $product['money'] - $product['sellmoney'];
    	    $this->product->delProductByPid($pid);
    	    $this->ptype_product->delPtypeProduct($pid);
    	    $productList = $this->product->getProductListByCid($product['cid'],'','','');
    	    $sellMoney = 0;
    	    if(!empty($productList)){
    	    	foreach ($productList as $_product){
    	    		if($_product['status']>1){
    	    			$sellMoney = $sellMoney+$_product['sellmoney'];
    	    		}else{
    	    			$sellMoney = $sellMoney+$_product['money'];
    	    		}
    	    	}
    	    }
    	    $this->contract->backMoneytoContract($product['cid'], $sellMoney);
    	    
    	    //记个文本日志
//     	    $log = array();
//     	    $log['title'] = '删除';
//     	    $log['status'] = '2';
//     	    $log['pid'] = $pid;
//     	    $log['cid'] = $product['cid'];
//     	    $log['back_money'] = $back_money;
//     	    $this->load->model('base/log_base', 'log_base');
//     	    $this->log_base->back_contract_log($log);
//             $ret = $this->product->delProductByPid($pid);
//             if(!$ret){
//                 exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
//             }
        }
        $log = $this->op->actionData($this->getSession('name'), '产品未发布中心', '', '删除未发布定期产品', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除未发布定期产品', 'forward', OP_DOMAIN.'/product/Unpublished'));
    }
    public function getProductBuyInfoByPid($pid=''){
        $flag = $this->op->checkUserAuthority('未发布定期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '未发布定期产品');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            
            $searchpname = trim($this->input->post('searchpname'));
            $searchtype = trim($this->input->post('searchtype'));
            $searchtitle = trim($this->input->post('searchtitle'));
            $searchtrxId = trim($this->input->post('searchtrxId'));
            
            if($searchpname && $searchpname != '请输入产品名称' && $this->input->request('op') == "search"){
                $product = $this->product->getProductList(array('pname'=>$searchpname),'','');
                if(empty($product)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'产品不存在')));
                }
                $product_buy = $this->product_buy->getProductBuyInfoByPid(array('pid'=>$product[0]['pid']),'ctime desc',array($psize, $offset));
                foreach ($product_buy as $key=>$val){
                    $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$val['uid']),'ctime desc','');
                    $product_buy[$key]['realname'] = $useridentity[0]['realname'];
                    $product_buy[$key]['idCard'] = $useridentity[0]['idCard'];
                    $product_buy[$key]['pname'] = $product[0]['pname'];
                    $data['list'] = $product_buy;
                    $count = count($this->product_buy->getProductBuyInfoByPid(array('pid'=>$product[0]['pid']),'',''));
                }
                
            }else if($searchtitle && $searchtitle != '请输入搜索内容' && $this->input->request('op') == "search"){
                
                $useridentity = $this->useridentity->getUseridentityListByLike($searchtitle,$searchtype,'');
                if(empty($useridentity)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'产品不存在')));
                }
                $uid = $useridentity[0]['uid'];
                $userproduct = $this->userproduct->getUserProductlistByUid($uid,array('uid'=>$uid),'ctime desc',array($psize, $offset));
                foreach ($userproduct as $key=>$val){
                    $userproduct[$key]['realname'] = $useridentity[0]['realname'];
                    $userproduct[$key]['idCard'] = $useridentity[0]['idCard'];
                    $userproduct[$key]['account'] = $useridentity[0]['phone'];
                    $userproduct[$key]['ctime'] = $val['buytime'];
                }
                $count = count($this->userproduct->getUserProductlistByUid($uid,array('uid'=>$uid),'',''));
                $data['list'] = $userproduct;
                $data['searchtitle'] = $searchtitle;
                
            }else if($searchtrxId && $searchtrxId != '请输入订单号' && $this->input->request('op') == "search"){
                
                $buy_log = $this->buy_log->getBuyLog($searchtrxId);
                if(empty($buy_log)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'产品不存在')));
                }
                $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$buy_log[0]['uid']),'','');
                $buy_log[0]['realname'] = $useridentity[0]['realname'];
                $buy_log[0]['idCard'] = $useridentity[0]['idCard'];
                $buy_log[0]['money'] = $buy_log[0]['amt'];
                $buy_log[0]['account'] = $useridentity[0]['phone'];
                $buy_log[0]['trxId'] = $buy_log[0]['ordid'];
                $data['list'] = $buy_log;
                $data['searchtrxId'] = $searchtrxId;
                $count = count($this->buy_log->getBuyLog($searchtrxId));
            }else{
                $product_buy = $this->product_buy->getProductBuyInfoByPid(array('pid'=>$pid),'ctime desc',array($psize, $offset));
                foreach ($product_buy as $key=>$val){
                    $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$val['uid']),'','');
                    $product = $this->product->getProductByPid($pid);
                    $product_buy[$key]['realname'] = $useridentity[0]['realname'];
                    $product_buy[$key]['idCard'] = $useridentity[0]['idCard'];
                    $product_buy[$key]['pname'] = $product['pname'];
                }
                $data['list'] = $product_buy;
                $data['searchpname'] = $searchpname;
                $data['pid'] = $pid;
                $count = count($this->product_buy->getProductBuyInfoByPid(array('pid'=>$pid),'',''));
            }
            
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'product/index?page=' . $page;
                if(!empty($searchpname)){
                    $data['rel'] .= '&title=' . $searchpname;
                }
            }else{
                $data['pageNum']    = 0;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            
            $this->load->view('/product/v_productBuyInfo',$data);
          
        }
    }
    
}