<?php
/**
 * 用户取现管理
 * * */
class withdraw extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '用户取现') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_withdraw_model', 'withdraw');
        $this->load->model('admin_account_model', 'account');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('用户取现', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户取现');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $withdrawList = array();
            $count = 0;
            if($this->input->request('op') == "search"){
            	$account = trim($this->input->post('account'));
            	if(!empty($account)){
		            $withdrawList = $this->withdraw->getWithdrawList(array('account'=>$account),'ctime desc',array($psize, $offset));
		            $count = $this->withdraw->getWithdrawCount(array('account'=>$account));
		            $data['account']=$account;
            	}else{
            		$withdrawList = $this->withdraw->getWithdrawList('','ctime desc',array($psize, $offset));
            		$count = $this->withdraw->getWithdrawCount('');
            	}
            }else{
            	$withdrawList = $this->withdraw->getWithdrawList('','ctime desc',array($psize, $offset));
            	$count = $this->withdraw->getWithdrawCount('');
            }
            $names = array();
            $this->load->model('admin_account_model', 'account');
            if(!empty($withdrawList)){
            	$this->load->model('admin_useridentity_model', 'useridentity');
	            foreach ($withdrawList as $withdraw){
	            	$user = $this->useridentity->getUseridentityByUid($withdraw['uid']);
	            	if(!empty($user)){
	            		$names[$withdraw['uid']] = $user['realname'];
	            	}
	            }
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['names'] = $names;
                $data['list'] = $withdrawList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1480');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '用户取现列表', '', '用户取现列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/withdraw/v_index', $data);
        }
    }
    
    public function addWithdraw(){
        $flag = $this->op->checkUserAuthority('用户取现', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户取现');
        } else {
            if($this->input->request('op') == 'addWithdraw'){
                $account = trim($this->input->post('account'));
                $money = trim($this->input->post('money'));
                $url = trim($this->input->post('url'));
                $remark = trim($this->input->post('remark'));
                $uid=0;
                if(!empty($account)){
                	$ret = $this->account->getAccountInfoByPhones($account);
                	if(empty($ret)){
                		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败,未找到此用户')));
                	}else{
                		if($ret[0]['private']==0){
                			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败,非法用户')));
                		}else{
                			$uid = $ret[0]['uid'];
                		}
                	}
                }else{
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败')));
                }
                
                $this->load->model('admin_balance_model', 'balance');
                $balance = $this->balance->get_user_balance($uid);
                if($balance<$money){
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'用户余额不足')));
                }
                
                $data = array();
                $data['money'] = $money;
                $data['url'] = $url;
                $data['remark'] = $remark;
                $data['uid'] = $uid;
                $data['account'] = $account;
                $data['ctime'] = NOW;  
                $ret = $this->withdraw->addWithdraw($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '用户取现列表', '', '用户取现列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加用户取现成功', array(), '用户取现列表 ', 'forward', OP_DOMAIN.'/withdraw'));
            }else{
                $this->load->view('/withdraw/v_addWithdraw');
            }
        }
    }
    public function editWithdraw(){
        $flag=$this->op->checkUserAuthority('用户取现',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户取现');
        }else{
            
            if($this->input->request('op') == 'editWithdraw'){
            	$id = trim($this->input->post('id'));
            	$withdraw= $this->withdraw->getWithdrawById($id);
            	if(empty($withdraw)){
            		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'未找到取现记录')));
            	}else if($withdraw['status']==1){
            		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'已取现记录不能修改')));
            	}
            	
                $account = trim($this->input->post('account'));
                $money = trim($this->input->post('money'));
                $url = trim($this->input->post('url'));
                $remark = trim($this->input->post('remark'));
                $uid=0;
                if(!empty($account)){
                	$ret = $this->account->getAccountInfoByPhones($account);
                	if(empty($ret)){
                		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败')));
                	}else{
                		if($ret[0]['private']==0){
                			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败,非法用户')));
                		}else{
                			$uid = $ret[0]['uid'];
                		}
                	}
                }else{
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加用户取现失败')));
                }
                
                $this->load->model('admin_balance_model', 'balance');
                $balance = $this->balance->get_user_balance($uid);
                if($balance<$money){
                	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'用户余额不足')));
                }
                
                $data = array();
                $data['money'] = $money;
                $data['url'] = $url;
                $data['remark'] = $remark;
                $data['uid'] = $uid;
                $data['account'] = $account;
               $ret = $this->withdraw->updateWithdrawById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '用户取现列表', '', '修改用户取现列表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改用户取现列表 ', 'forward', OP_DOMAIN.'/withdraw'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                 $rec= $this->withdraw->getWithdrawById($id);
                 if($rec[0]['status']==1){
                 	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'已取现记录不能修改')));
                 }
                 $data['detail'] = $rec[0];
                $this->load->view('/withdraw/v_editWithdraw', $data);
            }  
        }
    }
    
    public function initWithdraw(){
    	$flag=$this->op->checkUserAuthority('用户取现',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户取现');
    	}else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                 $rec= $this->withdraw->getWithdrawById($id);
                 if($rec[0]['status']==1){
                 	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'该记录已取现')));
                 }
                 $data['detail'] = $rec[0];
                $this->load->view('/withdraw/v_initWithdraw', $data);
    	}
    }
    
    public function detail(){
        $id = $this->uri->segment(3);
        if($id < 0 || !is_numeric($id)){
             exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
        }
        $rec= $this->withdraw->getWithdrawById($id);
        $data['detail'] = $rec[0];
        $this->load->view('/withdraw/v_detail', $data);
    }
    
    public function delWithdraw(){
        $flag=$this->op->checkUserAuthority('用户取现',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户取现');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->withdraw->delWithdrawById($id);
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '用户取现列表', '', '删除用户取现', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除用户取现', 'forward', OP_DOMAIN.'/withdraw'));
    }
    public function sendCode(){
    	$flag=$this->op->checkUserAuthority('用户取现',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户取现');
    	}else{
    		$id = $this->uri->segment(3);
    		if($id < 0 || !is_numeric($id)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    		}
    		$rec= $this->withdraw->send_code_msg('17612159262',$id);
    		if($rec){
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送成功')));
    		}else{
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送失败')));
    		}
    	}
    }
    
    public  function doWithdraw(){
    	$flag=$this->op->checkUserAuthority('用户取现',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '用户取现');
    	}else{
    		$id = trim($this->input->post('id'));
    		$code = trim($this->input->post('code'));
            $withdraw= $this->withdraw->getWithdrawById($id);
            if(empty($withdraw)){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'未找到取现记录')));
            }else if($withdraw[0]['status']==1){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'已取现记录不能修改')));
            }
            $savedcode = $this->withdraw->get_code_msg($id);
            if(empty($savedcode) || $savedcode != $code){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'验证码错误')));
            }
            $times = $this->withdraw->incr($id);
            if($times!=1){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请求重复')));
            }
            $update_data = array('status' => 1, 'dtime' => time());
            $ret = $this->withdraw->updateWithdrawById($id, $update_data);
            $this->load->model('user_log_base', 'user_log');
            $uid = $withdraw[0]['uid'];
            $pname = '系统取现';
            $money = $withdraw[0]['money'];
            if($money<0){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'取现金额错误')));
            }
            
            $this->load->model('admin_balance_model', 'balance');
            $balance = $this->balance->get_user_balance($uid);

            if($money>$balance){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'余额不足')));
            }
            $orderid = date('YmdHis') . rand(100000,999999);
        	$withdraw_log['uid'] = $uid;
	        $withdraw_log['orderid'] = $orderid;
	        $withdraw_log['ybdrawflowid'] = $orderid;
	        $withdraw_log['status_code'] = '';
	        $withdraw_log['money'] = $money;
	        $withdraw_log['logid'] = 0;
	        $withdraw_log['plat'] = 'system';
	        $withdraw_log['back_status'] = 'SUCCESS';
            $withdraw_log['status'] = 2;
            $withdraw_log['succtime'] = NOW;
	        $withdraw_id = $this->withdraw->addWithdrawLog($withdraw_log);
            
            
           
            $ret = $this->balance->cost_user_balance($uid, $money);
            
            $user_log_data = array(
            		'uid' => $uid,
                    'pid' => 0,
                    'pname' => '提现成功',
                    'orderid' => $orderid,
                    'money' => $money,
                    'paytime' => NOW,
                    'balance' => $balance-$money,
                    'action' => USER_ACTION_PCASHOUT,
            );
            $this->user_log->addUserLog($uid, $user_log_data);
            
            $log = $this->op->actionData($this->getSession('name'), '用户取现列表', '', '用户取现', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '取现成功', array(), '用户取现列表 ', 'forward', OP_DOMAIN.'/withdraw'));
             
    	}
    }
}