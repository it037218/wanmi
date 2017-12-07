<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_ranksend_model extends Basemodel {

    private $_table = 'cmibank.cmibank_ranksend';
    
    private $_incrKey = "user:ranksend:";
    
    public function __construct() {
        parent::__construct();
    }

    public function add($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        return true;
    }
    
    public function update($bid, $data){
        $this->updateDataSql($this->_table, $data, array('id' => $bid));
        return true;
    }
    
    public function delete($bid){
        $this->deleteDataSql($this->_table, array('id' => $bid));
        return true;
    }
    
    public function getByBid($bid){
        $where = array('id' => $bid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function getCount($where){
        return $this->selectDataCountSql($this->_table, $where);
    }
    public function send_code_msg($phone,$bid){
    	$key = _KEY_REDIS_RANKSEND_CODE_PREFIX_ . $bid;
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
    public function get_code_msg($bid){
    	$key = _KEY_REDIS_RANKSEND_CODE_PREFIX_ . $bid;
    	$code = self::$container['redis_default']->get($key);
    	return $code;
    }
    
    public function incr($bid){
    	$key = $this->_incrKey . $bid;
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
