<?php
require (APPPATH . 'libraries/ValidateCode.php');
require (APPPATH . 'libraries/top-sdk/TopSdk.php');
class login_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/user_base' , 'user_base');
    }


    public function login($account, $passworld,$device = ''){
        $uid = $this->user_base->getUidByAccount($account);
        if($device=='android'){
        	$this->load->model('base/pay_redis_base', 'pay_redis_base');
        	$this->pay_redis_base->incrAndoridVersion($uid);
        }
        if(!$uid){
            return false;
        }
        $accountinfo = $this->getAccountInfo($uid);
        if($accountinfo['pwd'] != $passworld){
            return false;
        }
        return $accountinfo;
    }
    
    public function getAccountInfo($uid){
        return $this->user_base->getAccountInfo($uid);
    }
    
    public function getDbAccountInfo($uid){
       return $this->user_base->_db_get_account_info_by_uid($uid, false);
    }
    
    public function createAccount($info) {
    	$c = new TopClient;
    	$c->appkey = '';//cmibank todo
    	$c->secretKey = '';//cmibank todo
    	$req = new OpenimUsersAddRequest;
    	$t_uid = 'cmibank'.$info['account'];
    	$req->setUserinfos("{'userid':'".$t_uid."','password':'0000000'}");
    	$resp = $c->execute($req);
    	if($resp->uid_succ->string){
    		$info['top_uid']=$t_uid;
    		$info['top_pwd']='0000000';
    	}
        return $this->user_base->createAccount($info);
    }
    
    public function getUidByAccount($account){
       return  $this->user_base->getUidByAccount($account);
    }
    
    public function getUidByMobile($mobile){
       return  $this->user_base->getUidByMobile($mobile);
    }

//     public function identifying_code($uid){
//        $_vc = new ValidateCode();  //实例验证码对象
//        $_vc->doimg();
//        $vc_code = $_vc->getCode();
//        $this->user_base->setValidateCode($uid, $vc_code);
//     }
    
//     public function check_identifying_code($uid, $input_code){
//         $vc_code = $this->user_base->getValidateCode($uid);
//         //echo $vc_code . '|' . $input_code;
//         if($vc_code != $input_code){
//             return false;
//         }
//         return true;
//     }
    
    public function modifyPassworld($uid, $new_pwd){
        return $this->user_base->updateAccountPwd($uid, $new_pwd);
    }
   
    public function updateAccountInfo($account){
        return $this->user_base->updateAccountInfo($account['uid'], $account);
    }
   

    
    public function check_loginPwd_code($phone, $input_code){
        $vc_code = $this->user_base->getLoginPwdCode($phone);
        if($vc_code != $input_code){
            return false;
        }
        return true;
    }
    

    public function check_phone_code($phone, $input_code){
        $vc_code = $this->user_base->getValidateCode($phone);
        if($vc_code != $input_code){
            return false;
        }
        return true;
    }
    
    public function updateAccountPwd($uid, $passworld){
        return $this->user_base->updateAccountPwd($uid, $passworld);
    }
    
    public function createMsgCode(){
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= mt_rand(0,9);
        }
        return $code;
    }
    
    public function incrValidateCode($phone){
    	return $this->user_base->incrValidateCode($phone);
    }
    
    public function incrLoginPwdCode($account){
    	return $this->user_base->incrLoginPwdCode($account);
    }
    
    public function incrPayCode($phone){
    	return $this->user_base->incrPayCode($phone);
    }
}


   
