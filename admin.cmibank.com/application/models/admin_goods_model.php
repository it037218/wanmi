<?php
/****
 * 代金券
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_goods_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_goods';
    private $duihuan_table = 'cmibank.cmibank_duihuang';
    public function __construct() {
        parent::__construct();
    }
    
    public function getGoodsList($where,$limit){
       	$sql = "SELECT * from $this->_table  where deleted=0  order by status asc ,rank desc limit ".$limit[1].", ".$limit[0];
    	return $this->executeSql($sql);
    }
    
    public function addGoods($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getGoodsCount($where){
    	return $this->selectDataCountSql($this->_table,$where);
    }
    
    public function getGoodsById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateGoodsById($id, $data){
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if($ret){
	    	self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_GOODS_DETAIL_.$id);
	    	self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.'1');
	    	self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.'0');
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public function delGoodsById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
}