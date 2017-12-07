<?php
/**
 * banner管理
 * * */
class buchang extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '补偿申请') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_buchang_model', 'buchang');
    }
    
    //末审核列表
    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('补偿申请', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '补偿申请');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->buchang->getBuchangeList(array('status' => 0), 'ctime desc', array($psize, $offset));
            $count = $this->buchang->getBuchangeCount(array('status' => 0));
            $data = array();
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1132');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $this->load->view('/buchang/v_index', $data);
        }
    }

    
    
    public function shenghe($page = 1) {
        $flag = $this->op->checkUserAuthority('补偿申请', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '补偿申请');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->buchang->getBuchangeList(array('status' => array(0, 1)), 'status, ctime desc', array($psize, $offset));
            $count = $this->buchang->getBuchangeCount(array('status' => array(0, 1)));
            $data = array();
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1133');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $this->load->view('/buchang/v_shenghe', $data);
        }
    }
    
    public function back_shenghe(){
        $flag = $this->op->checkUserAuthority('补偿申请', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加');
        } else {
            $bid = $this->input->get('code');
            $bids = $this->input->request('check_all');

            if (!empty($bids)){
                //批量操作
                $bids = explode(',', $bids);
                foreach ($bids as $bid){
                    $buchang_data = $this->buchang->getBuchangeByBid($bid);
                    if(!$buchang_data){
                        continue;
                    }
                    if($buchang_data['status'] == 1){
                        continue;
                    }
                    $ret = $this->buchang->deleteBuchange($bid);
                }
            }else{
                $buchang_data = $this->buchang->getBuchangeByBid($bid);
                if(!$buchang_data){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的补尝单据!')));
                }
                if($buchang_data['status'] == 1){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此单据已补!')));
                }
                $ret = $this->buchang->deleteBuchange($bid);
            }
            $log = $this->op->actionData($this->getSession('name'), '补偿撤销', '', '补偿撤销', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '撤销成功', array(), '补偿撤销', 'forward', OP_DOMAIN.'/buchang'));
        }
    }
    
    
    public function do_shenghe(){
        $flag = $this->op->checkUserAuthority('补偿申请', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加');
        } else {
        	$id = trim($this->input->post('id'));
        	$code = trim($this->input->post('code'));
            $buchang_data = $this->buchang->getBuchangeByBid($id);
            if(!$buchang_data){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的补尝单据!')));
            }
            if($buchang_data['status'] == 1){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此单据已补!')));
            }
            $savedcode = $this->buchang->get_code_msg($id);
            if(empty($savedcode) || $savedcode != $code){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'验证码错误')));
            }
            $times = $this->buchang->incr($id);
            if($times!=1){
            	exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请求重复')));
            }
            $update_data = array('status' => 1, 'sh_time' => time());
            $ret = $this->buchang->updateBuchange($id, $update_data);
            $this->load->model('user_log_base', 'user_log');
            $uid = $buchang_data['uid'];
            $pname = '老用户买送奖励(补发)';
            $action = USER_ACTION_INVITE;
            $orderid= 'ob'.$uid.date('YmdHis').mt_rand(100,999);
            if($buchang_data['btype'] == 2){
                $pname = '好友邀请奖励(补发)';
                $orderid= 'yq'.$uid.date('YmdHis').mt_rand(100,999);
            }else if($buchang_data['btype'] == 3){
            	$pname = '取现失败补偿';
                $orderid= 'qx'.$uid.date('YmdHis').mt_rand(100,999);
            }else if($buchang_data['btype'] == 4){
            	$pname = '充值失败偿';
                $action = USER_ACTION_PAY;
                $orderid= 'cz'.$uid.date('YmdHis').mt_rand(100,999);
            }
            
            $money = $buchang_data['money'];
            $this->load->model('admin_balance_model', 'balance');
            $this->balance->add_user_balance($uid, $money);
            
            $user_log_data = array(
                    'uid' => $uid,
                    'pid' => 0,
                    'pname' => $pname,
                    'paytime' => time(),
                    'money' => $money,
                    'balance' => $this->balance->get_user_balance($uid),
                    'action' => $action,
                    'orderid' => $orderid
            );
            $this->user_log->addUserLog($uid, $user_log_data);
            
            $log = $this->op->actionData($this->getSession('name'), '补偿审核发放', '', '补偿审核发放', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '发放成功', array(), '审核发放 ', 'forward', OP_DOMAIN.'/buchang/shenghe'));
        }
    }
    
    public function addbuchang()
    {
        $flag = $this->op->checkUserAuthority('补偿申请', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '添加');
        } else {
            if ($this->input->request('op') == 'add') {
                $btype = trim($this->input->post('btype'));
                $uids = trim($this->input->post('uid'));
                $money = trim($this->input->post('money'));
                $desc = trim($this->input->post('desc'));
                if (!$uids || !$btype || !$money || $money <= 0 || !in_array($btype, array(1, 2, 3, 4))) {
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '用户ID或类型、金额不能为空', array(), '补偿申请');
                    exit;
                }
                $this->load->model('admin_account_model', 'account');
                $str_uid = stristr($uids, ',');
                if ($str_uid) {
                    $uid_array = explode(',', $uids);
                    foreach ($uid_array as $uid) {
                        $uinfo = $this->account->getAccountByUid($uid);
                        if (!$uinfo) {
                            continue;
                        }
                        $data = array();
                        $data['btype'] = $btype;
                        $data['uid'] = $uid;
                        $data['money'] = $money;
                        $data['desc'] = $desc;
                        $data['ctime'] = time();
                        $ret = $this->buchang->addBuchange($data);
                    }
                } else {
                    $uinfo = $this->account->getAccountByUid($uids);
                    if (!$uinfo) {
                        echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '用户信息为空', array(), '补偿申请');
                        exit;
                    }
                    $data = array();
                    $data['btype'] = $btype;
                    $data['uid'] = $uids;
                    $data['money'] = $money;
                    $data['desc'] = $desc;
                    $data['ctime'] = time();
                    $ret = $this->buchang->addBuchange($data);
                    if ($ret == false) {
                        exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '申请失败')));
                    }
                }
                $log = $this->op->actionData($this->getSession('name'), '添加补偿单据', '', '添加补偿单据', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '申请成功', array(), '添加补偿单据 ', 'forward', OP_DOMAIN . '/buchang'));
            } else {
                $num = $this->input->request('num');
                $item['uid_desc'] = $num;
                $this->load->view('/buchang/v_add', $item);
            }
        }
    }

    
    public function sendCode(){
    	$flag=$this->op->checkUserAuthority('补偿申请',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '补偿申请');
    	}else{
    		$bid = $this->uri->segment(3);
    		if($bid < 0 || !is_numeric($bid)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    		}
    		$rec= $this->buchang->send_code_msg('17612159262',$bid);
    		if($rec){
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送成功')));
    		}else{
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'验证码发送失败')));
    		}
    	}
    }
    
    public function initBuchang(){
    	$flag=$this->op->checkUserAuthority('补偿申请',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '补偿申请');
    	}else{
    		$id = $this->uri->segment(3);
    		if($id < 0 || !is_numeric($id)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    		}
    		$buchang_data = $this->buchang->getBuchangeByBid($id);
            if(!$buchang_data){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的补尝单据!')));
            }
            if($buchang_data['status'] == 1){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此单据已补!')));
            }
    		$data['detail'] = $buchang_data;
    		$this->load->view('/buchang/v_initBuchang', $data);
    	}
    }
}