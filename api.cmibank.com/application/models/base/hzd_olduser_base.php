<?php

require_once 'basemodel.php'; 
//活期转定期
class hzd_olduser_base extends Basemodel{

    private $_table = 'cmibank_yunying.cmibank_hzd_olduser';
    
    public function add($data){
        return $this->insertDataSql($data, $this->_table);
    }   
    
    public function del_uid($uid = 0){
        return $this->deleteDataSql($this->_table, array('uid' => $uid));
    }

    public function del($oid){
        return $this->deleteDataSql($this->_table, array('oid' => $oid));
    }
    
    public function getList($psize, $offset){
        return $this->selectDataListSql($this->_table, NULL, NULL, array($psize, $offset));
    }
    
}
