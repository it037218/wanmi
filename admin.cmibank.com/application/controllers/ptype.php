<?php

/**
 * 权限管理
 * * */
class Ptype extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '产品队列管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_ptype_model', 'ptype');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('产品队列管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '产品队列管理');
        } else {
            $data = array();
            $data['list'] = $this->ptype->getPtypeList();
            $edatable = $this->op->getEditable($this->getSession('uid'),'1022');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '产品队列管理', '', '产品队列管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/ptype/v_index', $data);
        }
    }
    
    public function addptype(){
        $flag = $this->op->checkUserAuthority('产品队列管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            if($this->input->request('op') == 'addptype'){
                $pname = trim($this->input->post('name'));
                $rank = trim($this->input->post('rank'));
                $sub_income = trim($this->input->post('sub_income')) ? trim($this->input->post('sub_income')) : 0;
                $data['name'] = $pname;
                $data['rank'] = $rank;
                $data['ctime'] = time();                    //创建时间
                $ret = $this->ptype->addPtype($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加类型失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '产品队列管理', '', '添加产品信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '修改成功', array(), '修改产品信息 ', 'forward', OP_DOMAIN.'/ptype'));
            }else{
                $this->load->view('/ptype/v_addPtype');
            }
        }
    }
    
    
    public function editptype() {
        $flag = $this->op->checkUserAuthority('产品队列管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品类型');
        } else {
            if($this->input->request('op') == 'saveedit'){
               $ptid = $this->input->post('ptid');
               if(!$ptid){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               }
               $name = trim($this->input->post('name'));
               $rank = trim($this->input->post('rank'));
               $data['name'] = $name;
               $data['rank'] = $rank;
               $ret = $this->ptype->updatePtype($ptid, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '产品队列管理', '', '修改产品类型', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品类型', 'forward', OP_DOMAIN.'/ptype'));
            }else{
                $pid = $this->uri->segment(3);
                if($pid < 0 || !is_numeric($pid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->ptype->getPtypeByPtid($pid);
                $this->load->view('/ptype/v_editPtype', $data);
            }
        }
    }

    public function delPtype($ptid){
        $flag = $this->op->checkUserAuthority('产品队列管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '编辑产品');
        } else {
            if(!$ptid){
                echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '缺少必要的参数', array(), '编辑类型管理');
            }
            $ret = $this->ptype->check_ptype_can_del($ptid);
            if($ret > 0){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'该类型中还有产品，请先删除类型中的产品')));
            }
            $ret = $this->ptype->delPtype($ptid);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除类型失败')));
            }
            $log = $this->op->actionData($this->getSession('name'), '产品队列管理', '', '删除产品类型', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '删除产品类型 ', 'forward', OP_DOMAIN.'/ptype'));
        }
    }
    
}