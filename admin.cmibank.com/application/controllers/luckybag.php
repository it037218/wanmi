<?php
class luckybag extends Controller{ 
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '邀请红包'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_luckybag_model', 'admin_luckybag_model');
        $this->load->model('user_log_base', 'user_log');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_luckybag_model', 'admin_luckybag_model');
    }
    public function index(){
    	$flag = $this->op->checkUserAuthority('邀请红包统计',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'邀请红包统计');
    	}else{
    		$count = 0;
    		$page = max(1, intval($this->input->request('pageNum')));
    		$psize = max(20, intval($this->input->request('numPerPage')));
    		$data = array();
    		$offset = ($page - 1) * $psize;
    		$withdrawloglist = array();
	    	$searchparam = array();
    		if($this->input->request('op') == "search"){
    			$phone = trim($this->input->post('phone'));
    			$uid = trim($this->input->post('uid'));
    			$stime = trim($this->input->post('stime'));
    			$etime = trim($this->input->post('etime'));	
    			$status = trim($this->input->post('status'));
    			$type = trim($this->input->post('type'));
    			if(!empty($phone)){
    				$this->load->model('admin_account_model', 'account');
    				$uid = $this->account->getUidByAccount($phone);
    				if(!empty($uid)){
    					$uid = $uid[0]['uid'];
    				}
    				   $data['phone'] = $phone;
    			}
    			if(!empty($status)){
    				$searchparam['status'] = $status;
	    			$data['status'] = $status;
    			}else{
    				$data['status'] = 0;
    			}
    			$data['type'] = $type;
    			if(!empty($type)){
    				$searchparam['type'] = $type;
    			}
    		   if(!empty($uid)){
    		       $user = $this->useridentity->getUseridentityByUid($uid);
    		       $data['realname'] = $user['realname'];
    			   $searchparam['uid'] = $uid;
		    	   $data['uid'] = $uid;
    			}
    			if(!empty($stime)){
    				$searchparam['stime'] = strtotime($stime);
    				$data['stime'] = $stime;
    			}else {
    				$searchparam['stime'] = strtotime(date('Y-m-d',strtotime('-1 day')));
    				$data['stime'] = date('Y-m-d',strtotime('-1 day'));
    			}
    			if(!empty($etime)){
    				$searchparam['etime'] = strtotime($etime)+86400;
    				$data['etime'] = $etime;
    				
    			}else{
    				$searchparam['etime'] = NOW;
    				$data['etime'] = date('Y-m-d',NOW);
    			} 
    		}else{
    			$searchparam['stime'] = strtotime(date('Y-m-d',strtotime('-1 day')));
    			$data['stime'] = date('Y-m-d',strtotime('-1 day'));
    			$searchparam['etime'] = NOW;
    			$data['etime'] = date('Y-m-d',NOW);
    			$data['status'] = 2;
    			$searchparam['status'] = 2;
    			$data['type'] = 0;
    		}
    			$luckybagList= $this->admin_luckybag_model->getLuckyBagByCondition($searchparam,$offset,$psize);
    			if(!empty($uid)){
	    			$accetpedList = $this->admin_luckybag_model->getInvitedUserLuckybagList($uid);
	    			if($accetpedList){
	    				$luckybagList[] = $accetpedList[0];
	    			}
    			}
    			if(!empty($luckybagList)){
    				$names = array();
    				$phones = array();
    				foreach ($luckybagList as $luckybag){
	    				if(!array_key_exists($luckybag['uid'],$names)){
		            		$user = $this->useridentity->getUseridentityByUid($luckybag['uid']);
		            		if(!empty($user)){
			            		$names[$luckybag['uid']] = $user['realname'];
			            		$phones[$luckybag['uid']] = $user['phone'];
		            		}
		            	}
		            	if(!array_key_exists($luckybag['uuid'],$names)){
		            		$user = $this->useridentity->getUseridentityByUid($luckybag['uuid']);
		            		if(!empty($user)){
		            			$names[$luckybag['uuid']] = $user['realname'];
		            		}
		            	}
    				}
    				$count = $this->admin_luckybag_model->countLuckyBagByCondition($searchparam);
    				$data['names'] = $names;
    				$data['phones'] = $phones;
    				$sum_money = $this->admin_luckybag_model->sumLuckyBagByCondition($searchparam);
    				if($accetpedList){
    					$count++;
    					$sum_money +=$accetpedList[0]['money'];
    				}
    				$data['sum_money'] = $sum_money;
    			}
    		if(count($luckybagList)>0){
    			$data['pageNum']    = $page;
    			$data['numPerPage'] = $psize;
    			$data['count'] = $count;
    			$data['list'] = $luckybagList;
    		}else{
    			$data['list'] = $data['page'] = '';
    			$data['pageNum']    = 0;
    			$data['numPerPage'] = 0;
    			$data['count'] =  0;
    		}
    		$this->load->view('/luckybag/v_index',$data);
    	}   	 
    }
    
    public function listLuckybag(){
    	$flag = $this->op->checkUserAuthority('邀请红包', $this->getSession('uid'));        //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '邀请红包');
    		exit;
    	} else {
    		$data = array();
    		$page = max(1, intval($this->input->request('pageNum')));
    		$psize = max(20, intval($this->input->request('numPerPage')));
    		$offset = ($page - 1) * $psize;
    	
    		$luckybagList = $this->admin_luckybag_model->getLuckybagList(array('deleted'=>0),'ctime desc',array($psize, $offset));
    		$count = $this->admin_luckybag_model->getLuckybagCount(array('deleted'=>0));
    		if($count>0){
    			$data['pageNum']    = $page;
    			$data['numPerPage'] = $psize;
    			$data['count'] = $count;
    			$data['list'] = $luckybagList;
    		}else{
    			$data['pageNum'] = 1;
    			$data['numPerPage'] = 0;
    			$data['count'] = 0;
    			$data['list'] = $data['page'] = '';
    		}
    		$edatable = $this->op->getEditable($this->getSession('uid'),'10300');
    		if(!empty($edatable)){
    			$data['editable'] = $edatable[0]['editable'];
    		}else{
    			$data['editable']=0;
    		}
    		$log = $this->op->actionData($this->getSession('name'), '邀请红包', '', '邀请红包', $this->getIP(), $this->getSession('uid'));
    		$this->load->view('/luckybag/v_list', $data);
    	}
    }
    
    public function add(){
    	$flag = $this->op->checkUserAuthority('邀请红包', $this->getSession('uid'));   //检测用户操作权限
    	$data = array();
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '邀请红包');
    	} else {
    		if($this->input->request('op') == 'add'){
    			$name = trim($this->input->post('name'));
    			$money = trim($this->input->post('money'));
    			$bili = trim($this->input->post('bili'));
    			$goumaimoney = trim($this->input->post('goumaimoney'));
    			$goumaibeishu = trim($this->input->post('goumaibeishu'));
    			$days = trim($this->input->post('days'));
    			$ptids=trim($this->input->post('ptids'));
    			$pnames=trim($this->input->post('pnames'));
    			$data = array();
    			$data['name'] = $name;
    			if(!empty($bili)){
    				$data['bili'] = $bili;
    				$data['type'] = 2;
    			}
    			if(!empty($money)){
    				$data['money'] = $money;
    				$data['type'] = 1;
    			}
    			if(!empty($goumaimoney)){
    				$data['goumaimoney'] = $goumaimoney;
    				$data['usetype'] = 1;
    			}
    			if(!empty($goumaibeishu)){
    				$data['goumaibeishu'] = $goumaibeishu;
    				$data['usetype'] = 2;
    			}
    			$data['days'] = $days;
    			$data['ctime'] = NOW;
    			$data['ptids'] = $ptids;
    			$data['pnames'] = $pnames;
    			$ret = $this->admin_luckybag_model->addLuckybag($data);
    			if(!$ret){
    				exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加红包失败')));
    			}
    			$log = $this->op->actionData($this->getSession('name'), '邀请红包', '', '邀请红包', $this->getIP(), $this->getSession('uid'));
    			exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加红包成功', array(), '邀请红包 ', 'forward', OP_DOMAIN.'/luckybag/listLuckybag'));
    		}else{
    			$this->load->view('/luckybag/v_add');
    		}
    	}
    }
    public function edit(){
    	$flag=$this->op->checkUserAuthority('邀请红包',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '邀请红包');
    	}else{
    
    		if($this->input->request('op') == 'edit'){
    			$id = trim($this->input->post('id'));
    			$name = trim($this->input->post('name'));
    			$money = trim($this->input->post('money'));
    			$bili = trim($this->input->post('bili'));
    			$goumaimoney = trim($this->input->post('goumaimoney'));
    			$goumaibeishu = trim($this->input->post('goumaibeishu'));
    			$days = trim($this->input->post('days'));
    			$ptids=trim($this->input->post('ptids'));
    			$pnames=trim($this->input->post('pnames'));
    			$data = array();
    			$data['name'] = $name;
    			if(!empty($bili)){
    				$data['bili'] = $bili;
    				$data['type'] = 2;
    			}
    			if(!empty($money)){
    				$data['money'] = $money;
    				$data['type'] = 1;
    			}
    			if(!empty($goumaimoney)){
    				$data['goumaimoney'] = $goumaimoney;
    				$data['usetype'] = 1;
    			}
    			if(!empty($goumaibeishu)){
    				$data['goumaibeishu'] = $goumaibeishu;
    				$data['usetype'] = 2;
    			}
    			$data['days'] = $days;
    			$data['ctime'] = NOW;
    			$data['ptids'] = $ptids;
    			$data['pnames'] = $pnames;
    			$ret = $this->admin_luckybag_model->updateLuckybagById($id, $data);
    			if(!$ret){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
    			}
    			$log = $this->op->actionData($this->getSession('name'), '邀请红包', '', '修改红包列哦表', $this->getIP(), $this->getSession('uid'));
    			exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改红包列哦表 ', 'forward', OP_DOMAIN.'/luckybag/listLuckybag'));
    		}else{
    			$id = $this->uri->segment(3);
    			if($id < 0 || !is_numeric($id)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$rec= $this->admin_luckybag_model->getLuckybagById($id);
    			$data['detail'] = $rec[0];
    			$this->load->view('/luckybag/v_edit', $data);
    		}
    	}
    }
    public function del(){
    	$flag=$this->op->checkUserAuthority('邀请红包',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '邀请红包');
    	}else{
    		$id = $this->uri->segment(3);
    		$data['deleted'] = 1;
    		$ret = $this->admin_luckybag_model->updateLuckybagById($id, $data);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '邀请红包', '', '删除红包', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除红包', 'forward', OP_DOMAIN.'/luckybag/listLuckybag'));
    }
    
   	public function getLuckybagforUser(){
   			$count = 0;
   			$page = max(1, intval($this->input->request('pageNum')));
   			$psize = max(20, intval($this->input->request('numPerPage')));
   			$data = array();
   			$offset = ($page - 1) * $psize;
   			$luckybagList = array();
   			$uid = $this->uri->segment(3);
   			if(!empty($uid)){
   				$this->load->model('admin_account_model', 'account');
   				$account = $this->account->getAccountByUid($uid);
   				$data['phone'] = $account['account'];
   			}
   			$searchparam = array();
   			$data['status'] = 2;
   			if(!empty($uid)){
   				$user = $this->useridentity->getUseridentityByUid($uid);
   				$data['realname'] = $user['realname'];
   				$searchparam['uid'] = $uid;
   				$data['uid'] = $uid;
   			}
   			
   			$searchparam['etime'] = NOW;
   			$searchparam['stime'] = 1462781437;
   			$searchparam['status'] = 2;
   			$luckybagList= $this->admin_luckybag_model->getLuckyBagByCondition($searchparam,$offset,$psize);
   			$accetpedList = $this->admin_luckybag_model->getInvitedUserLuckybagList($uid);
   			if($accetpedList){
   				$luckybagList[] =  $accetpedList[0];
   			}
   			if(!empty($luckybagList)){
   				$names = array();
   				$phones = array();
   				foreach ($luckybagList as $luckybag){
   					if(!array_key_exists($luckybag['uid'],$names)){
   						$user = $this->useridentity->getUseridentityByUid($luckybag['uid']);
   						if(!empty($user)){
   							$names[$luckybag['uid']] = $user['realname'];
   							$phones[$luckybag['uid']] = $user['phone'];
   						}
   					}
   					if(!array_key_exists($luckybag['uuid'],$names)){
   						$user = $this->useridentity->getUseridentityByUid($luckybag['uuid']);
   						if(!empty($user)){
   							$names[$luckybag['uuid']] = $user['realname'];
   						}
   					}
   				}
   				$count = $this->admin_luckybag_model->countLuckyBagByCondition($searchparam);
   				$data['names'] = $names;
   				$data['phones'] = $phones;
   				$sum_money = $this->admin_luckybag_model->sumLuckyBagByCondition($searchparam);
   				if($accetpedList){
    				$count++;
    				$sum_money +=$accetpedList[0]['money'];
	    		}
	    		$data['sum_money'] = $sum_money;
   			}
   			if($count>0){
   				$data['pageNum']    = $page;
   				$data['numPerPage'] = $psize;
   				$data['count'] = $count;
   				$data['list'] = $luckybagList;
   			}else{
   				$data['list'] = $data['page'] = '';
   				$data['pageNum']    = 0;
   				$data['numPerPage'] = 0;
   				$data['count'] =  0;
   			}
   			$this->load->view('/luckybag/v_index',$data);
   		}
}