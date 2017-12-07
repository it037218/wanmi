<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
// error_reporting(E_ALL);

require_once (APPPATH . 'libraries/llpay/lib/llpay_notify.class.php');


class llpay_notify extends Controller {

    private $llpay_config;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/llpay_logic', 'llpay_logic');
        $this->config->load('cfg/llpay_config', true, true);
        $this->llpay_config = $this->config->item('cfg/llpay_config');
    }
    
//     public function withdraw_notify(){
//         $msg = json_encode($_REQUEST);
//         $this->back_withdraw_log($msg);
//         echo $msg;
//     }
    
    public function pay_notify(){
        $raw_post_data = file_get_contents('php://input', 'r');
        $msg = $raw_post_data;
        $this->back_pay_log($msg);
        //计算得出通知验证结果
        $llpayNotify = new LLpayNotify($this->llpay_config);
        $llpayNotify->verifyNotify();
        if ($llpayNotify->result) { //验证成功
            //获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $no_order = $llpayNotify->notifyResp['no_order'];//商户订单号
            $oid_paybill = $llpayNotify->notifyResp['oid_paybill'];//连连支付单号
            $result_pay = $llpayNotify->notifyResp['result_pay'];//支付结果，SUCCESS：为支付成功
            $money = $llpayNotify->notifyResp['money_order'];// 支付金额
            $dt_order = $llpayNotify->notifyResp['dt_order'];
            if($result_pay == "SUCCESS"){
                $ordid = $no_order;
                //防并发  redis锁
                $this->load->model('base/pay_redis_base', 'pay_redis_base');
                $incr = $this->pay_redis_base->incr($ordid);
                if($incr != 1){
                    die("{'ret_code':'9999','ret_msg':'重复的订单请求!'}");
                    exit;
                }
                $this->load->model('base/pay_log_base', 'pay_log');
                $order_info = $this->pay_log->getLogByOrdid($ordid);
                if(empty($order_info)){
                    die("{'ret_code':'9999','ret_msg':'找不到订单号!'}");
                    exit;
                }
                if($order_info['platform'] != 'llpay'){
                    die("{'ret_code':'9999','ret_msg':'支付平台错误!'}");
                    exit;
                }
                //查看订单是否已完结
                if($order_info['isback'] == 1 || $order_info['status'] == 1){
                    die("{'ret_code':'9999','ret_msg':'重复的订单请求!'}");
                    exit;
                }
//                 '{
//                     "acct_name":"王相尧",
//                     "bank_code":"03080000",
//                     "dt_order":"20151221172314",
//                     "id_no":"430525198703302314",
//                     "id_type":"0",
//     			       "info_order":"充值",
//                     "money_order":"0.01",
//                     "no_agree":"2015122137120509",
//                     "no_order":"201512211723141208319692",
//                     "oid_partner":"201408071000001543",
//                     "oid_paybill":"2015122139246729",
//                     "pay_type":"D",
//                     "result_pay":"SUCCESS",
//                     "settle_date":"20151221",
//                     "sign":"e3bcdd4cbc550e5a39472eacc2f83eac",
//                     "sign_type":"MD5"
//                 }';
                
                $uid = $order_info['uid'];
                $createOrderTime = $order_info['ctime'];
                
                if(!$money || $money <= 0 || $money != $order_info['amt']){
                    die("{'ret_code':'9999','ret_msg':'金额错误!'}");
                    exit;
                }
                $this->load->model('base/user_log_base', 'user_log_base');
                
                //查询订单结果
                $parameter = array(
                    'sign_type' => 'MD5',
                    'no_order' => $ordid,
                    'dt_order' => $dt_order
                );
                $queryInfo = $this->llpay_logic->queryOrder($parameter);
                $queryInfo = json_decode($queryInfo, true);
                if((string)$queryInfo['result_pay'] != 'SUCCESS' || $queryInfo['money_order'] != $money ){
                    die("{'ret_code':'9999','ret_msg':'请求参数与订单查询参数不符'}");
                    exit;
                }
                
                $log_data = array();
                $log_data['isback'] = 1;
                $log_data['status'] = 1;
                $log_data['errormsg'] = '';
                $log_data['errorcode'] = '';
                $log_data['trxid'] = $oid_paybill;
                $this->pay_log->updateOrder($ordid, $log_data);
                //加钱
                $this->load->model('base/balance_base', 'balance_base');
                $balance = $this->balance_base->get_user_balance($uid);
                $balance += $money;
            
                //写用户日志
                $user_log_data = array(
                    'uid' => $uid,
                    'pid' => 0,
                    'pname' => '充值',
                    'paytime' => NOW,
                    'money' => $money,
                    'balance' => $balance,
                    'orderid' => $ordid,
                    'action' => USER_ACTION_PAY
                );
                $this->user_log_base->addUserLog($uid, $user_log_data);
                
                $ret = $this->balance_base->add_user_balance($uid, $money);
                if($ret){
                    //绑定用户信息
                    if($llpayNotify->notifyResp['no_agree']){
                        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
                        $identity_data['requestid'] = $llpayNotify->notifyResp['no_agree'];
                        $where = array('uid' => $uid);
                        $this->load->model('logic/user_logic', 'user_logic');
                        $this->user_logic->updateUserIdentity($identity_data, $where);
                    }
                }
                file_put_contents("/tmp/llpay_log_success.txt", "异步通知 验证成功\n" . json_encode($llpayNotify->result), FILE_APPEND);
                die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
                
            }else{
                //失败的订单
                $log_data = array();
                $log_data['isback'] = 1;
                $log_data['errormsg'] = '失败';
                $log_data['errorcode'] = '8899';
                $log_data['trxid'] = $ordid;
                $this->pay_log->updateOrder($ordid, $log_data);
                //写用户日志
                $uid = $order_info['uid'];
                
                $this->load->model('base/balance_base', 'balance_base');
                $balance = $this->balance_base->get_user_balance($uid);
                $user_log_data = array(
                    'uid' => $uid,
                    'pid' => 0,
                    'paytime' => $createOrderTime,
                    'pname' => '充值（充值失败)',
                    'money' => $money,
                    'balance' => $balance,
                    'action' => USER_ACTION_PAY_FAIL
                );
                $this->user_log_base->addUserLog($uid, $user_log_data);
            }
            file_put_contents("/tmp/llpay_log_success.txt", "异步通知 验证成功\n", FILE_APPEND);
            die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } 
        
        file_put_contents("/tmp/llpay_log_failed.txt", "异步通知 验证失败\n", FILE_APPEND);
        //验证失败
        die("{'ret_code':'9999','ret_msg':'验签失败'}");
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        
    }

    
    public function withdraw_notify(){
        $raw_post_data = file_get_contents('php://input', 'r');
        $msg = $raw_post_data;
        $this->back_withdraw_log($msg);
        
        $llpayNotify = new LLpayNotify($this->llpay_config);
        $llpayNotify->withdrawVerifyNotify();
        if ($llpayNotify->result) { //验证成功
            //获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $no_order = $llpayNotify->notifyResp['no_order'];//商户订单号
            $oid_paybill = $llpayNotify->notifyResp['oid_paybill'];//连连支付单号
            $result_pay = $llpayNotify->notifyResp['result_pay'];//支付结果，SUCCESS：为支付成功
            $money = $llpayNotify->notifyResp['money_order'];// 支付金额
            $dt_order = $llpayNotify->notifyResp['dt_order'];
            $info_order = $llpayNotify->notifyResp['info_order'];
            $date = substr($no_order, 0,14);
            $year = date('Y', strtotime($date));
            $week = date('W', strtotime($date));
            $this->load->model('base/withdraw_log_base', 'withdraw_log_base');
            $orderInfo = $this->withdraw_log_base->getLogByOrderId($no_order, $year, $week);
            if(!$orderInfo){
                die("{'ret_code':'9999','ret_msg':'order not found!'}");
                exit;
            }
            if($orderInfo['status'] != 0){
                die("{'ret_code':'9999','ret_msg':'order is complete!'}");
                exit;
            }
//             if($orderInfo['dt_order'] != $dt_order){
//                 die("{'ret_code':'9999','ret_msg':'dt_order is not equal!'}");
//                 exit;
//             }
            $status = 1;
            if($result_pay == "SUCCESS"){
                $pname = '提现成功';
                $status = 2;
                $back_status = $result_pay;
            }else if($result_pay == 'FAILURE' || $result_pay == 'CANCEL' ){
                $pname = '提现失败(' . $info_order . ',将于次日17点之回到账户)';
                $back_status = $result_pay;
                $this->config->load('cfg/banklist', true, true);
                $banklist = $this->config->item('cfg/banklist');
                
                $this->load->model('logic/user_identity_logic', 'user_identity_logic');
                $identity_result = $this->user_identity_logic->getPublicUserIdentity($orderInfo['uid'], 'all');
                $id_num = strtoupper($identity_result['idCard']);
                
                $id_name = $identity_result['realname'];
                $bank_code = $identity_result['bankcode'];
                
                $bank_name = $banklist[$bank_code]['name'];
                $account_no = $identity_result['cardno'];
                $faild_log_data = array(
                    'uid' => $orderInfo['uid'],
                    'orderid' => $no_order,
                    'money' => $orderInfo['money'],
                    'realname' => $id_name,
                    'bankname' => $bank_name,
                    'bankcode' => $bank_code,
                    'cardNo' => $account_no,
                    'back_code' => (string)$xml->body->tran_resp_code,
                    'back_msg' =>  (string)$xml->body->tran_resp_desc,
                    'logid' => $orderInfo['logid'],
                    'plat' => 'jyt',
                	'ctime' =>NOW
                );
                $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
                $failedInfo = $this->withdraw_failed_log_base->getFailedLogByOrderId($no_order);
                if(empty($failedInfo)){
                    $this->withdraw_failed_log_base->addFailedLog($faild_log_data);
                }
            }else{
                die("{'ret_code':'9999','ret_msg':'验签失败'}");
            }

            $data = array('back_status' => $back_status, 'status' => $status, 'succtime' => time(), 'status_code' => $result_pay, 'ybdrawflowid' => $oid_paybill);
            $where = array('id' => $orderInfo['id']);
            $ret = $this->withdraw_log_base->updateDrawLog($data, $where, $year, $week);
            if($ret){
                $update_data = array('orderid' => $orderInfo['orderid'], 'pname' => $pname, 'paytime' => time());
                $update_where = array('id' => $orderInfo['logid']);
                $this->load->model('base/user_log_base', 'user_log_base');
                $ret = $this->user_log_base->updateUserLogOnlyWithDraw($orderInfo['uid'], array('all', 'out'),$update_data, $update_where);
            }
            file_put_contents("/tmp/llpay_log_success.txt", "异步通知 验证成功\n" . json_encode($llpayNotify->result), FILE_APPEND);
            die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
        }
        
        file_put_contents("/tmp/llpay_log_failed.txt", "异步通知 验证失败\n", FILE_APPEND);
        //验证失败
        die("{'ret_code':'9999','ret_msg':'验签失败'}");
        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        
    }
    
    public function back_pay_log($msg){
        $logFile = '/tmp/llpay_pay_back.log' . date('Y-m-d');
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
    
    public function back_withdraw_log($msg){
        $logFile = '/tmp/llpay_withdraw_back.log' . date('Y-m-d');
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