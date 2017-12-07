<?php

//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class user_log_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_log_';
    private $in_arr = array(USER_ACTION_PAY , USER_ACTION_PREPAYMENT, USER_ACTION_LREPAYMENT, USER_ACTION_ACTIVITY, USER_ACTION_INVITE);
    private $out_arr = array(USER_ACTION_PCASHOUT , USER_ACTION_PRODUCT, USER_ACTION_LONGPRODUCT);
     
    public function addUserLog($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['ctime'] = NOW;
        $where = array('uid' => $uid, 'status' => 0);
        $insertid = $this->insertDataSql($data, $table);
        if($this->getCacheSize($uid)){
            $data['id'] = $insertid;
            $this->addCache($uid, $data);
        }
        return $insertid;
    }
    
    private function getCacheSize($uid){
       $all_key = _KEY_REDIS_USER_LOG_PREFIX_ALL . $uid;
       return self::$container['redis_default']->setSize($all_key, 1);
    }
    
    public function getFirstBuyMoney($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT money  FROM " . $table . " WHERE uid = " . $uid . " AND action =1 order by ctime desc limit 0,1";
        $data = $this->executeSql($sql);
        return $data[0]['money'];
    }
    
    private function addCache($uid, $data){
        $all_key = _KEY_REDIS_USER_LOG_PREFIX_ALL . $uid;
        $size = $this->getCacheSize($uid, $all_key);
        if($size > 0){
            self::$container['redis_default']->setAdd($all_key, json_encode($data), 1, $data['id']);
        }
        if(in_array($data['action'], $this->in_arr)){
            $key = _KEY_REDIS_USER_LOG_PREFIX_IN . $uid;
            $size = $this->getCacheSize($uid, $key);
            if($size > 0){
                self::$container['redis_default']->setAdd($key, json_encode($data), 1, $data['id']);
            }
            if($data['action'] == USER_ACTION_PREPAYMENT){
                $product_key = _KEY_REDIS_USER_LOG_PREFIX_PRODUCT . $uid;
                $size = $this->getCacheSize($uid, $product_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($product_key, json_encode($data), 1, $data['id']);
                }
            }else if($data['action'] == USER_ACTION_LREPAYMENT){
                $longproduct_key = _KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT . $uid;
                $size = $this->getCacheSize($uid, $longproduct_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($longproduct_key, json_encode($data), 1, $data['id']);
                }
            }else if($data['action'] == USER_ACTION_LONGTOBALANCE){
                $longtobalance_key = _KEY_REDIS_USER_LOG_PREFIX_LONGTOBALANCE . $uid;
                $size = $this->getCacheSize($uid, $longtobalance_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($longtobalance_key, json_encode($data), 1, $data['id']);
                }
                
                $longall_key = _KEY_REDIS_USER_LOG_PREFIX_LONGALL . $uid;
                $size = $this->getCacheSize($uid, $longall_key);
                if($size > 0){
                	self::$container['redis_default']->setAdd($longall_key, json_encode($data), 1, $data['id']);
                }
            }
        }
        if(in_array($data['action'], $this->out_arr)){
            $out_key = _KEY_REDIS_USER_LOG_PREFIX_OUT . $uid;
            $size = $this->getCacheSize($uid, $out_key);
            if($size > 0){
                self::$container['redis_default']->setAdd($out_key, json_encode($data), 1, $data['id']);
            }
            if($data['action'] == USER_ACTION_PRODUCT){
                $product_key = _KEY_REDIS_USER_LOG_PREFIX_PRODUCT . $uid;
                $size = $this->getCacheSize($uid, $product_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($product_key, json_encode($data), 1, $data['id']);
                }
            }else if($data['action'] == USER_ACTION_LONGPRODUCT){
                $longproduct_key = _KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT . $uid;
                $size = $this->getCacheSize($uid, $longproduct_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($longproduct_key, json_encode($data), 1, $data['id']);
                }
                
                $longall_key = _KEY_REDIS_USER_LOG_PREFIX_LONGALL . $uid;
                $size = $this->getCacheSize($uid, $longall_key);
                if($size > 0){
                	self::$container['redis_default']->setAdd($longall_key, json_encode($data), 1, $data['id']);
                }
            }else if($data['action'] == USER_ACTION_PCASHOUT){
                $pcashout_key = _KEY_REDIS_USER_LOG_PREFIX_CASHOUT . $uid;
                $size = $this->getCacheSize($uid, $pcashout_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($pcashout_key, json_encode($data), 1, $data['id']);
                }
            }
        }
    }
    
    public function _get_db_UserLog($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid);
        return $this->selectDataListSql($table, $where, 'ctime desc');
    }
    
    public function init_cache($uid){
        $data = $this->_get_db_UserLog($uid);
        if($data){
            self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_ALL . $uid);
            self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_OUT . $uid);
            self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_IN . $uid);
            self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_PRODUCT . $uid);
            self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT . $uid);
            foreach ($data as $key => $value){
                $this->addCache($uid, $value);
            }
            return true;
        }else{
            return false;
        }
    }
    
    private function getRedisKey($type, $uid){
        if($type == 'all'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_ALL.$uid;
        }else if($type == 'in'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_IN . $uid;
        }else if($type == 'out'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_OUT . $uid;
        }else if($type == 'product'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_PRODUCT . $uid;
        }else if($type == 'longproduct'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT . $uid;
        }else if($type == 'longtobalance'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_LONGTOBALANCE . $uid;
        }else if($type == 'cashout'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_CASHOUT . $uid;
        }else if($type == 'longall'){
            $key = _KEY_REDIS_USER_LOG_PREFIX_LONGALL . $uid;
        }
        if(!$key){
            return false;
        }
        return $key;
    }
    
    public function getUserLog($uid, $type, $start, $end){
        $key = $this->getRedisKey($type, $uid);
        $data = self::$container['redis_default']->setRange($key, $start, $end, 1);
        $size = self::$container['redis_default']->setSize($key, 1);
        if($start > $size){
            return array();
        }
        if(empty($data)){
            $ret = $this->init_cache($uid);
            if($ret){
                $data = self::$container['redis_default']->setRange($key, $start, $end, 1);
            }
        }
        $rtn = array();
        foreach ($data as $key => $value){
            $rtn[$key] = json_decode($value, true);
        }
        return $rtn;
    }
    
    public function _get_db_user_log_by_id($id, $uid){
        $tableName = $this->getTableIndex($uid, $this->_table);
        $data = $this->selectDataSql($tableName, array('id' => $id));
        return $data;
    }
    
    public function updateUserLogOnlyWithDraw($uid, $removetype, $update_data, $update_where){
        if(!isset($update_where['id'])){
            return false;
        }
        $olddata = $this->_get_db_user_log_by_id($update_where['id'], $uid);
        $tableName = $this->getTableIndex($uid, $this->_table);
        $ret = $this->updateDataSql($tableName, $update_data, $update_where);
        if($ret){
            $newdata = $olddata;
            foreach ($update_data as $_k => $_v){
                $newdata[$_k] = $update_data[$_k];
            }
            //先简单处理
            $removetype = array('all', 'in', 'out', 'product', 'longproduct');
            foreach ($removetype as $type){
                $key = $this->getRedisKey($type, $uid);
                self::$container['redis_default']->delete($key);
            }
        }
        return $ret;
    }
    public function getUserLogByid($uid,$id){
    	$_table_index = $uid % 16;
    	$table = $this->_table . $_table_index;
    	$sql = "select * from $table where id=$id";
    	return $this->executeSql($sql);
    }
}
