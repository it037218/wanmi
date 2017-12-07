<?php
error_reporting(E_ALL);
require_once (APPPATH . 'libraries/llpay/waplib/llpay_submit.class.php');
class llpay_wap_logic extends CI_Model {
    
    private $llpay_notify;
    private $llpay_config;

    private $llpay_gateway_new = 'https://yintong.com.cn/traderapi/cardandpay.htm'; //支付
  
    private $notify_url;
    
    private $return_url;
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->config->load('cfg/llpay_config', true, true);
        $this->llpay_config = $this->config->item('cfg/llpay_config');
        $this->notify_url = DOMAIN . 'llpay_notify/pay_notify';
        $this->return_url = "https://api.cmibank.com/h5/home.php";
    }   
    
    public function bulidRequestForm($parameter){
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        $parameter["oid_partner"] = trim($this->llpay_config['oid_partner']);
        $parameter["app_request"] = trim($this->llpay_config['app_request']);
        $parameter["sign_type"] = trim($this->llpay_config['sign_type']);
        $parameter["valid_order"] = trim($this->llpay_config['valid_order']);
        /************************************************************/
        $parameter["notify_url"] = $this->notify_url;
        $parameter["url_return"] = $this->return_url;
        //建立请求
        $wapllpaySubmit = new WAPLLpaySubmit($this->llpay_config);
        $html_text = $wapllpaySubmit->buildRequestForm($parameter, "post", "确认");
        return $html_text;
    }
    
    
}


   
