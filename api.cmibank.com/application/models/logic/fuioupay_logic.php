<?php

require_once APPPATH.'models/base/basemodel.php'; 
include(APPPATH.'libraries/fuiou.class.php');

class fuioupay_logic extends Basemodel {
    
    private $withdrawTable = 'cmibank_log.cmibank_withdraw_failed_log';

    private $fuioupay;

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        
        $this->config->load('cfg/fuiou_config', true, true);
        $fuioupay_config = $this->config->item('cfg/fuiou_config');
        $this->fuioupay = new fuiou($fuioupay_config);
    }
    
    public function withdraw($orderno, $bankno, $cityno , $accntno ,$accntnm, $amount) {
        $data = $this->fuioupay->withdraw($orderno, $bankno, $cityno , $accntno ,$accntnm, $amount);
        return $data;
    }
    
    public function VerifySign($post,$sign) {
        return $this->fuioupay->VerifySign($post,$sign);
    }
    
    public function queryWithDrawOrder($orderno,$startdt,$enddt,$transst) {
        return $this->fuioupay->queryWithDrawOrder($orderno,$startdt,$enddt,$transst);
    }
    
    public function queryWithDrawStatus($orderno,$startdt,$enddt,$transst) {
        return $this->fuioupay->queryWithDrawStatus($orderno,$startdt,$enddt,$transst);
    }
    
    public function queryFailWithDraw($orderid) {
        return $this->selectDataSql($this->withdrawTable,array('orderid' => $orderid));
    }
    
}