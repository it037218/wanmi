<?php

require_once 'basemodel.php';
class corporation_base extends Basemodel {

    private $_table = 'cmibank.cmibank_corporation';
    
    public function __construct() {
        parent::__construct();
    }

    public function getCorpByCid($cid){
        $key = _KEY_REDIS_CORPORATION_DETAIL_PREFIX_ . $cid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $cid) {
            $corpInfo = $self->_get_db_corp_detail($cid);
            if(empty($corpInfo)) return false;
            return json_encode($corpInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_corp_detail($cid){
      return $this->DBR->select('*')->from($this->_table)->where('corid',$cid)->get()->row_array();
    }
}
