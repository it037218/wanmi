<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_txredistest_model extends Basemodel {
  
    public function test(){
        $key = "test_redis";
        echo $key;
        echo self::$container['redis_tx']->save($key, "测试数据");
        echo self::$container['redis_tx']->get($key);
    }
    
}