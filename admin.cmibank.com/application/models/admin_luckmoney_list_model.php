<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_luckmoney_list_model extends Basemodel {
    
     private $_table = 'cmibank_activity.cmibank_luckmoney_list';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function addluckmoney($data){
         return $this->insertDataSql($data, $this->_table);
     }
     
     public function getLuckMoneyList($where,$order,$limit=''){
         return $this->selectDataListSql($this->_table, $where,$order,$limit);
     }
     public function getLuckMoneyRedisList($time){
         $zerotime = mktime(0,0,0);
         $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_LIST_PREFIX_ . date('Ymd');
         $data = self::$container['redis_default']->setRangeBySorce($key, $zerotime, $time);
         if(empty($data)){
             return false;
         }
         return array_pop($data);
     }
     
     public function deleteluckmoney($lmid){
         return $this->deleteDataSql($this->_table, array('lmid' => $lmid));
     }
     
     public function getLuckMoneyLikeLname($lname,$limit=''){
         $sql = "select * from $this->_table where lname like '%$lname%' and lstime <".time()." order by lstime desc";
         if(!empty($limit)){
             $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
         }
         return $this->executeSql($sql);
     }
     
     public function getLuckMoneyEnd($limit=''){
         $sql = "select * from $this->_table where lstime < ".time()." order by lstime desc";
         if(!empty($limit)){
             $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
         }
         return $this->executeSql($sql);
     }
     public function autotianchong($lname){
         $sql = "select *  from " .$this->_table. " where lname like '%$lname%' order by lmid desc limit 1";
         return $this->executeSql($sql);
     }
     public function getLuckMoneyByLmid($lmid) {
         $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_ . $lmid;
         $self = $this;
         $return = $this->remember($key, 0 , function() use($self , $lmid) {
             $luckmoneyinfo = $self->_db_getLuckMoneyByLmid($lmid);
             if(empty($luckmoneyinfo)) return false;
             return json_encode($luckmoneyinfo);
         } , _REDIS_DATATYPE_STRING);
         return json_decode($return, true);
     }
     
     public function _db_getLuckMoneyByLmid($lmid){
         return $this->selectDataSql($this->_table, array('lmid' => $lmid));
     }
     
     public  function getLuckMoneyListCount(){
         return $this->selectDataCountSql($this->_table);
     }  

     public function updateLuckMoneyList($lmid, $data){
 
         return $this->updateDataSql($this->_table, $data, array('lmid' => $lmid));
     }
     
     //$yugaotime  uninx
     public function addLuckMoneyRedisList($lmid, $yugaotime){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_LIST_PREFIX_.date('Ymd',$yugaotime);
        $data = self::$container['redis_default']->setAdd($key, $lmid,1,$yugaotime);
        return $data;
     }
     public function delRedis($lmid,$yugaotime){
         $key1 = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_LIST_PREFIX_.date('Ymd',$yugaotime);
         self::$container['redis_default']->setMove($key1,$lmid,1);
         
         
         $key2 = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_ .$lmid;
         self::$container['redis_app_w']->delete($key2);
     }
     public function downtolineRedis($lmid){
         $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_ .$lmid;
         self::$container['redis_app_w']->delete($key);
     }
     public function countJoinPeople($lmid){
         $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_JOIN_PREFIX_.$lmid;
         $result =self::$container['redis_app_w']->setSize($key,1);
         return $result;
     }
     public function countPeople($lmid){
         $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_.$lmid;
         $result =self::$container['redis_app_w']->setSize($key,1);
         return $result;
     }
     public function getIncrRedis($lmid){
         $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_INCR_PREFIX_.$lmid;
         $result =self::$container['redis_app_w']->get($key);
         return $result;
         
     }
    
     
}