<?php
/****
 * 代金券
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_userpay_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_userpay';
    
    private $_incrKey = "user:pay:";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getUserpayList($where,$order,$limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addUserpay($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getUserpayCount($where){
    	return $this->selectDataCountSql($this->_table,$where);
    }
    
    public function getUserpayById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateUserpayById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('id' => $id));
    }
    
    public function delUserpayById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    public function send_code_msg($phone,$userpayid){
    	$key = _KEY_REDIS_USERPAY_CODE_PREFIX_ . $userpayid;
    	$code = $this->createMsgCode();
    	try {
    		include(APPPATH . 'libraries/submail.lib.php');
    		$submail = new submail();
    		$values = array('code' => $code);
    		$rtn = $submail->send_msg($phone, $values, 'AUf8l3');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
	    	self::$container['redis_default']->save($key, $code, 600);
    		return true;
    
    	} catch (Exception $e) {
    		return false;
    	}
    }
    
    public function get_code_msg($userpayid){
    	$key = _KEY_REDIS_USERPAY_CODE_PREFIX_ . $userpayid;
    	$code = self::$container['redis_default']->get($key);
    	return $code;
   }    
    	
    public function incr($userpayid){
    	$key = $this->_incrKey . $userpayid;
        $rtn = self::$container['redis_default']->incr($key);
        self::$container['redis_default']->expire($key , $ttl = 30);
        return $rtn;
    }
    private function createMsgCode(){
    	$code = '';
    	for ($i = 0; $i < 4; $i++) {
    		$code .= mt_rand(0,9);
    	}
    	return $code;
    }
}