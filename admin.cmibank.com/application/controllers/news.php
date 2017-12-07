<?php
/**
 * news活动管理
 * * */
class news extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '公司动态') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_news_model', 'news');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('公司动态', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '公司动态');
            exit;
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->news->getNewsList('', 'ctime desc', array($psize, $offset));
            $count = $this->news->getNewsCount('');

         	$data = array();
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'9035');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '公司动态', '', '公司动态', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/news/v_index', $data);
        }
    }
    
    public function addNews(){
        $flag = $this->op->checkUserAuthority('抵用券列表', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '公司动态');
        } else {
            if($this->input->request('op') == 'addnews'){
                $title = trim($this->input->post('title'));
                $link = trim($this->input->post('link'));
                $img = trim($this->input->post('img'));
                $data = array();
                $data['title'] = $title;
                $data['img'] = $img;
                $data['link'] = $link;
                $data['status'] = 0;
                $data['ctime'] = NOW;
                $ret = $this->news->addNews($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加消息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '公司动态', '', '公司动态', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加消息成功', array(), '公司动态 ', 'forward', OP_DOMAIN.'/news'));
            }else{
                $this->load->view('/news/v_add');
            }
        }
    }
    public function editNews(){
        $flag=$this->op->checkUserAuthority('公司动态',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '公司动态');
        }else{
            if($this->input->request('op') == 'editnews'){
               $id = $this->input->post('id');    
               if(!$id){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               } 
               	$id = trim($this->input->post('id'));
                $title = trim($this->input->post('title'));
                $link = trim($this->input->post('link'));
                $img = trim($this->input->post('img'));
                $data = array();
                $data['title'] = $title;
                $data['img'] = $img;
                $data['link'] = $link;
               $ret = $this->news->updateNewsById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '公司动态', '', '修改公司动态列表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改公司动态列表 ', 'forward', OP_DOMAIN.'/news'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $sendList = $this->news->getNewsById($id);
                $data['detail'] =$sendList[0];
                $this->load->view('/news/v_edit', $data);
            }  
        }
    }
    
    public function detail(){
    			$id = $this->uri->segment(3);
    			if($id < 0 || !is_numeric($id)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$sendList = $this->news->getNewsById($id);
    			$this->load->model('admin_mess_model', 'mess');
    			$messList = $this->mess->getAvailableMess();
    			$data['detail'] =$sendList[0];
    			$data['messList'] =$messList;
    			$this->load->view('/news/v_detail', $data);
    }
    
    public function delNews(){
        $flag=$this->op->checkUserAuthority('公司动态',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '公司动态');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->news->delNewsById($id);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '公司动态', '', '删除公司动态', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除公司动态', 'forward', OP_DOMAIN.'/news'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('公司动态',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '公司动态');
    	}else{
            $id = $this->uri->segment(3);
            $ret = $this->news->updateNewsById($id,array('status'=>1,'stime'=>NOW));
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发布失败')));
            }
        }
    	$log = $this->op->actionData($this->getSession('name'), '公司动态', '', '公司动态', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发放成功', array(), '公司动态', 'forward', OP_DOMAIN.'/news'));
    }
}