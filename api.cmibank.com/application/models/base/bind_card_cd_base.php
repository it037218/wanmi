<?php

require_once 'basemodel.php'; 

class bind_card_cd_base extends Basemodel{

    
    private $key = 'bindcard:uid:';
    
    private $jyt_validate_key = 'jyt:validate:';
    
    private $hdsj_validate_key = 'hdsj:validate:';
    
    public function incr_jyt_validate(){
    	$key = $this->jyt_validate_key . date('Ym');
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , 86400 * 40);
    	return $rtn;
    }
    
    public function incr_hdsj_validate(){
    	$key = $this->hdsj_validate_key . date('Ym');
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , 86400 * 40);
    	return $rtn;
    }
    
    public function incr($uid){
        $key = $this->key . $uid;
        $rtn = self::$container['redis_app_w']->incr($key);
        self::$container['redis_app_w']->expire($key , $ttl = 1800);
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
    
}
