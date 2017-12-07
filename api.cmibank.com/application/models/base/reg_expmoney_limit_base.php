<?php

require_once 'basemodel.php';
class reg_expmoney_limit_base extends Basemodel{

    private $key = 'regexpmoney:incr:';

    public function incr(){
        $key = $this->key . date('Ymd');
        $rtn = self::$container['redis_app_w']->incr($key);
        return $rtn;
    }

    public function get(){
        $key = $this->key . date('Ymd');
        $rtn = self::$container['redis_app_w']->get($key);
        return $rtn;
    }

}
