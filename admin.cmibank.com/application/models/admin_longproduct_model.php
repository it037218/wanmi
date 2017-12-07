<?php
/****
 * 活期权限管理
 * **/
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_longproduct_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_longproduct';
    
    public function __construct() {
        parent::__construct();
    } 
    
    public function addLongproduct($data){
         return $this->insertDataSql($data, $this->_table);
    }
    public function autotianchong($pname){
        $sql = "select *  from " .$this->_table. " where pname like '%$pname%' order by pid desc limit 1";
        return $this->executeSql($sql);
    }
    public function getLongproductList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function getLongproductlikepname($searchpname, $limit){
        
        $sql = "SELECT * FROM $this->_table WHERE `pname` like '%".$searchpname ."%' order by `pid` desc";
        
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    public function getLongproduct($start,$end, $limit){
        $sql = "SELECT * FROM $this->_table WHERE `odate` BETWEEN '$start' and '$end' order by `pid` desc";

        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public  function getLongproductCount(){
        return $this->selectDataCountSql($this->_table);
    }
    
    public function getLongproductByPid($pid){
        return $this->selectDataSql($this->_table, array('pid' => $pid));
    }
    
    public function _flushlongproductDetailRedisCache($pid){
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_.$pid;
        self::$container['redis_app_w']->delete($key);
        $ret = $this->getRedisLongproductDetailInfo($pid);
        return $ret;
    }
    
    public function getRedisLongproductDetailInfo($pid) {
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_. $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $currentInfo = $self->getLongproductByPid($pid);
            if(empty($currentInfo)) return false;
            return json_encode($currentInfo);
        } , _REDIS_DATATYPE_STRING);
        return json_decode($return, true);
    }
    
    public function updateLongproduct($pid, $data){
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
    public function addLongporduct($data){
        return $this->insertDataSql($data, $this->_table);
    }
    public function delLongproduct($pid){
        if(!$this->deleteDataSql($this->_table, array('pid' => $pid))){
            return false;
        }
        return true;
    }
    public function moveYugaoLongProduct($ptid,$pid){
        $key = _KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ptid;
        $longproduct = $this->getLongproductByPid($pid);
        $new_data = array('online_time'=>strtotime($longproduct['online_time']),'pid'=>$longproduct['pid'],'ptid'=>$ptid);
        self::$container['redis_app_w']->setMove($key,json_encode($new_data) ,1);
    }
    public function moveOnlineLongproduct($ptid, $pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_app_w']->listRemove($key, $pid);
    }
    
    public function getLongproductDetail($pid){
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_ . $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $longproductInfo = $self->_get_db_longproduct_detail($pid);
            if(empty($longproductInfo)) return false;
            return json_encode($longproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_longproduct_detail($pid){
        return $this->DBR->select('*')->from($this->_table)->where('pid',$pid)->get()->row_array();
    }
    
    public function addlongProductToSellOutList($pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_LONGPRODUCT_LIST_PREFIX_ . $odate;
        $longproduct = $this->getLongproductDetail($pid);
        return self::$container['redis_default']->setAdd($sellOutkey, json_encode($longproduct), 1, $longproduct['sellouttime']);
    }
    /**
     *
     * @param unknown $id
     * @param unknown $status 2下架,  3售罄,  4停售
     */
    public function updateLongproductStatus($pid, $data){
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    public function getAllMoney($uid){
    	$table = $this->getTableIndex($uid, 'cmibank.cmibank_userlongproduct_');
    	$sql = "SELECT SUM(money) as totalmoney,count(*) as totalcount  FROM ".$table." WHERE `uid` =  ".$uid;
    	$ret =  $this->executeSql($sql);
    	return $ret;
    }
}