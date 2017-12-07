<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_user_notice_model extends Basemodel {
    
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
}