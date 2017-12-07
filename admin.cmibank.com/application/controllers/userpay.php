<?php
/**
 * 用户充值管理
 * * */
class userpay extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '用户充值') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_userpay_model', 'userpay');
        $this->load->model('admin_account_model', 'account');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('用户充值', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户充值');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $userpayList = array();
            $count = 0;
            if($this->input->request('op') == "search"){
            	$account = trim($this->input->post('account'));
            	if(!empty($account)){
		            $userpayList = $this->userpay->getUserpayList(array('account'=>$account),'ctime desc',array($psize, $offset));
		            $count = $this->userpay->getUserpayCount(array('account'=>$account));
		            $data['account']=$account;
            	}else{
            		$userpayList = $this->userpay->getUserpayList('','ctime desc',array($psize, $offset));
            		$count = $this->userpay->getUserpayCount('');
            	}
            }else{
            	$userpayList = $this->userpay->getUserpayList('','ctime desc',array($psize, $offset));
            	$count = $this->userpay->getUserpayCount('');
            }
            $names = array();
            $this->load->model('admin_account_model', 'account');
            if(!empty($userpayList)){
            	$this->load->model('admin_useridentity_model', 'useridentity');
	            foreach ($userpayList as $userpay){
	            	$user = $this->useridentity->getUseridentityByUid($userpay['uid']);
	            	if(!empty($user)){
	            		$names[$userpay['uid']] = $user['realname'];
	            	}
	            }
            }
            $count = $this->userpay->getUserpayCount('');
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['names'] = $names;
                $data['list'] = $userpayList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1470');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '用户充值列表', '', '用户充值列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/userpay/v_index', $data);
        }
    }
    
    public function addUserpay(){
        $flag = $this->op->checkUserAuthority('用户充值', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户充值');
        } else {
            if($this->input->request('op') == 'addUserpay'){
                $account = trim($this->input->post('account'));
                $money = trim($this->input->post('money'));
                $url = trim($this->input->post('url'));
                $remark = trim($this->input->post('remark'));
                $uid=0;
                if(!empty($account)){
                	$ret = $this->account->getAccountInfoByPhones($account);
                	if(empty($ret)){
                		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败,未找到此用户')));
                	}else{
//                		if($ret[0]['private']==0){
//                			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败,非法用户')));
//                		}else{
//                			$uid = $ret[0]['uid'];
//                		}
                	}
                }else{
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败')));
                }
                
                $data = array();
                $data['money'] = $money;
                $data['url'] = $url;
                $data['remark'] = $remark;
                $data['uid'] = $uid;
                $data['account'] = $account;
                $data['ctime'] = NOW;  
                $ret = $this->userpay->addUserpay($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '用户充值列表', '', '用户充值列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加用户充值成功', array(), '用户充值列表 ', 'forward', OP_DOMAIN.'/userpay'));
            }else{
                $this->load->view('/userpay/v_addUserpay');
            }
        }
    }
    public function editUserpay(){
        $flag=$this->op->checkUserAuthority('用户充值',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户充值');
        }else{
            
            if($this->input->request('op') == 'editUserpay'){
            	$id = trim($this->input->post('id'));
            	$userpay= $this->userpay->getUserpayById($id);
            	if(empty($userpay)){
            		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'未找到充值记录')));
            	}else if($userpay['status']==1){
            		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'已充值记录不能修改')));
            	}
            	
                $account = trim($this->input->post('account'));
                $money = trim($this->input->post('money'));
                $url = trim($this->input->post('url'));
                $remark = trim($this->input->post('remark'));
                $uid=0;
                if(!empty($account)){
                	$ret = $this->account->getAccountInfoByPhones($account);
                	if(empty($ret)){
                		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败')));
                	}else{
                		if($ret[0]['private']==0){
                			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败,非法用户')));
                		}else{
                			$uid = $ret[0]['uid'];
                		}
                	}
                }else{
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户充值失败')));
                }
                
                $data = array();
                $data['money'] = $money;
                $data['url'] = $url;
                $data['remark'] = $remark;
                $data['uid'] = $uid;
                $data['account'] = $account;
               $ret = $this->userpay->updateUserpayById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '用户充值列表', '', '修改用户充值列表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改用户充值列表 ', 'forward', OP_DOMAIN.'/userpay'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                 $rec= $this->userpay->getUserpayById($id);
                 if($rec[0]['status']==1){
                 	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'已充值记录不能修改')));
                 }
                 $data['detail'] = $rec[0];
                $this->load->view('/userpay/v_editUserpay', $data);
            }  
        }
    }
    
    public function initUserpay(){
    	$flag=$this->op->checkUserAuthority('用户充值',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户充值');
    	}else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                 $rec= $this->userpay->getUserpayById($id);
                 if($rec[0]['status']==1){
                 	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'该记录已充值')));
                 }
                 $data['detail'] = $rec[0];
                $this->load->view('/userpay/v_initpay', $data);
    	}
    }
    
    public function detail(){
        $id = $this->uri->segment(3);
        if($id < 0 || !is_numeric($id)){
             exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
        }
        $rec= $this->userpay->getUserpayById($id);
        $data['detail'] = $rec[0];
        $this->load->view('/userpay/v_detail', $data);
    }
    
    public function delUserpay(){
        $flag=$this->op->checkUserAuthority('用户充值',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户充值');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->userpay->delUserpayById($id);
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '用户充值列表', '', '删除用户充值', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除用户充值', 'forward', OP_DOMAIN.'/userpay'));
    }
    public function sendCode(){
    	$flag=$this->op->checkUserAuthority('用户充值',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户充值');
    	}else{
    		$id = $this->uri->segment(3);
    		if($id < 0 || !is_numeric($id)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    		}
    		$rec= $this->userpay->send_code_msg('17612159262',$id);
    		if($rec){
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送成功')));
    		}else{
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送失败')));
    		}
    	}
    }
    
    public  function doUserPay(){
    	$flag=$this->op->checkUserAuthority('用户充值',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户充值');
    	}else{
    		$id = trim($this->input->post('id'));
    		$code = trim($this->input->post('code'));
            $userpay= $this->userpay->getUserpayById($id);
            if(empty($userpay)){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'未找到充值记录')));
            }else if($userpay[0]['status']==1){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'已充值记录不能修改')));
            }
            $savedcode = $this->userpay->get_code_msg($id);
            if(empty($savedcode) || $savedcode != $code){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'验证码错误')));
            }
            $times = $this->userpay->incr($id);
            if($times!=1){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请求重复')));
            }
            $update_data = array('status' => 1, 'dtime' => time());
            $ret = $this->userpay->updateUserpayById($id, $update_data);
            $this->load->model('user_log_base', 'user_log');
            $uid = $userpay[0]['uid'];
            $pname = '系统充值';
            $money = $userpay[0]['money'];
            if($money<0){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'充值金额错误')));
            }
            
            $orderid = date('YmdHis') . rand(100000,999999);
            $pay_log = array(
            		'uid' => $uid,
            		'ordid' => $orderid,
            		'amt' => $money,
            		'platform' => 'system',
            		'curcode' => 'RMB',
            		'ctime' => NOW,
            		'status' => 1,
            );
            $this->load->model('admin_pay_log_model' , 'pay_log');
            $this->pay_log->addpaylog($pay_log);
            
            
            $this->load->model('admin_balance_model', 'balance');
            $this->balance->add_user_balance($uid, $money);
            
            $user_log_data = array(
            		'uid' => $uid,
            		'pid' => 0,
            		'pname' => $pname,
            		'paytime' => time(),
            		'money' => $money,
            		'orderid'=>$orderid,
            		'balance' => $this->balance->get_user_balance($uid),
            		'action' => USER_ACTION_PAY
            );
            $this->user_log->addUserLog($uid, $user_log_data);
            
            $log = $this->op->actionData($this->getSession('name'), '用户充值列表', '', '用户充值', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '充值成功', array(), '用户充值列表 ', 'forward', OP_DOMAIN.'/userpay'));
             
    	}
    }
}