<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_usercontract_model extends Basemodel {

    private $_table = 'cmibank.cmibank_usercontract';
    
    public function __construct() {
        parent::__construct();
    }

    
    public function getUsercontractList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    
    
    public function getUsercontractByUcid($ucid){
        return $this->selectDataSql($this->_table, array('ucid' => $ucid));
    }
    
    public function updateUsercontract($ucid, $data){
        return $this->updateDataSql($this->_table, $data, array('ucid' => $ucid));
    }
    
    public function getcanUseUsercontract(){
        $sql = "select * from " . $this->_table . " where tplname != '' and tplnumber != '' and profid != '' and tpl_pagename != ''";
        $rtn = $this->executeSql($sql);
        return $rtn;
    }
    
    public function addUsercontract($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
	public function delUsercontract($ucid){
	    return $this->deleteDataSql($this->_table, array('ucid' => $ucid));
	}
	
	public function getUserContractCount(){
	    return $this->selectDataCountSql($this->_table);
	}
	
	
	
}
