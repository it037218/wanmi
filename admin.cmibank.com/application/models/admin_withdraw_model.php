<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_withdraw_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_withdraw';
    
    private $withdraw_log_table = 'cmibank_log.cmibank_withdraw_log';
    
    private $_incrKey = "user:withdraw:";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getWithdrawList($where,$order,$limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function addWithdraw($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    public function getWithdrawCount($where){
    	return $this->selectDataCountSql($this->_table,$where);
    }
    
    public function getWithdrawById($id){
        return $this->selectDataListSql($this->_table, array('id'=>$id));
    }
    
    public function updateWithdrawById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('id' => $id));
    }
    
    public function delWithdrawById($id){
    	return $this->deleteDataSql($this->_table, array('id'=>$id));
    }
    public function send_code_msg($phone,$withdrawid){
    	$key = _KEY_REDIS_WITHDRAW_CODE_PREFIX_ . $withdrawid;
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
    
    public function get_code_msg($withdrawid){
    	$key = _KEY_REDIS_WITHDRAW_CODE_PREFIX_ . $withdrawid;
    	$code = self::$container['redis_default']->get($key);
    	return $code;
   }    
    	
    public function incr($withdrawid){
    	$key = $this->_incrKey . $withdrawid;
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
    
    public function getWithdrawLogIndex($data_y = '', $data_w = ''){
    	$data_y = $data_y ? $data_y : date("Y");
    	$data_w = $data_w ? $data_w : date("W");
    	if($data_w == 52){
    		$data_y = '2016';
    	}
    	return $this->withdraw_log_table . '_' . $data_y . '_' . $data_w;
    }
    
    public function addWithdrawLog($data){
    	$table = $this->getWithdrawLogIndex();
    	return $this->insertDataSql($data, $table);
    }
}