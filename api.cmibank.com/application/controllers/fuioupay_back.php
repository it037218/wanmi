<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class fuioupay_back extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->config->load('cfg/fuiou_config', true, true);
        $this->fuiou_config = $this->config->item('cfg/fuiou_config');
    }

    public function payNotify(){
//        file_put_contents('fuioupay_back_pay_post_20170919.log', json_encode($_POST));
//        file_put_contents('fuioupay_back_pay_raw_20170919.log', file_get_contents("php://input"));
    	$type = trim($this->input->post('TYPE'));
    	$version = trim($this->input->post('VERSION'));
    	$responseCode = trim($this->input->post('RESPONSECODE'));
    	$responseMsg = trim($this->input->post('RESPONSEMSG'));
    	$mchntcd = trim($this->input->post('MCHNTCD'));
    	$orderid = trim($this->input->post('MCHNTORDERID'));//商户订单号
    	$fuiouorderid = trim($this->input->post('ORDERID'));//富友订单号
    	$amt = trim($this->input->post('AMT'));
    	$bankcard = trim($this->input->post('BANKCARD'));
    	$SIGN = trim($this->input->post('SIGN'));
    	$signString = $type."|".$version."|".$responseCode."|".$mchntcd."|".$orderid."|".$fuiouorderid."|".$amt."|".$bankcard."|".$this->fuiou_config['merchant_key'];
		$this->fuiounotify_log($signString.'-'.$SIGN.'-'.md5($signString)."-".$responseMsg);
		$this->load->model('base/pay_log_base', 'pay_log');
        $this->load->model('base/user_log_base', 'user_log_base');
        $this->load->model('base/balance_base', 'balance_base');
        $order_info = $this->pay_log->getLogByOrdid($orderid);
        $amount = $amt/100;
        if(empty($order_info)){
            die("{'ret_code':'9999','ret_msg':'找不到订单号!'}");
            exit;
        }
        $uid = $order_info['uid'];
   		if($SIGN == md5($signString)){
   			if($responseCode=="0000"){      //已经交易成功，直接加加钱 修改订单状态
	            $this->load->model('base/pay_redis_base', 'pay_redis_base');
	            $incr = $this->pay_redis_base->incr($orderid);
	            if($incr != 1){
	                echo '订单重复处理';  
	            	exit;
	            }
	            
	            //查看订单是否已完结
	            if($order_info['status'] == 1){
	            	echo '订单已经完成';
	            	exit;
	            }
	            if($order_info['isback'] == 1){
	            	if(empty($order_info['errorcode'])){
		            	echo '订单已经完成';
		            	exit;
	            	}
	            }
	            if(!$amount || $amount <= 0 || $amount != $order_info['amt']){
	            	die("{'ret_code':'9999','ret_msg':'金额错误!'}");
	            	exit;
	            }
	            $log_data = array();
	            $log_data['isback'] = 1;
	            $log_data['status'] = 1;
	            $log_data['errormsg'] = '';
	            $log_data['errorcode'] = '';
	            $log_data['trxid'] = $fuiouorderid;
	            $orderret = $this->pay_log->updateOrder($orderid, $log_data);
	            if(!$orderret){
	            	die("{'ret_code':'9999','ret_msg':'重复的订单请求!'}");
	            }
	            if($orderret){
		            //加钱
		            $balance = $this->balance_base->get_user_balance($uid);
		            $balance += $amount;
		            
		            //写用户日志
		            $user_log_data = array(
		                'uid' => $uid,
		                'pid' => 0,
		                'pname' => '充值',
		                'paytime' => NOW,
		                'money' => $amount,
		                'balance' => $balance,
		                'orderid' => $orderid,
		                'action' => USER_ACTION_PAY
		            );
		            $this->user_log_base->addUserLog($uid, $user_log_data);
		            $ret = $this->balance_base->add_user_balance($uid, $amount);
		            $this->load->model('base/pay_redis_base', 'pay_redis_base');
		            $this->pay_redis_base->setfuiouorder($orderid);
		            echo '订单完成';        
		            exit;
	            }
        	}else{//支付失败
        		$log_data = array();
        		$log_data['isback'] = 1;
        		$log_data['errormsg'] = $responseMsg;
        		$log_data['errorcode'] = $responseCode;
        		$log_data['trxid'] = $fuiouorderid;
        		$orderret = $this->pay_log->updateOrder($orderid, $log_data);
        		if(!$orderret){
        			die("{'ret_code':'9999','ret_msg':'重复的订单请求!'}");
        		}
        		$msg=NOW.'|'.$uid.'|'.$bankcard.'|'.$responseMsg.'|'.$responseCode.'|'.$amount.'|'.$orderid.'|'.$fuiouorderid;
        		$this->fuiounotify_fall_log($msg);
        		echo '订单完成';   
        		exit;
        	}	
   		}else{//sign 错误，不处理
   			header("HTTP/1.0 404 Not Found");
   			exit;
   		}
    }
    
    //提现退票通知
    public function backNotify() {
        $mac = trim($this->input->post('mac'));
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $verifyresult = $this->fuioupay_logic->VerifySign($this->input->post(),$mac);
        if($verifyresult){
            $orderid = trim($this->input->post('orderno'));
            $state = trim($this->input->post('state'));
            $reason = trim($this->input->post('reason'));
            $result = trim($this->input->post('result'));
            $futporderno = trim($this->input->post('futporderno'));
            $this->load->model('base/withdraw_log_base', 'withdraw_log');
            $date = substr($orderid, 2,8);
            $year = date('Y', strtotime($date));
            $week = date('W', strtotime($date));
            $orderInfo = $this->withdraw_log->getLogByOrderId($orderid, $year, $week);
            if(!$orderInfo){
                echo 'order not found!';
                exit;
            }
            if($orderInfo['status'] != 0){
                echo 'S0000000';
                exit;
            }
            
            $status = 1;
            $action_type = USER_ACTION_PCASHOUT;

            $pname = '提现退回';
            if($state == 1){
                $pname = '提现失败('.$result.$reason .',将于次日回到账户)';
                $back_status = 'FAILED';
                $this->config->load('cfg/banklist', true, true);
                $banklist = $this->config->item('cfg/banklist');
                $action_type = USER_ACTION_WITHDRAWFAILED;
                
                //$identity_result['cardno'] = '6226091210143311';      //尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
                $this->load->model('logic/user_identity_logic', 'user_identity_logic');
                $identity_result = $this->user_identity_logic->getPublicUserIdentity($orderInfo['uid'], 'all');
                $id_num = strtoupper($identity_result['idCard']);
                $id_name = $identity_result['realname'];
                $bank_code = $identity_result['bankcode'];
                $bank_name = $banklist[$bank_code]['name'];
                $account_no = $identity_result['cardno'];
                $faild_log_data = array(
                    'uid' => $orderInfo['uid'],
                    'orderid' => $orderid,
                    'money' => $orderInfo['money'],
                    'realname' => $id_name,
                    'bankname' => $bank_name,
                    'bankcode' => $bank_code,
                    'cardNo' => $account_no,
                    'back_code' => $futporderno,
                    'back_msg' => $result.'-'.$reason,
                    'logid' => $orderInfo['logid'],
                    'plat' => 'fuiou',
                    'ctime' =>NOW
                );
                $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
                $failedInfo = $this->withdraw_failed_log_base->getFailedLogByOrderId($orderid);
                if(empty($failedInfo)){
                    $this->withdraw_failed_log_base->addFailedLog($faild_log_data);
                }
            }else{
                exit;
            }
            $this->load->model('base/withdraw_log_base', 'withdraw_log_base');
            $data = array('back_status' => $back_status, 'status' => $status, 'succtime' => time(), 'status_code' => $state);
            $where = array('id' => $orderInfo['id']);
            $ret = $this->withdraw_log_base->updateDrawLog($data, $where, $year, $week);
            if($ret){
                $this->load->model('base/user_log_base', 'user_log_base');
                $update_data = array('orderid' => $orderInfo['orderid'], 'pname' => $pname, 'paytime' => time(), 'action' => $action_type);
                $isfind = strpos($orderInfo['logid'], ',');
                if($isfind){
                    $update_logid = explode(',', $orderInfo['logid']);
                }else{
                    $update_logid = $orderInfo['logid'];
                }

                if(is_array($update_logid)){
                    foreach ($update_logid as $userlogid){
                            $this->user_log_base->updateUserLogByIdForWithdrawNotify($orderInfo['uid'],$userlogid, $update_data,true);
                    }
                }else{
                    $update_where = array('id' => $update_logid);
                    $ret = $this->user_log_base->updateUserLogByIdForWithdrawNotify($orderInfo['uid'],$update_logid, $update_data,true);
                }
            }
            echo '1';
            exit;
        }else{
            echo '回调错误';
        }
    }
    
    private function fuiounotify_fall_log($msg){
    	if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
    		$logFile = './fuiounotify_log.'.date("Y-m-d");
    	}else{
    		$logFile = '/usr/logs/fuiounotify_fall_log.'.date("Y-m-d");
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
    
    private function fuiounotify_log($msg){
    	if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
    		$logFile = './fuiounotify_log.'.date("Y-m-d");
    	}else{
    		$logFile = '/usr/logs/fuiounotify_log.'.date("Y-m-d");
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
}	

/* End of file test.php */
/* Location: ./application/controllers/test.php */