<?php

require_once 'basemodel.php'; 

class tg_user_identity_base extends Basemodel{

    private $_table = 'cmibank.cmibank_tg_user_identity';

    public function getUserIdentity_db($uid){
        $result = $this->selectDataSql($this->_table, array('uid' => $uid));
        return !empty($result) ? $result : false;
    }
    
    public function getUserListIdentity_db($uids){
        $result = $this->selectDataListSql($this->_table, array('uid' => $uids));
        return !empty($result) ? $result : false;
    }
    
    public function getUserIdentity($uid){
        $key = _KEY_REDIS_TG_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userIdentity = $self->getUserIdentity_db($uid);
            if(empty($userIdentity)) return false;
            return json_encode($userIdentity);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    
    public function getUserIdentityByColumn($where){
        $result = $this->selectDataSql($this->_table, $where);
        return !empty($result) ? $result : false;
    }
    
    //设置身份信息一定是生成数据的第一步
    public function initUserIdentity($data){
        if(!isset($data['uid']) || !isset($data['idCardNo']) || !isset($data['realname'])){
            return false;
        }
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function updateUserIdentity($data, $where){
        $key = _KEY_REDIS_TG_USER_IDENTITY_DETAIL_PREFIX_ . $where['uid'];
        self::$container['redis_default']->delete($key);
        $ret = $this->updateDataSql($this->_table, $data, $where);
        return $ret;
    }
    
    public function delUserIdentityCache($uid){
        $key = _KEY_REDIS_TG_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
    }
    
    public function set_isnew($uid, $is_new = 0){
        $data = array('isnew' => $is_new);
        $where = array('uid' => $uid);
        $result = $this->updateDataSql($this->_table, $data, $where);
        $key = _KEY_REDIS_TG_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        return $result;
    }
    
}
