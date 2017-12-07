<?php

require_once 'basemodel.php';
class ltype_base extends Basemodel {

    private $_table = 'cmibank.cmibank_ltype';
    
    public function __construct() {
        parent::__construct();
    }

    
    public function getLtypeList(){
        $key = _KEY_REDIS_SYSTEM_LTYPE_LIST_PREFIX_;
        $data = self::$container['redis_default']->setRange($key, 0, -1, 1);
        if(empty($data)){
            $this->rebuildLtypeListRedisCache();
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
    
    public function get_db_LtypeList(){
        return $this->selectDataListSql($this->_table, null, 'rank desc, ctime desc');
    }
    
    public function rebuildLtypeListRedisCache() {
        $result = $this->get_db_LtypeList();
        if(count($result) > 0){
            $time = time();
            $key = _KEY_REDIS_SYSTEM_LTYPE_LIST_PREFIX_;
            self::$container['redis_default']->delete($key);
            foreach ($result as $_Ltype){
                $score = $_Ltype['rank'] ?  $_Ltype['rank'] * 3000000000 + $time : $_Ltype['ctime'];
                self::$container['redis_default']->setAdd($key, json_encode($_Ltype), 1, $score);
            }
        }
        return true;
    }
    
    
    
}
