<?php

//体验金购买   单个产品获得一次收益CD
require_once 'basemodel.php'; 

class exp_cd_base extends Basemodel{
    
    private $key = 'cd:';
    
    public function get($uid, $pid){
        $key = $this->getKey($uid, $pid);
        $data = self::$container['redis_app_r']->get($key);
        return $data;
    }
    
    public function set($uid, $pid){
        $key = $this->getKey($uid, $pid);
        self::$container['redis_app_w']->save($key, $pid, 86400);
        return true;
    }
    
    public function getKey($uid, $pid){
        return $this->key . ':' . date('Y-m-d') . ':' . $pid . "_" . $uid ;
    }
    
    
    
}
