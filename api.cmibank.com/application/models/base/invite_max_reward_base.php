<?php

require_once 'basemodel.php';
class invite_max_reward_base extends Basemodel{

    private $key = 'invite:max_reward:';
    
    private $yangmao_key = 'invite:yangmao:';
    
    private $newyq_key = 'invite:max_reward1130:';

    public function incrSignal($uid){
    	$key = $this->newyq_key . $uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400 * 6);
    	return $rtn;
    }
    
    public function incr($uid, $money){
        $key = $this->key . $uid;
        $rtn = self::$container['redis_app_w']->incrWithValue($key, $money);
        self::$container['redis_app_w']->expire($key , 86400 * 30);
        return $rtn;
    }

    public function get($uid){
        $key = $this->key . $uid;
        $rtn = self::$container['redis_app_w']->get($key);
        return $rtn;
    }

    public function delete($uid){
        $key = $this->key . $uid;
        $rtn = self::$container['redis_app_w']->delete($key);
        return $rtn;
    }
    
    public function incr_yangmao($uid){
    	$key = $this->yangmao_key . $uid;
    	$rtn = self::$container['redis_default']->incr($key);
    	return $rtn;
    }
}
