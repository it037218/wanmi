<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class fuioupay extends Controller {

	private $notify_url;
    public function __construct($lock = true)
    {
        parent::__construct();
        $this->load->model('logic/user_logic', 'user_logic');
        $this->check_link();
        
        if($lock === true){
             $this->check_login();
        }
        
        $this->config->load('cfg/fuiou_config', true, true);
        $this->fuiou_config = $config =  $this->config->item('cfg/fuiou_config');
        $this->notify_url = $config['pay_notify_url'];
    }

    public function pay(){
//        $response = array('error'=> 4025, 'msg'=>'10月24日15点开放充值!');
//        $this->out_print($response);
    	if(NOW <= mktime(0,40,0)){
    		$response = array('error'=> 4025, 'msg'=>'支付渠道正在维护，请稍后再试!');
    		$this->out_print($response);
    	}
        
    	$this->load->model('logic/cd_logic', 'cd_logic');
    	$cd_data = $this->cd_logic->getUserCd($this->uid);
    	if($cd_data['pay'] <= 0){
    		$response = array('error'=> 4018, 'msg'=>'今日充值次数已达上限');
    		$this->out_print($response);
    	}
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all'); 
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error' => 4000, 'msg' => '请先进行绑卡操作!');
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
        $acct_name = $identity_result['realname'];                  //银行卡用户名
        $id_num = strtoupper($identity_result['idCard']);                     //身份证号
        $card_no = $identity_result['cardno'];                      //银行卡号
        $bank_code = $identity_result['bankcode'];
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        if(!isset($banklist[$bank_code])){
        	$response = array('error'=> 4001, 'msg'=>'暂不支持此银行');
        	$this->out_print($response);
        }
        $bank_name = $banklist[$bank_code]['name'];
        $money = $this->input->request('money_order');                 //充值金额
        if($money < 100){
            $response = array('error'=> 5050, 'msg'=>'最低充值金额为100元');
            $this->out_print($response);
        }
        if($this->input->post('platform') == 'android'){
            $ordid = $this->input->post('orderid');
        }else{
            $ordid = uniqid('fy'.date('Ymd'));
        }
        
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
        
        if($this->input->post('platform') == 'android'){
            $orgString = $this->fuiou_config['merchant_id']."|".($money*100)."|".$this->fuiou_config['merchant_key'];
        }else{
            $orgString = "02"."|"."2.0"."|".$this->fuiou_config['merchant_id']."|".$ordid."|".$this->uid."|".($money*100)."|".$card_no."|".$this->notify_url."|".$acct_name."|".$id_num."|"."0"."|".$this->fuiou_config['merchant_key'];
        }
        
	$this->fuiounotify_log("orgString:".$orgString);
        $return_data = array (
            "merchant_id" => $this->fuiou_config['merchant_id'],
            "sign"=>md5($orgString),
            "acct_name" => $acct_name,                      //银行卡用户名
            "dt_order" => date('YmdHis'),                   //14位数字，精确到秒
            "id_no" => $id_num,                              //身份证号
            "money" => $money,                        //充值金额
            "order" => $ordid,                         
            "notify_url" => $this->notify_url,              
            "card_no" => $card_no,                          //银行卡号
            "user_id" => $this->uid,
            "bank_name" =>$bank_name,
            "valid_order" => '30',                          //订单有效时间
        );
        
        if(!empty($ordid)){
            //创建购买订单
            $pay_log = array(
                'uid' => $this->uid,
                'ordid' => $ordid,
                'amt' => $money,
                'platform' => 'fuiou',
                'curcode' => 'RMB',
                'ctime' => NOW,
                'status' => 0,
            );
            $this->load->model('base/pay_log_base' , 'pay_log');
            $order_result = $this->pay_log->createOrder($pay_log);
            if($order_result){
                //减掉充值次数
                $cd_data = $this->cd_logic->getUserCd($this->uid);
                $cd_data['pay']--;
                $this->cd_logic->setUserCd($this->uid, $cd_data);
            }else{
                $return_data['order'] = '';
            }
        }
        $response = array('error'=> 0, 'data'=> $return_data);
        $this->out_print($response);
    }
    
    public function queryWithDrawOrder() {
        $orderno = $this->input->request('orderid');
        $startdt = $this->input->request('startdt');
        $enddt = $this->input->request('enddt');
        $transst = $this->input->request('transst');
        if(empty($orderno) || empty($startdt)  || empty($enddt)){
            $response = array('error' => 1001, 'data' => '参数错误！');
            $this->out_print($response);
        }
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $return_data = $this->fuioupay_logic->queryWithDrawOrder($orderno,$startdt,$enddt,$transst);
        if($return_data['ret'] == '000000'){
            $response = array('error' => 0, 'data' => $return_data['trans']);
            $this->out_print($response);
        }else{
            $response = array('error' => 1000, 'data' => $return_data['memo']);
            $this->out_print($response);
        }
    }
    
    public function queryWithDrawStatus() {
        $orderno = $this->input->request('orderid');
        $startdt = $this->input->request('startdt');
        $enddt = $this->input->request('enddt');
        $transst = $this->input->request('transst');

        if(empty($orderno) || empty($startdt)  || empty($enddt)){
            $response = array('error' => 1001, 'data' => '参数错误！');
            $this->out_print($response);
        }
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $return_data = $this->fuioupay_logic->queryWithDrawStatus($orderno,$startdt,$enddt,$transst);
        if($return_data['ret'] == '000000'){
            $response = array('error' => 0, 'data' => $return_data['trans']);
            $this->out_print($response);
        }else{
            $response = array('error' => 1000, 'data' => $return_data['memo']);
            $this->out_print($response);
        }
    }
    
    public function queryWithDrawArrive() {
        $orderno = $this->input->request('orderid');
        $startdt = $this->input->request('startdt');
        $enddt = $this->input->request('enddt');
        $transst = $this->input->request('transst');
        
        if(empty($orderno)){
            $response = array('code' => '1004', 'data' => '请求参数错误！');
            $this->out_print($response);
        }
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $date = substr($orderno, 2,12);
        
        if(substr($orderno, 0, 2) != 'fu' || !strtotime($date)){
            $response = array('code' => '1005', 'data' => '提现订单号错误！');
            $this->out_print($response);
        }

        $statusReturn_data = $this->fuioupay_logic->queryFailWithDraw($orderno);

        $this->load->model('base/withdraw_log_base', 'withdraw_log');

        $year = date('Y', strtotime($date));
        $week = date('W', strtotime($date));
        $withdraw_log = $this->withdraw_log->getLogByOrderId($orderno, $year, $week);

        //$status['status']状态说明：0，：提交处理，1：失败，2：处理中，3：成功。4：交易废弃
        if($statusReturn_data){
            if($withdraw_log['status'] == 4){
                $response = array('code' => '3333', 'data' => '','msg' => '交易已废弃！');
                $this->out_print($response);
            }
            $response = array('code' => '4444', 'data' => $statusReturn_data,'msg' => '产生提现退票');
            $this->out_print($response);
        }else{
            //查询提现记录状态，如果status=3，提现成功。直接返回提现到账
            if ($withdraw_log['status'] == 3){
                $response = array('code' => '0000', 'data' => '','msg' => '提现到账！');
                $this->out_print($response);
            }else {
                $return_data = $this->fuioupay_logic->queryWithDrawOrder($orderno, $startdt, $enddt, $transst);
                if ($return_data['ret'] == '000000') {
                    if ($return_data['trans']['state'] != 1) {
                        if ($return_data['trans']['reason'] == '交易已废弃') {
                            //设为失败,向cmibank_withdraw_log_,cmibank_user_log,cmibank_withdraw_failed_log写入失败信息
                            $this->wasteLog($withdraw_log, '交易已废弃');
                            $status['status'] = '4';
                            $this->withdraw_log->updateDrawLog($status, array('orderid' => $orderno), $year, $week);
                            $this->UpdateUserLog($statusReturn_data['uid'], $orderno);
                            $response = array('code' => '3333', 'data' => $return_data['trans']['result'], 'tdata' => $return_data, 'reason' => (is_string($return_data['trans']['reason']) ? $return_data['trans']['reason'] : ''));
                            $this->out_print($response);
                        } else {
                            $response = array('code' => '1111', 'data' => array('withdrawTime' => $date, 'nowTime' => date("YmdHis")), 'msg' => $return_data['trans']['result']);
                            $this->out_print($response);
                        }
//                        $response = array('code' => '3333', 'data' => $return_data['trans']['result'], 'tdata' => $return_data, 'reason' => (is_string($return_data['trans']['reason']) ? $return_data['trans']['reason'] : ''));
//                        $this->out_print($response);
                    }
                } else {
                    $response = array('code' => '1001', 'data' => $return_data['memo']);
                    $this->out_print($response);
                }
                $status = array();
                if (substr($date, -4) > 1600) {
                    if (time() - strtotime($date) < 115380) {
                        $response = array('code' => '1111', 'data' => array('withdrawTime' => $date, 'nowTime' => date("YmdHis")), 'msg' => '正在处理中1！');
                        $this->out_print($response);
                    } else {
                        $status['status'] = '3';
                        $this->withdraw_log->updateDrawLog($status, array('orderid' => $orderno), $year, $week);
                        $response = array('code' => '0000', 'data' => '', 'msg' => '提现到账！');
                        $this->out_print($response);
                    }
                } else {
                    if (time() - strtotime($date) < 28980) {
                        $response = array('code' => '1111', 'data' => array('withdrawTime' => $date, 'nowTime' => date("YmdHis")), 'msg' => '正在处理中2！');
                        $this->out_print($response);
                    } else {
                        $status['status'] = 3;
                        $this->withdraw_log->updateDrawLog($status, array('orderid' => $orderno), $year, $week);
                        $response = array('code' => '0000', 'data' => '', 'msg' => '提现到账！');
                        $this->out_print($response);
                    }
                }
            }
        }
    }

    public function queryOrder(){
    	$orderid = $this->input->request('orderid');
    	$ischeck = 0;
    	$status = 2;
    	$this->load->model('base/pay_redis_base', 'pay_redis_base');
    	$orderstat = $this->pay_redis_base->getfuiouorder($orderid);
    	$this->load->model('base/pay_log_base', 'pay_log_base');
    	$orderInfo = $this->pay_log_base->getLogByOrdid($orderid);
    	if($orderstat == '1'){
    		//查询后台钱到了没
    		if($orderInfo['isback'] == 1){
	    		if($orderInfo['status'] == 0){
	    			$status = 0;
	    			$response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '支付失败', 'check' => 1));
	    			$this->out_print($response);
	    		}else{
	    			$status = 1;
	    			$this->load->model('logic/cd_logic', 'cd_logic');
	    			$cd_data = $this->cd_logic->getUserCd($this->uid);
	    			$cd_data['pay']--;
	    			$this->cd_logic->setUserCd($this->uid, $cd_data);
		    		$response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '支付完成', 'check' => 1));
		    		$this->out_print($response);
	    		}
    		}
    	}else{
    		if($orderInfo['isback'] == 1){
    			if($orderInfo['status'] == 0){
    				$status = 0;
    				$response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '交易失败!', 'check' => 1));
    				$this->out_print($response);
    			}else{
    				$status = 1;
    				$response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '支付完成', 'check' => 1));
    				$this->out_print($response);
    			}
    		}else{
	    		$response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => '银行处理中', 'check' => 0));
	    		$this->out_print($response);
    		}
    	}
    }
    
    /**
     * 从数据库查询提现失败订单，富友提现退票通知记录
     */
    public function queryFailWithDraw() {
        $orderid = trim($this->input->post('orderid'));
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $result = $this->fuioupay_logic->queryFailWithDraw($orderid);
        $response = array('error' => 0, 'data' => $result);
        $this->out_print($response);
    }
    
    public function __withDraw() {
        error_reporting(E_ALL);
        $cost_money = $money = trim($this->input->post('amount'));               //取现金额
        
//        $response = array('error'=> 4017, 'msg'=>'由于恶意刷量，延迟开启提现功能');
//        $this->out_print($response);
        
        if(!$money || $money <= 0){
            $response = array('error'=> 4017, 'msg'=>'请输入取现金额');
            $this->out_print($response);
        }
        
        if($cost_money > 500000){
        	$response = array('error'=> 4017, 'msg'=>'单笔提现不能高于50万');//富友实际500万
        	$this->out_print($response);
        }
        
        if($money < 100){
        	$response = array('error'=> 4017, 'msg'=>'您最低提现金额为100元');
        	$this->out_print($response);
        }
        
        $tpwd = trim($this->input->post('tpwd'));
        
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $identity_result = $this->user_identity_base->getUserIdentity($this->uid);
        if(empty($identity_result)){
            $response = array('error' => 4019, 'msg' => '用户信息错误');
            $this->out_print($response);
        }
        
        //对11-12 00：00：00 ~ 11-12 07：00：00 异常体验金限制
        if($identity_result['isnew'] == 1){
            $this->load->model('base/user_expmoney_base' , 'user_expmoney_base');
            $expmoneyList = $this->user_expmoney_base->get_user_expmoney_list($this->uid);
            if(count($expmoneyList) > 0){
                if($expmoneyList[0]['status'] == 2 && $expmoneyList[0]['utime'] !== 0 ){
                    $response = array('error' => 4022, 'msg' => '你尚未投资过定期，不能提现');
                    $this->out_print($response);
                }
            }
        }
        //对11-12 00：00：00 ~ 11-12 07：00：00 end
        
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
        
        $ret = $this->check_withdraw_money($this->uid, $cost_money);
        if(!$ret){
               $response = array('error' => 12006, 'msg' => '账目金额不符,请联系客服!');
               $this->out_print($response);
        }
        
        $orderid = 'fu'.date('YmdHis') . $this->uid . mt_rand(1000,9999);
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
        $bankno = $banklist[$bank_code]['fuiou_bank_code'];
        $bank_name = $banklist[$bank_code]['name'];
        $cityno = ($identity_result['cityid'] ? $identity_result['cityid'] : '1000');
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
        $withdraw_log['plat'] = 'fuiou';
        $withdraw_id = $this->withdraw_log->addLog($withdraw_log);
        
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        
        $return_data = $this->fuioupay_logic->withdraw($orderid, $bankno, $cityno, $account_no ,$id_name, $cost_money*100);
        $is_failed = true;
        $return_code = $return_data['ret'];
        $return_msg =  $return_data['memo'];
        if($return_code=='000000'){
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
                        'plat' => 'fuiou',
                        'ctime' =>NOW
        	);
        	$this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
        	$this->withdraw_failed_log_base->addFailedLog($faild_log_data);
        	
        	$withdraw_log = array();
        	$withdraw_log['back_status'] = 'FU_FAILD';
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
    
    private function fuiounotify_log($msg){
    	if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
    		$logFile = './fuioupay_log.'.date("Y-m-d");
    	}else{
    		$logFile = '/usr/logs/fuioupay_log.'.date("Y-m-d");
    	}
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

    /**
     * 添加废弃日志
     * @param array $withdraw_log
     * @param string $reason
     * @return bool
     */
    public function wasteLog($withdraw_log = array(),$reason = ''){
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $this->load->model('base/withdraw_failed_log_base', 'failed_log');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($withdraw_log['uid'], 'all');
        $bank_code = $identity_result['bankcode'];
        $bank_name = $banklist[$bank_code]['name'];
        //废弃流水号
        $futporderno = 'F_'.date('YmdHis', time());
        $faild_log_data = array(
            'uid' => $withdraw_log['uid'],
            'orderid' => $withdraw_log['orderid'],
            'money' => $withdraw_log['money'],
            'realname' => $identity_result['realname'],
            'bankname' => $bank_name,
            'bankcode' => $identity_result['bankcode'],
            'cardNo' => $identity_result['cardno'],
            'back_code' => $futporderno,
            'back_msg' => $reason,
            'logid' => $withdraw_log['logid'],
            'plat' => 'fuiou',
            'ctime' =>NOW
        );
        $result = $this->failed_log->addFailedLog($faild_log_data);
        return $result ? true : false;
    }

    /**
     * 交易订单废弃，更新用户表
     * @param int $uid
     * @param string $orderid
     * @return mixed
     */
    public function UpdateUserLog($uid = 0, $orderid = ''){
        $this->load->model('base/user_log_base', 'user_log');
        $data['pname'] = '交易已废弃';
        $data['action'] = USER_ACTION_WITHDRAWWASTE;
        return $this->user_log->updateUserLogOnlyWithDraw($uid,'',$data,array('orderid'=>$orderid));
    }
}	

/* End of file test.php */
/* Location: ./application/controllers/test.php */