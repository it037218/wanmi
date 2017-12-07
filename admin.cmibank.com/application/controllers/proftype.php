<?php

/**
 * 业务类型管理
* * */
class proftype extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '业务类型管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_proftype_model', 'proftype');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('业务类型管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '业务类型管理');
        } else {
            $data = array();
            $data['list'] = $this->proftype->getproftypeList();
            $edatable = $this->op->getEditable($this->getSession('uid'),'1034');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '业务类型管理', '', '业务类型管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/proftype/v_index', $data);
        }
    }
    
    public function addproftype(){
        $flag = $this->op->checkUserAuthority('业务类型管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            if($this->input->request('op') == 'addproftype'){
                $proftype = trim($this->input->post('proftype'));
                $profname = trim($this->input->post('profname'));
                
                $data['proftype'] = $proftype;
                $data['profname'] = $profname;
                $ret = $this->proftype->addproftype($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加类型失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '业务类型管理', '', '添加产品信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '修改成功', array(), '修改产品信息 ', 'forward', OP_DOMAIN.'/proftype'));
            }else{
                $this->load->view('/proftype/v_addproftype');
            }
        }
    }


    public function editproftype() {
        $flag = $this->op->checkUserAuthority('业务类型管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑业务类型');
        } else {
            if($this->input->request('op') == 'saveproftype'){
                $profid = trim($this->input->post('profid'));
                if(!$profid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $proftype = trim($this->input->post('proftype'));
                $profname = trim($this->input->post('profname'));
                $data['proftype'] = $proftype;
                $data['profname'] = $profname;
                $ret = $this->proftype->updateproftype($profid, $data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '业务类型管理', '', '修改业务类型', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改业务类型', 'forward', OP_DOMAIN.'/proftype'));
            }else{
                $profid = $this->uri->segment(3);
                if($profid < 0 || !is_numeric($profid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->proftype->getproftypeByProfid($profid);
                $this->load->view('/proftype/v_editproftype', $data);
            }
        }
    }

    public function delproftype($ptid){
        $flag = $this->op->checkUserAuthority('业务类型管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '编辑产品');
        } else {
            if(!$ptid){
                echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '缺少必要的参数', array(), '编辑类型管理');
            }
			/****
            $ret = $this->proftype->check_proftype_can_del($ptid);
            if($ret > 0){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'该类型中还有产品，请先删除类型中的产品')));
            }
			**/
            $ret = $this->proftype->delproftype($ptid);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除类型失败')));
            }
            $log = $this->op->actionData($this->getSession('name'), '业务类型管理', '', '删除业务类型', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '删除业务类型 ', 'forward', OP_DOMAIN.'/proftype'));
        }
    }
    
    public function getprofnamebyproftype($proftype){
        $where = array('proftype' => $proftype);
        $data = $this->proftype->getprofnamebyproftype($where);
        $rtn = array();
        foreach($data as $key => $val){
            $rtn[$key][0] = $val['profid'];
            $rtn[$key][1] = $val['profname'] . '-' . $val['profname'];
        }
        echo json_encode($rtn);
        exit;
    }

}