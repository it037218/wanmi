<?php

require_once 'basemodel.php'; 

class cd_base extends Basemodel{

    private $_init = array();
    
    private $key = 'cd:';
    
    public function __construct() {
        $this->config->load('cfg/bankCardLimit', true, true);
        $bankCardLimit = $this->config->item('cfg/bankCardLimit');
        $this->_init = $bankCardLimit;
    }
    public function get($uid){
        $key = $this->key . $uid;
        $change = false;
        $data = self::$container['redis_app_r']->get($key);
        $data = json_decode($data, true);
        if(empty($data)){
            $data = $this->_init;
            self::$container['redis_app_w']->save($key, json_encode($data), 86400*31);
        }
        $month_time = strtotime(date('Y-m-01', time()));
        if($data['mt'] < $month_time){
            $data = $this->_init;
            self::$container['redis_app_w']->save($key, json_encode($data), 86400*31);
        }
        if($data['t'] < mktime(0,0,0)){
        	$temp = $data;
        	$data = $this->_init;
        	$data['free_withDraw'] = $temp['free_withDraw'];
        	$data['mt'] = $temp['mt'];
        	self::$container['redis_app_w']->save($key, json_encode($data), 86400*31);
        }
        return $data;
    }
    
    public function set($uid, $data){
        $key = $this->key . $uid;
        return self::$container['redis_app_w']->save($key, json_encode($data), 86400*31);
    }
    
    
}
