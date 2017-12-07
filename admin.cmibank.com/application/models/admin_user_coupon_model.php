<?php

require_once APPPATH. 'models/base/basemodel.php';

class admin_user_coupon_model extends Basemodel{

    private $_table = 'cmibank.cmibank_user_coupon_';
    private $_coupon_view = 'cmibank.view_coupon';
    private $table_sql = ' (SELECT * FROM cmibank.cmibank_user_coupon_0 UNION 
SELECT * FROM cmibank.cmibank_user_coupon_1 UNION
SELECT * FROM cmibank.cmibank_user_coupon_2 UNION
SELECT * FROM cmibank.cmibank_user_coupon_3 UNION
SELECT * FROM cmibank.cmibank_user_coupon_4 UNION
SELECT * FROM cmibank.cmibank_user_coupon_5 UNION
SELECT * FROM cmibank.cmibank_user_coupon_6 UNION
SELECT * FROM cmibank.cmibank_user_coupon_7 UNION
SELECT * FROM cmibank.cmibank_user_coupon_8 UNION
SELECT * FROM cmibank.cmibank_user_coupon_9 UNION
SELECT * FROM cmibank.cmibank_user_coupon_10 UNION
SELECT * FROM cmibank.cmibank_user_coupon_11 UNION
SELECT * FROM cmibank.cmibank_user_coupon_12 UNION
SELECT * FROM cmibank.cmibank_user_coupon_13 UNION 
SELECT * FROM cmibank.cmibank_user_coupon_14 UNION 
SELECT * FROM cmibank.cmibank_user_coupon_15) ';
    
    private $send_key = 'coupon:send:';

    public function addCoupon($uid, $data){
    	$table = $this->getTableIndex($uid, $this->_table);
        $insertid = $this->insertDataSql($data, $table);
        if($insertid){
	        $key = _KEY_REDIS_USER_CONPON_PREFIX_ . $uid;
	        self::$container['redis_default']->delete($key);
        }
        return $insertid;
    }
    
    public function updateCoupon($data, $id, $uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$ret = $this->updateDataSql($table, $data, array('id' => $id));
    	if($ret){
	    	$key = _KEY_REDIS_USER_CONPON_PREFIX_ . $uid;
	    	self::$container['redis_default']->delete($key);
    	}
    	return $ret;
    }
    
    public function get_user_coupon_list($uid){
    	$key = _KEY_REDIS_USER_CONPON_PREFIX_ . $uid;
    	$couponList = self::$container['redis_default']->setRange($key, 0, -1, 1);
    	if(empty($couponList)){
	    	$data = $this->getUserCouponList($uid);
	    	if($data){
	    		foreach ($data as $value){
	    			self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['etime']);
	    		}
	    		self::$container['redis_default']->expire($key, 86400);
	    	}
	    	return $data;
    	}else{
    		$rtn = array();
    		foreach ($couponList as $key => $value){
    			$rtn[$key] = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    
    public function getUserCouponList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where utime is null and etime >".NOW." order by etime desc";
    	return $this->executeSql($sql);
    }
    
    public function getUserCouponDetail($uid,$id){
    	$table = $this->getTableIndex($uid, $this->_table);
    	return $this->selectDataSql($table, array('id' => $id));
    }
    
    public function getUserConponByCondition($queryparam,$offset,$psize){
    	if(!empty($queryparam['uid'])){
    		$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    		$sql = "SELECT * FROM ".$table." as c where  c.uid=".$queryparam['uid'];
    	}else{
    		$sql = "SELECT * FROM ".$this->_coupon_view." as c where  1=1 ";
    	}
    		if(!empty($queryparam['type'])){
    			$sql = $sql." and type=".$queryparam['type'];
    		}
    		if(!empty($queryparam['ptid'])){
    			$sql = $sql." and ptid =".$queryparam['ptid'];
    			
    		}
    		if(!empty($queryparam['status'])){
    			$sql = $sql." and ";
    			switch ($queryparam['status']){
    				case 1:$sql = $sql." utime is null and etime>".NOW;break;
    				case 2:$sql = $sql." utime is not null";break;
    				case 3:$sql = $sql." utime is null and etime<".NOW;break;
    			}
    		}
    		if(!empty($queryparam['sendmoney'])){
    			$sql = $sql." and sendmoney=".$queryparam['sendmoney'];
    			 
    		}
    		if(isset($queryparam['days'])){
    			$end = strtotime(date('Y-m-d',time()))+86400*($queryparam['days']+1);
    			$start = strtotime(date('Y-m-d',time()))+86400*$queryparam['days'];
    			$sql = $sql." and etime<".$end." and etime>".$start;
    		
    		}
    		if(!empty($queryparam['stime'])){
    			$stime = strtotime($queryparam['stime']);
    			$sql = $sql." and ctime>".$stime;
    		
    		}
    		if(!empty($queryparam['etime'])){
    			$etime = strtotime($queryparam['etime'])+86400;
    			$sql = $sql." and ctime<".$etime;
    		
    		}
    		$sql = $sql.' order by ctime desc limit '.$offset.','.$psize;
    		$ret = $this->executeSql($sql);
    		return $ret;
    }
    
    public function getTotalNotExpired(){
    	$sql = "SELECT count(*) as count , sum(sendmoney) as totalmoney FROM ".$this->_coupon_view." as c where utime is null and etime>".NOW;
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotalUsed(){
    	$sql = "SELECT count(*) as count , sum(sendmoney) as totalmoney FROM ".$this->_coupon_view." as c where utime is not null";
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotalExpired(){
    	$sql = "SELECT count(*) as count , sum(sendmoney) as totalmoney FROM ".$this->_coupon_view." as c where etime<".NOW." and utime is null";
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotal(){
    	$sql = "SELECT count(*) as count , sum(sendmoney) as totalmoney FROM ".$this->_coupon_view." as c ";
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function countUserConponByCondition($queryparam){
    	if(!empty($queryparam['uid'])){
    		$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    		$sql = "SELECT count(id) as count FROM ".$table." as c where  c.uid=".$queryparam['uid'];
    	}else{
    		$sql = "SELECT count(*) as count FROM ".$this->_coupon_view." as c where  1=1 ";
    	}
    	if(!empty($queryparam['type'])){
    		$sql = $sql." and type=".$queryparam['type'];
    	}
    		if(!empty($queryparam['ptid'])){
    			$sql = $sql." and ptid =".$queryparam['ptid'];
    			
    		}
    		if(!empty($queryparam['status'])){
    			$sql = $sql." and ";
    			switch ($queryparam['status']){
    				case 1:$sql = $sql." utime is null and etime>".NOW;break;
    				case 2:$sql = $sql." utime is not null";break;
    				case 3:$sql = $sql." utime is null and etime<".NOW;break;
    			}
    		}
    		if(!empty($queryparam['sendmoney'])){
    			$sql = $sql." and sendmoney=".$queryparam['sendmoney'];
    			 
    		}
    		if(isset($queryparam['days'])){
    			$end = strtotime(date('Y-m-d',time()))+86400*($queryparam['days']+1);
    			$start = strtotime(date('Y-m-d',time()))+86400*$queryparam['days'];
    			$sql = $sql." and etime<".$end." and etime>".$start;
    		
    		}
    		if(!empty($queryparam['stime'])){
    			$stime = strtotime($queryparam['stime']);
    			$sql = $sql." and ctime>".$stime;
    		
    		}
    		if(!empty($queryparam['etime'])){
    			$etime = strtotime($queryparam['etime'])+86400;
    			$sql = $sql." and ctime<".$etime;
    		
    		}
    		$ret = $this->executeSql($sql);
    		return $ret[0]['count'];
    }
    
    public function sumUserConponByCondition($queryparam){
    	if(!empty($queryparam['uid'])){
    		$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    		$sql = "SELECT sum(sendmoney) as sum_sendmoney,sum(buymoney) as sum_buymoney FROM ".$table." as c where  c.uid=".$queryparam['uid'];
    	}else{
    		$sql = "SELECT sum(sendmoney) as sum_sendmoney,sum(buymoney) as sum_buymoney FROM ".$this->_coupon_view." as c where  1=1 ";
    	}
    	if(!empty($queryparam['type'])){
    		$sql = $sql." and type=".$queryparam['type'];
    	}
    	if(!empty($queryparam['ptid'])){
    		$sql = $sql." and ptid =".$queryparam['ptid'];
    		 
    	}
    	if(!empty($queryparam['status'])){
    		$sql = $sql." and ";
    		switch ($queryparam['status']){
    			case 1:$sql = $sql." utime is null and etime>".NOW;break;
    			case 2:$sql = $sql." utime is not null";break;
    			case 3:$sql = $sql." utime is null and etime<".NOW;break;
    		}
    	}
    	if(!empty($queryparam['sendmoney'])){
    		$sql = $sql." and sendmoney=".$queryparam['sendmoney'];
    
    	}
    	if(isset($queryparam['days'])){
    			$end = strtotime(date('Y-m-d',time()))+86400*($queryparam['days']+1);
    			$start = strtotime(date('Y-m-d',time()))+86400*$queryparam['days'];
    			$sql = $sql." and etime<".$end." and etime>".$start;
    		
    		}
    	if(!empty($queryparam['stime'])){
    		$stime = strtotime($queryparam['stime']);
    		$sql = $sql." and ctime>".$stime;
    
    	}
    	if(!empty($queryparam['etime'])){
    		$etime = strtotime($queryparam['etime'])+86400;
    		$sql = $sql." and ctime<".$etime;
    
    	}
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    
    
    public function countUserCouponList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT count(id) as count FROM ".$table." where utime is null and uid=".$uid." and etime >".NOW." order by etime desc";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['count'];
    }
    
    public function incr($id){
    	$key = $this->send_key . $id;
    	$rtn = self::$container['redis_default']->incr($key);
    	self::$container['redis_default']->expire($key , $ttl = 600);
    	return $rtn;
    }
    
    public function send_coupon_msg($phone, $count,$money){
    	try {
	    	include(APPPATH . 'libraries/submail.lib.php');
	    	$submail = new submail();
	    	$values = array('count' => $count,'money' => $money);
	    	$rtn = $submail->send_msg($phone, $values, 'TA8WE4');
	    	$rtn = json_decode($rtn, true);
	    	if($rtn['status'] == 'error'){
	    		return false;
	    	}
	    	return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    
    public function sumCouponMoneyByUid($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "select sum(sendmoney) as sum_sendmoney from $table where uid=$uid and utime is not null";
    	$ret=$this->executeSql($sql);
    	return $ret[0]['sum_sendmoney'];
    }
}
