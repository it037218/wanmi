<?php

require_once 'basemodel.php';  

class news_base extends Basemodel{
    
	private $_table = 'cmibank.cmibank_news';
	
    public function getNewslist($start,$end){
    	$key = _KEY_REDIS_SYSTEM_NEWS_LIST_PREFIX_;
    	$data = self::$container['redis_default']->setRange($key,$start,$end,$order=1);
    	foreach ($data as $key => $_d){
    		$data[$key] = json_decode($_d, true);
    	}
    	return $data;
    }  
    
}