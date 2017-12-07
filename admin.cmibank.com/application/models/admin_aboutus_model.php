<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_aboutus_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_aboutus';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function addAboutus($data){
         if(!$this->insertDataSql($data, $this->_table)){
             return false;
         }
         $this->rebuildAboutusListRedisCache();
         return true;
     }
     public function getAboutusList($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     public  function getAboutusCount(){
         return $this->selectDataCountSql($this->_table);
     }
     public function getAboutusByAid($aid){
         return $this->selectDataSql($this->_table, array('aid' => $aid));
     }
     public function editAboutus($aid, $data){
         return $this->updateDataSql($this->_table, $data, array('aid'=>$aid));
     }
     public function delAboutus($aid){
        if(!$this->deleteDataSql($this->_table, array('aid' => $aid))){
            return false;
        }
        return true;
     }
     public function getAboutusListByLiketitle($name, $limit = ''){
         $sql = "SELECT * FROM $this->_table WHERE `title` like '%".$name ."%' order by ctime desc";
         if(!empty($limit)){
             $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
         }
         return $this->executeSql($sql);
     }
     public function rebuildAboutusListRedisCache() {
         $result = $this->getAboutusList('','ctime desc','');
         $key = _KEY_REDIS_SYSTEM_ABOUTUS_LIST_PREFIX_;
         $time = time();
         self::$container['redis_default']->delete($key);
         foreach ($result as $_k=>$aboutus){         
            self::$container['redis_default']->setAdd($key, json_encode($aboutus), 1,  $aboutus['aid']);
         }
         return true;
     }
     
}