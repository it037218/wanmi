<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

/**
 * 活期合同模板
 * * */
class admin_klproductcontract_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_klproductcontract';
    
    public function __construct(){
        parent::__construct();
    }
    
    public function addKlproductcontract($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->setCache($data['cid'], $data);
        return true;
    }
    
    public function editKlproductcontract($cid,$data){
        $this->updateDataSql($this->_table, $data, array('cid'=>$cid));
        $this->setCache($data['cid'], $data);
        return true;
    }
    
    public function setCache($cid, $data){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCTCONTRACT_DETAIL_PREFIX_. $cid;
        self::$container['redis_default']->save($key, json_encode($data));
    }
    
    public function moveCache($cid){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCTCONTRACT_DETAIL_PREFIX_. $cid;
        self::$container['redis_default']->delete($key);
    }
    
    public function delKlproductcontract($cid){
        if(!$this->deleteDataSql($this->_table, array('cid' => $cid))){
            return false;
        }
        $this->moveCache($cid);
        return true;
    }
    
    public function getKlproductcontractList($where,$order,$limit){
        return $this->selectDataListSql($this->_table,$where,$order,$limit);
    }
    
    public function  getKlproductcontractCount(){
        return $this->selectDataCountSql($this->_table);
    }
    
    public function getKlproductcontractByCid($cid){
        return $this->selectDataSql($this->_table, array('cid' => $cid));
    }
    
    public function updateKlproductcontract($cid,$data){
       $this->updateDataSql($this->_table, $data, array('cid' => $cid));
       $this->moveCache($cid);
       return true;
    }
    
    public function rebuildBannerListRedisCache() {
        $result = $this->getKlproductcontractList('','ctime desc','');
        if(count($result) > 0){
            $time = time();
            $format_arr = array();
            foreach ($result as $lpconract){ 
                $key = _KEY_REDIS_SYSTEM_KLPRODUCTCONTRACT_DETAIL_PREFIX_. $lpconract['cid'];
                self::$container['redis_default']->save($key, json_encode($lpconract));
            }
        }
        return true;
    }
    
}