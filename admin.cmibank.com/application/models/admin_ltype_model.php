<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_ltype_model extends Basemodel {

    private $_table = 'cmibank.cmibank_ltype';
    
    public function __construct() {
        parent::__construct();
    }

    public function addLtype($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->rebuildltypeListRedisCache();
        return true;
    }
    
    
    public function delLtype($ptid){
        if(!$this->deleteDataSql($this->_table, array('ptid' => $ptid))){
            return false;
        }
        $this->rebuildltypeListRedisCache();
        return true;
    }
	
    public function updateLtype($ptid, $data){
        $this->updateDataSql($this->_table, $data, array('ptid' => $ptid));
        $this->rebuildltypeListRedisCache();
        return true;
    }
    
    public function getLtypeByctid($ptid){
        $where = array('ptid' => $ptid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getLtypeList(){
        return $this->selectDataListSql($this->_table, null, 'rank desc, ctime desc');
    }
    
    public function rebuildltypeListRedisCache() {
        $result = $this->getltypeList();
        if(count($result) > 0){
            $time = time();
            $key = _KEY_REDIS_SYSTEM_LTYPE_LIST_PREFIX_;
            self::$container['redis_default']->delete($key);
            foreach ($result as $_ltype){
                $score = $_ltype['rank'] ?  $_ltype['rank'] * 3000000000 + $time : $_ltype['ctime'];
                self::$container['redis_default']->setAdd($key, json_encode($_ltype), 1, $score);
            }
        }
        return true;
    }
    
    public function check_ltype_can_del($ptid){
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ .$ptid;
        return self::$container['redis_default']->listSize($key);
    }
    
}
