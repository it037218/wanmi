<?php

require_once 'basemodel.php';
class equalptype_base extends Basemodel {

    private $_table = 'cmibank.cmibank_equalptype';
    
    public function __construct() {
        parent::__construct();
    }

    
    public function getPtypeList(){
        $key = _KEY_REDIS_SYSTEM_EQUALPTYPE_LIST_PREFIX_;
        $data = self::$container['redis_default']->setRange($key, 0, -1, 1);
        if(empty($data)){
            $this->rebuildPtypeListRedisCache();
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
    
    public function get_db_PtypeList(){
        return $this->selectDataListSql($this->_table, null, 'rank desc, ctime desc');
    }
    
    public function rebuildPtypeListRedisCache() {
        $result = $this->get_db_PtypeList();
        if(count($result) > 0){
            $time = time();
            $key = _KEY_REDIS_SYSTEM_EQUALPTYPE_LIST_PREFIX_;
            self::$container['redis_default']->delete($key);
            foreach ($result as $_ptype){
                $score = $_ptype['rank'] ?  $_ptype['rank'] * 3000000000 + $time : $_ptype['ctime'];
                self::$container['redis_default']->setAdd($key, json_encode($_ptype), 1, $score);
            }
        }
        return true;
    }
    
    
    
}
