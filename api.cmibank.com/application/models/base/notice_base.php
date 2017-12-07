<?php

require_once 'basemodel.php'; 

class notice_base extends Basemodel{
    
	private $_table = 'cmibank.cmibank_notice';
	
    public function getNoticelistCode($start,$end){
        $key = _KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_;
        $data = self::$container['redis_app_r']->setRange($key,$start,$end,$order=0);
        if(empty($data)){
        	$this->rebuildNoticeRedisCache();
        	$data = self::$container['redis_default']->setRange($key,$start,$end,$order=0);
        }
        foreach ($data as $key => $_d){
            $data[$key] = json_decode($_d, true);
        }
        return $data;
    }  
    
    public function get_db_notice(){
    	return $this->selectDataListSql($this->_table, array('status' => '1'), 'ctime desc');
    }
    
    public function rebuildNoticeRedisCache() {
    	$result = $this->get_db_notice();
    	if(count($result) > 0){
    		$key = _KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_;
    		self::$container['redis_default']->delete($key);
    		foreach ($result as $_notice){
    			self::$container['redis_default']->setAdd($key, json_encode($_notice), 1, $_notice['nid']);
    		}
    	}
    	return true;
    }
    
    public function del(){
        $key = _KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_;
        return self::$container['redis_app_r']->delete($key);
    }
}