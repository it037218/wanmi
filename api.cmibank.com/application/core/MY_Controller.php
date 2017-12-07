<?php

require_once APPPATH . 'libraries/base.lib.php';
# 加密类
require_once APPPATH . 'libraries/xxtea.php';

/**
 * controller
 */
class Controller extends baseController {

    const COOKIE_KEY     = '19840322';  //存储Cookie的加密私钥
    //返回值状态
    const AJ_RET_SUCC = 200;
    const AJ_RET_FAIL = 300;
    const AJ_RET_FORB = 300;
    const AJ_RET_NOLOGIN = 301;
    
    private $all_ip = array();
    
    // 返回值类型
    const JSON = 'application/json';
    const HTML = 'text/html';
    const JAVASCRIPT = 'text/javascript';
    const JS = 'text/javascript';
    const TEXT = 'text/plain';
    const XML = 'text/xml';
    
    protected $uid = 0;
    protected $account = 0;
    protected $lock_request = false;
    public function __construct() {
        parent :: __construct();
        $this->uid = $this->getCookie('uid');
        $this->account = $this->getCookie('account');
    }
    
    public function check_login($lock = true){
        if($this->uid == 0){
            $response = array('error'=> 1000, 'msg'=>'请先登录');
            $this->out_print($response);
        }
        if($this->uid && $lock){
            $this->load->model('base/lock_base', 'lock_base');
            
            $lock_ret = $this->lock_base->addredislock($this->uid, $this->uri->uri_string);
            $this->lock_request = true;
            if(!$lock_ret){
                $response = array('error' => 111111, 'msg' => '抱歉！您的操作太频繁了，请稍后试试');
                $this->out_print($response, 'json', false);
            }
        }
        return $this->uid;
    }
    
    public function check_link(){
        return true;
//         if($this->getIP() == '127.0.0.1'){
//             return true;
//         }
        
//         if($this->getIP() == '116.226.147.81'){
//             return true;
//         }
        
//         if($_SERVER['HTTPS'] != 'on' || $_SERVER['HTTP_SCHEME'] != 'https'){
//             $response = array('error' => 3333, 'msg' => '非法请求！');
//             $this->out_print($response, 'json', false);
//         }
    }
    
    public function out_print($result, $format = 'json', $drop_lock = true, $log = false, $data = array()) {
        $allowOrigin = array('http://api.cmibank.vip', 'http://www.cmibank.com', 'http://api.cmibank.com');
        $origin = $this->origin();
        if (!in_array($origin, $allowOrigin)) {
            $origin = '*';
        }
//        header("Access-Control-Allow-Origin:http://api.cmibank.com");
        header("Access-Control-Allow-Origin:" . $origin);
        header("Access-Control-Allow-Credentials: true");
        $result['serverip'] = '0.0.0.0';
        //$result['serverip'] = $_SERVER["SERVER_ADDR"];
        if (!empty($result)) {
            echo $format == 'json' ? json_encode($result) : $result;
        }
//         var_dump($drop_lock);
//         var_dump($this->lock_request);
        if ($drop_lock && $this->lock_request == true) {
            $this->load->model('base/lock_base', 'lock_base');
            $lock_ret = $this->lock_base->delredislock($this->uid, $this->uri->uri_string);
        }
        if ($log) {
            $this->load->model('base/error_log_base', 'error_log_base');
            $this->error_log_base->addLog($result, $data);
        }
        exit;
    }

    public function origin()
	{
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			$origin = $_SERVER['HTTP_ORIGIN'];
		} else if (isset($_SERVER['HTTP_REFERER'])) {
			$url = $_SERVER['HTTP_REFERER'];
			$ary = parse_url($url);
			$origin = $ary['scheme'].'://'.$ary['host'];
		} else {
			$origin = '*';
		}
		return $origin;
	}
    
    public  function cookieEncode($content)
    {
        $content .= '|' . NOW;
        return base64_encode(lib_xxtea::encrypt($content, self::COOKIE_KEY));
    }
    
/**
	 *解密COOKIE内容
	 * @param type $content
	 * @return type bool
	 */
	public static function cookieDecode($content)
	{
		$ret = lib_xxtea::decrypt(base64_decode($content), self::COOKIE_KEY);
		if($ret) {
			$row = explode('|', $ret);
			if(isset($row[2]))
			{
				$row=substr($ret, 0,-11);
				return $row;
			}
			return $row[0];
		}else {
			return false;
		}
	}
	
	public static function setCookies($params, $expire=0,$domainHost=NULL)
	{
	    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	    if(intval($expire)>1)$expireTime =NOW + intval($expire);
	    if(!is_array($params)) {
	        return false;
	    }
	    foreach($params as $key => $value) {
			$cookieValue = self::cookieEncode($value);
	        setcookie(PF.'_'.$key, $cookieValue, $expire, '/', $domainHost);
	    }
	    return $cookieValue;
	}
	
	/**
	 *获取COOKIE内容
	 * @param type $key
	 * @return string
	 */
	public static function getCookie($key)
	{
	    $val='';
	    $name=PF.'_'.$key;
	    if(isset($_COOKIE[$name])) {
	        
	        $val = self::cookieDecode($_COOKIE[$name]);
	    }
	    return $val;
	}
	
	
	public function error_log($msg){
	    $logFile = '/tmp/crontab_error.log';
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
	
	public function buy_log($msg){
	    $logFile = '/tmp/buy_error.log';
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
	
	public function buy_long_log($msg){
	    $logFile = '/tmp/buy_long_error.log';
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
    
	public function ltp_log($msg){
	    $logFile = '/tmp/ltp_log.log';
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
	
	public function invite_err_log($msg){
	    if(isset($_SERVER['SystemRoot']) && ($_SERVER['SystemRoot'] == 'C:\Windows' || $_SERVER['SystemRoot'] == 'C:\WINDOWS')){
	        $logFile = './invite_err_log.'.date("Y-m-d");
	    }else{
	        $logFile = '/tmp/invite_err_log.log.'.date("Y-m-d");
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
	
	
	public function no_failed_log($msg){
	    if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
	        $logFile = './no_failed_log.'.date("Y-m-d");
	    }else{
	        $logFile = '/tmp/no_failed_log.'.date("Y-m-d");
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
	
	public function crontab_run($msg, $filename = 'crontab_run_log.'){
	    if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
	       $logFile = './'. $filename .date("Y-m-d");
	    }else{
	       $logFile = '/tmp/'. $filename .date("Y-m-d");
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
	
	public function wee_withdraw_log($msg){
	    if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
	        $logFile = './wee_withdraw_log.log.'.date("Y-m-d");
	    }else{
	        $logFile = '/tmp/wee_withdraw_log.log.'.date("Y-m-d");
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

	public function encode_invite($uid){
	    return base64_encode($uid + 9999);
	}
	
	public function decode_invite($code){
	    return base64_decode($code) - 9999;
	}
	
	//用户取现检查    用户UID, 用户要取现的钱
	public function check_withdraw_money($uid, $do_withdraw_money){
	    $this->load->model('base/longmoney_base', 'longmoney_base');
	    $longmoney = $this->longmoney_base->getUserLongMoney($uid);
	    //echo "活期:" .  $longmoney . '<br />';
	    $this->load->model('base/balance_base', 'balance_base');
	    $balance = $this->balance_base->get_user_balance($uid);
	    //echo "余额:" .  $balance . '<br />';
	    //定期
	    $this->load->model('logic/user_logic', 'user_logic');
	    $sum_product = $this->user_logic->get_not_finished_product($uid);
	    //echo "定期:" . $sum_product . '<br />';
	    //充值
	    $this->load->model('base/user_log_base', 'user_log_base');
	    $pay_money = $this->user_log_base->sum_money_by_action($uid, 0);
	    //echo "充值:" .  $pay_money . '<br />';
	    //取现
	    $withdraw_money = $this->user_log_base->sum_money_by_action($uid, 2);
	    //echo "取现:" .  $withdraw_money . '<br />';
	    //活期利息
	    $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
	    $lprofit = $this->ulp_profit_log_base->sum_user_longproduct_profit($uid);
	    //echo "活期利息:" . $lprofit . '<br />';
	    //定期利息
	    $sum_product_profit = $this->user_logic->get_finished_product_profit($uid);
	    //echo "定期利息:" . $sum_product_profit . '<br />';
	    //活动奖励
	    $activity_money = $this->user_log_base->sum_money_by_action($uid, 5);
	    //echo "活动奖励:" . $activity_money . '<br />';
	    //邀请奖励
	    $invite_reward_money = $this->user_log_base->sum_money_by_action($uid, 6);
            
	    //受邀请首投奖励
	    $be_invite_reward_money = $this->user_log_base->sum_money_by_action($uid, 44);
	    //echo "邀请奖励:" . $invite_reward_money . '<br />';
	    //体验金
	    $tiyangjing_money = $this->user_log_base->sum_money_by_action($uid, 7);
	    //echo "体验金奖励发放:" . $tiyangjing_money . '<br />';
	
	    $withdraw_failed = $this->user_log_base->sum_money_by_action($uid, 20);
	    //echo "取现失败:" . $withdraw_failed . '<br />';
	    $withdraw_back = $this->user_log_base->sum_money_by_action($uid, 21);
	    //echo "取现退回:" . $withdraw_back . '<br />';
	
	    //加的钱
	    $add_money = $pay_money + $activity_money + $invite_reward_money + $be_invite_reward_money + $tiyangjing_money + $sum_product_profit + $lprofit;
	    //拥有的钱
	    $has_money = $balance + $sum_product + $longmoney;
	
	    $cost_money = $withdraw_money + $do_withdraw_money;
	    $diff = $has_money - ($add_money - $withdraw_money);
	    if($diff > 10){
	        //记录
	        $log = array(
	            'uid' => $uid,
	            'do_withdraw' => $do_withdraw_money,
	            'diff' => $diff
	        );
	        $this->crontab_run(json_encode($log), 'do_withdraw_check.');
	        return false;
	    }else{
	        return true;
	    }
	}
	
	
	
}