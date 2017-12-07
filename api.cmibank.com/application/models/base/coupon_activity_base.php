<?php

require_once 'basemodel.php'; 

class coupon_activity_base extends Basemodel{

    public $_table = 'cmibank.cmibank_coupon_activity';
   
    public $coupon_table = 'cmibank.cmibank_coupon';
    
    public $coupon_send_flag = 'coupon:times20170707:';
    
    public function incr_coupon_send_flag($uid){
    	$key = $this->coupon_send_flag . $uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400 * 7);
    	return $rtn;
    }
    
    public function getCouponActivityInfo($type){
    	switch ($type){
    		case COUPON_ACTIVITY_REGEDIT:$key= _KEY_REDIS_COUPON_REGEDIT_INFO_PREFIX_;break;
    		case COUPON_ACTIVITY_VALIDATE:$key= _KEY_REDIS_COUPON_VALIDATE_INFO_PREFIX_;break;
    		case COUPON_ACTIVITY_BUY:$key= _KEY_REDIS_COUPON_BUY_INFO_PREFIX_;break;
    		case COUPON_ACTIVITY_FIRSTBUY:$key= _KEY_REDIS_COUPON_FIRSTBUY_INFO_PREFIX_;break;
    	}
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $type) {
            $activityInfo = $self->_get_db_couponActivity_info($type);
            if(empty($activityInfo)) return false;
            return json_encode($activityInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_couponActivity_info($type){
    	$sql = "SELECT * FROM ".$this->_table." where status=2 and type=$type and etime >".NOW." and stime<".NOW;
    	$ret = $this->executeSql($sql);
    	if(!empty($ret)){
    		return $ret;
    	}else{
    		return null;
    	}
    }
    
    
    public function getCouponActivityDetail($type,$activityid,$cids){
    	$key='';
    	switch ($type){
    		case COUPON_ACTIVITY_REGEDIT:$key= _KEY_REDIS_COUPON_REGEDIT_DETAIL_PREFIX_.$activityid;break;
    		case COUPON_ACTIVITY_VALIDATE:$key= _KEY_REDIS_COUPON_VALIDATE_DETAIL_PREFIX_.$activityid;break;
    		case COUPON_ACTIVITY_BUY:$key= _KEY_REDIS_COUPON_BUY_DETAIL_PREFIX_.$activityid;break;
    		case COUPON_ACTIVITY_FIRSTBUY:$key= _KEY_REDIS_COUPON_FIRSTBUY_DETAIL_PREFIX_.$activityid;break;
    	}
    	$self = $this;
    	$return = $this->remember($key, 0 , function() use($self , $type,$cids) {
    		$activityDetail = $self->_get_db_couponActivity_detail($cids);
    		if(empty($activityDetail)) return false;
    		return json_encode($activityDetail);
    	} , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
    		return json_decode($return , true);
    }
    
    public function _get_db_couponActivity_detail($cids){
    	$cidArray = explode(",", $cids);
    	$sql = "SELECT * FROM ".$this->coupon_table." where (days!=0 or etime >".NOW.") and id in(";
    	foreach ($cidArray as $cid){
    		$sql = $sql.$cid.",";
    	}
    	$sql = substr($sql,0,-1);
    	$sql = $sql.")";
    	return $this->executeSql($sql);
    }
    
    public function getCouponDetail($id){
    	$key= _KEY_REDIS_COUPON_JIFENG_DETAIL_PREFIX_.$id;
    	$self = $this;
    	$return = $this->remember($key, 0 , function() use($self , $id) {
    		$couponInfo = $self->_get_db_coupon_info($id);
    		if(empty($couponInfo)) return false;
    		return json_encode($couponInfo);
    	} , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
    		return json_decode($return , true);
    }
    
    public function _get_db_coupon_info($id){
    	return $this->DBR->select('*')->from($this->coupon_table)->where('id',$id)->get()->row_array();
    }
}
