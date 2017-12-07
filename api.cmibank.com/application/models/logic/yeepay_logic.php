<?php
require (APPPATH . 'libraries/yeepay.class.php');
class yeepay_logic extends CI_Model {
    
    private $yeepay_api;

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->config->load('cfg/yeepay_config', true, true);
        $yeepay_config = $this->config->item('cfg/yeepay_config');
        $this->yeepay_api = new yeepay($yeepay_config);
        
    }   
    
    public function bandcard($identityid, $requestid, $cardno, $idcardno, $username, $phone, $userip){
        $data = $this->yeepay_api->bindBankcard($identityid, $requestid, $cardno, $idcardno, $username, $phone, $userip);
        return $data;
    }
    
    public function bindBankcardConfirm($requestid, $validatecode){
        $data = $this->yeepay_api->bindBankcardConfirm($requestid, $validatecode);
        return $data;
    }
    
    
    public function directPayment($orderid, $transtime, $amount, $productname, $identityid, $card_top, $card_last, $orderexpdate, $userip){
        $data = $this->yeepay_api->directPayment($orderid, $transtime, $amount, $productname, $identityid, $card_top, $card_last, $orderexpdate, $userip);
        return $data;
    }
    
    public function withdraw($requestid, $identityid, $card_top, $card_last, $amount, $userip){
        $data = $this->yeepay_api->withdraw($requestid, $identityid, $card_top, $card_last, $amount, $userip);
        return $data;
    }
    
    public function withdrawQuery($requestid, $ybdrawflowid) {
        $data = $this->yeepay_api->withdrawQuery($requestid, $ybdrawflowid);
        return $data;
    }
    
    public function confirmPayment($orderid){
        $data = $this->yeepay_api->confirmPayment($orderid);
        return $data;
    }
    
    public function queryOrder($orderid){
        $data = $this->yeepay_api->queryOrder($orderid);
        return $data;
    }
    
    public function bankcardList($identityid, $identitytype){
        $data = $this->yeepay_api->bankcardList($identityid, $identitytype);
        return $data;
    }
    
    //解析易宝返回
    public function parseReturn($data, $encryptkey){
        return $this->yeepay_api->parseReturn($data, $encryptkey);
    }
    
    public function queryWithdrawBlance($data, $encryptkey){
        return $this->yeepay_api->queryWithdrawBlance();
    }
    
}


   
