<?php

require_once 'basemodel.php'; 

class luckybag_accepted_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_luckybag_accepted';
    
	public function add($data){
        $insertid = $this->insertDataSql($data, $this->_table);
        return $insertid;
    }
    
    public function get_user_accepted_luckybag_list($uid){
    	$key = _KEY_REDIS_USER_LUCKYBAG_ACCEPTED_LIST_PREFIX_ . $uid;
    	$couponList = self::$container['redis_default']->setRange($key, 0, -1, 1);
    	if(empty($couponList)){
    		$data = $this->getUserLuckybagList($uid);
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['utime']);
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
    
    public function getUserLuckybagList($uid){
    	$sql = "SELECT * FROM $this->_table where uid= $uid or uuid = $uid order by utime desc";
    	return $this->executeSql($sql);
    }
    
    public function getYesLuckybag($odate){
    	$stime = strtotime($odate);
    	$etime = $stime+86400;
    	$sql = "select sum(money) as sum_money from $this->_table where utime<$etime and utime>$stime";
    	$ret = $this->executeSql($sql);
    	return empty($ret[0]['sum_money'])?0:$ret[0]['sum_money']*2;
    }
}
