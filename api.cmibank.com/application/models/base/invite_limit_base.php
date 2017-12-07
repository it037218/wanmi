<?php

require_once 'basemodel.php';
class invite_limit_base extends Basemodel{

    private $key = 'invite:limit:1123';
    
    private $key2 = 'invite:limit:1124';//邀请人记录

    public function incr(){
        $key = $this->key;
        $rtn = self::$container['redis_app_w']->incr($key);
        self::$container['redis_app_w']->expire($key , 86400 * 30);
        return $rtn;
    }
    
    public function incr2(){
        $key = $this->key2;
        $rtn = self::$container['redis_app_w']->incr($key);
        self::$container['redis_app_w']->expire($key , 86400 * 30);
        return $rtn;
    }

    public function get($orderid){
        $key = $this->key;
        $rtn = self::$container['redis_app_w']->get($key);
        return $rtn;
    }
    
    public function get2(){
        $key = $this->key2;
        $rtn = self::$container['redis_app_w']->get($key);
        return $rtn;
    }

    public function delete($_key){
        $key = $this->key . $_key;
        $rtn = self::$container['redis_app_w']->delete($key);
        return $rtn;
    }
}
