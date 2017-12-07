<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
error_reporting(E_ALL);

class baofoopay_notify extends Controller {

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
        header("Content-type:text/html; charset=UTF-8");
        require_once APPPATH . 'config/cfg/baofoo_ini.php';
        $endata_content = $_REQUEST["data_content"];
        
        if(empty($endata_content)){
            die("No parameters are received [data_content]");
        }
//         Log::returnLogWirte("异步通知原文：".$endata_content);
//         echo $pfxfilename . '<br />';
//         echo $cerfilename . '<br />';
//         echo $private_key_password . '<br />';
        $baofoosdk = new BaofooSdk($pfxfilename,$jxcerfilename,$private_key_password); //实例化加密类。
        $endata_content = $baofoosdk->decryptByPublicKey($endata_content);	//RSA解密
        
        //echo $endata_content;
        Log::returnLogWirte("异步通知解密原文：".$endata_content);
        
        if(!empty($endata_content)){
            if($data_type =="xml"){
                $endata_content = SdkXML::XTA($endata_content);
            }else{
                $endata_content = json_decode($endata_content,TRUE);
            }
        }else{
            Log::returnLogWirte("异步通知解密结果：解析失败!");
            die("解析失败!");
        }
        // print_r($endata_content);
        //重要步聚
        $ordid = $endata_content['trans_id'];
        $trans_no = $endata_content['trans_no'];
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
        
        //查看订单是否已完结
        if($order_info['isback'] == 1 || $order_info['status'] == 1){
            die("{'ret_code':'9999','ret_msg':'重复的订单请求!!'}");
            exit;
        }
        
        if($endata_content['resp_code'] == '0000'){
            $money = $endata_content['succ_amt'];
            $uid = $order_info['uid'];
        	
            $this->load->model('logic/cd_logic', 'cd_logic');
        	$cd_data = $this->cd_logic->getUserCd($uid);
        	$cd_data['pay']--;
        	$this->cd_logic->setUserCd($uid, $cd_data);
            
        	$createOrderTime = $order_info['ctime'];
            
            if(!$money || $money <= 0 || $money != $order_info['amt']){
                die("{'ret_code':'9999','ret_msg':'金额错误!'}");
                exit;
            }
            $this->load->model('base/user_log_base', 'user_log_base');
            
            $log_data = array();
            $log_data['isback'] = 1;
            $log_data['status'] = 1;
            $log_data['errormsg'] = '';
            $log_data['errorcode'] = '';
            $log_data['trxid'] = $trans_no;
            $orderret = $this->pay_log->updateOrder($ordid, $log_data);
            if(!$orderret){
                die("{'ret_code':'9999','ret_msg':'重复的订单请求!'}");
            }
            if($orderret){
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
                echo "OK";//接收到通知并处理本地数据后返回OK
                exit;
            }
            
        }else{
            //失败的订单
            $log_data = array();
            $log_data['isback'] = 1;
            $log_data['errormsg'] = $endata_content['resp_msg'];
            $log_data['errorcode'] = $endata_content['resp_code'];
            $log_data['trxid'] = $trans_no;
            $orderret = $this->pay_log->updateOrder($ordid, $log_data);
            if(!$orderret){
                die("{'ret_code':'9999','ret_msg':'重复的订单请求!'}");
            }
            //写用户日志
            $uid = $order_info['uid'];
            
            $this->load->model('base/balance_base', 'balance_base');
            $balance = $this->balance_base->get_user_balance($uid);
            $user_log_data = array(
                'uid' => $uid,
                'pid' => 0,
                'paytime' => $createOrderTime,
                'pname' => '充值（' . $endata_content['resp_msg'] . ')',
                'money' => $money,
                'balance' => $balance,
                'action' => USER_ACTION_PAY_FAIL
            );
            $this->user_log_base->addUserLog($uid, $user_log_data);
            echo "OK";//接收到通知并处理本地数据后返回OK
            exit;
        }
        
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