<?php
/**
 * messsend活动管理
 * * */
class messsend extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '消息管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_messsend_model', 'messsend');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('消息管理', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '消息管理');
            exit;
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->messsend->getMessSendList('', 'ctime desc', array($psize, $offset));
            $count = $this->messsend->getMessSendCount('');

         	$data = array();
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'9034');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '消息管理', '', '消息管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/messsend/v_index', $data);
        }
    }
    
    public function addMessSend(){
        $flag = $this->op->checkUserAuthority('抵用券列表', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '消息管理');
        } else {
            if($this->input->request('op') == 'addmesssend'){
                $title = trim($this->input->post('title'));
                $type = trim($this->input->post('type'));
                $accounts = trim($this->input->post('accounts'));
                $content = trim($this->input->post('content'));
                $link = trim($this->input->post('link'));
                $data = array();
                $data['title'] = $title;
                $data['type'] = $type;
                $data['link'] = $link;
                $data['content'] = $content;
                $data['accounts'] = $accounts;
                $data['status'] = 0;
                $data['ctime'] = NOW;
                $ret = $this->messsend->addMessSend($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加消息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '消息管理', '', '消息管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加消息成功', array(), '消息管理 ', 'forward', OP_DOMAIN.'/messsend'));
            }else{
                $this->load->view('/messsend/v_addMessSend');
            }
        }
    }
    public function editMessSend(){
        $flag=$this->op->checkUserAuthority('消息管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '消息管理');
        }else{
            if($this->input->request('op') == 'editmesssend'){
               $id = $this->input->post('id');    
               if(!$id){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               } 
               $id = trim($this->input->post('id'));
               $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $accounts = trim($this->input->post('accounts'));
                $cids = trim($this->input->post('cids'));
                $data = array();
                $data['name'] = $name;
                $data['type'] = $type;
                $data['cids'] = $cids;
                $data['accounts'] = $accounts;
               $ret = $this->messsend->updateMessSendById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '消息管理', '', '修改消息管理列表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改消息管理列表 ', 'forward', OP_DOMAIN.'/messsend'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $sendList = $this->messsend->getMessSendById($id);
                $data['detail'] =$sendList[0];
                $this->load->view('/messsend/v_editMessSend', $data);
            }  
        }
    }
    
    public function detail(){
    			$id = $this->uri->segment(3);
    			if($id < 0 || !is_numeric($id)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$sendList = $this->messsend->getMessSendById($id);
    			$this->load->model('admin_mess_model', 'mess');
    			$messList = $this->mess->getAvailableMess();
    			$data['detail'] =$sendList[0];
    			$data['messList'] =$messList;
    			$this->load->view('/messsend/v_detail', $data);
    }
    
    public function delMessSend(){
        $flag=$this->op->checkUserAuthority('消息管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '消息管理');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->messsend->delMessSendById($id);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '消息管理', '', '删除消息管理', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除消息管理', 'forward', OP_DOMAIN.'/messsend'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('消息管理',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '消息管理');
    	}else{
    		$id = $this->uri->segment(3);
    		$messsendList = $this->messsend->getMessSendById($id);
    		$messsend = array();
    		if(empty($messsendList)){
    			return;
    		}else{
    			$messsend =  $messsendList[0];
    		};
    		$sendtimes = $this->messsend->incr($id);
    		if($sendtimes!=1){
    			return;
    		}
    		$this->load->model('admin_account_model', 'account');
    		$this->load->model('admin_user_notice_model', 'admin_user_notice_model');
    		if($messsend['type']==1){
	    		$psize = 100;
	    		$count = $this->account->getAccountCount();
	    		$max_page = ceil($count/$psize);
	    		for($page = 1; $page <= $max_page; $page++){
	    			$offset = ($page - 1) * $psize;
	    			$accountList = $this->account->getAccountUidList($offset, $psize);
	    			if(!empty($accountList)){
	    				foreach ($accountList as $account){
	    					$notice_data = array(
        							'uid' => $account['uid'],
        							'title' => $messsend['title'],
        							'content' => $messsend['content'],
	    							'link' => $messsend['link'],
        							'ctime' => NOW
        					);
        					$this->admin_user_notice_model->addNotice($account['uid'],$notice_data);
	    				}
	    			}
	    		}
    		}else{
    			$accountList = explode(",", $messsend['accounts']);
    			foreach ($accountList as $account){
    				$uid = $this->account->getUidByAccount($account);
    				if(!empty($uid)){
    					$notice_data = array(
    							'uid' => $uid[0]['uid'],
    							'title' => $messsend['title'],
        						'content' => $messsend['content'],
	    						'link' => $messsend['link'],
        						'ctime' => NOW
    					);
    					$this->admin_user_notice_model->addNotice($uid[0]['uid'],$notice_data);
    				}
    			}
    		}
    		$updatedata['status'] = 1;
    		$updatedata['stime'] = NOW;
    		$ret = $this->messsend->updateMessSendById($id,$updatedata);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发放失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '消息管理', '', '消息管理', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发放成功', array(), '消息管理', 'forward', OP_DOMAIN.'/messsend'));
    }
}