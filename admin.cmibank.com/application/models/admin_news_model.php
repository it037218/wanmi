<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_news_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_news';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getNewsList($where, $order, $limit){
       return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addNews($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    
    public function getNewsCount($where){
    	return $this->selectDataCountSql($this->_table, $where);
    }
    
    public function getNewsById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateNewsById($id, $data){
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if ($ret){
    		return $this->rebuildNewsRedisCache();
    	}else{
    		return $ret;
    	}
    }
    
    public function delNewsById($id){
    	$ret = $this->deleteDataSql($this->_table, array('id'=>$id));
    	if ($ret){
    		return $this->rebuildNewsRedisCache();
    	}else{
    		return $ret;
    	}
    }
    public function rebuildNewsRedisCache() {
    	$result = $this->get_db_News();
    	if(count($result) > 0){
    		$key = _KEY_REDIS_SYSTEM_NEWS_LIST_PREFIX_;
    		self::$container['redis_default']->delete($key);
    		foreach ($result as $_News){
    			self::$container['redis_default']->setAdd($key, json_encode($_News), 1, $_News['ctime']);
    		}
    	}
    	return true;
    }
    
    public function get_db_News(){
    	return $this->selectDataListSql($this->_table, array('status'=>1), 'ctime desc');
    }
}