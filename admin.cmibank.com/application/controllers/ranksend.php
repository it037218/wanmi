<?php
/**
 * ranksend管理
 * * */
class ranksend extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '积分奖励') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_ranksend_model', 'ranksend');
    }
    
    //末审核列表
    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('积分奖励', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '积分奖励');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->ranksend->getList('', 'ctime desc', array($psize, $offset));
            $count = $this->ranksend->getCount('');
            $data = array();
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'9036');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $this->load->view('/ranksend/v_index', $data);
        }
    }

    
    
    public function shenghe($bid){
        $flag = $this->op->checkUserAuthority('积分奖励', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加');
        } else {
            $ranksend_data = $this->ranksend->getByBid($bid);
            if(!$ranksend_data){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的奖励记录!')));
            }
            if($ranksend_data['status'] == 1){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此奖励已发放!')));
            }
            
            $ret = $this->ranksend->deleteranksende($bid);
            $log = $this->op->actionData($this->getSession('name'), '补偿撤销', '', '补偿撤销', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '撤销成功', array(), '补偿撤销', 'forward', OP_DOMAIN.'/ranksend'));
        }
    }
    
    
    public function send(){
        $flag = $this->op->checkUserAuthority('积分奖励', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加');
        } else {
        	$id = trim($this->input->post('id'));
        	$code = trim($this->input->post('code'));
            $ranksend_data = $this->ranksend->getByBid($id);
            if(!$ranksend_data){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的奖励记录!')));
            }
            if($ranksend_data['status'] == 1){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此奖励已发放!')));
            }
            $savedcode = $this->ranksend->get_code_msg($id);
            if(empty($savedcode) || $savedcode != $code){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'验证码错误')));
            }
            $times = $this->ranksend->incr($id);
            if($times!=1){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请求重复')));
            }
            $update_data = array('status' => 1, 'stime' => time());
            $ret = $this->ranksend->update($id, $update_data);
            $this->load->model('user_log_base', 'user_log');
            $accounts = $ranksend_data['accounts'];
            $this->load->model('admin_balance_model', 'balance');
            $this->load->model('admin_account_model', 'account');
            $accountArray = explode('|', $accounts );
            foreach ($accountArray as $temp_account){
            	$_account = explode(',', $temp_account );
            	$uid = $this->account->getUidByAccount($_account[0]);
            	if(!empty($uid)){
		            $this->balance->add_user_balance($uid[0]['uid'], $_account[1]);
		            $user_log_data = array(
		                    'uid' => $uid[0]['uid'],
		                    'pid' => 0,
		                    'pname' => '积分奖励',
		                    'paytime' => time(),
		                    'money' => $_account[1],
		                    'balance' => $this->balance->get_user_balance($uid[0]['uid']),
		                    'action' => USER_ACTION_ACTIVITY
		                );
		            $this->user_log->addUserLog($uid[0]['uid'], $user_log_data);
            	}
            }
            
            $log = $this->op->actionData($this->getSession('name'), '积分奖励发放', '', '积分奖励发放', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '发放成功', array(), '积分奖励发放 ', 'forward', OP_DOMAIN.'/ranksend'));
        }
    }
    
    public function add() {
        $flag = $this->op->checkUserAuthority('积分奖励', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加');
        } else {
            if($this->input->request('op') == 'add'){
            	$remark = trim($this->input->post('remark'));
                $accounts = trim($this->input->post('accounts'));
                if(empty($accounts)){
                	echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '账号不能为空', array(), '积分奖励');
                	exit;
                }
                $accountArray = explode('|', $accounts );
                if(empty($accountArray)){
                	echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '账号不能为空', array(), '积分奖励');
                	exit;
                }
//                 $money = trim($this->input->post('money'));
//                 if(!$money || $money <= 0 ){
//                     echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '金额不能为空', array(), '积分奖励');
//                     exit;
//                 }
                $data = array();
                $data['accounts'] = $accounts;
                $data['counts'] = count($accountArray);
                $data['remark'] = $remark;
                $data['ctime'] = time();
                $ret = $this->ranksend->add($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '添加积分奖励', '', '添加积分奖励', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加成功', array(), '添加积分奖励 ', 'forward', OP_DOMAIN.'/ranksend'));
            }else{
                $this->load->view('/ranksend/v_add');
            }
        }
    }
    
    public function sendCode(){
    	$flag=$this->op->checkUserAuthority('积分奖励',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '积分奖励');
    	}else{
    		$bid = $this->uri->segment(3);
    		if($bid < 0 || !is_numeric($bid)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    		}
    		$rec= $this->ranksend->send_code_msg('15099918724',$bid);
    		if($rec){
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送成功')));
    		}else{
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送失败')));
    		}
    	}
    }
    
    public function init(){
    	$flag=$this->op->checkUserAuthority('积分奖励',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '积分奖励');
    	}else{
    		$id = $this->uri->segment(3);
    		if($id < 0 || !is_numeric($id)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    		}
    		$ranksend_data = $this->ranksend->getByBid($id);
            if(!$ranksend_data){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的奖励记录!')));
            }
            if($ranksend_data['status'] == 1){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此奖励已发放!')));
            }
    		$data['detail'] = $ranksend_data;
    		$this->load->view('/ranksend/v_init', $data);
    	}
    }
    
    public function del(){
    	$flag=$this->op->checkUserAuthority('积分奖励',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '积分奖励');
    	}else{
    		$id = $this->uri->segment(3);
    		$ret = $this->ranksend->delete($id);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '积分奖励', '', '积分奖励', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除积分奖励', 'forward', OP_DOMAIN.'/ranksend'));
    }
    
    public function update(){
    	$flag=$this->op->checkUserAuthority('发放抵用券',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '发放抵用券');
    	}else{
    		if($this->input->request('op') == 'update'){
    			$id = trim($this->input->post('id'));
    			if(!$id){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$remark = trim($this->input->post('remark'));
                $accounts = trim($this->input->post('accounts'));
                if(empty($accounts)){
                	echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '账号不能为空', array(), '积分奖励');
                	exit;
                }
                $accountArray = explode('|', $accounts );
                if(empty($accountArray)){
                	echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '账号不能为空', array(), '积分奖励');
                	exit;
                }
//                 $money = trim($this->input->post('money'));
//                 if(!$money || $money <= 0 ){
//                     echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '金额不能为空', array(), '积分奖励');
//                     exit;
//                 }
                $data = array();
                $data['accounts'] = $accounts;
                $data['counts'] = count($accountArray);
                $data['remark'] = $remark;
    			$ret = $this->ranksend->update($id, $data);
    			if(!$ret){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
    			}
    			if(!$ret){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
    			}
    			$log = $this->op->actionData($this->getSession('name'), '积分奖励', '', '修改积分奖励', $this->getIP(), $this->getSession('uid'));
    			exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改积分奖励 ', 'forward', OP_DOMAIN.'/ranksend'));
    		}else{
    			$id = $this->uri->segment(3);
    			if($id < 0 || !is_numeric($id)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$sendList = $this->ranksend->getByBid($id);
    			$data['detail'] =$sendList;
    			$this->load->view('/ranksend/v_update', $data);
    		}
    	}
    }
    public function detail(){
    	$flag=$this->op->checkUserAuthority('发放抵用券',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '发放抵用券');
    	}else{
    			$id = $this->uri->segment(3);
    			if($id < 0 || !is_numeric($id)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$sendList = $this->ranksend->getByBid($id);
    			$data['detail'] =$sendList;
    			$this->load->view('/ranksend/v_detail', $data);
    	}
    }
}