<?php
/****
 * 充值
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_chongzhi_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_chongzhi';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getChongzhiList($limit){
        return $this->selectDataListSql($this->_table, null, 'ctime desc', $limit);
    }
    
    public function getChongzhiByCondition($where,$offset,$psize){
    	$sql = "SELECT * from $this->_table where 1=1";
    	if(!empty($where['type'])){
    		$sql = $sql." and type=".$where['type'];
    	}
    	if(!empty($where['stime'])){
    		$stime = strtotime($where['stime']);
    		$sql = $sql." and ctime>".$stime;
    		}
    	if(!empty($where['etime'])){
    		$etime = strtotime($where['etime']);
    		$sql = $sql." and ctime<".$etime;	
    	}
    	$sql = $sql.' order by ctime desc limit '.$offset.','.$psize;
    	return $this->executeSql($sql);
    }
    
    public function countChongzhiByCondition($where){
    	$sql = "SELECT count(id) as count from $this->_table where 1=1";
    	if(!empty($where['type'])){
    		$sql = $sql." and type=".$where['type'];
    	}
    	if(!empty($where['stime'])){
    		$stime = strtotime($where['stime']);
    		$sql = $sql." and ctime>".$stime;
    	}
    	if(!empty($where['etime'])){
    		$etime = strtotime($where['etime']);
    		$sql = $sql." and ctime<".$etime;
    	}
    	$ret = $this->executeSql($sql);
    	return $ret[0]['count'];
    }
    
    public function addChongzhi($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getChongzhiCount(){
    	return $this->selectDataCountSql($this->_table);
    }
    
    public function getChongzhiById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateChongzhiById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('id' => $id));
    }
    
    public function sumStockMoneyInclued(){
    	$sql = "SELECT sum(money) as sum_money from $this->_table";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_money'];
    }
    public function delChongzhiById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
}