<?php

require_once 'basemodel.php'; 

class new_user_log_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_log_';

    private $in_arr = array(USER_ACTION_PAY , USER_ACTION_PREPAYMENT, USER_ACTION_LREPAYMENT, USER_ACTION_LONGTOBALANCE, USER_ACTION_ACTIVITY, USER_ACTION_INVITE, USER_ACTION_EXPMONEY);
    
    private $out_arr = array(USER_ACTION_PCASHOUT , USER_ACTION_PRODUCT, USER_ACTION_LONGPRODUCT);
    public function addUserLog($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['ctime'] = NOW;
        $where = array('uid' => $uid, 'status' => 0);
        $insertid = $this->insertDataSql($data, $table);
        $data['id'] = $insertid;
        $this->addCache($uid, $data);
        return $insertid;
    }
    
    private function getCacheSize($uid, $key){
       return self::$container['redis_default']->setSize($key, 1);
    }
    
    private function addCache($uid, $data){
        $all_key = _KEY_REDIS_USER_LOG_PREFIX_ALL . $uid;
        self::$container['redis_default']->setAdd($all_key, json_encode($data), 1, $data['ctime']);
        if(in_array($data['action'], $this->in_arr)){
            $key = _KEY_REDIS_USER_LOG_PREFIX_IN . $uid;
            $size = $this->getCacheSize($uid, $key);
            if($size > 0){
                self::$container['redis_default']->setAdd($key, json_encode($data), 1, $data['ctime']);
            }
            if($data['action'] == USER_ACTION_PREPAYMENT){
                $product_key = _KEY_REDIS_USER_LOG_PREFIX_PRODUCT . $uid;
                $size = $this->getCacheSize($uid, $product_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($product_key, json_encode($data), 1, $data['ctime']);
                }
            }else if($data['action'] == USER_ACTION_LREPAYMENT){
                $longproduct_key = _KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT . $uid;
                $size = $this->getCacheSize($uid, $longproduct_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($longproduct_key, json_encode($data), 1, $data['ctime']);
                }
            }else if($data['action'] == USER_ACTION_LONGTOBALANCE){
                $longtobalance_key = _KEY_REDIS_USER_LOG_PREFIX_LONGTOBALANCE . $uid;
                $size = $this->getCacheSize($uid, $longtobalance_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($longtobalance_key, json_encode($data), 1, $data['ctime']);
                }
            }
        }
        if(in_array($data['action'], $this->out_arr)){
            $out_key = _KEY_REDIS_USER_LOG_PREFIX_OUT . $uid;
            $size = $this->getCacheSize($uid, $out_key);
            if($size > 0){
                self::$container['redis_default']->setAdd($out_key, json_encode($data), 1, $data['ctime']);
            }
            if($data['action'] == USER_ACTION_PRODUCT){
                $product_key = _KEY_REDIS_USER_LOG_PREFIX_PRODUCT . $uid;
                $size = $this->getCacheSize($uid, $product_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($product_key, json_encode($data), 1, $data['ctime']);
                }
            }else if($data['action'] == USER_ACTION_LONGPRODUCT){
                $longproduct_key = _KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT . $uid;
                $size = $this->getCacheSize($uid, $longproduct_key);
                if($size > 0){
                    self::$container['redis_default']->setAdd($longproduct_key, json_encode($data), 1, $data['ctime']);
                }
            }
        }
    }
    
        
    private function buildCache($uid, $data, $redis_key){
        self::$container['redis_default']->setAdd($redis_key, json_encode($data), 1, $data['ctime']);
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
        }
        if(!$key){
            return false;
        }
        return $key;
    }
    
    private function getAction($type){
        if($type == 'all'){
            $action = array();
        }else if($type == 'in'){
            $action = array(USER_ACTION_PAY , USER_ACTION_PREPAYMENT, USER_ACTION_LREPAYMENT, USER_ACTION_LONGTOBALANCE, USER_ACTION_ACTIVITY, USER_ACTION_INVITE, USER_ACTION_EXPMONEY);
        }else if($type == 'out'){
            $action = array(USER_ACTION_PCASHOUT , USER_ACTION_PRODUCT, USER_ACTION_LONGPRODUCT);
        }else if($type == 'product'){
            $action = array(USER_ACTION_PRODUCT);
        }else if($type == 'longproduct'){
            $action = array(USER_ACTION_LONGPRODUCT);
        }else if($type == 'longtobalance'){
            $action = array(USER_ACTION_LONGTOBALANCE);
        }
        return $action;
    }
    
    public function getUserLog($uid, $type, $start, $end){
        $key = $this->getRedisKey($type, $uid);
        $data = self::$container['redis_default']->setRange($key, $start, $end, 1);
        $size = self::$container['redis_default']->setSize($key, 1);
        if($start > $size){
            return array();
        }
        if(empty($data)){
            $ret = $this->init_cache($uid, $type, $start);
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
    
    public function _get_db_UserLog($uid, $limit = array(), $action = array()){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid);
        if($action){
            $where['action'] = $action;
        }
        return $this->selectDataListSql($table, $where, 'ctime desc', $limit, true);
    }
    
    public function init_cache($uid, $type, $start){
        $db_start = $start;
        $db_psize = 20;
        $db_action = $this->getAction($type);
        $redis_key = $this->getRedisKey($type, $uid);
        $data = $this->_get_db_UserLog($uid, array($db_psize, $db_start), $db_action);
        if($data){
            //清除缓存
            self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_ALL . $uid);
            if(in_array($db_action, $this->in_arr)){
                self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_IN . $uid);
            }else{
                self::$container['redis_default']->delete(_KEY_REDIS_USER_LOG_PREFIX_OUT . $uid);
            }
            self::$container['redis_default']->delete($redis_key);
            foreach ($data as $key => $value){
                $this->buildCache($uid, $value, $redis_key);
            }
            return true;
        }else{
            return false;
        }
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
            $removetype = array('all', 'in', 'out', 'product', 'longproduct','longtobalance');
            foreach ($removetype as $type){
                $key = $this->getRedisKey($type, $uid);
                self::$container['redis_default']->delete($key);
//                 $size = self::$container['redis_default']->setSize($key, 1);
//                 if($size < 0){
//                     continue;
//                 }
//                 $olddata['ctime'];
//                 $a = self::$container['redis_default']->setMove($key, json_encode($olddata), 1); //这里有BUG，没有删到，后面再调
//                 $b = self::$container['redis_default']->setAdd($key, json_encode($newdata), 1, $newdata['ctime']);
//                 var_dump($a);
//                 var_dump($b);
            }
        }
    }
    
}
