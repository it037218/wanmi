<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_redbag_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_redbag_log';
    private $redbag_table = 'cmibank.cmibank_redbag';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getRedbagList($param,$limit=''){
    	$sql = "select redbag.*, count(log.id) as acceptcounts from $this->redbag_table as redbag left join $this->_table as log on redbag.id=log.rid where redbag.deleted=0 ";
    	if(isset($param['code'])){
    		$sql = $sql." and redbag.code like '%".$param['code']."%' ";
    	}
    	if(isset($param['name'])){
    		$sql = $sql." and redbag.name like '%".$param['name']."%' ";
    	}
    	$sql = $sql." group by redbag.id order by redbag.id desc ";
    	if(!empty($limit)){
    		$sql = $sql." limit ".$limit[0].",".$limit[1];
    	}
    	return $this->executesql($sql);
    }
    
    public function getRedbagListByAccount($account){
    	$sql = "SELECT * from $this->_table where phone=$account  order by ctime desc";
    	return $this->executeSql($sql);
    }
    public function getAvailableRedbag(){
    	$sql = "SELECT * from $this->redbag_table where deleted=0 and (etime>".NOW." or days!=0)";
    	return $this->executeSql($sql);
    }
    
    public function addRedbag($data){
    	if(!$this->insertDataSql($data, $this->redbag_table)){
    		return false;
    	}
    	return true;
    }
    public function getRedbagCount($where){
    	return $this->selectDataCountSql($this->redbag_table,$where);
    }
    
    public function getRedbagById($id){
    	return $this->selectDataListSql($this->redbag_table, array('id'=>$id));
    }
    
    public function updateRedbagById($id, $data){
    	return $this->updateDataSql($this->redbag_table, $data, array('id' => $id));
    }
    
    public function delRedbagById($id){
    	return $this->deleteDataSql($this->redbag_table, array('id'=>$id));
    }
    
    public function getRedbagLogById($rid){
    	return $this->selectDataListSql($this->_table,array('rid'=>$rid), 'ctime desc', '');
    }
    
    public function sumRedbagByUid($account){
    	$sql= "select sum(money) as sum_money from $this->_table where phone=$account and utime is not null";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }
}