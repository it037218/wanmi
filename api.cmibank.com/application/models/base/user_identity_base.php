<?php

require_once 'basemodel.php'; 

class user_identity_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_identity';

    public function getUserIdentity_db($uid){
        $result = $this->selectDataSql($this->_table, array('uid' => $uid));
        return !empty($result) ? $result : false;
    }
    
    public function getUserListIdentity_db($uids){
        $result = $this->selectDataListSql($this->_table, array('uid' => $uids));
        return !empty($result) ? $result : false;
    }
    
    public function getUserIdentity($uid){
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userIdentity = $self->getUserIdentity_db($uid);
            if(empty($userIdentity)) return false;
            return json_encode($userIdentity);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function getUserIdentityByIdcard($idcard){
        $result = $this->selectDataSql($this->_table, array('idcard' => $idcard));
        return !empty($result) ? $result : false;
    }
    
    public function getUserIdentityByRequestId($requestid){
        $result = $this->selectDataSql($this->_table, array('requestid' => $requestid));
        return !empty($result) ? $result : false;
    }
    
    public function setUserTpwd($uid, $tpwd){
        $data = array('tpwd' => $tpwd);
        $where = array('uid' => $uid);
        $result = $this->updateDataSql($this->_table, $data, $where);
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        return $result;
    }
    
    public function set_isnew($uid, $is_new = 0){
        $data = array('isnew' => $is_new);
        $where = array('uid' => $uid);
        $result = $this->updateDataSql($this->_table, $data, $where);
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        return $result;
    }
    
    public function set_h_isnew($uid, $is_new = 0){
        $data = array('h_isnew' => $is_new);
        $where = array('uid' => $uid);
        $result = $this->updateDataSql($this->_table, $data, $where);
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        return $result;
    }
    
    //设置身份信息一定是生成数据的第一步
    public function initUserIdentity($data){
        if(!isset($data['uid']) || !isset($data['idCard']) || !isset($data['realname'])){
            return false;
        }
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function updateUserIdentity($data, $where){
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $where['uid'];
        self::$container['redis_default']->delete($key);
        $ret = $this->updateDataSql($this->_table, $data, $where);
        return $ret;
    }
    
    public function delUserIdentityCache($uid){
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
    }
    
     public function getUseridentityList($where,$order = null,$limit = null){
         return $this->selectDataListSql($this->_table,$where,$order,$limit, true);
     }
     
     public function getUserIdentityWithWhere($where){
         return $this->selectDataSql($this->_table,$where);
     }
     
     public  function getUseridentityCount($where){
         return $this->selectDataCountSql($this->_table, $where);
     }
     
     public function queryBankCode($fix_card){
         $sql = "SELECT bankcode FROM " . $this->_table . " WHERE cardno like '$fix_card%' limit 1";
         $data = $this->executeSql($sql);
         return $data;
     }
     public function countByPhone($phone){
     	$sql = "SELECT count(uid) as counts FROM $this->_table  WHERE phone=$phone and isvalidate=1";
     	$data = $this->executeSql($sql);
     	return $data[0]['counts']?$data[0]['counts']:0;
     }
}
