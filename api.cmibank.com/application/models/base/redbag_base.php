<?php

require_once 'basemodel.php'; 

class redbag_base extends Basemodel{

    private $_table = 'cmibank.cmibank_redbag';
    
    public function get_db($code){
        return $this->selectDataSql($this->_table, array('code' => $code));
    }
    
    public function get_redbag_detail($code){
        $key = _KEY_REDIS_REDBAG_DETAIL_PREFIX_ . $code;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self, $code) {
            $redbag = $self->get_db($code);
            if(empty($redbag)) return false;
            return json_encode($redbag);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function update_redbag_db_detail($data, $code){
        $ret = $this->updateDataSql($this->_table, $data, array('code' => $code));
        if($ret){
            $key = _KEY_REDIS_REDBAG_DETAIL_PREFIX_ . $code;
            self::$container['redis_default']->delete($key);
        }
        return $ret;
    }
    
    public function setRedbagMoney($code,$moneyArray){
    	$key = _KEY_REDIS_REDBAG_MONEYARRAY_PREFIX_ . $code;
    	$rtn = self::$container['redis_default']->save($key, json_encode($moneyArray), 86400*7);
    	return $rtn;
    }
    
    public function getRedbagMoney($code){
    	$key = _KEY_REDIS_REDBAG_MONEYARRAY_PREFIX_ . $code;
    	$rtn = self::$container['redis_default']->get($key);
    	return $rtn?json_decode($rtn):'';
    }
    
    //增加红包数量
	public function incr($code){
    	$key = _KEY_REDIS_REDBAG_COUNT_PREFIX_ . $code;
    	$rtn = self::$container['redis_default']->incr($key);
    	return $rtn;
    }
    
    public function remove_count($code){
    	$key = _KEY_REDIS_REDBAG_COUNT_PREFIX_ . $code;
    	$rtn = self::$container['redis_default']->delete($key);
    	return $rtn;
    }
    
    //记录用户红包金额+有效期
    public function set_user_redbag_money($account,$log,$timeout){
    	$key = _KEY_REDIS_REDBAG_USER_PREFIX_ . $account;
    	$rtn = self::$container['redis_default']->save($key, json_encode($log), $timeout);
    	return $rtn;
    }
    
    
    public function get_user_redbag_list($code){
    	$key = _KEY_REDIS_REDBAG_LIST_PREFIX_ . $code;
    	$data = self::$container['redis_default']->setRange($key, 0, -1,1);
    	$rtn = array();
    	if($data){
    		foreach ($data as $key => $_v){
    			$rtn[$key] = json_decode($_v, true);
    		}
    	}
    	return $rtn;
    }
    
    public function add_user_redbag_to_list($code,$value){
    	$key = _KEY_REDIS_REDBAG_LIST_PREFIX_ . $code;
    	$rtn = self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['ctime']);
    	return $rtn;
    }
    
    
    public function update_redis_redbag_detail($code,$redbag){
    	$key = _KEY_REDIS_REDBAG_DETAIL_PREFIX_ . $code;
    	$rtn = self::$container['redis_default']->save($key, json_encode($redbag));
    	return $rtn;
    }
    
    public function get_user_redbag_money($account){
    	$key = _KEY_REDIS_REDBAG_USER_PREFIX_ . $account;
    	$rtn = self::$container['redis_default']->get($key);
    	return $rtn ? json_decode($rtn , true) : false;
    }
    
    public function delete_user_redbag_money($account){
    	$key = _KEY_REDIS_REDBAG_USER_PREFIX_ . $account;
    	$rtn = self::$container['redis_default']->delete($key);
    	return $rtn;
    }
    
	public function incr_user_redbag_money_withCode($account,$code){
    	$key = _KEY_REDIS_REDBAG_USER_PREFIX_ .$code.":". $account;
    	$rtn = self::$container['redis_default']->incr($key);
    	self::$container['redis_default']->expire($key , $ttl = 86400*7);
    	return $rtn;
    }
    
    //增加红包数量
    public function incrTotal(){
    	$key = _KEY_REDIS_REDBAG_TOTAL_COUNT_PREFIX_;
    	$rtn = self::$container['redis_default']->incr($key);
    	return $rtn;
    }
}
