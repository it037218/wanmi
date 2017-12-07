<?php

require_once 'basemodel.php'; 

class user_notice_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_notice_';

    public function addNotice($uid, $data){
    	$table = $this->getTableIndex($uid, $this->_table);
        $insertid = $this->insertDataSql($data, $table);
        if($insertid){
        	$key = _KEY_REDIS_USER_NOTICE_PREFIX_ . $uid;
        	self::$container['redis_default']->delete($key);
        }
        return $insertid;
    }
    
    public function get_user_notice_list($uid){
    	$key = _KEY_REDIS_USER_NOTICE_PREFIX_ . $uid;
    	$noticeList = self::$container['redis_default']->setRange($key, 0,20,1);
    	if(empty($noticeList)){
	    	$data = $this->getUserNoticeList($uid);
	    	if($data){
	    		foreach ($data as $value){
	    			self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['ctime']);
	    		}
	    		self::$container['redis_default']->expire($key, 86400);
	    	}
	    	return $data;
    	}else{
    		$rtn = array();
    		foreach ($noticeList as $key => $value){
    			$rtn[$key] = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    
    public function getUserNoticeList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where  uid= $uid order by ctime desc limit 0,20";
    	return $this->executeSql($sql);
    }
}
