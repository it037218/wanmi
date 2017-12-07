<?php

/**
 * 权限管理
 * * */
class kltype extends Controller {

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
        $this->load->model('admin_kltype_model', 'kltype');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('快乐宝产品列队', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '快乐宝产品列队');
        } else {
            $data = array();
            $data['list'] = $this->kltype->getKltypeList();
            $log = $this->op->actionData($this->getSession('name'), '产品队列管理', '', '快乐宝产品列队', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/kltype/v_index', $data);
        }
    }
    
    public function addKltype(){
        $flag = $this->op->checkUserAuthority('快乐宝产品列队', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '添加快乐宝产品列队');
        } else {
            if($this->input->request('op') == 'addkltype'){
                $pname = trim($this->input->post('name'));
                $rank = trim($this->input->post('rank'));
                $sub_income = trim($this->input->post('sub_income')) ? trim($this->input->post('sub_income')) : 0;
                $data['name'] = $pname;
                $data['rank'] = $rank;
                $data['ctime'] = time();                    //创建时间
                $ret = $this->kltype->addKltype($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加快乐宝产品列队失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '快乐宝产品列队', '', '添加快乐宝产品列队', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加成功', array(), '添加快乐宝产品列队 ', 'forward', OP_DOMAIN.'/kltype'));
            }else{
                $this->load->view('/kltype/v_addKltype');
            }
        }
    }
    
    
    public function editKltype() {
        $flag = $this->op->checkUserAuthority('快乐宝产品列队', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品类型');
        } else {
            if($this->input->request('op') == 'saveedit'){
               $ltid = $this->input->post('ptid');
               if(!$ltid){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               }
               $name = trim($this->input->post('name'));
               $rank = trim($this->input->post('rank'));
               $data['name'] = $name;
               $data['rank'] = $rank;
               $ret = $this->kltype->updateKltype($ltid, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '快乐宝产品列队', '', '修改产品类型', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品类型', 'forward', OP_DOMAIN.'/kltype'));
            }else{
                $cid = $this->uri->segment(3);
                if($cid < 0 || !is_numeric($cid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->kltype->getKltypeByctid($cid);
                $this->load->view('/kltype/v_editKltype', $data);
            }
        }
    }

    public function delKltype($ltid){
        $flag = $this->op->checkUserAuthority('快乐宝产品列队', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '删除快乐宝产品列队');
        } else {
            if(!$ltid){
                echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '缺少必要的参数', array(), '删除快乐宝产品列队');
            }
            $ret = $this->kltype->check_kltype_can_del($ltid);
            if($ret > 0){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'该类型中还有产品，请先删除类型中的产品')));
            }
            $ret = $this->kltype->delKltype($ltid);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除类型失败')));
            }
            $log = $this->op->actionData($this->getSession('name'), '快乐宝产品列队', '', '删除产品类型', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除产品类型 ', 'forward', OP_DOMAIN.'/kltype'));
        }
    }
    
}