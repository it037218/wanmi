<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_banner_model extends Basemodel {

    private $_table = 'cmibank.cmibank_banner';
    
    public function __construct() {
        parent::__construct();
    }

    public function addBanner($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        return true;
    }
    
    public function delBanner($bid){
        if(!$this->deleteDataSql($this->_table, array('bid' => $bid))){
            return false;
        }
        //$this->delBannerCacheBylocation($bid, $localtion);
        return true;
    }
	
    public function updateBanner($bid, $data){
        $this->updateDataSql($this->_table, $data, array('bid' => $bid));
        return true;
    }
    
    public function getBannerByBid($bid){
        $where = array('bid' => $bid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    
    public function getBannerList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    
    public function getBannerCount(){
        return $this->selectDataCountSql($this->_table);
    }
    
    
    public function updateBannerbylocation($location, $data){
        $this->updateDataSql($this->_table, $data, array('location' => $location));
        return true;
    }
   
    public function getBannerListByLiketitle($name, $limit = '') {
        $sql = "SELECT * FROM $this->_table WHERE `title` like '%".$name ."%' order by ctime desc";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql); 
      
    }
    public function getBannerbetweenTime($stime,$etime, $limit = '') {
        $sql = "SELECT * FROM $this->_table WHERE `ctime` BETWEEN $stime and $etime order by ctime desc";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public function getBannerCacheAllLocation(){
        $key = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX;
        $data = self::$container['redis_app_w']->hashGet($key, array(), 1);
        return $data;
    }
    
    public function getBannerCacheByLocation($location){
        $key = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX;
        $data = self::$container['redis_app_r']->hashGet($key,$location);
        return json_decode($data, true);
    }
   
    public function addBannerCacheByLocation($info){
        $key = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX;
        return self::$container['redis_app_w']->hashSet($key, array($info['location'] => json_encode($info)));
    }
    
    public function delBannerCacheBylocation($location){
        $key = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX ;
        return self::$container['redis_app_w']->hashDel($key, $location);
    }
    
    public function countBannerCache(){
        $key = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX ;
        return self::$container['redis_app_r']->hashLen($key);
    }
}
