<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 用户账户（资产）信息
 */
class account extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/user_logic', 'user_logic');
        $this->check_login();
    }

    public function setUserIdCard(){
        $realname = $this->input->post('realname');
        $idcard = $this->input->post('idcard');
        if(!$realname || !$idcard){
            $response = array('error'=> 4060, 'msg'=>'真实姓名和卡号不能为空');
            $this->out_print($response);
        }
        if(!$this->checkIdentity($idcard)){
            $response = array('error'=> 4061, 'msg'=>'身份证号码不符合规范');
            $this->out_print($response);
        }
        $user_identity = $this->user_logic->getUserIdentity($this->uid);
        if(!empty($user_identity['realname']) &&  !empty($user_identity['idCard'])){
            $response = array('error'=> 4062, 'msg'=>'身份信息已认证，不要重复提交');
            $this->out_print($response);
        }
        $idcard_data = array(
            'uid' => $this->uid,
            'idcard' => $idcard,
            'realname' => $realname
        );
        $result = $this->user_logic->setUserIdcardInfo($idcard_data);
        if(!$result){
            $response = array('error'=> 4063, 'msg'=>'初始化身份信息失败，请重新提交');
            $this->out_print($response);
        }
        $data = array('idcard' => $idcard, 'realname' => $realname);
        $response = array('error'=> 0, 'data'=> array($data));
        $this->out_print($response);
    }
    
    //设置交易密码
    public function setTpwd(){
        $tpwd = $this->input->post('tpwd');
        if(empty($tpwd)){
            $response = array('error'=> 4040, 'msg'=>'交易密码不能为空');
            $this->out_print($response);
        }
        if(strlen($tpwd) != 32){
            $response = array('error'=> 4040, 'msg'=>'交易密码不能为空');
            $this->out_print($response);
        }
        $user_identity = $this->user_logic->getUserIdentity($this->uid);
        if(!$user_identity['realname'] || !$user_identity['idCard']){
            $response = array('error'=> 4041, 'msg'=>'请先完成第三方身份认证');
            $this->out_print($response);
        }
        if(!empty($user_identity['tpwd'])){
            $response = array('error'=> 4042, 'msg'=>'如需修改交易密码，请用修改功能');
            $this->out_print($response);
        }
        $result = $this->user_logic->setUserTpwd($this->uid, $tpwd);
        if(!$result){
            $response = array('error'=> 4053, 'msg'=>'修改失败');
            $this->out_print($response);
        }
        $response = array('error'=> 0, 'data'=> array());
        $this->out_print($response);
    }
    
    //修改交易密码
    public function modifyTpwd(){
        $tpwd = $this->input->post('tpwd');
        $new_tpwd = $this->input->post('new_tpwd');
        if(empty($tpwd) || empty($new_tpwd)){
            $response = array('error'=> 4050, 'msg'=>'交易密码不能为空');
            $this->out_print($response);
        }
        if($tpwd == $new_tpwd){
            $response = array('error'=> 4051, 'msg'=>'新旧密码不能相同');
            $this->out_print($response);
        }
        $user_identity = $this->user_logic->getUserIdentity($this->uid);
        if($user_identity['tpwd'] != $tpwd){
            $response = array('error'=> 4052, 'msg'=>'旧密码错误');
            $this->out_print($response);
        }
        $result = $this->user_logic->setUserTpwd($this->uid, $new_tpwd);
        if(!$result){
            $response = array('error'=> 4053, 'msg'=>'修改失败');
            $this->out_print($response);
        }
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $this->pay_redis_base->delwithdrawtpwdtimes($this->account);
        $response = array('error'=> 0, 'data'=> array());
        $this->out_print($response);
    }
    
    //找回交易密码 第一步，身份证信息确认
    public function findTpwd_step1(){
        $idcard = $this->input->post('idcard');
        $realname = $this->input->post('realname');
        if(empty($idcard) || empty($realname)){
            $response = array('error'=> 4070, 'msg'=>'请填写正确的资料');
            $this->out_print($response);
        }
        if(!$this->checkIdentity($idcard)){
            $response = array('error'=> 4071, 'msg'=>'身份证号码不符合规范');
            $this->out_print($response);
        }
        $user_identity = $this->user_logic->getUserIdentity($this->uid);
        
        if($user_identity['idCard'] != $idcard || $realname != $user_identity['realname']){
            $response = array('error'=> 4072, 'msg'=>'身份信息验证失败');
            $this->out_print($response);
        }
        //验证码短信 先模仿
        //$code = $this->user_logic->makeSMSCode($this->uid);
        //
        $count = $this->user_logic->incrModifyTpwdCode($this->uid);
        if($count>5){
        	$response = array('error'=> 2020, 'msg'=>'尝试次数过多，请30分钟后再试！');
        	$this->out_print($response);
        }
        $code = $this->user_logic->createMsgCode();
        $this->load->model('logic/msm_logic', 'msm_logic');
        $this->msm_logic->send_tpwd_check_code($this->uid, $code, $this->account);
        
        $response = array('error'=> 0, 'data'=> array('message' => '验证码短信已发送，请注意查收！'));
        $this->out_print($response);
    }
    
    public function validateTpwd_code(){
    	$code = $this->input->post('code');
    	if(empty($code)){
    		$response = array('error'=> 4090, 'msg'=>'验证码错误');
    		$this->out_print($response);
    	}
    	$code_server = $this->user_logic->getModifyTpwdCode($this->uid);
    	if(empty($code_server) || $code_server != $code){
    		$response = array('error'=> 4091, 'msg'=>'验证码错误');
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'msg'=>'验证成功');
    		$this->out_print($response);
    	}
    }
    public function findTpwd_step2(){
        $code = $this->input->post('code');
        $newTpwd = $this->input->post('newTpwd');
        if(empty($code) || empty($newTpwd)){
            $response = array('error'=> 4090, 'msg'=>'错误的参数');
            $this->out_print($response);
        }
        $user_identity = $this->user_logic->getUserIdentity($this->uid);
        if($user_identity['tpwd']==$newTpwd){
        	$response = array('error'=> 4091, 'msg'=>'新密码不能与旧密码相同');
        	$this->out_print($response);
        }
        if(strlen($newTpwd) != 32){
            $response = array('error'=> 4089, 'msg'=>'交易密码长度错');
            $this->out_print($response);
        }
        $code_server = $this->user_logic->getModifyTpwdCode($this->uid);
        if(empty($code_server) || $code_server != $code){
            $response = array('error'=> 4091, 'msg'=>'错误的参数');
            $this->out_print($response);
        }
        $result = $this->user_logic->setUserTpwd($this->uid, $newTpwd);
        if(!$result){
            $response = array('error'=> 4092, 'msg'=>'修改失败');
            $this->out_print($response);
        }
        $this->user_logic->moveModifyTpwdCode($this->uid);
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $this->pay_redis_base->delwithdrawtpwdtimes($this->account);
        $response = array('error'=> 0, 'data'=> array());
        $this->out_print($response);
    }
    
    
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */