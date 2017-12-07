<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_kltype_model extends Basemodel {

    private $_table = 'cmibank.cmibank_kltype';
    
    public function __construct() {
        parent::__construct();
    }

    public function addKltype($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->rebuildkltypeListRedisCache();
        return true;
    }
    
    
    public function delKltype($ptid){
        if(!$this->deleteDataSql($this->_table, array('ptid' => $ptid))){
            return false;
        }
        $this->rebuildkltypeListRedisCache();
        return true;
    }
	
    public function updateLtype($ptid, $data){
        $this->updateDataSql($this->_table, $data, array('ptid' => $ptid));
        $this->rebuildkltypeListRedisCache();
        return true;
    }
    
    public function getKltypeByctid($ptid){
        $where = array('ptid' => $ptid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getKltypeList(){
        return $this->selectDataListSql($this->_table, null, 'rank desc, ctime desc');
    }
    
    public function rebuildkltypeListRedisCache() {
        $result = $this->getkltypeList();
        if(count($result) > 0){
            $time = time();
            $key = _KEY_REDIS_SYSTEM_KLTYPE_LIST_PREFIX_;
            self::$container['redis_default']->delete($key);
            foreach ($result as $_ltype){
                $score = $_ltype['rank'] ?  $_ltype['rank'] * 3000000000 + $time : $_ltype['ctime'];
                self::$container['redis_default']->setAdd($key, json_encode($_ltype), 1, $score);
            }
        }
        return true;
    }
    
    public function check_kltype_can_del($ptid){
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ .$ptid;
        return self::$container['redis_default']->listSize($key);
    }
    
}
