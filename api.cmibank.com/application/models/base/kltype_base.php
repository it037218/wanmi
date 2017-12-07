<?php

require_once 'basemodel.php';
class kltype_base extends Basemodel {

    private $_table = 'cmibank.cmibank_kltype';
    
    public function __construct() {
        parent::__construct();
    }

    
    public function getKltypeList(){
        $key = _KEY_REDIS_SYSTEM_KLTYPE_LIST_PREFIX_;
        $data = self::$container['redis_default']->setRange($key, 0, -1, 1);
        if(empty($data)){
            $this->rebuildKltypeListRedisCache();
            $data = self::$container['redis_default']->setRange($key, 0, -1, 1);
        }
        $rtn_array = array();
       
        foreach($data as $value){
            $value = json_decode($value, true);
            unset($value['rank'], $value['ctime']);
            $rtn_array[$value['ptid']] = $value;
        }
        return $rtn_array;
    }
    
    public function get_db_KltypeList(){
        return $this->selectDataListSql($this->_table, null, 'rank desc, ctime desc');
    }
    
    public function rebuildKltypeListRedisCache() {
        $result = $this->get_db_KltypeList();
        if(count($result) > 0){
            $time = time();
            $key = _KEY_REDIS_SYSTEM_KLTYPE_LIST_PREFIX_;
            self::$container['redis_default']->delete($key);
            foreach ($result as $_Kltype){
                $score = $_Kltype['rank'] ?  $_Kltype['rank'] * 3000000000 + $time : $_Kltype['ctime'];
                self::$container['redis_default']->setAdd($key, json_encode($_Kltype), 1, $score);
            }
        }
        return true;
    }
    
    
    
}
