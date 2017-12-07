<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_messsend_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_notice_send';
    
    private $user_table = 'cmibank.cmibank_user_notice_';
    
    private $send_key = 'mess:send:';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function addNoticeForUser($uid, $data){
    	$table = $this->getTableIndex($uid, $this->user_table);
    	$insertid = $this->insertDataSql($data, $table);
    	if($insertid){
    		$key = _KEY_REDIS_USER_NOTICE_PREFIX_ . $uid;
    		self::$container['redis_default']->delete($key);
    	}
    	return $insertid;
    }
    
    public function getMessSendList($where, $order, $limit){
       return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addMessSend($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    
    public function getMessSendCount($where){
    	return $this->selectDataCountSql($this->_table, $where);
    }
    
    public function getMessSendById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateMessSendById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('id' => $id));;
    }
    
    public function delMessSendById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    
    public function incr($id){
    	$key = $this->send_key . $id;
    	$rtn = self::$container['redis_default']->incr($key);
    	self::$container['redis_default']->expire($key , $ttl = 600);
    	return $rtn;
    }
  	
}