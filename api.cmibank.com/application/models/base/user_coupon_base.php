<?php

require_once 'basemodel.php'; 

class user_coupon_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_coupon_';
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

    private $coupon_expire_key = 'coupon:notice:';
    
    
    public function getExpKey($uid){
    	$key = $this->coupon_expire_key . $uid;
    	return self::$container['redis_default']->get($key);
    }
    
    public function setExpKey($uid){
    	$key = $this->coupon_expire_key . $uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400*3);
    	return $rtn;
    }
    
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
    	$min = NOW;
    	$max = NOW+86400*100;
    	$couponList = self::$container['redis_default']->setRangeBySorce($key, $min,$max);
    	$etime = strtotime(date('Y-m-d',time()))+259199;
    	$flag = $this->getExpKey($uid);
    	if(empty($couponList)){
	    	$data = $this->getUserCouponList($uid);
	    	if($data){
	    		foreach ($data as $value){
	    			self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['etime']);
	    			if(empty($flag) && $value['etime']>NOW && $value['etime']<=$etime && empty($value['utime'])){
	    				$this->load->model('base/user_notice_base', 'user_notice_base');
	    				$days = floor(($value['etime']-NOW)/86400);
	    				$temp_days = '今天';
	    				if($days>=1){
	    					$temp_days = $days.'天后';
	    				}
	    				$notice_data = array(
	    						'uid' => $uid,
	    						'title' => '抵用券即将过期提醒',
	    						'content' => "您有一张价值".floor($value['sendmoney'])."元的现金抵用券将于".$temp_days."过期，赶快去使用吧！",
	    						'ctime' => NOW
	    				);
	    				$this->user_notice_base->addNotice($uid,$notice_data);
	    				$this->setExpKey($uid);
	    			}
	    		}
	    		self::$container['redis_default']->expire($key, 86400);
	    	}
	    	return $data;
    	}else{
    		$rtn = array();
    		foreach ($couponList as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    			if(empty($flag) && $temp['etime']>NOW &&  $temp['etime']<=$etime && empty($temp['utime'])){
    				$this->load->model('base/user_notice_base', 'user_notice_base');
    				$days = floor(($temp['etime']-NOW)/86400);
    				$temp_days = '今天';
    				if($days>=1){
    					$temp_days = $days.'天后';
    				}
    				$notice_data = array(
	    						'uid' => $uid,
	    						'title' => '抵用券即将过期提醒',
	    						'content' => "您有一张价值".floor($temp['sendmoney'])."元的现金抵用券将于".$temp_days."过期，赶快去使用吧！",
	    						'ctime' => NOW
	    			);
    				$this->user_notice_base->addNotice($uid,$notice_data);
    				$this->setExpKey($uid);
    			}
    		}
    		return $rtn;
    	}
    }
    
    
    public function getUserCouponList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where utime is null and etime >".NOW." and uid= $uid order by etime desc";
    	return $this->executeSql($sql);
    }
    
    public function get_user_used_coupon_list($uid,$page){
    	$key = _KEY_REDIS_USER_USED_CONPON_PREFIX_ . $uid;
    	$min = ($page-1)*5;
    	$max = $page*5;
    	$couponList = self::$container['redis_default']->setRange($key, $min,$max);
    	if(empty($couponList)){
    		$data = $this->getUserUsedCouponList($uid);
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['etime']);
    			}
    			self::$container['redis_default']->expire($key, 86400);
    		}
    		$couponList = self::$container['redis_default']->setRange($key, $min,$max);
    	}
    	
    	if(empty($couponList)){
    		return null;
    	}else{
    		$rtn = array();
    		foreach ($couponList as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    
    
    public function getUserUsedCouponList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where utime>0  and uid= $uid order by etime desc limit 50";
    	return $this->executeSql($sql);
    }
    
    public function get_user_expired_coupon_list($uid,$page){
    	$key = _KEY_REDIS_USER_EXPIRED_CONPON_PREFIX_ . $uid;
    	$min = ($page-1)*5;
    	$max = $page*5;
    	$couponList = self::$container['redis_default']->setRange($key, $min,$max);
    	if(empty($couponList)){
    		$data = $this->getUserExpiredCouponList($uid);
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['etime']);
    			}
    			self::$container['redis_default']->expire($key, 86400);
    		}
    		$couponList = self::$container['redis_default']->setRange($key, $min,$max);
    	}
    	
    	if(empty($couponList)){
    		return null;
    	}else{
    		$rtn = array();
    		foreach ($couponList as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    public function getUserExpiredCouponList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where utime is null and etime <".NOW." and uid= $uid order by etime desc limit 50";
    	return $this->executeSql($sql);
    }
    
    public function getUserCouponDetail($uid,$id){
    	$table = $this->getTableIndex($uid, $this->_table);
    	return $this->selectDataSql($table, array('id' => $id));
    }
    
    public function getUserCouponUsedbyDate($odate){
    	$time = strtotime($odate);
    	$start_time = $time;
    	$end_time = $time + 86400;
    	$sql = "SELECT sum(sendmoney) as sendmoney FROM ".$this->table_sql." as c where utime is not null and utime<$end_time and utime>$time";
    	$data = $this->executeSql($sql);
    	return $data[0]['sendmoney'];
    }
}
