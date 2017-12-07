<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_version_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_version';
    
    public function __construct(){
        parent::__construct();
    }
    
    public function getVersionListByLikefeatures($name, $limit = ''){
        $sql = "SELECT * FROM $this->_table WHERE `number` like '%".$name ."%' order by ctime desc";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public function getVersionList($where,$order,$limit){
        return $this->selectDataListSql($this->_table,$where,$order,$limit);
    }
    
    public  function getVersionCount(){
        return $this->selectDataCountSql($this->_table);
    }
    
    public function addVersion($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->rebuildVersionListRedisCache();
        return true;
    }
    
    function rebuildVersionListRedisCache(){
         $result = $this->getVersionList('','ctime desc','');
         $key = _KEY_REDIS_SYSTEM_VERSION_LIST_PREFIX_;
         $time = time();
         self::$container['redis_default']->delete($key);
         foreach ($result as $_k=>$version){         
            self::$container['redis_default']->setAdd($key, json_encode($version), 1,  $version['vid']);
         }
         return true;
    }
    
    public function delVersion($vid){
        if(!$this->deleteDataSql($this->_table, array('vid' => $vid))){
            return false;
        }
        return true;
    }
    public function getVersionByVid($vid){
        return $this->selectDataSql($this->_table, array('vid' => $vid));
    }
    
    public function editVersion($vid, $data){
       $this->updateDataSql($this->_table, $data, array('vid'=>$vid));
       $this->rebuildVersionListRedisCache();
       return true;
       
    }
}