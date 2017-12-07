<?php

require_once 'basemodel.php'; 

class pay_redis_base extends Basemodel{

    private $key = 'payorder:orderid:';
    
    private $fuiouback = 'fuioupayorder:orderid:';
    
    private $keepAndoridVersion = 'andorid:version:';
    
    private $pay_tpwd_times = 'tpwd_pay:';
    private $buy_tpwd_times = 'tpwd_buy:';
    private $withdraw_tpwd_times = 'tpwd_withdraw:';
    
    private $weehours_times = 'weehours:';
    
    private $soldproduct = 'soldProduct:';
    
    public function incrSoldproduct($pid){
    	$key = $this->soldproduct . $pid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400);
    	return $rtn;
    }
    
    public function incrweehourstimes($orderid){
    	$key = $this->weehours_times . $orderid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400);
    	return $rtn;
    }
    
    public function addredislock($key, $ttl=0){
    	if(empty($ttl)){
	    	$ttl = 3600;
    	}
    	$ret = self::$container['redis_app_w']->save($key, 1, $ttl, 0, 1);
    	if($ret){
    		self::$container['redis_app_w']->expire($key , $ttl);
    	}
    	return $ret;
    }
    
    public function delredislock($key){
    	return self::$container['redis_app_w']->delete($key);
    }
    
 
    public function incrbuytpwdtimes($account){
    	$key = $this->buy_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 10800);
    	return $rtn;
    }
    
    public function getbuytpwdtimes($account){
    	$key = $this->buy_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    public function delbuytpwdtimes($account){
    	$key = $this->buy_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->delete($key);
    	return $rtn;
    }
    public function incrwithdrawtpwdtimes($account){
    	$key = $this->withdraw_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 10800);
    	return $rtn;
    }
    
    public function getwithdrawtpwdtimes($account){
    	$key = $this->withdraw_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    public function delwithdrawtpwdtimes($account){
    	$key = $this->withdraw_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->delete($key);
    	return $rtn;
    }
    public function incrpaytpwdtimes($account){
    	$key = $this->pay_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 10800);
    	return $rtn;
    }
    
    public function getpaytpwdtimes($account){
    	$key = $this->pay_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    public function delpaytpwdtimes($account){
    	$key = $this->pay_tpwd_times . $account;
    	$rtn = self::$container['redis_app_w']->delete($key);
    	return $rtn;
    }
    
	public function incrAndoridVersion($uid){
    	$key = $this->keepAndoridVersion . $uid;
        $rtn = self::$container['redis_app_w']->incr($key);
        self::$container['redis_app_w']->expire($key , $ttl = 3);
        return $rtn;
    }
    public function expAndoridVersion($uid){
    	$key = $this->keepAndoridVersion . $uid;
    	$rtn = self::$container['redis_app_w']->delete($key);
    	return $rtn;
    }
    
    
    public function getAndoridVersion($uid){
    	$key = $this->keepAndoridVersion . $uid;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    public function incr($orderid){
        $key = $this->key . $orderid;
        $rtn = self::$container['redis_app_w']->incr($key);
        self::$container['redis_app_w']->expire($key , $ttl = 30);
        return $rtn;
    }
    
    public function setfuiouorder($orderid){
    	$key = $this->fuiouback . $orderid;
    	$rtn = self::$container['redis_app_w']->save($key, 1, 600);
    	return true;
    }
    public function getfuiouorder($orderid){
    	$key = $this->fuiouback . $orderid;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    public function get($orderid){
        $key = $this->key . $orderid;
        $rtn = self::$container['redis_app_w']->get($key);
        return $rtn;
    }
    
    public function delete($orderid){
        $key = $this->key . $orderid;
        $rtn = self::$container['redis_app_w']->delete($key);
        return $rtn;
    }
    
    public function getWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_RESTRICT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    public function getDefaultWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
}
