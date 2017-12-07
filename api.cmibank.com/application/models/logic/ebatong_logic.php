<?php
require (APPPATH . 'libraries/ebatong.class.php');
class ebatong_logic extends CI_Model {
    
    private $ebatong_api;

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->config->load('cfg/ebatong_config', true, true);
        $ebatong_config = $this->config->item('cfg/ebatong_config');
        $this->ebatong_api = new ebatong($ebatong_config);
        
    }
    
    public function getDynNum($customer_id, $card_no, $real_name, $cert_no, $cert_type, $out_trade_no, $amount, $bank_code, $card_bind_mobile_phone_no){
        $data = $this->ebatong_api->getDynNum($customer_id, $card_no, $real_name, $cert_no, $cert_type, $out_trade_no, $amount, $bank_code, $card_bind_mobile_phone_no);
        return $data;
    }
    
    
}


   
