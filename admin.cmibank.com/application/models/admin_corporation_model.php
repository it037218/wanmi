<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_corporation_model extends Basemodel {

    private $_table = 'cmibank.cmibank_corporation';
    
    public function __construct() {
        parent::__construct();
    }

    public function getCorporationList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function getAllCorporation(){
        $sql = "select * from " . $this->_table . " order by ctime";
        return $this->executeSql($sql);
    }
    
    public function getCorporationByCid($cid){
        return $this->selectDataSql($this->_table, array('corid' => $cid));
    }
    
    public function updateCorporation($cid, $data){
        return $this->updateDataSql($this->_table, $data, array('corid' => $cid));
    }
    
    public function addCorporation($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
	public function getCorporationCount(){
	    return $this->selectDataCountSql($this->_table);
	}
	
	public function getcoridByCname($cname){
	    return $this->selectDataSql($this->_table, array('cname' => $cname));
	}
	public function getcnnamelistBycname($cname){
	    $sql = "select cname from " . $this->_table . " where cname like '%$cname%'";
	    return $this->executeSql($sql);
	}
	public function getCorporationInCorid($corids){
	    $sql = "select * from $this->_table where corid in ($corids)";
	    return $this->executeSql($sql);
	}
	public function delCorporationByCorid($corid){
		return $this->deleteDataSql($this->_table, array('corid' => $corid));
	}
	
	
}
