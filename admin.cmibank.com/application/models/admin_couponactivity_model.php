<?php
/****
 * 代金券活动
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_couponactivity_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_coupon_activity';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getCouponActivityList($where,$order,$limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addCouponActivity($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getCouponActivityCount(){
    	return $this->selectDataCountSql($this->_table);
    }
    
    public function getCouponActivityById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateCouponActivityById($id, $data){
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if($ret){
    		$conpon = $this->getCouponActivityById($id);
    		$infokey='';
    		$detailkey='';
    		switch ($conpon[0]['type']){
    			case COUPON_ACTIVITY_REGEDIT:
    				$infokey= _KEY_REDIS_COUPON_REGEDIT_INFO_PREFIX_;
    				$detailkey= _KEY_REDIS_COUPON_REGEDIT_DETAIL_PREFIX_.$id;
    				break;
    			case COUPON_ACTIVITY_VALIDATE:
    				$infokey= _KEY_REDIS_COUPON_VALIDATE_INFO_PREFIX_;
    				$detailkey= _KEY_REDIS_COUPON_REGEDIT_DETAIL_PREFIX_.$id;
    				break;
    			case COUPON_ACTIVITY_BUY:
    				$infokey= _KEY_REDIS_COUPON_BUY_INFO_PREFIX_;
    				$detailkey= _KEY_REDIS_COUPON_BUY_DETAIL_PREFIX_.$id;
    				break;
    			case COUPON_ACTIVITY_FIRSTBUY:
    				$infokey= _KEY_REDIS_COUPON_FIRSTBUY_INFO_PREFIX_;
    				$detailkey= _KEY_REDIS_COUPON_FIRSTBUY_DETAIL_PREFIX_.$id;
    				break;
    		}
    		self::$container['redis_default']->delete($infokey);
    		self::$container['redis_default']->delete($detailkey);
    		return true;
    	}
    	return false;
    }
    
    public function downlineCouponActivityById($id){
    	$data['status'] = 3;
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if($ret){
    		
    	}
    	return true;
    }
    
    public function delCouponActivityById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
    public function getOnlineCouponActivity($type){
    	$sql = "SELECT * from $this->_table where status=2 and type= $type and etime>".NOW;
    	return $this->executeSql($sql);
    }
    
}