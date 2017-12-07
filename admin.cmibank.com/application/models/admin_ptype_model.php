<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_ptype_model extends Basemodel {

    private $_table = 'cmibank.cmibank_ptype';
    
    public function __construct() {
        parent::__construct();
    }

    public function addptype($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->rebuildPtypeListRedisCache();
        return true;
    }
    
    
    public function delPtype($ptid){
        if(!$this->deleteDataSql($this->_table, array('ptid' => $ptid))){
            return false;
        }
        $this->rebuildPtypeListRedisCache();
        return true;
    }
	
    public function updatePtype($ptid, $data){
        $this->updateDataSql($this->_table, $data, array('ptid' => $ptid));
        $this->rebuildPtypeListRedisCache();
        return true;
    }
    
    public function getPtypeByPtid($ptid){
        $where = array('ptid' => $ptid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getPtypeList(){
        return $this->selectDataListSql($this->_table, null, 'rank desc, ctime desc');
    }
    
    public function rebuildPtypeListRedisCache() {
        $result = $this->getPtypeList();
        if(count($result) > 0){
            $time = time();
            $key = _KEY_REDIS_SYSTEM_PTYPE_LIST_PREFIX_;
            self::$container['redis_default']->delete($key);
            foreach ($result as $_ptype){
                $score = $_ptype['rank'] ?  $_ptype['rank'] * 3000000000 + $time : $_ptype['ctime'];
                self::$container['redis_default']->setAdd($key, json_encode($_ptype), 1, $score);
            }
        }
        return true;
    }
    
    public function check_ptype_can_del($ptid){
        $key = _KEY_REDIS_SYSTEM_ONLINE_PRODUCT_LIST_PREFIX_ . $ptid;
        return self::$container['redis_default']->listSize($key);
    }
    
}
