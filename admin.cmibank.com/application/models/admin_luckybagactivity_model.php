<?php
/****
 * 代金券活动
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_luckybagactivity_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_luckybag_activity';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getLuckybagActivityList($where,$order,$limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addLuckybagActivity($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getLuckybagActivityCount(){
    	return $this->selectDataCountSql($this->_table);
    }
    
    public function getLuckybagActivityById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateLuckybagActivityById($id, $data){
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if($ret){
    		$luckybag = $this->getLuckybagActivityById($id);
    		$activityKey = _KEY_REDIS_LUCKYBAG_BUY_INFO_PREFIX_;
    		self::$container['redis_default']->delete($activityKey);
    		self::$container['redis_default']->delete(_KEY_REDIS_LUCKYBAG_BUY_INFO_DETAIL_PREFIX_.$luckybag[0]['lid']);
    		if($luckybag[0]['status']==2){
    			self::$container['redis_default']->save($activityKey, json_encode($luckybag[0]),0);
    		}
    		return true;
    	}
    	return false;
    }
    
    
    public function delLuckybagActivityById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
    public function getOnlineLuckybagActivity($type){
    	$sql = "SELECT * from $this->_table where status=2 and type= $type and etime>".NOW;
    	return $this->executeSql($sql);
    }
    
}