<?php

require_once 'basemodel.php';
class klproductcontract_base extends Basemodel {

    private $_table = 'cmibank.cmibank_klproductcontract';
    
    public function __construct() {
        parent::__construct();
    }

    public function getContractByCid($cid){
        $key = _KEY_REDIS_SYSTEM_KLRODUCTCONTRACT_DETAIL_PREFIX_ . $cid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $cid) {
            $contractInfo = $self->_get_db_contract_detail($cid);
            if(empty($contractInfo)) return false;
            return json_encode($contractInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_contract_detail($cid){
        return $this->DBR->select('*')->from($this->_table)->where('cid', $cid)->get()->row_array();
    }
    
    
    public function setKlproductCache($klproduct){
        $this->load->model('base/klproduct_base', 'klproduct_base');
        return $this->klproduct_base->setKlProductCache($klproduct);
    }
    
}
