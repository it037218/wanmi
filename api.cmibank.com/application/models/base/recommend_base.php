<?php

require_once 'basemodel.php'; 

class recommend_base extends Basemodel{

    public function getrecommend(){
        $key = _KEY_REDIS_COMPETITIVE_PREFIX_;
        return self::$container['redis_default']->get($key);
    }
    
}
