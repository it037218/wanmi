<?php

require_once (APPPATH . 'libraries/llpay/lib/llpay_notify.class.php');
require_once (APPPATH . 'libraries/llpay/lib/llpay_submit.class.php');
class llpay_logic extends CI_Model {
    
    private $llpay_notify;
    private $llpay_config;

    private $llpay_gateway_new = 'https://yintong.com.cn/traderapi/cardandpay.htm'; //支付
    
    private $llpay_query_url = 'https://yintong.com.cn/traderapi/orderquery.htm';
    
    private $llpay_bind_card_url = 'https://yintong.com.cn/traderapi/userbankcard.htm';
    
    private $llpay_unbind_card_url = 'https://yintong.com.cn/traderapi/bankcardunbind.htm';
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->config->load('cfg/llpay_config', true, true);
        $this->llpay_config = $this->config->item('cfg/llpay_config');
        
        //$this->llpay_notify = new LLpayNotify($llpay_config);
        
    }   
    
    public function bulidPaySign($parameter){
        $keys = array("busi_partner","dt_order","info_order",
                          "money_order","name_goods","no_order",
                          "notify_url","oid_partner","risk_item",
                          "sign_type","valid_order");
        
        $parameter['oid_partner'] = $this->llpay_config['oid_partner'];
        $toSignParams = $parameter;
        
        foreach ($toSignParams as $key => $val){
            if(!in_array($key, $keys)){
                unset($toSignParams[$key]);
            }
        }
        $llpaySubmit = new LLpaySubmit($this->llpay_config);
        ksort($toSignParams);
        $sign = $llpaySubmit->buildRequestMysign($toSignParams);
        $parameter['sign'] = $sign;
        return $parameter;
    }
    
    
    public function withDraw($parameter){
        $llpaySubmit = new LLpaySubmit($this->llpay_config);
        $parameter['oid_partner'] = $this->llpay_config['oid_partner'];
        //print_r($parameter);
        $html_text = $llpaySubmit->buildRequestJSON($parameter, $this->llpay_gateway_new);
        return $html_text;
    }
    
    
    public function queryOrder($parameter){
        $llpaySubmit = new LLpaySubmit($this->llpay_config);
        
        $parameter['oid_partner'] = $this->llpay_config['oid_partner'];
//         print_r($parameter);
        $html_text = $llpaySubmit->buildRequestJSON($parameter, $this->llpay_query_url);
        return $html_text;
    }
    
    //查询绑定信息
    public function querybindcard($parameter){
        $llpaySubmit = new LLpaySubmit($this->llpay_config);
        $parameter['oid_partner'] = $this->llpay_config['oid_partner'];
        $html_text = $llpaySubmit->buildRequestJSON($parameter, $this->llpay_bind_card_url);
        return $html_text;
    }
    
    public function unbindcard($parameter){
        $llpaySubmit = new LLpaySubmit($this->llpay_config);
        $parameter['oid_partner'] = $this->llpay_config['oid_partner'];
        $html_text = $llpaySubmit->buildRequestJSON($parameter, $this->llpay_unbind_card_url);
        return $html_text;
    }
    
}


   
