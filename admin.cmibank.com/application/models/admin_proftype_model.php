<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_proftype_model extends Basemodel {

    private $_table = 'cmibank.cmibank_proftype';
    
    public function __construct() {
        parent::__construct();
    }

    public function addproftype($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        return true;
    }
    
    public function delproftype($ptid){
        if(!$this->deleteDataSql($this->_table, array('profid' => $ptid))){
            return false;
        }
        return true;
    }
	
    public function updateproftype($ptid, $data){
        $this->updateDataSql($this->_table, $data, array('profid' => $ptid));
        
        return true;
    }
    
    public function getproftypeByProfid($ptid){
        $where = array('profid' => $ptid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getproftypeList(){
        return $this->selectDataListSql($this->_table, null, null, array(1000, 0));
    }
    
    public function getProftypeGroup(){
        $sql = "select proftype from " . $this->_table . " group by proftype";
        return $this->executeSql($sql);
    }
    
    public function getprofnamebyproftype($where){
        return $this->selectDataListSql($this->_table, $where, null , array(1000, 0));
    }
}
