<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class jytpay extends Controller {

    public function __construct($lock = true)
    {
//         $lock = false;
        parent::__construct();
        if($lock == true){
            $response = array('error'=> 4001, 'msg'=>'错误的系统调用关系!');
            $this->out_print($response);
        }
        $this->check_login($lock);
        $this->load->model('logic/jytpay_logic', 'jytpay_logic');
        $this->load->model('base/bind_card_cd_base', 'bind_card_cd_base');
        $this->load->model('logic/user_logic', 'user_logic');
    	$this->load->model('logic/user_identity_logic', 'user_identity_logic');
        
    }
    
    
    public function pay(){
//         $response = array('error'=> 4019, 'msg'=>'金运通支付渠道维护，请切换至其他渠道再试');
//         $this->out_print($response);
        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        if($cd_data['pay'] <= 0){
            $response = array('error'=> 4018, 'msg'=>'今日充值次数已达上限');
            $this->out_print($response);
        }
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $tran_amt = $this->input->request('amount');

        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all');
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error'=> 4000, 'msg'=>'请先绑定银行卡信息');
            $this->out_print($response);
        }
        $mobileVerify = trim($this->input->request('code'));
        if(!empty($mobileVerify)){
	        if(strlen($mobileVerify) != MOBILEVERIFY_LEN){
	        	$response = array('error'=> 1003, 'msg'=>'验证码长度错误');
	        	$this->out_print($response);
	        }
	        $res = $this->user_logic->check_pay_code($identity_result['phone'], $mobileVerify);
	        if(!$res){
	        	$response = array('error'=> 10021, 'msg'=>'手机验证码错误');
	        	$this->out_print($response);
	        }
        }else{
        	$response = array('error'=> 10021, 'msg'=>'手机验证码错误');
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
        $id_num = strtoupper($identity_result['idCard']);
        $id_name = $identity_result['realname'];
        $bank_code = $identity_result['bankcode'];
        $zj = '01';
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        if(!isset($banklist[$bank_code])){
            $response = array('error'=> 4001, 'msg'=>'暂不支持此银行');
            $this->out_print($response);
        }
        //$identity_result['cardno'] = '6226091210148313';      //尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
        $bank_name = $banklist[$bank_code]['name'];
        $account_no = $identity_result['cardno'];
        if($identity_result['cardno'] == '6217004150002630047'){
            $response = array('error' => 12001, 'msg' => '黑名单卡号!请联系客户!');
            $this->out_print($response);
        }
        $this->config->load('cfg/jytpay_config', true, true);
        $jytpay_config = $this->config->item('cfg/jytpay_config');
        $orderid = $jytpay_config['merchant_id'] . date('YmdHis') . rand(100000,999999);    //请根据商户系统自行定义订单号
        $money = $tran_amt;
        
        $cid = $this->input->post('cid');
        $ptid = $this->input->post('ptid');
        if(!empty($cid)){
        	$this->load->model('base/user_coupon_base' , 'user_coupon_base');
        	$conpon = $this->user_coupon_base->getUserCouponDetail($this->uid,$cid);
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
        
        //创建购买订单
        $pay_log = array(
            'uid' => $this->uid,
            'ordid' => $orderid,
            'amt' => $money,
            'platform' => 'jytpay',
            'curcode' => 'RMB',
            'ctime' => NOW,
            'status' => 0,
        );
        $this->load->model('base/pay_log_base' , 'pay_log');
        $this->pay_log->createOrder($pay_log);
        $mobile = $identity_result['phone'];
        //$data = $this->jytpay_logic->pay($this->uid, $orderid, $tran_amt, $id_num, $id_name, $bank_name, $account_no, $mobile, $zj);
$data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><head><version>1.0.0</version><tran_type>02</tran_type><merchant_id>290060100031</merchant_id><tran_date>20160701</tran_date><tran_time>143217</tran_time><tran_flowid>29006010003120160701143217384219</tran_flowid><tran_code>TC2001</tran_code><resp_code>S0000000</resp_code><resp_desc>执行成功</resp_desc></head><body><tran_resp_code>S0000000</tran_resp_code><tran_resp_desc>交易成功</tran_resp_desc><tran_state>01</tran_state><remark></remark></body></message>";
        if(!$data){
            $response = array('error'=> 4002, 'msg'=>'验签失败');
            $this->out_print($response);
        }
        
        $data = simplexml_load_string($data);
//         print_r($data);
        if($data->head->resp_code == 'E0000000'){
            //直接是处理中
            $data = array(
                "amount" => $money ,					//充值金额 分
                "merchantaccount" => $jytpay_config['merchant_id'],		//商务号
                "orderid" => $orderid,	//订单号
                "yborderid"=> $orderid,	//易宝交易号
                'status' => 2,
            );
            
            $response = array('error'=> 0, 'data' => $data);
            $this->out_print($response);
        }
        //print_r($data);
        if($data->head->resp_code != 'S0000000'){   //失败。。。也记录
            $log_data = array();
            $log_data['isback'] = 1;
            $log_data['errormsg'] = (string)$data->head->resp_desc;
            $log_data['errorcode'] = (string)$data->head->resp_code;
            $log_data['trxid'] = $orderid;
            $this->pay_log->updateOrder($orderid, $log_data);
            $this->load->model('base/user_log_base', 'user_log_base');
            $this->load->model('base/balance_base', 'balance_base');
            $balance = $this->balance_base->get_user_balance($this->uid);
            $user_log_data = array(
                'uid' => $this->uid,
                'pid' => 0,
                'paytime' => NOW,
                'pname' => '充值（' . (string)$data->head->resp_desc . ')',
                'money' => $money,
                'orderid' => $orderid,
                'balance' => $balance,
                'action' => USER_ACTION_PAY_FAIL
            );
            $this->user_log_base->addUserLog($this->uid, $user_log_data);
            
            $response = array('error'=> 4003, 'msg'=> (string)$data->head->resp_desc);
            $this->out_print($response); 
        }
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        $cd_data['pay']--;
        $this->cd_logic->setUserCd($this->uid, $cd_data);
        if($data->body->tran_state == '00' || $data->body->tran_state == '15'){            //受理中          测试环境->尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
            $data = array(
                "amount" => $money ,					//充值金额 分
                "merchantaccount" => $jytpay_config['merchant_id'],		//商务号
                "orderid" => $orderid,	//订单号
                "yborderid"=> $orderid,	//易宝交易号
                'status' => 2,
            );
            $response = array('error'=> 0, 'data' => $data);
            $this->out_print($response);            
        }else if($data->body->tran_state == '01'){      //已经交易成功，直接加加钱 修改订单状态
            $this->load->model('base/pay_redis_base', 'pay_redis_base');
            $incr = $this->pay_redis_base->incr($orderid);
            if($incr != 1){
                $data = array(
                    "amount" => $money ,					//充值金额分
                    "merchantaccount" => $jytpay_config['merchant_id'],		//商务号
                    "orderid" => $orderid,	//订单号
                    "yborderid"=> $orderid,	//易宝交易号
                    "status" => 1,
                    'balance' => $balance
                );
                $response = array('error'=> 0, 'data' => $data);
                $this->out_print($response);
            }
            $this->load->model('base/pay_log_base', 'pay_log');
            $order_info = $this->pay_log->getLogByOrdid($orderid);
            //查看订单是否已完结
            if($order_info['isback'] == 1 || $order_info['status'] == 1){
                $data = array(
                    "amount" => $money ,					//充值金额分
                    "merchantaccount" => $jytpay_config['merchant_id'],		//商务号
                    "orderid" => $orderid,	//订单号
                    "yborderid"=> $orderid,	//易宝交易号
                    "status" => 1,
                );
                $response = array('error'=> 0, 'data' => $data);
                $this->out_print($response);
            }
            $log_data = array();
            $log_data['isback'] = 1;
            $log_data['status'] = 1;
            $log_data['errormsg'] = '';
            $log_data['errorcode'] = '';
            $log_data['trxid'] = $orderid;
            $this->pay_log->updateOrder($orderid, $log_data);
            //加钱
            $this->load->model('base/balance_base', 'balance_base');
            $balance = $this->balance_base->get_user_balance($this->uid);
            $balance += $money;
            
            //写用户日志
            $user_log_data = array(
                'uid' => $this->uid,
                'pid' => 0,
                'pname' => '充值',
                'paytime' => NOW,
                'money' => $money,
                'balance' => $balance,
                'orderid' => $orderid,
                'action' => USER_ACTION_PAY
            );
            $this->load->model('base/user_log_base', 'user_log_base');
            $this->user_log_base->addUserLog($this->uid, $user_log_data);
            $ret = $this->balance_base->add_user_balance($this->uid, $money);
            //$this->user_logic->send_pay_msg($identity_result['phone'], $money);
            $data = array(
                "amount" => $money ,					//充值金额
                "merchantaccount" => $jytpay_config['merchant_id'],		//商务号
                "orderid" => $orderid,	//订单号
                "yborderid"=> $orderid,	//易宝交易号
                'status' => 1,
                'balance' => $balance
            );
            $response = array('error'=> 0, 'data' => $data);
            $this->out_print($response);
        }
    }
    
    public function queryOrder(){
        $query_ordid = $this->input->request('orderid');
        $type = isset($_REQUEST['type']) ? $this->input->request('type') : 'pay';
        
        //请根据商户系统自行定义订单号
//         $data = $this->jytpay_logic->queryPayOrdid($query_ordid, $type);
		$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><head><version>1.0.0</version><tran_type>02</tran_type><merchant_id>290060100031</merchant_id><tran_date>20160704</tran_date><tran_time>091346</tran_time><tran_flowid>29006010003120160704091346740517</tran_flowid><tran_code>TC2001</tran_code><resp_code>S0000000</resp_code><resp_desc>执行成功</resp_desc></head><body><tran_resp_code>S0000000</tran_resp_code><tran_resp_desc>交易成功</tran_resp_desc><tran_state>01</tran_state><remark></remark></body></message>";
       	$data = simplexml_load_string($data);
        //print_r($data);
        if(!$data){
            $response = array('error'=> 4002, 'msg'=>'验签失败');
            $this->out_print($response);
        }
        if((string)$data->body->tran_resp_code != 'S0000000'){
        	if((string)$data->body->tran_resp_code == 'E0000000'){
        		$response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '银行处理中'));
        	}else{
    	        $response = array('error' => 0, 'data' => array('status' => 0, 'errormsg' => '银行接口维护，请稍后再试!'));
	            $this->out_print($response);
        	}
        }
        $status = 2;
        if((string)$data->body->tran_state == '01'){
            $status = 1;
        }else if((string)$data->body->tran_state == '03'){
            $status = 0;
        }
        $response = array('error' => 0, 'data' => array('status' => $status));
        if($status == 2){
            $response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '银行处理中'));
        }
        	
//         $response = array('error' => 0, 'data' => array('status' => $status));
        $this->out_print($response);
    }
    
    public function __withDraw(){
        if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
            die('内网不能取现');
        }else if((isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '117.50.2.20') 
            || (isset($_SERVER['HOSTNAME']) && $_SERVER['HOSTNAME'] == '117-50-2-20')){
            die('测试环境不能取现');
        }

        $cost_money = $money = trim($this->input->post('amount'));
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
        $this->config->load('cfg/jytpay_config', true, true);
        $jytpay_config = $this->config->item('cfg/jytpay_config');
        $orderid = $jytpay_config['merchant_id'] . date('YmdHis') . rand(100000,999999);    //请根据商户系统自行定义订单号
        
        //交易密码
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
        
        if($identity_result['tpwd'] != $tpwd){
        	$this->pay_redis_base->incrwithdrawtpwdtimes($identity_result['phone']);
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->pay_redis_base->delwithdrawtpwdtimes($identity_result['phone']);
        if($identity_result['isvalidate'] == 0){
            $ck_orderid = '290060120005' . date('YmdHis') . rand(100000,999999);    //请根据商户系统自行定义订单号;
            $num = $this->bind_card_cd_base->get($this->uid);
            $cardno = $identity_result['cardno'];          //银行卡号
            $idcardno = $identity_result['idCard'];        //身份证号
            $username = $identity_result['realname'];      //用户名
            $phone = $identity_result['phone'];            //银行预留电话
            
            $validate = $this->validate($ck_orderid, $cardno, $idcardno, $username, $phone);
            
            if((string)$validate->head->resp_code == 'S0000000' ){
                //绑定卡
                $update_identity = array('isvalidate' => 1);
                $where = array('uid' => $this->uid);
                $rtn = $this->user_logic->updateUserIdentity($update_identity, $where);
                if(!$rtn){
                    $response = array('error' => 12001, 'msg' => '用户信息更新失败');
                    $this->out_print($response);
                }
            }else{
                $update_identity = array('ischeck' => 0, 'isvalidate' => 0);
                $where = array('uid' => $this->uid);
                $rtn = $this->user_logic->updateUserIdentity($update_identity, $where);
                //次数+1 单用户不能超过5次   CD 24小时
                $this->bind_card_cd_base->incr($this->uid);
                $response = array('error' => 12001, 'msg' => '银行预留信息与平台信息不符!请重新绑定! 为了您的账户安全考虑, 未成功绑定银行卡前, 您的账户将暂时无法进行充值, 提现操作.');
                $this->out_print($response);
            }
        }

        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all');

        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error'=> 4000, 'msg'=>'请先绑定银行卡信息');
            $this->out_print($response);
        }
        if($identity_result['bankcode'] == 'CMBCHINA'){
        	if(NOW >= mktime(23,40,0)){
        		$response = array('error'=> 4025, 'msg'=>'23:40~00:40为招商银行渠道维护时间,请您避开该时间段交易!');
        		$this->out_print($response);
        	}
        }
        $this->load->model('base/balance_base', 'balance_base');
        $balance = $this->balance_base->get_user_balance($this->uid);
        if($balance < $cost_money){
            $response = array('error' => 12002, 'msg' => '用户余额不足');
            $this->out_print($response);
        }
        
        $ret = $this->check_withdraw_money($this->uid, $cost_money);
        if(!$ret){
            $response = array('error' => 12006, 'msg' => '账目金额不符,请联系客服!');
            $this->out_print($response);
        }

        //取现次数限制  测试先不用
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
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        if(!isset($banklist[$bank_code])){
            $response = array('error'=> 4001, 'msg'=>'占不支持此银行');
            $this->out_print($response);
        }
        
        //$identity_result['cardno'] = '6226091210143311';      //尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
        
        $bank_name = $banklist[$bank_code]['name'];
        $account_no = $identity_result['cardno'];
        $zj = isset($identity_result['zj']) ? $identity_result['zj'] : '01';
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
        //系统取现日志
        $this->load->model('base/withdraw_log_base', 'withdraw_log');
        $withdraw_log['uid'] = $this->uid;
        $withdraw_log['orderid'] = $orderid;
        $withdraw_log['ybdrawflowid'] = $orderid;
        $withdraw_log['status_code'] = '';
        $withdraw_log['money'] = $money;
        $withdraw_log['logid'] = 0;
        $withdraw_log['plat'] = 'jyt';
        $withdraw_id = $this->withdraw_log->addLog($withdraw_log);
        
        //$data = $this->jytpay_logic->withdraw($this->uid, $orderid, $money, $id_num, $id_name, $bank_name, $account_no, $zj);
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><head><version>1.0.0</version><tran_type>02</tran_type><merchant_id>290060100031</merchant_id><tran_date>20160704</tran_date><tran_time>093314</tran_time><tran_flowid>29006010003120160704093313152080</tran_flowid><tran_code>TC1002</tran_code><resp_code>S0000330</resp_code><resp_desc>CCCCCCC</resp_desc></head><body><tran_state>01</tran_state><remark></remark></body></message>";
        //$this->front_withdraw_log($data);
        
        $data = simplexml_load_string($data);
        //print_r($data);
//         if($this->uid == 120837){
//             $data->head->resp_code = 'E0000000';
//         }
//         print_r($data);
        $this->load->model('base/user_log_base', 'user_log_base');
        if($data->head->resp_code == 'E0000000'){
            $user_log_data = array(
                'uid' => $this->uid,
                'pid' => 0,
                'pname' => '取现处理中',
                'orderid' => $orderid,
                'money' => $cost_money,
                'balance' => $balance - $cost_money,
                'action' => USER_ACTION_PCASHOUT
            );
        }else if($data->head->resp_code != 'S0000000' || !$data || $data->body->tran_state == '03'){   //失败
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['money'] = $cost_money;
            $err_data['balance'] = $balance - $cost_money;
            $err_data['method'] = __METHOD__;
//             $this->balance_base->add_user_balance($this->uid, $money, true);
            //写用户日志
            $user_log_data = array(
                'uid' => $this->uid,
                'pid' => 0,
                'pname' => '提现失败(' . (string)$data->head->resp_desc . ',次日17点前退回账户)',
                'orderid' => $orderid,
                'money' => $cost_money,
                'balance' => $balance - $cost_money,
                'action' => USER_ACTION_WITHDRAWFAILED
            );
            $withdraw_log = array();
            $withdraw_log['back_status'] = 'NB_FAILD';
            $withdraw_log['status'] = 1;
            $withdraw_log['succtime'] = NOW;
            $response = array('error'=> '10' . (string)$data->body->tran_state, 'msg'=> (string)$data->head->resp_desc);
            $this->load->model('base/error_log_base', 'error_log_base');
            $this->error_log_base->addLog($response, $err_data);
            
            $userlogid = trim($this->input->post('userlogid'));
            if(!empty($userlogid)){
            	$update_data = array();
            	$update_data['desc']=1;
            	$this->user_log_base->updateUserLogByIdForWithdraw($this->uid,$update_data, $userlogid);
            }
            
            $is_failed = true;
        }else if($data->head->resp_code == 'S0000000'){
            $withdraw_log = array();
            if((string)$data->body->tran_state == '00' || (string)$data->body->tran_state == '15'){ //受理中 会有回调
                //写用户日志
                $user_log_data = array(
                    'uid' => $this->uid,
                    'pid' => 0,
                    'pname' => '取现处理中',
                    'orderid' => $orderid,
                    'money' => $cost_money,
                    'balance' => $balance - $cost_money,
                    'action' => USER_ACTION_PCASHOUT
                );
            }else if((string)$data->body->tran_state == '01'){      //已经交易成功 无回调
                //写用户日志
                $user_log_data = array(
                    'uid' => $this->uid,
                    'pid' => 0,
                    'pname' => '提现成功',
                    'orderid' => $orderid,
                    'money' => $cost_money,
                    'paytime' => NOW,
                    'balance' => $balance - $cost_money,
                    'action' => USER_ACTION_PCASHOUT,
                );
                $withdraw_log['back_status'] = 'SUCCESS';
                $withdraw_log['status'] = 2;
                $withdraw_log['succtime'] = NOW;
                $userlogid = trim($this->input->post('userlogid'));
                if(!empty($userlogid)){
                	$update_data = array();
                	$update_data['paytime']=NOW;
	                $this->user_log_base->updateUserLogByIdForWithdraw($this->uid,$update_data, $userlogid);
                }
            }
        }
        
        $last_id = $this->user_log_base->addUserLog($this->uid, $user_log_data);
        $withdraw_log['logid'] = $last_id;
        $this->withdraw_log->updateDrawLog($withdraw_log, array('id' => $withdraw_id));
        
        if($is_failed){
            $faild_log_data = array(
                'uid' => $this->uid,
                'orderid' => $orderid,
                'money' => $cost_money,
                'realname' => $id_name,
                'bankname' => $bank_name,
                'bankcode' => $bank_code,
                'cardNo' => $account_no,
                'back_code' => (string)$data->head->resp_code,
                'back_msg' =>  (string)$data->head->resp_desc,
                'logid' => $last_id,
                'plat' => 'jyt',
            	'ctime' =>NOW
            );
            $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
            $this->withdraw_failed_log_base->addFailedLog($faild_log_data);
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
        $this->load->model('logic/user_logic', 'user_logic');
        $code = $this->user_logic->createMsgCode();
        //发送短信
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        $bankname = $bankCfg[$bankCode]['name'];
        $msg_value = array('code' => $code, 'bankname' => $bankname);
        $this->load->model('logic/msm_logic', 'msm_logic');
        $this->user_logic->setBindBankPhone($this->uid, $phone);
        $this->msm_logic->send_bindBank_code($phone, $msg_value);
        $this->user_logic->setBindBankCode($this->uid, $code);
        $response = array('error'=> 0, 'data'=> array('message' => '手机验证码发送成功!'));
        $this->out_print($response);
    }
    
    public function front_withdraw_log($msg){
        $logFile = '/tmp/jytpay_withdraw_front_xml.log' . date('Y-m-d');
        $fp = fopen($logFile, 'a');
        $isNewFile = !file_exists($logFile);
        if (flock($fp, LOCK_EX)) {
            if ($isNewFile) {
                chmod($logFile, 0666);
            }
            fwrite($fp, $msg . "\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
    
    public function validate($orderid, $cardno, $idcardno, $username, $phone, $bank_code = ''){
        $this->load->model('logic/jytpay_logic', 'jytpay_logic');
        $data = $this->jytpay_logic->validate($orderid, $cardno, $idcardno, $username, $phone, $bank_code);
        return  simplexml_load_string($data);
    }
    
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */