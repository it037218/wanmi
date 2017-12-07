<?php
/****
 * 快乐宝权限管理
 * **/
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_klproduct_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_klproduct';
    
    public function __construct() {
        parent::__construct();
    } 
    
    public function addKlproduct($data){
         return $this->insertDataSql($data, $this->_table);
    }
    
    public function autotianchong($pname){
        $sql = "select *  from " .$this->_table. " where pname like '%$pname%' order by pid desc limit 1";
        return $this->executeSql($sql);
    }
    
    public function getKlproductList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function getKlproductlikepname($searchpname, $limit){
        $sql = "SELECT * FROM $this->_table WHERE `pname` like '%".$searchpname ."%' order by `pid` desc";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public function getKlproduct($start,$end, $limit){
        $sql = "SELECT * FROM $this->_table WHERE `odate` BETWEEN '$start' and '$end' order by `pid` desc";

        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public  function getKlproductCount(){
        return $this->selectDataCountSql($this->_table);
    }
    
    public function getKlproductByPid($pid){
        return $this->selectDataSql($this->_table, array('pid' => $pid));
    }
    
    public function _flushklproductDetailRedisCache($pid){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_.$pid;
        self::$container['redis_app_w']->delete($key);
        $ret = $this->getRedisKlproductDetailInfo($pid);
        return $ret;
    }
    
    public function getRedisKlproductDetailInfo($pid) {
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_. $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $currentInfo = $self->getKlproductByPid($pid);
            if(empty($currentInfo)) return false;
            return json_encode($currentInfo);
        } , _REDIS_DATATYPE_STRING);
        return json_decode($return, true);
    }
    
    public function updateKlproduct($pid, $data){
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
    public function addKlporduct($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function delKlproduct($pid){
        if(!$this->deleteDataSql($this->_table, array('pid' => $pid))){
            return false;
        }
        return true;
    }
    
    public function moveYugaoKlProduct($ptid,$pid){
        $key = _KEY_REDIS_SYSTEM_YUGAO_KLPRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ptid;
        $klproduct = $this->getKlproductByPid($pid);
        $new_data = array('online_time'=>strtotime($klproduct['online_time']),'pid'=>$klproduct['pid'],'ptid'=>$ptid);
        self::$container['redis_app_w']->setMove($key,json_encode($new_data) ,1);
    }
    
    public function moveOnlineKlproduct($ptid, $pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_app_w']->listRemove($key, $pid);
    }
    
    public function getKlproductDetail($pid){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_ . $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $klproductInfo = $self->_get_db_klproduct_detail($pid);
            if(empty($klproductInfo)) return false;
            return json_encode($klproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_klproduct_detail($pid){
        return $this->DBR->select('*')->from($this->_table)->where('pid',$pid)->get()->row_array();
    }
    
    public function addklProductToSellOutList($pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_KLPRODUCT_LIST_PREFIX_ . $odate;
        $klproduct = $this->getKlproductDetail($pid);
        return self::$container['redis_default']->setAdd($sellOutkey, json_encode($klproduct), 1, $klproduct['sellouttime']);
    }
    
    /**
     * @param unknown $id
     * @param unknown $status 2下架,  3售罄,  4停售
     */
    public function updateKlproductStatus($pid, $data){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
}