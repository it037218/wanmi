<?php

require_once 'basemodel.php'; 

class expmoney_log_base extends Basemodel{

    private $_table = 'cmibank.cmibank_expmoney_log_';
     
    public function addLog($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $insertid = $this->insertDataSql($data, $table);
        $size = $this->getCacheSize($uid);
        if($size){
            $data['id'] = $insertid;
            $this->addCache($uid, $data);
        }
        return $insertid;
    }
    
    private function getCacheSize($uid){
       $all_key = _KEY_REDIS_EXPMONEY_LOG_PREFIX_ALL . $uid;
       return self::$container['redis_default']->setSize($all_key, 1);
    }
    
    private function addCache($uid, $data){
        $all_key = _KEY_REDIS_EXPMONEY_LOG_PREFIX_ALL . $uid;
        self::$container['redis_default']->setAdd($all_key, json_encode($data), 1, $data['ctime']);
    }
    
    public function _get_db_UserLog($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid);
        $data = $this->selectDataListSql($table, $where, 'ctime desc');
        return $data;
    }
    
    public function init_cache($uid){
        $data = $this->_get_db_UserLog($uid);
        if($data){
            self::$container['redis_default']->delete(_KEY_REDIS_EXPMONEY_LOG_PREFIX_ALL . $uid);
            foreach ($data as $key => $value){
                $this->addCache($uid, $value);
            }
            return true;
        }else{
            return false;
        }
    }
    
    public function getLog($uid, $start, $end){
        $key = _KEY_REDIS_EXPMONEY_LOG_PREFIX_ALL . $uid;
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
    
    
}
