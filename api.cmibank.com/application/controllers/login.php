<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/login_logic', 'login_logic');
        $this->check_link();
        $this->load->model('base/user_notice_base', 'user_notice_base');
    }

    public function index(){
        $account = trim($this->input->post('account'));     //账号
        $password = trim($this->input->post('password'));    //密码
        if(empty($account) || empty($password)){
            $response = array('error'=> 1020, 'msg'=>'账号密码不能为空');
            $this->out_print($response);
        }
        $tempdevice =  trim($this->input->post('device'));
        $userInfo = $this->login_logic->login($account, $password,$tempdevice);
        if($userInfo['forbidden'] == 1){
            $response = array('error'=> 1021, 'msg'=>'该用户已注销，如有疑问请联系客服');
            $this->out_print($response);
        }
        $blackbox = trim($this->input->post('blackbox'));
        $ip = $this->getIP();
//         if($blackbox){
//             if(!$userInfo){
//                 $state = 1;
//             }else{
//                 $state = 0;
//             }
//             $device =  trim($this->input->post('device'));
//             $this->load->model('logic/tongdun_logic','tongdun_logic');
//             $ret = $this->tongdun_logic->login_check_phone($account, $ip, $blackbox, $state, $device);
//             if($ret === false){
//                 $response = array('error'=> 4334, 'msg'=>'当前登录环境异常,请更换网络环境!');
//                 $this->out_print($response);
//             }
//             if(isset($ret['msg'])){
//                 $response = array('error'=> 4335, 'msg'=> $ret['msg']);
//                 $this->out_print($response);
//             }
//         }
        if(!$userInfo){
            $response = array('error'=> 1021, 'msg'=>'账号或密码错误');
            $this->out_print($response);
        }
        $updateParams = array();
        $updateParams['uid'] = $userInfo['uid'];
        $updateParams['ltime'] = $userInfo['ltime'] = time();
        if($userInfo['lip'] != $ip){
            $updateParams['lip'] = $userInfo['lip'] = $ip;
        }
        $updateParams['device'] = $tempdevice;
        $this->login_logic->updateAccountInfo($updateParams);
        $userInfo['code'] = $this->encode_invite($userInfo['uid']);
        
        //$_cookie_params['uid'] = $userInfo['uid'];
        //$_cookie_params['account'] = $userInfo['account'];
        $_cookie_params['loginip'] = $ip;
        $this->setCookies($_cookie_params);
		$userInfo['cmibank_uid'] = $this->setCookies(array('uid'=>$userInfo['uid']));
		$userInfo['cmibank_account'] = $this->setCookies(array('account'=>$userInfo['account']));
        $response = array('error'=> 0, 'data'=> $this->filter_userinfo($userInfo));
        $this->out_print($response);
    }
    
    public function filter_userinfo($userInfo){
        unset($userInfo['pwd']);
        unset($userInfo['nick']);
        unset($userInfo['realname']);
        unset($userInfo['idCard']);
        unset($userInfo['avatar']);
        unset($userInfo['email']);
        unset($userInfo['aliopenid']);
        unset($userInfo['address']);
        unset($userInfo['status']);
        unset($userInfo['upcount']);
        //unset($userInfo['uid']);
        return $userInfo;
    }
    

	//发送手机验证码
	public function send_phone_code(){
	    $phone = trim($this->input->post('phone'));
	    //判断账号是否存在
	    $res = $this->login_logic->getUidByAccount($phone);
	    if($res){
	        $response = array('error'=> 1006, 'msg'=>'该手机已注册');
	        $this->out_print($response);
	    }
	    $count = $this->login_logic->incrValidateCode($phone);
	    if($count>5){
	    	$response = array('error'=> 2020, 'msg'=>'您操作过于频繁，请半小时后再试！');
	    	$this->out_print($response);
	    }
	    $code = $this->login_logic->createMsgCode();
	    $this->load->model('logic/msm_logic', 'msm_logic');
		$ret = $this->msm_logic->send_phone_code($phone, $code);
		if(!$ret){
		    $response = array('error'=> 1051, 'msg'=>'电话号码错误!');
            $this->out_print($response);
		}
		$response = array('error'=> 0, 'data'=> array());
		$this->out_print($response);
	}
	
	public function send_pay_code(){
		$money = trim($this->input->post('money'));
		if($money<0){
			$response = array('error'=> 2030, 'msg'=>'金额错误！');
			$this->out_print($response);
		}
		$this->load->model('base/user_identity_base' , 'user_identity_base');
		$useridentity = $this->user_identity_base->getUserIdentity($this->uid);
		if(empty($useridentity)){
			$response = array('error'=> 2031, 'msg'=>'请先绑卡！');
			$this->out_print($response);
		}
		$count = $this->login_logic->incrPayCode($useridentity['phone']);
		if($count>5){
			$response = array('error'=> 2020, 'msg'=>'您操作过于频繁，请半小时后再试！');
			$this->out_print($response);
		}
		$code = $this->login_logic->createMsgCode();
		$this->config->load('cfg/banklist', true, true);
		$bankCfg = $this->config->item('cfg/banklist');
		$bankname = $bankCfg[$useridentity['bankcode']]['name'];
		$banknum = substr($useridentity['cardno'],-4);
		$values = array('code' => $code, 'amount' => $money,'bankname' =>$bankname,'banknum' =>$banknum);
		$this->load->model('logic/msm_logic', 'msm_logic');
		$ret = $this->msm_logic->send_pay_code($useridentity['phone'],$values);
		if(!$ret){
			$response = array('error'=> 1051, 'msg'=>'电话号码错误!');
			$this->out_print($response);
		}
		$response = array('error'=> 0, 'data'=> array());
		$this->out_print($response);
	}
	
	public function validate_phone_code(){
		$mobileVerify = trim($this->input->post('mobileVerify'));
		$account = trim($this->input->post('account'));
		if(strlen($mobileVerify) != MOBILEVERIFY_LEN){
			$response = array('error'=> 1003, 'msg'=>'验证码长度错误');
			$this->out_print($response);
		}
		$res = $this->login_logic->check_phone_code($account, $mobileVerify);
		if(!$res){
			$response = array('error'=> 10021, 'msg'=>'手机验证码错误');
			$this->out_print($response);
		}else{
			$response = array('error'=> 0, 'msg'=> '验证成功');
			$this->out_print($response);
		}
	}
	

    public function regUser(){
//        $response = array('error'=> 1002, 'msg'=>'由于恶意刷量，11月13日10点整恢复');
//        $this->out_print($response);
        $invite_code = trim($this->input->post('code'));
        $account = trim($this->input->post('account'));             //账号
        $plat = trim($this->input->post('plat'));                   //平台
        $password1 = trim($this->input->post('password1'));         //密码
        $password2 = trim($this->input->post('password2'));         //密码
        $mobileVerify = trim($this->input->post('mobileVerify'));   //手机验证码
        $invite_code = trim($this->input->post('code'));
        $idfa = trim($this->input->post('idfa'));
        if(strlen($password1) < 6) {
            $response = array('error'=> 1001, 'msg'=>'密码太短，请不要少于6位');
            $this->out_print($response);
        }
        if($password1 != $password2){
            $response = array('error'=> 1002, 'msg'=>'密码不一致');
            $this->out_print($response);
        }
        if(strlen($mobileVerify) != MOBILEVERIFY_LEN){
            $response = array('error'=> 1003, 'msg'=>'验证码长度错误');
            $this->out_print($response);
        }
        if(!$this->_check_mobile($account)) {
            $response = array('error'=> 1004, 'msg'=>'手机号码格式错误');
            $this->out_print($response);
        }
        
        $blackbox = trim($this->input->post('blackbox'));
        $ip = $this->getIP();
//        	if($blackbox){
//             $device =  trim($this->input->post('device'));
//             $this->load->model('logic/tongdun_logic','tongdun_logic');
//             $ret = $this->tongdun_logic->register_check_phone($account, $ip, $blackbox, $device);
//             if($ret === false){
//                 $response = array('error'=> 4334, 'msg'=>'当前登录环境异常,请更换网络环境!');
//                 $this->out_print($response);
//             }
//             if(isset($ret['msg'])){
//                 $response = array('error'=> 4335, 'msg'=> $ret['msg']);
//                 $this->out_print($response);
//             }
//        	}
//         检查手机验证码 submail.cn
//         if($invite_code == 'MTMwODM2'){
//             echo $account;
//             echo $mobileVerify;
//         }
        $res = $this->login_logic->check_phone_code($account, $mobileVerify);
        if(!$res){
            $response = array('error'=> 10021, 'msg'=>'手机验证码错误');
            $this->out_print($response);
        }
        //判断账号是否存在
        $res = $this->login_logic->getUidByAccount($account);
        if($res){
            $response = array('error'=> 1006, 'msg'=>'账号已存在');
            $this->out_print($response);
        }
        $from = trim($this->input->post('from'));
        if($from && $from == 'web'){
            $password1 = strtoupper(md5($password1));
        }
        $regUser = array();
        $regUser['account'] = $account;
        $regUser['pwd'] = $password1;
        $now_time = time();
        $regUser['ctime'] = $now_time;      //创建时间
        $regUser['ltime'] = $now_time;      //最后一次登录时间
        
        $regUser['cip'] = $ip;      //创建IP
        $regUser['lip'] = $ip;      //最后一次登录IP
        $regUser['plat'] = $plat;
        $regUser['idfa'] = $idfa ? $idfa : '';
        //组装数据，准备注册
        $regUser = $this->login_logic->createAccount($regUser);
        if(!$regUser['uid']){
            $response = array('error'=> 1008, 'msg'=>'注册失败');
            $this->out_print($response);
        }
        if($invite_code){
            //添加邀请关系
            $invite_uid = $this->decode_invite($invite_code);
//             var_dump($invite_uid);
            $invite_account = $this->login_logic->getAccountInfo($invite_uid);
//             var_dump($invite_account);
            if($invite_account){
                $this->load->model('logic/invite_logic', 'invite_logic');
                $this->invite_logic->add_invite($regUser['uid'], $regUser['account'], $invite_uid, $invite_account['account']);
            }else{
                $log_msg = array();
                $log_msg['reguser'] = $regUser;
                $log_msg['code'] = $invite_code;
                $this->invite_err_log(json_encode($log_msg));
            }
        }
//         $send_expmoney = 0;
//         if($plat == 'appStorePay'){
//             $send_expmoney = 2688;
//         }else{
//             $this->config->load('cfg/activity_time', true, true);
//             $activity_time = $this->config->item('cfg/activity_time');
//             $red_bag_activity_id = 3;
//             $activityCfg = $activity_time[$red_bag_activity_id];
//             if(strtotime($activityCfg['starttime']) < NOW && strtotime($activityCfg['endtime']) > NOW){
//                 $send_expmoney = $activityCfg['expmoney'];
//                 //判断人数
//                 $this->load->model('base/reg_expmoney_limit_base', 'reg_expmoney_limit_base');
//                 $limit_num = $this->reg_expmoney_limit_base->incr();
//                 if($limit_num > 5000){
//                     $send_expmoney = 0;
//                 }
//             }
//         }
//         if($send_expmoney > 0){
//             //添加体验金
//             $this->load->model('logic/expmoney_logic', 'expmoney_logic');
//             $this->expmoney_logic->add_expmoney($regUser['uid'], $send_expmoney);
//             $exp_balance = $this->expmoney_logic->get_expmoney($regUser['uid']);
//             //添加体验金日志
//             $exp_log_data = array(
//                 'uid' => $regUser['uid'],
//                 'ctime' => NOW,
//                 'log_desc' => '注册奖励',
//                 'money' => $send_expmoney,
//                 'action' => EXPMONEY_LOG_ADD,
//                 'balance'  => $exp_balance
//             );
//             $log_data = $this->expmoney_logic->addLog($regUser['uid'], $exp_log_data);
//         }
        $_cookie_params['uid'] = $regUser['uid'];
        $_cookie_params['account'] = $regUser['account'];
        $_cookie_params['loginip'] = $ip;
        $this->setCookies($_cookie_params);
        unset($regUser['pwd']);
        $regUser['code'] = $this->encode_invite($regUser['uid']);
		$this->load->model('logic/expmoney_activity_logic', 'expmoney_activity_logic');
		$this->expmoney_activity_logic->sendExpmoney(EXPMONEY_ACTIVITY_REGEDIT,$regUser['uid']);
        $this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
        $count = $this->coupon_activity_logic->sendCoupon(COUPON_ACTIVITY_REGEDIT,0,$regUser['uid'],$regUser['account']);
        $regUser['couponcounts'] = $count['singlcount'];
        if($count['singlcount']>0){
        	$this->load->model('logic/msm_logic', 'msm_logic');
        	$this->msm_logic->send_coupon_msg($account, $count['singlcount'],$count['moneycount']);
			$notice_data = array(
					'uid' => $regUser['uid'],
					'title' => '抵用券获得提醒',
					'content' => "恭喜您获得了".$count['singlcount']."张共价值".$count['moneycount']."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！！",
					'ctime' => NOW
			);
			$this->user_notice_base->addNotice($regUser['uid'],$notice_data);
        }
        $response = array('error'=> 0, 'data'=> $this->filter_userinfo($regUser));
        $this->out_print($response);
    }
    
    
    public function regQudaoUser(){
    	$account = trim($this->input->post('account'));             //账号
    	$plat = trim($this->input->post('plat'));                   //平台
    	$password1 = trim($this->input->post('password'));         //密码
    	$mobileVerify = trim($this->input->post('mobileVerify'));   //手机验证码
    	$idfa = trim($this->input->post('idfa'));
    	if(strlen($password1) < 6) {
    		$response = array('error'=> 1001, 'msg'=>'密码太短，请不要少于6位');
    		$this->out_print($response);
    	}
    	if(strlen($mobileVerify) != MOBILEVERIFY_LEN){
    		$response = array('error'=> 1003, 'msg'=>'验证码长度错误');
    		$this->out_print($response);
    	}
    	if(!$this->_check_mobile($account)) {
    		$response = array('error'=> 1004, 'msg'=>'手机号码格式错误');
    		$this->out_print($response);
    	}
    	$ip = $this->getIP();
    	//判断账号是否存在
    	$res = $this->login_logic->getUidByAccount($account);
    	if($res){
    		$response = array('error'=> 1006, 'msg'=>'账号已存在');
    		$this->out_print($response);
    	}
    	$res = $this->login_logic->check_phone_code($account, $mobileVerify);
    	if(!$res){
    		$response = array('error'=> 10021, 'msg'=>'手机验证码错误');
    		$this->out_print($response);
    	}
    	$from = trim($this->input->post('from'));
    	if($from && $from == 'web'){
    		$password1 = strtoupper(md5($password1));
    	}
    	$regUser = array();
    	$regUser['account'] = $account;
    	$regUser['pwd'] = $password1;
    	$now_time = time();
    	$regUser['ctime'] = $now_time;      //创建时间
    	$regUser['ltime'] = $now_time;      //最后一次登录时间
    
    	$regUser['cip'] = $ip;      //创建IP
    	$regUser['lip'] = $ip;      //最后一次登录IP
    	$regUser['plat'] = $plat;
    	$regUser['idfa'] = $idfa ? $idfa : '';
    	//组装数据，准备注册
    	$regUser = $this->login_logic->createAccount($regUser);
    	if(!$regUser['uid']){
    		$response = array('error'=> 1008, 'msg'=>'注册失败');
    		$this->out_print($response);
    	}
    	unset($regUser['pwd']);
		$this->load->model('logic/expmoney_activity_logic', 'expmoney_activity_logic');
		$this->expmoney_activity_logic->sendExpmoney(EXPMONEY_ACTIVITY_REGEDIT,$regUser['uid']);
    	$this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
    	$count = $this->coupon_activity_logic->sendCoupon(COUPON_ACTIVITY_REGEDIT,0,$regUser['uid'],$regUser['account']);
    	$regUser['couponcounts'] = $count['singlcount'];
    	if($count['singlcount']>0){
    		$this->load->model('logic/msm_logic', 'msm_logic');
    		$this->msm_logic->send_coupon_msg($account, $count['singlcount'],$count['moneycount']);
        	$notice_data = array(
        			'uid' => $regUser['uid'],
        			'title' => '抵用券获得提醒',
        			'content' => "恭喜您获得了".$count['singlcount']."张共价值".$count['moneycount']."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！",
        			'ctime' => NOW
        	);
        	$this->user_notice_base->addNotice($regUser['uid'],$notice_data);
    	}
    	$response = array('error'=> 0, 'data'=> $this->filter_userinfo($regUser));
        $this->out_print($response);
    }
    
    public function regLuckybagUser(){
    	$account = trim($this->input->post('account'));             //账号
    	$plat = trim($this->input->post('plat'));                   //平台
    	$password1 = trim($this->input->post('password'));         //密码
    	$mobileVerify = trim($this->input->post('mobileVerify'));   //手机验证码
    	$idfa = trim($this->input->post('idfa'));
    	if(strlen($password1) < 6) {
    		$response = array('error'=> 1001, 'msg'=>'密码太短，请不要少于6位');
    		$this->out_print($response);
    	}
    	if(strlen($mobileVerify) != MOBILEVERIFY_LEN){
    		$response = array('error'=> 1003, 'msg'=>'验证码长度错误');
    		$this->out_print($response);
    	}
    	if(!$this->_check_mobile($account)) {
    		$response = array('error'=> 1004, 'msg'=>'手机号码格式错误');
    		$this->out_print($response);
    	}
    	$ip = $this->getIP();
    	//判断账号是否存在
    	$res = $this->login_logic->getUidByAccount($account);
    	if($res){
    		$response = array('error'=> 1006, 'msg'=>'账号已存在');
    		$this->out_print($response);
    	}
    	$res = $this->login_logic->check_phone_code($account, $mobileVerify);
    	if(!$res){
    		$response = array('error'=> 10021, 'msg'=>'手机验证码错误');
    		$this->out_print($response);
    	}
    	$from = trim($this->input->post('from'));
    	if($from && $from == 'web'){
    		$password1 = strtoupper(md5($password1));
    	}
    	$regUser = array();
    	$regUser['account'] = $account;
    	$regUser['pwd'] = $password1;
    	$now_time = time();
    	$regUser['ctime'] = $now_time;      //创建时间
    	$regUser['ltime'] = $now_time;      //最后一次登录时间
    
    	$regUser['cip'] = $ip;      //创建IP
    	$regUser['lip'] = $ip;      //最后一次登录IP
    	$regUser['plat'] = $plat;
    	$regUser['idfa'] = $idfa ? $idfa : '';
    	
    	$invite_code = trim($this->input->post('code'));
        $lid = trim($this->input->post('lid'));
        $invite_uid = $this->decode_invite($invite_code);
        $this->load->model('logic/luckybag_logic', 'luckybag_logic');
        $logic_response = $this->luckybag_logic->getLuckyBag($invite_uid,$account,$lid);
        if(!empty($logic_response)){
        	if($logic_response['error']!=0){
        		$this->out_print($logic_response);
        	}
        }
    	$regUser = $this->login_logic->createAccount($regUser);
    	if(!$regUser['uid']){
    		$response = array('error'=> 1008, 'msg'=>'注册失败');
    		$this->out_print($response);
    	}
        
        $invite_account = $this->login_logic->getAccountInfo($invite_uid);
        if($invite_account){
        	$this->load->model('logic/invite_logic', 'invite_logic');
        	$this->invite_logic->add_invite($regUser['uid'], $regUser['account'], $invite_uid, $invite_account['account']);
        }else{
        	$log_msg = array();
        	$log_msg['reguser'] = $regUser;
        	$log_msg['invite_code'] = $invite_code;
        	$this->invite_err_log(json_encode($log_msg));
        }
        
    	unset($regUser['pwd']);
    
    	$this->load->model('logic/expmoney_activity_logic', 'expmoney_activity_logic');
    	$this->expmoney_activity_logic->sendExpmoney(EXPMONEY_ACTIVITY_REGEDIT,$regUser['uid']);
    
    	$this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
    	$count = $this->coupon_activity_logic->sendCoupon(COUPON_ACTIVITY_REGEDIT,0,$regUser['uid'],$regUser['account']);
    	$regUser['couponcounts'] = $count['singlcount'];
    	if($count['singlcount']>0){
    		$this->load->model('logic/msm_logic', 'msm_logic');
    		$this->msm_logic->send_coupon_msg($account, $count['singlcount'],$count['moneycount']);
    		$notice_data = array(
    				'uid' => $regUser['uid'],
    				'title' => '抵用券获得提醒',
    				'content' => "恭喜您获得了".$count['singlcount']."张共价值".$count['moneycount']."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！",
    				'ctime' => NOW
    		);
    		$this->user_notice_base->addNotice($regUser['uid'],$notice_data);
    	}
		$response = array('error'=> 0, 'data'=> $this->filter_userinfo($regUser));
    	$this->out_print($response);
    }
    
    public function send_email(){
        $email = $this->input->post('email');
        var_dump($this->login_logic->send_email($email));
    }
    
    public function verification_account(){
        $account = trim($this->input->post('account'));
        if(!$this->_check_mobile($account)){
            $response = array('error'=> 1004, 'msg'=>'账号格式错误');
            $this->out_print($response);
        }
        $uid = $this->login_logic->getUidByAccount($account);
        if($uid){
            $response = array('error'=> 1011, 'msg'=>'账号已存在');
            $this->out_print($response);
        }else{
            $response = array('error'=> 0, 'data'=>'[]');
            $this->out_print($response);
        }
    }
    
    public function verifyLuckyBagAccount(){
    	$account = trim($this->input->post('account'));
    	if(!$this->_check_mobile($account)){
    		$response = array('error'=> 1004, 'msg'=>'账号格式错误');
    		$this->out_print($response);
    	}
    	$uid = $this->login_logic->getUidByAccount($account);
    	if($uid){
    		$response = array('error'=> 1011, 'msg'=>'您已是易米融注册用户，该活动仅限未注册用户参加，如果还没有安装易米融，那就赶紧下载吧！');
    		$this->out_print($response);
    	}
    	$code = trim($this->input->post('code'));
    	$lid = trim($this->input->post('lid'));
    	$redbag_uid = $this->decode_invite($code);
    	$this->load->model('logic/luckybag_logic', 'luckybag_logic');
    	$luckybagDetail = $this->luckybag_logic->getLuckybagDetailByid($redbag_uid,$lid);
    	$errorCode = 1;
    	if($luckybagDetail){
    		if($luckybagDetail['status']==0){
    			$errorCode=0;
    		}
    	}
    	if($errorCode){
	    	$response = array('error'=> 1004, 'msg'=>'红包已被领走');
	    	$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'msg'=>'');
            $this->out_print($response);
        }
    }
    
//     public function verification_mobile(){
//         $mobile = trim($this->input->post('mobile'));
//         if(!$this->_check_mobile($mobile)) {
//             $response = array('error'=> 1005, 'msg'=>'手机号码格式错误');
//             $this->out_print($response);
//         }
//         $uid = $this->login_logic->getUidByMobile($mobile);
//         if($uid){
//             $response = array('error'=> 1021, 'msg'=>'此手机号码已经注册');
//             $this->out_print($response);
//         }else{
//             $response = array('error'=> 0, 'data'=>'[]');
//             $this->out_print($response);
//         }
//     }
    
//     public function getIdentifyingCode(){
//         $this->check_login();
//         $this->login_logic->identifying_code($this->uid);
//     }

    
    public function findLoginPwd_step1(){
        $account = trim($this->input->post('account'));
        if(!$this->_check_mobile($account)){
            $response = array('error'=> 1004, 'msg'=>'账号格式错误');
            $this->out_print($response);
        }
        $uid = $this->login_logic->getUidByAccount($account);
        if(!$uid){
            $response = array('error'=> 1011, 'msg'=>'不存在的账号');
            $this->out_print($response);
        }
        
        $count = $this->login_logic->incrLoginPwdCode($account);
        if($count>5){
        	$response = array('error'=> 2020, 'msg'=>'您操作过于频繁，请半小时后再试！');
        	$this->out_print($response);
        }
        $code = $this->login_logic->createMsgCode();
        $this->load->model('logic/msm_logic', 'msm_logic');
        $ret = $this->msm_logic->send_loginPwd_code($account, $code);
        if(!$ret){
            $response = array('error'=> 1012, 'msg'=>'发送失败，请重新发送!');
            $this->out_print($response);
        }
        $response = array('error'=> 0, 'data'=> array('message' => '验证码短信已发送，请注意查收！'));
        $this->out_print($response);
    }
    
    public function validate_loginpwd(){
    	$account = trim($this->input->post('account'));
    	$code = trim($this->input->post('code'));
    	$ret = $this->login_logic->check_loginPwd_code($account, $code);
    	if(!$ret){
    		$response = array('error'=> 21013, 'msg'=>'验证码错误');
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'msg'=>'验证码正确');
    		$this->out_print($response);
    	}
    }
    
    public function findLoginPwd_step2(){
        $account = trim($this->input->post('account'));
        $code = trim($this->input->post('code'));
        $pwd = trim($this->input->post('pwd'));
        if(!$code || !$pwd){
            $response = array('error'=> 21011, 'msg'=>'不存在的账号');
            $this->out_print($response);
        }
        if(strlen($pwd) < 6) {
            $response = array('error'=> 21012, 'msg'=>'密码太短，请不要少于6位');
            $this->out_print($response);
        }
        $ret = $this->login_logic->check_loginPwd_code($account, $code);
        if(!$ret){
            $response = array('error'=> 21013, 'msg'=>'验证码错误');
            $this->out_print($response);
        }
        $uid = $this->login_logic->getUidByAccount($account);
        if(!$uid){
            $response = array('error'=> 21014, 'msg'=>'不存在的账号');
            $this->out_print($response);
        }
        $ret = $this->login_logic->updateAccountPwd($uid, $pwd);
        if(!$ret){
            $response = array('error'=> 21015, 'msg'=>'重置登录密码失败，请重试');
            $this->out_print($response);
        }
        $response = array('error'=> 0, 'data'=> array('message' => '重置登录密码成功'));
        $this->out_print($response);
    }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */