<?php

require_once 'basemodel.php'; 
define('LOCK_KEY', 'lock:user:');
class lock_base extends Basemodel{

    public function addredislock($uid, $uri_str){
        $key = LOCK_KEY . $uri_str . $uid;
        $ttl = 10;
        $ret = self::$container['redis_app_w']->save($key, 1, $ttl, 0, 1);
        if($ret){
            self::$container['redis_app_w']->expire($key , $ttl);
        }
        return $ret;
    }
    
    public function delredislock($uid, $uri_str){
        $key = LOCK_KEY . $uri_str . $uid;
        return self::$container['redis_app_w']->delete($key);
    }
    
    
}
