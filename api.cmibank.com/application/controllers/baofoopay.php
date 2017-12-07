<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class baofoopay extends Controller {

    private $pay_notify_url;
    private $withdraw_notify_url;
    
    private $diff_bankcode_map = array(
            'BOCO' => 'BCOM',
            'ECITIC' => 'CITIC',
            'PINGAN' => 'PAB',
            'POST' => 'PSBC',
            'CMBCHINA' => 'CMB',
        );
    
    public function __construct($lock = true)
    {
        parent::__construct();
        //$this->check_link();
        $this->check_login($lock);
        $this->pay_notify_url = DOMAIN . 'baofoopay_notify/pay_notify';
        $this->withdraw_notify_url = DOMAIN . 'baofoopay_notify/withdraw_notify';
    }

    public function pay_ready(){       
    	
    	if(NOW <= mktime(0,40,0)){
    		$response = array('error'=> 4025, 'msg'=>'00:00~00:40为银行渠道维护时间,请您避开该时间段交易!');
    		$this->out_print($response);
    	}
    	
    	$this->load->model('logic/cd_logic', 'cd_logic');
    	$cd_data = $this->cd_logic->getUserCd($this->uid);
    	if($cd_data['pay'] <= 0){
    		$response = array('error'=> 4018, 'msg'=>'今日充值次数已达上限');
    		$this->out_print($response);
    	}
    	
        require (APPPATH . 'config/cfg/baofoo_ini.php');
        $request_url = "https://gw.baofoo.com/apipay/sdk";  //SDK尊享版请求地址
        
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all');
        
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error' => 11999, 'msg' => '请先进行绑卡操作!');
            $this->out_print($response);
        }
        $tpwd = trim($this->input->post('tpwd'));
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
        $this->pay_redis_base->delpaytpwdtimes($identity_result['phone']);
        
        $txn_amt = trim($this->input->post('money'));//交易金额
        if($txn_amt < 1){
            $response = array('error'=> 4023, 'msg'=>'充值金额错误');
            $this->out_print($response);
        }
        
        $orderid = date("YmdHis") . $this->uid . mt_rand(100000,999999);
        
        $couponId = $this->input->post('cid');
        $ptid = $this->input->post('ptid');
        if(!empty($couponId)){
        	$this->load->model('base/user_coupon_base' , 'user_coupon_base');
        	$conpon = $this->user_coupon_base->getUserCouponDetail($this->uid,$couponId);
        	if(empty($conpon)){
        		$response = array('error'=> 6060, 'msg'=>'抵用券不可用');
        		$this->out_print($response);
        	}
        	if($conpon['etime']<NOW){
        		$response = array('error'=> 6061, 'msg'=>'抵用券已过期');
        		$this->out_print($response);
        	}
        	if($conpon['stime']>NOW){
        		$response = array('error'=> 6062, 'msg'=>'抵用券暂不可用');
        		$this->out_print($response);
        	}
        	if(!empty($conpon['utime'])){
        		$response = array('error'=> 6063, 'msg'=>'抵用券不可用');
        		$this->out_print($response);
        	}
        	$ptidArray = explode(",", $conpon['ptids']);
        	if(!in_array($ptid,$ptidArray)){
        		$response = array('error'=> 6064, 'msg'=>'抵用券不适用该产品');
        		$this->out_print($response);
        	}
        }
        
        $txn_amt = $txn_amt * 100;
        $id_card = $identity_result['idCard'];                        //身份证号
        $id_holder = $identity_result['realname'];                    //银行卡用户名
        $mobile = $identity_result['phone'];	                      //持卡人手机号
        $acc_no = $identity_result['cardno'];	                      //银行卡号
        
        $pay_code = $identity_result['bankcode'];	                      //银行编码
//         if($identity_result['fengkong'] == 1){
//             $response = array('error' => 12888, 'msg' => '已列为风险账户，请联系管理员');
//             $this->out_print($response);
//         }
//         if(empty($identity_result['last_cardno'])){
//             $update_data = array();
//             $update_data['last_cardno'] = $acc_no;
//             $where = array();
//             $where['uid'] = $this->uid;
//             $this->user_logic->updateUserIdentity($update_data, $where);
//         }else if($identity_result['last_cardno'] != $acc_no){   //应对有BUG API可换卡的情况
//             $update_data = array();
//             $update_data['fengkong'] = 1;
//             $where = array();
//             $where['uid'] = $this->uid;
//             $this->user_logic->updateUserIdentity($update_data, $where);
//             $response = array('error' => 12999, 'msg' => '已列为风险账户，请联系管理员');
//             $this->out_print($response);
//         }
        if(isset($this->diff_bankcode_map[$pay_code])){
            $pay_code = $this->diff_bankcode_map[$pay_code];
        }
        $txn_sub_type = '02';
        //ob_start (); //打开缓冲区
        $arr = array (
            'txn_sub_type' => $txn_sub_type,          //SDK交易类型为02
            'biz_type' => "0000",
            'terminal_id' => $terminal_id,
            'member_id' => $member_id,
            'pay_code' => $pay_code,
            'acc_no' => $acc_no,
            'id_card_type' => "01",
            'id_card' => $id_card,
            'id_holder' => $id_holder,
            'mobile' => $mobile,
            'trans_id' => $orderid,
            'txn_amt' => $txn_amt,
            'trade_date' => date('YmdHis'),
            'return_url' => $this->pay_notify_url
        );
        
        $baofoosdk = new BaofooSdk($pfxfilename,$cerfilename,$private_key_password); //初始化加密类。
        
        if($data_type == "json"){
            $Encrypted_string = str_replace("\\/", "/",json_encode($arr));//转JSON
        }else{
            $toxml = new SdkXML();
            $Encrypted_string = $toxml->toXml($arr);//转XML
        }
        Log::LogWirte("请求的明文：".$Encrypted_string); //记录密文
        $Encrypted = $baofoosdk->encryptedByPrivateKey($Encrypted_string);	//先BASE64进行编码再RSA加密
//         Log::LogWirte("请求密文：".$Encrypted); //记录密文
        $ApiPostData["txn_sub_type"] = $txn_sub_type;
        $ApiPostData["data_content"] = $Encrypted;
        $Result = HttpClient::Post($ApiPostData, $request_url);//发送请求并接收结果
        Log::LogWirte("返回结果：".$Result);
        $return_data = json_decode($Result, true);
        if($return_data['retCode'] != '0000'){
            $response = array('error'=> 0, 'data' => $return_data);
            $this->out_print($response);
        }
        $return_data['orderid'] = $orderid;
        //创建购买订单
        $pay_log = array(
            'uid' => $this->uid,
            'ordid' => $orderid,
            'trxid' => $return_data['tradeNo'],
            'amt' => $txn_amt/100,
            'platform' => 'baofoo',
            'curcode' => 'RMB',
            'ctime' => NOW,
            'status' => 0,
        );
        $this->load->model('base/pay_log_base' , 'pay_log');
        $this->pay_log->createOrder($pay_log);
        $response = array('error'=> 0, 'data' => $return_data);
        $this->out_print($response);
    }
    
    public function __withDraw(){
        error_reporting(E_ALL);
        
        $cost_money = $money = trim($this->input->post('amount'));               //取现金额
        if(!$money || $money <= 0){
            $response = array('error'=> 4017, 'msg'=>'请输入取现金额');
            $this->out_print($response);
        }
        if($cost_money > 500000){
        	$response = array('error'=> 4017, 'msg'=>'单笔提现不能高于50万');
        	$this->out_print($response);
        }
        if($money < 1){
        	$response = array('error'=> 4017, 'msg'=>'最低取现1元');
        	$this->out_print($response);
        }
        
       	$tpwd = trim($this->input->post('tpwd'));
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $identity_result = $this->user_identity_base->getUserIdentity($this->uid);
        if(empty($identity_result)){
            $response = array('error' => 4019, 'msg' => '用户信息错误');
            $this->out_print($response);
        }
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        if($identity_result['fengkong'] == 1){
        	$response = array('error'=> 4001, 'msg'=>'当前账户交易异常已被冻结，如有疑问，请致电客服');
        	$this->out_print($response);
        }
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $tpwd_times = $this->pay_redis_base->getwithdrawtpwdtimes($identity_result['phone']);
        if((!empty($tpwd_times))&&$tpwd_times>=3){
        	$response = array('error'=> 4040, 'msg'=>'支付密码已尝试3次，请3小时后再试');
        	$this->out_print($response);
        }
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
        	$response = array('error'=> 4000, 'msg'=>'请先绑定银行卡信息');
        	$this->out_print($response);
        }
        if($identity_result['isvalidate'] == 0){
        	$response = array('error'=> 4030, 'msg'=>'请先绑卡');
        	$this->out_print($response);
        }
        if($identity_result['tpwd'] != $tpwd){
        	$this->pay_redis_base->incrwithdrawtpwdtimes($identity_result['phone']);
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->pay_redis_base->delwithdrawtpwdtimes($identity_result['phone']);
        
        $this->load->model('base/balance_base', 'balance_base');
        $balance = $this->balance_base->get_user_balance($this->uid);
        if($balance < $cost_money){
        	$response = array('error' => 12002, 'msg' => '用户余额不足');
        	$this->out_print($response);
        }
        
//         $ret = $this->check_withdraw_money($this->uid, $cost_money);
//         if(!$ret){
//         	$response = array('error' => 12006, 'msg' => '账目金额不符,请联系客服!');
//         	$this->out_print($response);
//         }
        $orderid = 'bf'.date('YmdHis') . $this->uid . mt_rand(1000,9999);
        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        if($cd_data['withDraw'] <= 0){
        	$response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
        	$this->out_print($response);
        }
        
        if($cd_data['free_withDraw'] > 0){      //如果有免费次数就减免费次数
        	$cd_data['free_withDraw']--;
        }else{                                  //如果没有就扣手续费
        	$money -= WITHDRAW_SXF;
        	if($money <= 0){
        		$response = array('error'=> 4118, 'msg'=>'您当前账户余额不足' . WITHDRAW_SXF . '元, 无法提现, 请明日重试');
        		$this->out_print($response);
        	}
        	//记录手续费情况
        	$this->load->model('base/withdraw_sxf_log_base', 'withdraw_sxf_log');
        	$withdraw_sxf_log = array(
        			'uid' => $this->uid,
        			'orderid' => $orderid,
        			'money' => $cost_money,
        			'sxf' => WITHDRAW_SXF,
        			'sd_money' => $money,
        			'ctime' => time()
        	);
        	$this->withdraw_sxf_log->createLog($withdraw_sxf_log);
        }
        
        $cd_data['withDraw']--;
        $this->cd_logic->setUserCd($this->uid, $cd_data);
        
        $id_num = strtoupper($identity_result['idCard']);
        $id_name = $identity_result['realname'];
        $bank_code = $identity_result['bankcode'];
        $mobile = $identity_result['phone'];
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        if(!isset($banklist[$bank_code])){
        	$response = array('error'=> 4001, 'msg'=>'暂不支持此银行');
        	$this->out_print($response);
        }
        $bank_name = $banklist[$bank_code]['name'];
        $account_no = $identity_result['cardno'];
        $is_failed = false;
        $ret = $this->balance_base->cost_user_balance($this->uid, $cost_money);
        if(!$ret){
        	$err_data = array();
        	$err_data['uid'] = $this->uid;
        	$err_data['pid'] = 0;
        	$err_data['ptype'] = 'product';
        	$err_data['money'] = $cost_money;
        	$err_data['balance'] = $balance;
        	$response = array('error'=> 3333, 'msg'=> '余额不足');
        	$this->out_print($response, 'json',  true,  true, $err_data);
        }
        
        $dt_order = date('YmdHis');
        
        //系统取现日志
        $this->load->model('base/withdraw_log_base', 'withdraw_log');
        $withdraw_log['uid'] = $this->uid;
        $withdraw_log['orderid'] = $orderid;
        $withdraw_log['ybdrawflowid'] = $orderid;
        $withdraw_log['status_code'] = '';
        $withdraw_log['money'] = $money;
        $withdraw_log['logid'] = 0;
        $withdraw_log['plat'] = 'baofoo';
        $withdraw_id = $this->withdraw_log->addLog($withdraw_log);
        
        $this->load->model('logic/baofoopay_logic', 'baofoopay_logic');
        $return_data = $this->baofoopay_logic->withDraw($orderid, $money,$id_name, $mobile,$id_num , $account_no, $bank_name);
        $is_failed = true;
        $return_code = $return_data['data']['trans_content']['trans_head']['return_code'];
        $return_msg =  $return_data['data']['trans_content']['trans_head']['return_msg'];
        if($return_code=='0000'||$return_code=='0300'||$return_code=='0401'||$return_code=='0999'){
        	$user_log_data = array(
                'uid' => $this->uid,
                'pid' => 0,
                'pname' => '取现处理中',
                'orderid' => $orderid,
                'money' => $cost_money,
                'balance' => $balance - $cost_money,
                'action' => USER_ACTION_PCASHOUT
            );
        	$is_failed = false;
        }else{
        	$user_log_data = array(
        			'uid' => $this->uid,
        			'pid' => 0,
        			'pname' => '提现失败(' . $return_msg . ',次日17点前退回账户)',
        			'orderid' => $orderid,
        			'money' => $cost_money,
        			'balance' => $balance - $cost_money,
        			'action' => USER_ACTION_WITHDRAWFAILED
        	);
        }
        $this->load->model('base/user_log_base', 'user_log_base');
        $last_id = $this->user_log_base->addUserLog($this->uid, $user_log_data);
        $userlogid = trim($this->input->post('userlogid'));
        if(!empty($userlogid)){
        	if($is_failed){
        		$this->user_log_base->updateUserLogByIdForWithdrawNotify($this->uid,$userlogid, '',false);
        	}else{
	        	$last_id = $last_id.','.$userlogid;
        	}
        }
        if($is_failed){
        	$faild_log_data = array(
        			'uid' => $this->uid,
                	'orderid' => $orderid,
                	'money' => $cost_money,
                	'realname' => $id_name,
                	'bankname' => $bank_name,
                	'bankcode' => $bank_code,
                	'cardNo' => $account_no,
        			'back_code' => $return_code,
        			'back_msg' =>  $return_msg,
        			'logid' => $last_id,
        			'plat' => 'baofoo',
        			'ctime' =>NOW
        	);
        	$this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
        	$this->withdraw_failed_log_base->addFailedLog($faild_log_data);
        	
        	$withdraw_log = array();
        	$withdraw_log['back_status'] = 'BF_FAILD';
        	$withdraw_log['status'] = 1;
        	$withdraw_log['succtime'] = NOW;
        	$withdraw_log['logid'] = $last_id;
        	$withdraw_log['status_code'] = $return_code;
        	$this->withdraw_log->updateDrawLog($withdraw_log, array('id' => $withdraw_id));
        	
        	$err_data = array();
        	$err_data['uid'] = $this->uid;
        	$err_data['money'] = $cost_money;
        	$err_data['balance'] = $balance - $cost_money;
        	$err_data['method'] = 'baofoo.withDraw';
        	$response = array('error'=> '10' . $return_code, 'msg'=> $return_msg);
        	$this->load->model('base/error_log_base', 'error_log_base');
        	$this->error_log_base->addLog($response, $err_data);
        }else{
        	$withdraw_log = array();
        	$withdraw_log['logid'] = $last_id;
        	$this->withdraw_log->updateDrawLog($withdraw_log, array('id' => $withdraw_id));
        }
        $return_data = array(
            "amount" => $cost_money,
            "balance" => $balance - $cost_money,
            "requestid" => $orderid,
            "status" => "SUCCESS",
        	"time" =>NOW,
        );
        $response = array('error' => 0, 'data' => $return_data);
        $this->out_print($response);
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */