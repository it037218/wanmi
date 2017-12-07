<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_expmoneyactivity_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_expmoney_activity';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getExpmoneyActivityList($where,$order,$limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addExpmoneyActivity($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getExpmoneyActivityCount(){
    	return $this->selectDataCountSql($this->_table);
    }
    
    public function getExpmoneyActivityById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateExpmoneyActivityById($id, $data){
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if($ret){
    		$expmoneyActivity = $this->getExpmoneyActivityById($id);
    		$infokey='';
    		switch ($expmoneyActivity[0]['type']){
    			case EXPMONEY_ACTIVITY_REGEDIT:
    				$infokey= _KEY_REDIS_EXPMONEY_REG_DETAIL_PREFIX_;
    				break;
    		}
    		self::$container['redis_default']->delete($infokey);
    		return true;
    	}
    	return false;
    }
    
    public function downlineExpmoneyActivityById($id){
    	$data['status'] = 3;
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	return true;
    }
    
    public function delExpmoneyActivityById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
    public function getOnlineExpmoneyActivity($type){
    	$sql = "SELECT * from $this->_table where status=2 and type= $type and etime>".NOW;
    	return $this->executeSql($sql);
    }
    
}