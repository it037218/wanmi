<?php
/**
 * redbag活动管理
 * * */
class redbag extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '微信红包管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_redbag_model', 'redbag');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('微信红包管理', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '微信红包管理');
            exit;
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $param = array();
         	$data = array();
            if($this->input->request('op') == "search"){
            	$code = trim($this->input->post('code'));
            	if(!empty($code)){
            		$param['code'] = $code;
            		$data['code'] = $code;
            	}
            	$name = trim($this->input->post('name'));
            	if(!empty($name)){
            		$param['name'] = $name;
            		$data['name'] = $name;
            	}
            }
            $list = $this->redbag->getRedbagList($param, array($offset,$psize));
            $count = count($this->redbag->getRedbagList($param));
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'9065');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '微信红包管理', '', '微信红包管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/redbag/v_index', $data);
        }
    }
    
    public function getListByAccount() {
    	$flag = $this->op->checkUserAuthority('微信红包管理', $this->getSession('uid'));        //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '微信红包管理');
    		exit;
    	} else {
    		$account = trim($this->input->post('account'));
    		if(empty($account)){
    			$account = $this->uri->segment(3);
    		}
    		$list = $this->redbag->getRedbagListByAccount($account);
    		$this->load->model('admin_account_model', 'account');
    		$this->load->model('admin_useridentity_model', 'useridentity');
    		$names = array();
    		if(!empty($list)){
    			foreach ($list as $val){
    				$uid = $this->account->getUidByAccount($val['phone']);
    				if(!empty($uid)){
    					$user = $this->useridentity->getUseridentityByUid($uid[0]['uid']);
    					if(!empty($user)){
    						$names[$val['phone']] = $user['realname'];
    					}
    				}
    			}
    		}
    		$data = array();
    		$data['list'] = $list;
    		$data['names'] = $names;
    		$data['account'] = $account;
    		$log = $this->op->actionData($this->getSession('name'), '微信红包管理', '', '微信红包管理', $this->getIP(), $this->getSession('uid'));
    		$this->load->view('/redbag/v_baglist', $data);
    	}
    }
    
    public function addRedbag(){
        $flag = $this->op->checkUserAuthority('微信红包管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '微信红包管理');
        } else {
            if($this->input->request('op') == 'addredbag'){
                $name = trim($this->input->post('name'));
                $user_type = trim($this->input->post('user_type'));
                $redbag_type = trim($this->input->post('redbag_type'));
                $money = trim($this->input->post('money'));
                $counts = trim($this->input->post('counts'));
                $code = date('YmdHis');
                $data = array();
                $data['name'] = $name;
                $data['code'] = $code;
                $data['user_type'] = $user_type;
                $data['redbag_type'] = $redbag_type;
                $data['money'] = $money;
                $data['counts'] = $counts;
                $data['ctime'] = NOW;
                $ret = $this->redbag->addRedbag($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加微信红包失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '微信红包管理', '', '微信红包管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加微信红包成功', array(), '微信红包管理 ', 'forward', OP_DOMAIN.'/redbag'));
            }else{
                $this->load->view('/redbag/v_add');
            }
        }
    }
    
    public function delredbag(){
        $flag=$this->op->checkUserAuthority('微信红包管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '微信红包管理');
        }else{
            $id = $this->uri->segment(3);
            $data['status'] = 0;
            $data['deleted'] = 1;
            $data['dtime'] = NOW;
            $ret = $this->redbag->updateredbagById($id, $data);
            if(!$ret){
            	exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '微信红包管理', '', '删除抵用券', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除抵用券', 'forward', OP_DOMAIN.'/redbag'));
    }
    
    public function getList(){
    	$flag=$this->op->checkUserAuthority('微信红包管理',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '微信红包管理');
    	}else{
    		$id = $this->uri->segment(3);
    		$list = $this->redbag->getRedbagLogById($id);
    		$this->load->model('admin_account_model', 'account');
    		$this->load->model('admin_useridentity_model', 'useridentity');
    		$names = array();
    		if(!empty($list)){
    			foreach ($list as $val){
    				$uid = $this->account->getUidByAccount($val['phone']);
    				if(!empty($uid)){
	    				$user = $this->useridentity->getUseridentityByUid($uid[0]['uid']);
	    				if(!empty($user)){
	    					$names[$val['phone']] = $user['realname'];
	    				}
    				}
    			}
    		}
    		$data['list'] = $list;
    		$data['names'] = $names;
    	}
    	$this->load->view('/redbag/v_list', $data);
    }
}