<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_notice_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_notice';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function updateNotice($nid, $data){
          return  $this->updateDataSql($this->_table, $data, array('nid' => $nid));     
     }
     public function addNotice($data){
         return $this->insertDataSql($data, $this->_table);
     }
     public function getNoticeList($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     
     public function getNoticeBynid($nid){
         $where = array('nid' => $nid);
         return $this->selectDataSql($this->_table, $where);
     }
     public function addNoticeCache($data){
         $key = _KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_;
         return self::$container['redis_app_w']->setAdd($key, json_encode($data),1,$data['nid']);
     }
     public function delNoticeCacheBynid($nid){
         $key = _KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_ ;
         $notice = $this->getNoticeBynid($nid);
         return self::$container['redis_app_w']->setMove($key, json_encode($notice),1);
     }
     public function delNotice($nid){
         if(!$this->deleteDataSql($this->_table, array('nid' => $nid))){
             return false;
         }
         return true;
     }
     
     public function _flushNoticeDetailRedisCache($nid){
         $key = _KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_;
         $data = $this->getNoticeBynid($nid);
         self::$container['redis_default']->setAdd($key, json_encode($data), 1,$data['nid']);
     }
}