<?php
/****
 * 代金券
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_coupon_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_coupon';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCouponList($where,$order,$limit){
        return $this->selectDataListSql($this->_table,$where, $order, $limit);
    }
    
    public function getAvailableCoupon(){
    	$sql = "SELECT * from $this->_table where deleted=0 and (etime>".NOW." or days!=0)";
    	return $this->executeSql($sql);
    }
    
    public function getAvailableCouponForJifeng(){
    	$sql = "SELECT * from $this->_table where  deleted=0 and (etime>".NOW." or days!=0)";
    	return $this->executeSql($sql);
    }
    
    public function addCoupon($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getCouponCount($where){
    	return $this->selectDataCountSql($this->_table,$where);
    }
    
    public function getCouponById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateCouponById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('id' => $id));
    }
    
    public function delCouponById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
}