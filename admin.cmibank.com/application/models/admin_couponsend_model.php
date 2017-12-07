<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_couponsend_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_coupon_send';
    
    public $coupon_table = 'cmibank.cmibank_coupon';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCouponSendList($where, $order, $limit){
       return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addCouponSend($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    
    public function getCouponSendCount($where){
    	return $this->selectDataCountSql($this->_table, $where);
    }
    
    public function getCouponSendById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateCouponSendById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('id' => $id));;
    }
    
    public function delCouponSendById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
  	
  	public function getCouponByCids($cids){
    	$cidArray = explode(",", $cids);
    	$sql = "SELECT * FROM ".$this->coupon_table." where (days!=0 or etime >".NOW.") and id in(";
    	foreach ($cidArray as $cid){
    		$sql = $sql.$cid.",";
    	}
    	$sql = substr($sql,0,-1);
    	$sql = $sql.")";
    	return $this->executeSql($sql);
    }	
}