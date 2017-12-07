<?php
define('EQUALAMOUNT_NAME', "等额本息合同模板");
/**
 * 合同管理
 * * */
class equalamountcontract extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == EQUALAMOUNT_NAME) {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_equalamountcontract_model', 'equalamountcontract');
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_product_remit_model','product_remit');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority(EQUALAMOUNT_NAME, $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), EQUALAMOUNT_NAME);
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            if($this->input->request('op') == "search"){
                $corname = trim($this->input->post('searchcorname'));
                $con_number = trim($this->input->post('searchcon_number'));
                $interesttime = trim($this->input->post('searchinteresttimet'));
                $repaymenttime = trim($this->input->post('searchrepaymenttime'));
                $contract = $this->equalamountcontract->getContractlistsql($corname,$con_number,$interesttime,$repaymenttime);
                $data['list'] = $contract;
                $count = count($contract);
                $this->load->model('admin_usercontract_model', 'usercontract');
                $result = $this->usercontract->getcanUseUsercontract();
                $usercontract = array();
                foreach ($result as $_uc){
                    $usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
                }
                $data['usercontract'] = $usercontract;
                $data['searchcorname'] = $corname;
                $data['searchcon_number'] = $con_number;
                $data['searchinteresttimet'] = $interesttime;
                $data['searchrepaymenttime'] = $repaymenttime;
            }else{
                $data['list'] = $this->equalamountcontract->getContractList('', 'ctime desc', array($psize, $offset));
                $count = $this->equalamountcontract->getContractCount();
                $this->load->model('admin_usercontract_model', 'usercontract');
                $result = $this->usercontract->getcanUseUsercontract();
                $usercontract = array();
                foreach ($result as $_uc){
                    $usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
                }
                $data['usercontract'] = $usercontract;
            }
            if($count > 0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'contract/index?page=' . $page;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), EQUALAMOUNT_NAME, '', EQUALAMOUNT_NAME, $this->getIP(), $this->getSession('uid'));
            $this->load->view('/equalamountcontract/v_index', $data);
        }
    }
    
    public function addequalamountcontract(){
        $flag = $this->op->checkUserAuthority(EQUALAMOUNT_NAME, $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加合同');
        } else {
            if($this->input->request('op') == 'addcontract'){
                $cname = trim($this->input->post('cname'));
                $this->load->model('admin_corporation_model', 'corporation');
                $ret_data = $this->corporation->getcoridByCname($cname);
                if(!$ret_data['corid']){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'错误的公司名称')));
                }
                $corid = $ret_data['corid'];
                $con_number = trim($this->input->post('con_number'));
                $con_money = trim($this->input->post('con_money'));
                $con_income = trim($this->input->post('con_income'));
                $interesttime = trim($this->input->post('interesttime'));
                $repaymenttime = trim($this->input->post('repaymenttime'));
                $repaymentday = trim($this->input->post('repaymentday'));
                $object_overview = trim($this->input->post('object_overview'));
                $object_desc = trim($this->input->post('object_desc'));
                $object_img = trim($this->input->post('object_img'));
                $capital_overview = trim($this->input->post('capital_overview'));
                $capital_desc = trim($this->input->post('capital_desc'));
                $capital_img = trim($this->input->post('capital_img'));
                $ucid = trim($this->input->post('ucid'));
                $con_bzjbl = trim($this->input->post('con_bzjbl'));
                $con_bzjbl = trim($this->input->post('con_bzjbl'));
                
                $data['corid'] = $corid;
                $data['corname'] = $cname;
                $data['con_number'] = $con_number;
                $data['con_money'] = $con_money;
                $data['real_money'] = $con_money;
                $data['con_income'] = $con_income;
                $data['interesttime'] = $interesttime;
                $data['repaymenttime'] = $repaymenttime;
                $data['repaymentday'] = $repaymentday;
                $data['object_overview'] = $object_overview;
                $data['object_desc'] = $object_desc;
                $data['object_img'] = $object_img;
                $data['capital_overview'] = $capital_overview;
                $data['capital_desc'] = $capital_desc;
                $data['capital_img'] = $capital_img;
                $data['con_bzjbl'] = $con_bzjbl; //保证金比例
                $data['ucid'] = $ucid;          //用户模板ID
                $data['ctime'] = time();        //创建时间
                $ret = $this->equalamountcontract->addcontract($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加合同失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), EQUALAMOUNT_NAME, '', '添加合同信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加成功', array(), '添加合同信息 ', 'forward', OP_DOMAIN.'/equalamountcontract'));
            }else{
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getcanUseUsercontract();
                $this->load->view('/equalamountcontract/v_addcontract', $data);
            }
        }
    }
    
    public function editequalamountcontract() {
        $flag = $this->op->checkUserAuthority(EQUALAMOUNT_NAME, $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            if($this->input->request('op') == 'editcontract'){
                $cid = $this->input->post('cid');
                if(!$cid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $con_number = trim($this->input->post('con_number'));
                $con_money = trim($this->input->post('con_money'));
                $con_income = trim($this->input->post('con_income'));
                $interesttime = trim($this->input->post('interesttime'));
                $repaymenttime = trim($this->input->post('repaymenttime'));
                $repaymentday = trim($this->input->post('repaymentday'));
                $object_overview = trim($this->input->post('object_overview'));
                $object_desc = trim($this->input->post('object_desc'));
                $object_img = trim($this->input->post('object_img'));
                $capital_overview = trim($this->input->post('capital_overview'));
                $capital_desc = trim($this->input->post('capital_desc'));
                $capital_img = trim($this->input->post('capital_img'));
                $ucid = trim($this->input->post('ucid'));
                $con_bzjbl = trim($this->input->post('con_bzjbl'));
                $data['con_number'] = $con_number;
                $data['real_money'] = $con_money;
                $data['con_money'] = $con_money;
                $data['con_income'] = $con_income;
                $data['interesttime'] = $interesttime;
                $data['repaymenttime'] = $repaymenttime;
                $data['repaymentday'] = $repaymentday;
                $data['object_overview'] = $object_overview;
                $data['object_desc'] = $object_desc;
                $data['object_img'] = $object_img;
                $data['capital_overview'] = $capital_overview;
                $data['capital_desc'] = $capital_desc;
                $data['capital_img'] = $capital_img;
                $data['con_bzjbl'] = $con_bzjbl;    //保证金比率
                $data['ucid'] = $ucid;              //用户模板ID
                $data['ctime'] = time();            //创建时间
                $ret = $this->equalamountcontract->updateContract($cid, $data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '修改合同信息', '', '修改合同信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改合同信息成功', array(), '修改合同信息 ', 'forward', OP_DOMAIN.'/equalamountcontract'));
            }else{
                $cid = $this->uri->segment(3);
                if($cid < 0 || !is_numeric($cid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $contraceInfo = $this->equalamountcontract->getcontractByCid($cid);
                $data['detail'] = $contraceInfo;
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getcanUseUsercontract();
                $data['diff_day'] = $this->diff_days(strtotime($contraceInfo['interesttime']), strtotime($contraceInfo['repaymenttime']));
//                 if($data['detail']['status'] == 1){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已开售合同不能修改')));
//                 }else if($data['detail']['status'] == 2){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'该合同已经被关闭，请启用之后再编辑')));
//                 }
                $this->load->view('/equalamountcontract/v_editontract', $data);
            }
        }
    }
    
    
    public function getContractByCorid($corid){
        
        $data = $this->equalamountcontract->getContractByCorid($corid);
        
        foreach ($data as $key => $val){
            $rtn[$key][0] = $val['cid'];
            $rtn[$key][1] = $val['con_number'];
        }
        echo json_encode($rtn);
        exit;
    }
    
    
    public function getContractByCid($cid){
        $data = $this->equalamountcontract->getContractByCid($cid);
        
        echo json_encode($data);
        exit;
    }
    
    public function delequalamountcontract($cid){
        $flag = $this->op->checkUserAuthority(EQUALAMOUNT_NAME, $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '删除合同');
        } else {
            $data = $this->equalamountcontract->getContractByCid($cid);
            if(empty($data)){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'不存在的合同ID')));
            }
            if($data['money'] != 0 ){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已开售合同不能删除')));
            }
            $this->equalamountcontract->delContract($cid);
            $log = $this->op->actionData($this->getSession('name'), '删除合同信息', '', '删除合同信息', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除合同信息成功', array(), '删除合同信息 ', 'forward', OP_DOMAIN.'/equalamountcontract'));
            
        }
    }

    public function bond() {
        $flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '保证金');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            if($this->input->request('op') == "search"){
                $corname = trim($this->input->post('searchcorname'));
                $con_number = trim($this->input->post('searchcon_number'));
                $interesttime = trim($this->input->post('searchinteresttimet'));
                $repaymenttime = trim($this->input->post('searchrepaymenttime'));
                $contract = $this->equalamountcontract->getContractlistsql($corname,$con_number,$interesttime,$repaymenttime);
                $data['list'] = $contract;
                $count = count($contract);
                $this->load->model('admin_usercontract_model', 'usercontract');
                $result = $this->usercontract->getcanUseUsercontract();
                $usercontract = array();
                foreach ($result as $_uc){
                    $usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
                }
                $data['usercontract'] = $usercontract;
                $data['searchcorname'] = $corname;
                $data['searchcon_number'] = $con_number;
                $data['searchinteresttimet'] = $interesttime;
                $data['searchrepaymenttime'] = $repaymenttime;
            }else{
                $data['list'] = $this->equalamountcontract->getContractList('', 'ctime desc', array($psize, $offset));
                $count = $this->equalamountcontract->getContractCount();
                $this->load->model('admin_usercontract_model', 'usercontract');
                $result = $this->usercontract->getcanUseUsercontract();
                $usercontract = array();
                foreach ($result as $_uc){
                    $usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
                }
                $data['usercontract'] = $usercontract;
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'contract/index?page=' . $page;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '保证金', '', '保证金', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/equalamountcontract/v_bood', $data);
        }
    }
    public function getBonddetail($cid){
        $flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '保证金');
        } else {
           $product_remit = array();
           $product = $this->product->getProductList(array('cid'=>$cid),'','');
           foreach ($product as $key=>$val){
               $product_remit[] = $this->product_remit->getProductRemitByPid($val['pid']);
           }
           $data['list'] = $product_remit; 
        }
        $this->load->view('/equalamountcontract/v_booddetail', $data);
    }
    
    private function diff_days($start, $now){
        $a_dt=getdate($start);
        $b_dt=getdate($now);
        $a_new=mktime(12,0,0,$a_dt['mon'],$a_dt['mday'],$a_dt['year']);
        $b_new=mktime(12,0,0,$b_dt['mon'],$b_dt['mday'],$b_dt['year']);
        return abs(($a_new-$b_new)/86400-1);
    }
}