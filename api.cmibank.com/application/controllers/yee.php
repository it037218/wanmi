<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
require (APPPATH . 'controllers/jytpay.php');
//require (APPPATH . 'controllers/baofoopay.php');
require (APPPATH . 'controllers/fuioupay.php');
require (APPPATH . 'controllers/weehours_withdraw.php');
class yee extends Controller {

    private $jyt_open_bank;
    public function __construct()
    {
        parent::__construct();
        $this->check_link();
        $this->load->model('logic/user_logic', 'user_logic');
//         $this->load->model('logic/yeepay_logic', 'yeepay_logic');
        $this->load->model('base/bind_card_cd_base', 'bind_card_cd_base');
        $this->check_login();
        //$this->yee_pay_bank = array('HXB','ICBC','ABC','CMBC');   //'CMBCHINA',
        //$this->yee_pay_bank = array('HXB');   //'CMBCHINA',
        $this->yee_pay_bank = array();

        $this->load->model('base/balance_base', 'balance_base');
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
    }

    private function weihu(){
        $start = strtotime('2016-06-14 23:00:00');
        $end   = strtotime('2016-06-14 23:59:59');
        if(NOW >= $start && NOW <= $end){
            $response = array('error'=> 4017, 'msg'=>'接银行渠道通知，为更好的服务于商户及客户，代收付系统将于2016年06月14日23：00——14日24:00进行系统巡检工作。');
            $this->out_print($response);
        }
//         $start = strtotime('22:40:00');
//         $end = strtotime('23:59:59');
//         $start_2 = strtotime('00:00:00');
//         $end_2 = strtotime('01:00:00');
//         if(NOW >= $start && NOW <= $end){
//             $response = array('error'=> 4016, 'msg'=>'22:40~次日凌晨01:00为非银行窗口交易时间,请您避开该时间段交易!');
//             $this->out_print($response);
//         }
//         if(NOW >= $start_2 && NOW <= $end_2){
//             $response = array('error'=> 4016, 'msg'=>'22:40~次日凌晨01:00为非银行窗口交易时间,请您避开该时间段交易!');
//             $this->out_print($response);
//         }
        
    }
    
    //不发短信充值
    public function directPayment(){
        //error_reporting(E_ALL);
        $this->weihu();
        if(NOW <= mktime(0,55,0)){
        	$response = array('error'=> 4025, 'msg'=>'00:00~00:55为银行渠道维护时间,请您避开该时间段交易!');
        	$this->out_print($response);
        }
        $amount = trim($this->input->post('amount'));
        //         if($amount < 50){
        //             $response = array('error'=> 4017, 'msg'=>'50起充!');
        //             $this->out_print($response);
        //         }
        if($amount > 100000){
            $response = array('error'=> 4017, 'msg'=>'超出限额!');
            $this->out_print($response);
        }
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if(empty($identity_result) || $identity_result['isvalidate'] == 0){
            $response = array('error' => 12000, 'msg' => '请先绑卡!');
            $this->out_print($response);
        }
//         if($identity_result['bankcode'] == 'CMBCHINA'){
//         		$response = array('error'=> 4025, 'msg'=>'招商银行渠道正在维护,请稍后再试!');
//         		$this->out_print($response);
//         }
        if($identity_result['bankcode'] == 'POST'){
        	if(NOW >= mktime(20,30,0)){
        		$response = array('error'=> 4025, 'msg'=>'20:30~00:40为邮政储蓄银行渠道维护时间,请您避开该时间段交易!');
        		$this->out_print($response);
        	}
        }
//         if($identity_result['bankcode'] == 'CMBC'){
//         	$start = strtotime('2016-11-13 00:00:01');
//         	$end   = strtotime('2016-11-13 12:30:00');
//         	if(NOW >= $start && NOW <= $end){
//         		$response = array('error'=> 4017, 'msg'=>'接民生银行渠道通知，为更好的服务于商户及客户，代收付系统将于2016年11月13日00:00-12:00进行系统巡检工作。');
//         		$this->out_print($response);
//         	}
//         }
//         if($identity_result['bankcode'] == 'CMBC'){
//         	$start = strtotime('2016-10-15 24:00:00');
//         	$end   = strtotime('2016-10-16 07:00:00');
//         	if(NOW >= $start && NOW <= $end){
//         		$response = array('error'=> 4025, 'msg'=>'2016-10-16 00:00~07:00 民生银行进行渠道维护,请您避开该时间段交易!');
//         		$this->out_print($response);
//         	}
//         }
        if($identity_result['cardno'] == '6217004150002630047'){
            $response = array('error' => 12001, 'msg' => '黑名单卡号!请联系客服!');
            $this->out_print($response);
        }
        $jytpay = new jytpay(false);

//         if(!in_array($identity_result['bankcode'], $this->jyt_open_bank)){
//             $response = array('error' => 12003, 'msg' => '安卓请下载最新版本,苹果可去XY助手下载最新版本!');
//             $this->out_print($response);
//         }
        $jytpay->pay();
    }
    
    //不发短信充值
    public function pay(){
        //error_reporting(E_ALL);
        $this->weihu();
        $response = array('error' => 12001, 'msg' => '易宝系统异常，请选择其它渠道!');
        $this->out_print($response);
        $amount = trim($this->input->post('amount'));
        //         if($amount < 50){
        //             $response = array('error'=> 4017, 'msg'=>'50起充!');
        //             $this->out_print($response);
        //         }
        if($amount > 100000){
            $response = array('error'=> 4017, 'msg'=>'超出限额!');
            $this->out_print($response);
        }
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if(empty($identity_result)){
            $response = array('error' => 12000, 'msg' => '请先进行绑卡操作!');
            $this->out_print($response);
        }
        //         $no_allow_bank = array('POST');
        //         if(in_array($identity_result['bankcode'], $no_allow_bank)){
        //             $response = array('error'=> 4017, 'msg'=>'该银行充值渠道正在维护中,具体时间请关注公告!');
        //             $this->out_print($response);
        //         }
        if($identity_result['bankcode'] == 'CMBCHINA'){
            $response = array('error' => 12001, 'msg' => '招行维护中，开放时间另行通知!');
            $this->out_print($response);
        }
        if($identity_result['bankcode'] == 'ABC'){
            $response = array('error' => 12001, 'msg' => '易宝农行系统异常，请选择其它渠道!');
            $this->out_print($response);
        }
        if($identity_result['cardno'] == '6217004150002630047'){
            $response = array('error' => 12001, 'msg' => '黑名单卡号!请联系客服!');
            $this->out_print($response);
        }
        
        $this->__pay($identity_result);
    }
    
    
    
    
    private function jyt_regist($jytpay){
        $cardno = trim($this->input->post('cardno'));          //银行卡号
        $idcardno = trim($this->input->post('idcardno'));      //身份证号
        $username = trim($this->input->post('name'));          //用户名
        $phone = trim($this->input->post('phone'));            //银行预留电话
        $bankCode = trim($this->input->post('bankid'));
        $zj = trim($this->input->post('zj'));
        if(empty($zj) || !in_array($zj, array('01','04','05'))){
            $zj = '01';
        }
        if(strlen($cardno) < 10){
            $response = array('error' => 11998, 'msg' => '错误的卡号');
            $this->out_print($response);
        }
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        if(!isset($bankCfg[$bankCode])){
            $response = array('error' => 11999, 'msg' => '暂不支持此银行!');
            $this->out_print($response);
        }
        $identityid = $this->uid;
        $requestid = $this->uid.date('Y-m-d').mt_rand(1000,9999);
        $userip = $this->getIP();                               //用户支付时使用的网络终端 IP
        $identity_result = $this->user_logic->getUserIdentityByIdcard($idcardno);
        
        //$balance = $this->balance_base->get_user_balance($this->uid);
        if($identity_result && $identity_result['ischeck']){
            $response = array('error' => 11999, 'msg' => '身份信息已存在');
            $this->out_print($response);
        }
        if($identity_result && $identity_result['cardno'] != $cardno){
            $response = array('error' => 12000, 'msg' => '只能重新绑定原卡');
            $this->out_print($response);
        }
        if(($identity_result && $identity_result['idCard'] != $idcardno) || ($identity_result && $identity_result['realname'] != $username)){
            $response = array('error' => 12000, 'msg' => '身份证与原预留信息不符');
            $this->out_print($response);
        }
        $ischeck = false;
        
        $orderid = '290060120005' . date('YmdHis') . rand(100000,999999);    //请根据商户系统自行定义订单号;
        $num = $this->bind_card_cd_base->get($this->uid);
        if($num > 5){
            $response = array('error' => 12001, 'msg' => '请稍后再试');
            $this->out_print($response);
        }
        $validate = $jytpay->validate($orderid, $cardno, $idcardno, $username, $phone);
        if((string)$validate->head->resp_code == 'S0000000'){
            //绑定卡
            $ischeck = true;
        }else{
            //次数+1 单用户不能超过5次   CD 24小时
            $this->bind_card_cd_base->incr($this->uid);
            $response = array('error' => 12001, 'msg' => '银行预留信息与输入信息不符');
            $this->out_print($response);
        }
    
        $yee_data = array();
        $yee_data['codesender'] = 'JYTPAY';
        $yee_data['requestid'] = time() . $this->uid;
        $yee_data['merchantaccount'] = "290060120005";
        
        $identity_data = array();
        $identity_data['realname'] = trim($username);
        $identity_data['idCard'] = $idcardno;
        $identity_data['cardno'] = $cardno;
        $identity_data['phone'] = $phone;
        $identity_data['bankcode'] = $bankCode;
        $identity_data['codesender'] = $yee_data['codesender'];
        $identity_data['requestid'] = $yee_data['requestid'];
        $identity_data['zj'] = $zj;
        if($ischeck == true){
            $identity_data['ischeck'] = 1;
            $identity_data['isvalidate'] = 1;
        }
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if($identity_result){
            $where = array('uid' => $this->uid);
            $rtn = $this->user_logic->updateUserIdentity($identity_data, $where);
            if(!$rtn){
                $response = array('error' => 12001, 'msg' => '用户信息更新失败');
                $this->out_print($response);
            }
            return $identity_result;
        }else{
            $identity_data['uid'] = $this->uid;
            
            $rtn = $this->user_logic->initUserIdentity($identity_data);
            if(!$rtn){
                $response = array('error' => 12001, 'msg' => '用户信息保存失败');
                $this->out_print($response);
            }
            return $identity_data;
        }
    }

    public function getMsgCode(){
        $bankCode = $this->input->post('bankid');
        if(!$bankCode){
            $response = array('error' => 12001, 'msg' => '请先选择银行!');
            $this->out_print($response);
        }
        $phone = $this->input->post('phone');
        if(!$phone){
            $response = array('error' => 12001, 'msg' => '请填写银行预留手机号!');
            $this->out_print($response);
        }
        //自己发验证短信
        $code = $this->user_logic->createMsgCode();
        //发送短信
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        $bankname = $bankCfg[$bankCode]['name'];
        $msg_value = array('code' => $code, 'bankname' => $bankname);
        $this->load->model('logic/msm_logic', 'msm_logic');
        $this->msm_logic->send_bindBank_code($phone, $msg_value);
        //设置发送短信手机
        $this->user_logic->setBindBankPhone($this->uid, $phone);
        $this->user_logic->setBindBankCode($this->uid, $code);
        
        $response = array('error'=> 0, 'data'=> array('message' => '手机验证码发送成功!'));
        $this->out_print($response);
    }
    
    

    public function regist(){
        $response = array('error' => 11999, 'msg' => '请下载最新客户端！');
        $this->out_print($response);

        $cardno = trim($this->input->post('cardno'));          //银行卡号
        $idcardno = trim($this->input->post('idcardno'));      //身份证号
        $username = trim($this->input->post('name'));          //用户名
        $phone = trim($this->input->post('phone'));            //银行预留电话
        $bankCode = trim($this->input->post('bankid'));
        
        if(empty($bankCode)){
            $response = array('error' => 11999, 'msg' => '请下载最新版本后绑卡');
            $this->out_print($response);
        }
        if(strlen($cardno) < 10){
            $response = array('error' => 11998, 'msg' => '错误的卡号');
            $this->out_print($response);
        }
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        if(!isset($bankCfg[$bankCode])){
            $response = array('error' => 11999, 'msg' => '暂不支持此银行!');
            $this->out_print($response);
        }
        $skill_check_bank = array('CMBC','BOCO','ECITIC','GDB','POST','CMBCHINA');
        $allow = true;
        if(in_array($bankCode, $skill_check_bank)){
            $allow = false;
        }
        $allow = false;
        $identityid = $this->uid;
        $requestid = $this->uid.date('Y-m-d').mt_rand(1000,9999);
        $userip = $this->getIP();                               //用户支付时使用的网络终端 IP
        $identity_result = $this->user_logic->getUserIdentityByIdcard($idcardno);
        if($identity_result && $identity_result['ischeck']){
            $response = array('error' => 11999, 'msg' => '身份信息已存在');
            $this->out_print($response);
        }
        if($identity_result && $identity_result['cardno'] != $cardno){
            $response = array('error' => 12000, 'msg' => '只能重新绑定原卡');
            $this->out_print($response);
        }
        //易宝绑卡
        if($allow == true){
            $yee_data = $this->yeepay_logic->bandcard($identityid, $requestid, $cardno, $idcardno, $username, $phone, $userip);
            if(isset($yee_data['error'])){
                //$response = array('error' => $data[1], 'msg' => $data[0]);
                $this->out_print($yee_data);
            }
        }else{  //模拟
            $yee_data = array();
            $yee_data['codesender'] = 'DXPAY';
            $yee_data['requestid'] = time() . $this->uid;
            $yee_data['merchantaccount'] = "10000419568";
            //自己发验证短信
            $code = $this->user_logic->createMsgCode();
            //发送短信
            $bankname = $bankCfg[$bankCode]['name'];
            $msg_value = array('code' => $code, 'bankname' => $bankname);
            $this->load->model('logic/msm_logic', 'msm_logic');
            $this->msm_logic->send_bindBank_code($phone, $msg_value);
            $this->user_logic->setBindBankCode($this->uid, $code);
        }
        $identity_data = array();
        $identity_data['realname'] = $username;
        $identity_data['idCard'] = $idcardno;
        $identity_data['cardno'] = $cardno;
        $identity_data['phone'] = $phone;
        $identity_data['bankcode'] = $bankCode;
        $identity_data['codesender'] = $yee_data['codesender'];
        $identity_data['requestid'] = $yee_data['requestid'];
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if($identity_result){
            $where = array('uid' => $this->uid);
            $rtn = $this->user_logic->updateUserIdentity($identity_data, $where);
            if(!$rtn){
                $response = array('error' => 12001, 'msg' => '用户信息更新失败');
                $this->out_print($response);
            }
        }else{
            $identity_data['uid'] = $this->uid;
            $rtn = $this->user_logic->initUserIdentity($identity_data);
            if(!$rtn){
                $response = array('error' => 12001, 'msg' => '用户信息保存失败');
                $this->out_print($response);
            }
        }
        $response = array('error' => 0, 'data' => $yee_data);
        $this->out_print($response);
    }
    
    public function regist_check(){
        $requestid = trim($this->input->post('requestid'));     //绑卡订单号
        $validatecode = trim($this->input->post('validatecode'));         //短信验证码
        $bankCode = trim($this->input->post('bankid'));
    
        $identity_result = $this->user_logic->getUserIdentityByRequestId($requestid);
        if(!$identity_result){
            $response = array('error' => 12000, 'msg' => '订单请求号不存在!');
            $this->out_print($response);
        }
        $skill_check_bank = array('CMBC','BOCO','ECITIC','GDB','POST','CMBCHINA');
        $allow = true;
        if(in_array($bankCode, $skill_check_bank)){
            $allow = false;
        }
        $allow = false;
        if($allow){
            $yee_data = $this->yeepay_logic->bindBankcardConfirm($requestid, $validatecode);
            if(isset($yee_data['error'])){
                $this->out_print($yee_data);
            }
        }else{
            //验证短信
            $code = $this->user_logic->getBindBankCode($this->uid);
            if($validatecode != $code){
                $response = array('error' => 12001, 'msg' => '验证码错误');
                $this->out_print($response);
            }
            $yee_data = array();
            $yee_data['bankcode'] = $bankCode;
        }
    
        //        $yee_data['bankcode'] = 'ICBC';
    
        $identity_data = array('ischeck' => 1, 'bankcode' => $yee_data['bankcode']);
        $where = array('uid' => $this->uid);
    
        $rtn = $this->user_logic->updateUserIdentity($identity_data, $where);
        if(!$rtn){
            $response = array('error' => 12001, 'msg' => '用户信息保存失败');
            $this->out_print($response);
        }
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $data['identity'] = $this->user_identity_logic->getPublicUserIdentity($this->uid);
        $this->load->model('base/balance_base' , 'balance_base');
        $data['balance'] = $this->balance_base->get_user_balance($this->uid);
        $data['server_time'] = NOW;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    
    /*
    0：IMEI
    1：MAC 地址
    2：用户ID
    3：用户Email
    4：用户手机号
    5：用户身份证号
    6：用户纸质订单协议号    新版检查是否有绑卡
     */
    public function yee_bind_check(){       //status 1易宝绑过卡   0易宝没绑卡
        $return_data = array('status' => 1);
        $identity_info = $this->user_logic->getUserIdentity($this->uid);
        if(empty($identity_info)){
            $response = array('error' => 11999, 'msg' => '请先完成绑卡操作');
            $this->out_print($response);
        }
        if($identity_info['bindyee'] == 1){
            $response = array('error' => 0, 'data' => $return_data);
            $this->out_print($response);
        }
        $yee_data = $this->yeepay_logic->bankcardList($this->uid, 2);
//         if($this->uid == 120846){
//             print_r($yee_data);
//         }
        if($yee_data['error'] || empty($yee_data['cardlist'])){
            $return_data['status'] = 0;
        }else{
            //更新user_identity字段
            $identity_data = array();
            $identity_data['bindyee'] = 1;
            $where = array('uid' => $this->uid);
            $this->user_logic->updateUserIdentity($identity_data, $where);
        }
        
        $response = array('error' => 0, 'data' => $return_data);
        $this->out_print($response);
    }
    
    //新版绑卡
    public function regist_yee(){
        $identity_info = $this->user_logic->getUserIdentity($this->uid);
        if(empty($identity_info)){
            $response = array('error' => 11999, 'msg' => '请先完成绑卡操作');
            $this->out_print($response);
        }
        if($identity_info['bindyee'] == 1){
            $response = array('error' => 11999, 'msg' => '已绑定!');
            $this->out_print($response);
        }
        $cardno = $identity_info['cardno'];
        $idcardno = $identity_info['idCard'];
        $username = $identity_info['realname'];
        $phone = $identity_info['phone'];
        $identityid = $this->uid;
        $requestid = $this->uid.date('Y-m-d').mt_rand(1000,9999);
        $userip = $this->getIP();                               //用户支付时使用的网络终端 IP
        //易宝绑卡
        $yee_data = $this->yeepay_logic->bandcard($identityid, $requestid, $cardno, $idcardno, $username, $phone, $userip);
        if(isset($yee_data['error'])){
            $response = array('error' => $yee_data['error'], 'msg' => $yee_data['msg']);
            $this->out_print($response);
        }
        $response = array('error' => 0, 'data' => $yee_data);
        $this->out_print($response);
    }
    
    //新牌绑卡确定 
    public function regist_yee_check(){
        $requestid = trim($this->input->post('requestid'));     //绑卡订单号
        $validatecode = trim($this->input->post('validatecode'));         //短信验证码
        if(!$validatecode){
            $response = array('error' => 11999, 'msg' => '请输入短信验证码');
            $this->out_print($response);
        }
        $yee_data = $this->yeepay_logic->bindBankcardConfirm($requestid, $validatecode);
        if(isset($yee_data['error'])){
            $response = array('error' => $yee_data['error'], 'msg' => $yee_data['msg']);
            $this->out_print($response);
        }
        $identity_data = array();
        $identity_data['bindyee'] = 1;
        $where = array('uid' => $this->uid);
        $this->user_logic->updateUserIdentity($identity_data, $where);
        $response = array('error'=> 0, 'data'=> array('status' => 1));
        $this->out_print($response);
    }
    
    //注册确认
    private function __pay($identity_result){
        $amount = trim($this->input->post('amount'));
        $amount = $amount * 100;
        $tpwd = trim($this->input->post('tpwd'));
        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        if($cd_data['pay'] <= 0){
            $response = array('error'=> 4018, 'msg'=>'今日充值次数已达上限');
            $this->out_print($response);
        }
        if(empty($identity_result)){
            $response = array('error' => 12001, 'msg' => '用户信息保存失败');
            $this->out_print($response);
        }
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        if($identity_result['tpwd'] != $tpwd){
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $orderid = date('YmdHis').$this->uid. mt_rand(1000, 9999);
        $card_top = substr($identity_result['cardno'], 0, 6);
        $card_last = substr($identity_result['cardno'], -4);
        $orderexpdate = 10;             //订单有效期10分钟
        $productname = '充值';
        $userip = $this->getIP();
        $transtime = NOW;
        $identityid = $this->uid;
        $money = $amount / 100;
        //创建购买订单
        $pay_log = array(
            'uid' => $this->uid,
            'ordid' => $orderid,
            'amt' => $money,
            'platform' => 'yeepay',
            'curcode' => 'RMB',
            'ctime' => time(),
            'status' => 0,
        );
        $this->load->model('base/pay_log_base' , 'pay_log');
        $this->pay_log->createOrder($pay_log);
    
        $yee_data = $this->yeepay_logic->directPayment($orderid, $transtime, $amount, $productname, $identityid, $card_top, $card_last, $orderexpdate, $userip);
        if(isset($yee_data['error'])){
            $this->out_print($yee_data);
        }
        $rtn = array();
        $response = array('error' => 0, 'data' => $yee_data);
        $this->out_print($response);
    }
    
    public function queryOrder(){
        $qudao = 'JYT';
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if(in_array($identity_result['bankcode'], $this->yee_pay_bank)){
            $qudao = 'YEE';
        }
        if(PAY_PLAT == 'JYT' && $qudao == 'JYT'){
            $jytpay = new jytpay(false);
            $jytpay->queryOrder();
        }else{
            $this->__queryOrder();
        }
    }
    
    
    public function yee_queryOrder(){
        $order = $this->input->post('orderid');
        if(!$order){
            $response = array('error' => 32001, 'msg' => '错误的订单号');
            $this->out_print($response);
        }
        $yee_data = $this->yeepay_logic->queryOrder($order);
        $error_msg = isset($yee_data['errormsg']) ? $yee_data['errormsg'] : '';
        $check = 0;
        if($yee_data['status'] == 1){       //如果返回成功
            //查询后台钱到了没
            $this->load->model('base/pay_log_base', 'pay_log_base');
            $orderInfo = $this->pay_log_base->getLogByOrdid($order);
            if($orderInfo['status'] == 0){
                $yee_data['status'] = 3;
            }else{
                $this->load->model('logic/cd_logic', 'cd_logic');
                $cd_data = $this->cd_logic->getUserCd($this->uid);
                $cd_data['pay']--;
                $this->cd_logic->setUserCd($this->uid, $cd_data);
            }
            $check = 1;
        }
        $response = array('error' => 0, 'data' => array('status' => $yee_data['status'], 'errormsg' => $error_msg, 'check' => $check));
        $this->out_print($response);
    }
    
    //不发短信充值
    public function withDraw(){

//         $response = array('error'=> 4017, 'msg'=>'取现升级维护中！请稍后再试!');
//         $this->out_print($response);

        if(NOW <= mktime(0,40,0)){
    		$response = array('error'=> 4025, 'msg'=>'00:00~00:40为银行渠道维护时间,请您避开该时间段交易!');
    		$this->out_print($response);
    	}
        $this->weihu();
        $flag = $this->pay_redis_base->getWithdraw();
        if($flag){
        	$weehours = new weehours_withdraw(false);
            $weehours->new_save_withdraw_order();
        }
        
//        $start = strtotime('2016-12-30 17:00:00');
//        $end = strtotime('2017-01-08 24:00:00');
//        if(NOW >= $start && NOW <= $end){
//            $weehours = new weehours_withdraw(false);
//            $weehours->new_save_withdraw_order();
//        }
        
//         if(NOW >= $start && NOW <= $end){
//             $weehours = new weehours_withdraw(false);
//             $weehours->new_save_withdraw_order();
//             exit;
//         }
//         if(date('w') == 6 || date('w') == 0 ){      //星期六和星期天
//             $weehours = new weehours_withdraw(false);
//             $weehours->new_save_withdraw_order();
//         }
//         if($this->uid == 23){
//             $baofoopay = new baofoopay(false);
//             $baofoopay->__withDraw();
//         }

//        $amount = trim($this->input->post('amount'));
//        if($amount>=8000){
//        	$weehours = new weehours_withdraw(false);
//        	$weehours->new_save_withdraw_order();
//        }
        
        $fuioupay = new fuioupay(false);
        $fuioupay->__withDraw();
    }
    
//     private function __withDraw(){
//         die('接口禁用');
//         if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
//             die('内网不能取现');
//         }else if((isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '117.50.2.20')
//             || (isset($_SERVER['HOSTNAME']) && $_SERVER['HOSTNAME'] == '120-132-48-121')){
//             die('测试环境不能取现');
//         }
//         //         $response = array('error' => 4019, 'msg' => '工作日方能提现');
//         //         $this->out_print($response);
//         $amount = trim($this->input->post('amount'));
//         if(!$amount || $amount <= 0){
//             $response = array('error'=> 4017, 'msg'=>'请输入取现金额');
//             $this->out_print($response);
//         }
//         $this->load->model('logic/cd_logic', 'cd_logic');
//         $cd_data = $this->cd_logic->getUserCd($this->uid);
//         if($cd_data['withDraw'] <= 0){
//             $response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
//             $this->out_print($response);
//         }
//         $this->cd_logic->setUserCd($this->uid, $cd_data);
//         $money = $amount;
//         $amount = $amount * 100;
//         $tpwd = trim($this->input->post('tpwd'));
//         $this->load->model('base/user_identity_base', 'user_identity_base');
//         $identity_result = $this->user_logic->getUserIdentity($this->uid);
//         if(empty($identity_result)){
//             $response = array('error' => 4019, 'msg' => '用户信息错误');
//             $this->out_print($response);
//         }
//         if(!$identity_result || !$identity_result['tpwd']){
//             $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
//             $this->out_print($response);
//         }
//         if($identity_result['tpwd'] != $tpwd){
//             $response = array('error'=> 4021, 'msg'=>'交易密码错误');
//             $this->out_print($response);
//         }
//         $this->load->model('base/balance_base', 'balance_base');
//         $balance = $this->balance_base->get_user_balance($this->uid);
//         if($balance < $money){
//             $response = array('error' => 12002, 'msg' => '用户余额不足');
//             $this->out_print($response);
//         }
//         $card_top = substr($identity_result['cardno'], 0, 6);
//         $card_last = substr($identity_result['cardno'], -4);
//         $userip = $this->getIP();
    
// //         $this->load->model('base/user_identity_base', 'user_identity_base');
// //         $user_identity = $this->user_identity_base->getUserIdentity($this->uid);
// //         if($user_identity['plat'] == 'invite' && $user_identity['isnew'] == 1){
// //             $cd_data = $this->load->model('base/user_base','user_base');
// //             $account = $this->user_base->getAccountInfo($this->uid);
// //             //取活动配置
// //             //对比活动配置的开始时间和用户注册时间
// //             //取用户余额
// //             //如果用户余额-取现金额 小于 10元  提示 不能取现
// //         }

//         $ret = $this->balance_base->cost_user_balance($this->uid, $money);
//         if(!$ret){
//             $err_data = array();
//             $err_data['uid'] = $this->uid;
//             $err_data['pid'] = 0;
//             $err_data['ptype'] = 'product';
//             $err_data['money'] = $money;
//             $err_data['balance'] = $balance;
//             $response = array('error'=> 3333, 'msg'=> '余额不足');
//             $this->out_print($response, 'json',  true,  true, $err_data);
//         }
//         $yee_data = $this->yeepay_logic->withdraw($orderid, $this->uid, $card_top, $card_last, $amount, $userip);
//         if(isset($yee_data['error'])){
//             $err_data = array();
//             $err_data['uid'] = $this->uid;
//             $err_data['money'] = $money;
//             $err_data['balance'] = $balance - $money;
//             $err_data['method'] = __METHOD__;
//             $this->balance_base->add_user_balance($this->uid, $money, true);
//             $this->out_print($yee_data, 'json',  true,  true, $err_data);
//         }
    
//         //写用户日志
//         $user_log_data = array(
//             'uid' => $this->uid,
//             'pid' => 0,
//             'pname' => '取现处理中',
//             'orderid' => $orderid,
//             'money' => $money,
//             'balance' => $balance - $money,
//             'action' => USER_ACTION_PCASHOUT
//         );
//         $this->load->model('base/user_log_base', 'user_log_base');
//         $last_id = $this->user_log_base->addUserLog($this->uid, $user_log_data);
    
//         //系统取现日志
//         $this->load->model('base/withdraw_log_base', 'withdraw_log');
//         $withdraw_log = array();
//         $withdraw_log['uid'] = $this->uid;
//         $withdraw_log['orderid'] = $orderid;
//         $withdraw_log['ybdrawflowid'] = $yee_data['ybdrawflowid'];
//         $withdraw_log['status_code'] = $yee_data['status'];
//         $withdraw_log['money'] = $money;
//         $withdraw_log['logid'] = $last_id;
//         $this->withdraw_log->addLog($withdraw_log);
    
//         $response = array('error' => 0, 'data' => array('balance' => $balance - $money));
//         $this->out_print($response);
//     }
    
    public function withdrawQuery(){
        $orderid = $this->input->post('orderid');
        $ybdrawflowid = $this->input->post('ybdrawflowid');
        $yee_data = $this->yeepay_logic->withdrawQuery($orderid, $ybdrawflowid);
        $response = array('error' => 0, 'data' => $yee_data);
        $this->out_print($response);
    }
    
    
    public function longmoneyToBalance(){
        if(NOW <= mktime(2,0,0)){
            $response = array('error' => 4019, 'msg' => '0：00-2：00为系统结算时间，请您稍后再操作！');
            $this->out_print($response);
        }
        $out_money = $this->input->post('longmoney');
        $type = $this->input->post('type');
        $all_type = array(1, 2);
        if(!in_array($type, $all_type)){
            $type = 1;
        }
        if(!$out_money || $out_money <= 0){
            $response = array('error' => 4019, 'msg' => '超过单次转出限额!');
            $this->out_print($response);
        }
        $tpwd = $this->input->post('tpwd');
        if($out_money > 20000){
            $response = array('error' => 4019, 'msg' => '超过单次转出限额!');
            $this->out_print($response);
        }
        //新用户第一次转出
        $this->load->model('base/longmoney_base', 'longmoney_base');
        $this->load->model('base/user_log_base', 'user_log_base');
        $fristtime = $this->longmoney_base->getFirstTime($this->uid);
        $fristto = $this->user_log_base->_get_db_UserLog($this->uid,array(),array('action' => USER_ACTION_LONGTOBALANCE));
        if (!$fristto && $fristtime && NOW < strtotime('+1 days', $fristtime)){
            $response = array('error'=> 4090, 'msg'=>'首次可转出的时间是'.date('Y-m-d H:i:s', strtotime('+1 days', $fristtime)));
            $this->out_print($response);
        }

        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        if($cd_data['longmoneyToBalance'] <= 0){
            $response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
            $this->out_print($response);
        }
        $cd_data['longmoneyToBalance']--;
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if(empty($identity_result)){
            $response = array('error' => 4019, 'msg' => '用户信息错误');
            $this->out_print($response);
        }
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        
        $tpwd_times = $this->pay_redis_base->getwithdrawtpwdtimes($identity_result['phone']);
        if((!empty($tpwd_times))&&$tpwd_times>=3){
        	$response = array('error'=> 4040, 'msg'=>'支付密码已尝试3次，请3小时后再试');
        	$this->out_print($response);
        }
        
        
        if($identity_result['tpwd'] != $tpwd){
        	$this->pay_redis_base->incrwithdrawtpwdtimes($identity_result['phone']);
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->cd_logic->setUserCd($this->uid, $cd_data);
    
        //检查活期存额够不够
        $this->load->model('logic/longproduct_logic', 'longproduct_logic');
        $longmoney = $this->longproduct_logic->getLongmoney($this->uid);
        if($out_money > $longmoney){
            $response = array('error'=> 13001, 'msg' => '金额不足');
            $this->out_print($response);
        }
    
        $ordid = 'ltb' . $this->uid . time() . mt_rand(1000,9999);
        $out_money = number_format($out_money, 2, '.', '');
    
        $this->load->model('base/longtobalance_log_base', 'longtobalance_log_base');
        $longtobalance_log = array(
            'uid' => $this->uid,
            'ordid' => $ordid,
            'amt' => $out_money,
            'platform' => 'yee',
            'curcode' => 'RMB',
            'ctime' => time(),
            'status' => 1,
        );
        $ret = $this->longtobalance_log_base->createOrder($longtobalance_log);
        if(!$ret){
            $response = array('error'=> 13002, 'msg' => '订单创建失败');
            $this->out_print($response);
        }
        $cost_ret = $this->longproduct_logic->cost_longmoney($this->uid, $out_money);
        if(!$cost_ret){
            $response = array('error'=> 13002, 'msg' => '扣除活期余额失败');
            $this->out_print($response);
        }
    
        $this->load->model('logic/balance_logic' , 'balance_logic');
        $ret = $this->balance_logic->add_user_balance($this->uid, $out_money);
        $balance = $this->balance_logic->get_balance($this->uid);
        if(!$ret){
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['pid'] = 88;
            $err_data['ptype'] = 'longtobalance';
            $err_data['money'] = $out_money;
            $err_data['balance'] = $balance;
            $response = array('error'=> 13002, 'msg' => '添加至余额失败');
            $this->out_print($response, 'json',  true,  true, $err_data);
        }
        //写用户日志
        $user_log_data = array(
            'uid' => $this->uid,
            'money' => $out_money,
            'pname' => '活期转出',
            'balance' => $balance,
            'orderid' => $ordid,
            'action' => USER_ACTION_LONGTOBALANCE,              //活期转出到余额
            'desc' => $type                                     //1活期转出到余额  2到银行卡
        );

        $userlogid = $this->user_log_base->addUserLog($this->uid, $user_log_data);
        $rtn = array();
        $rtn['balance'] = $balance;
        $rtn['longmoney'] = round($longmoney - $out_money, 2);
        $rtn['userlogid'] = $userlogid;
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    
    
    public function klmoneyToBalance(){
        if(NOW <= mktime(2,0,0)){
            $response = array('error' => 4019, 'msg' => '0-2点快乐宝结算期间，不能转出!');
            $this->out_print($response);
        }
        $out_money = $this->input->post('klmoney');
        $type = $this->input->post('type');
        $tpwd = $this->input->post('tpwd');
        $all_type = array(1, 2);
        if(!in_array($type, $all_type)){
            $type = 1;
        }
        if(!$out_money || $out_money <= 0){
            $response = array('error' => 4010, 'msg' => '超过单次转出限额!');
            $this->out_print($response);
        }
        if($out_money > 20000){
            $response = array('error' => 4019, 'msg' => '超过单次转出限额!');
            $this->out_print($response);
        }
        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        if($cd_data['longmoneyToBalance'] <= 0){
            $response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
            $this->out_print($response);
        }
        $cd_data['longmoneyToBalance']--;
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if(empty($identity_result)){
            $response = array('error' => 4019, 'msg' => '用户信息错误');
            $this->out_print($response);
        }
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        if($identity_result['tpwd'] != $tpwd){
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->cd_logic->setUserCd($this->uid, $cd_data);
    
        //检查快乐宝存额够不够
        $this->load->model('logic/klproduct_logic', 'klproduct_logic');
        $klmoney = $this->klproduct_logic->getklmoney($this->uid);
        if($out_money > $klmoney){
            $response = array('error'=> 13001, 'msg' => '金额不足');
            $this->out_print($response);
        }
    
        $ordid = 'kltb' . $this->uid . time() . mt_rand(1000,9999);
        $out_money = number_format($out_money, 2, '.', '');
    
        $this->load->model('base/kltobalance_log_base', 'kltobalance_log_base');
        $kltobalance_log = array(
            'uid' => $this->uid,
            'ordid' => $ordid,
            'amt' => $out_money,
            'platform' => 'yee',
            'curcode' => 'RMB',
            'ctime' => time(),
            'status' => 1,
        );
        $ret = $this->kltobalance_log_base->createOrder($kltobalance_log);
        if(!$ret){
            $response = array('error'=> 13002, 'msg' => '订单创建失败');
            $this->out_print($response);
        }
        $cost_ret = $this->klproduct_logic->cost_klmoney($this->uid, $out_money);
        if(!$cost_ret){
            $response = array('error'=> 13002, 'msg' => '扣除快乐宝余额失败');
            $this->out_print($response);
        }
    
        $this->load->model('logic/balance_logic' , 'balance_logic');
        $ret = $this->balance_logic->add_user_balance($this->uid, $out_money);
        $balance = $this->balance_logic->get_balance($this->uid);
        if(!$ret){
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['pid'] = 88;
            $err_data['ptype'] = 'kltobalance';
            $err_data['money'] = $out_money;
            $err_data['balance'] = $balance;
            $response = array('error'=> 13002, 'msg' => '添加至余额失败');
            $this->out_print($response, 'json',  true,  true, $err_data);
        }
        //写用户日志
        $user_log_data = array(
            'uid' => $this->uid,
            'money' => $out_money,
            'pname' => '快乐宝转出',
            'balance' => $balance,
            'orderid' => $ordid,
            'action' => USER_ACTION_KLTOBALANCE,              //活期转出到余额
            'desc' => $type                                     //1活期转出到余额  2到银行卡
        );
        $this->load->model('base/user_log_base', 'user_log_base');
        $this->user_log_base->addUserLog($this->uid, $user_log_data);
        $rtn = array();
        $rtn['balance'] = $balance;
        $rtn['klmoney'] = round($klmoney - $out_money, 2);
        $response = array('error'=> 0, 'data'=> $rtn);
        $this->out_print($response);
    }
    public function verfifytpwd(){
    	$tpwd = trim($this->input->post('tpwd'));
    	 
    	$identity_result = $this->user_logic->getUserIdentity($this->uid);
    	if(empty($identity_result) || $identity_result['ischeck'] != 1){
    		$response = array('error'=> 4000, 'msg'=>'请先绑定银行卡信息');
    		$this->out_print($response);
    	}
    	if(!$identity_result || !$identity_result['tpwd']){
    		$response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
    		$this->out_print($response);
    	}
    	$this->load->model('base/pay_redis_base', 'pay_redis_base');
    	$tpwd_times = $this->pay_redis_base->getpaytpwdtimes($identity_result['phone']);
    	if((!empty($tpwd_times))&&$tpwd_times>=3){
    		$response = array('error'=> 4040, 'msg'=>'支付密码已尝试3次，请3小时后再试');
    		$this->out_print($response);
    	}
    	 
    	if($identity_result['tpwd'] != $tpwd){
    		$this->pay_redis_base->incrpaytpwdtimes($identity_result['phone']);
    		$response = array('error'=> 4021, 'msg'=>'交易密码错误');
    		$this->out_print($response);
    	}
    	$response = array('error'=> 0, 'msg'=>'密码正确');
    	$this->out_print($response);
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */