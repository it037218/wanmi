<?php

require_once 'basemodel.php'; 

class user_base extends Basemodel{

    public function updateAccountPwd($uid, $passworld){
        $ret = $this->_db_update_account_pwd_by_uid($uid, $passworld);
        if($ret){
            return $this->rebuildAccountInfoCache($uid);
        }else{
            return false;
        }        
    }
    
    public function updateAccountInfo($uid, $data){
        $ret = $this->_db_update_account_info_by_uid($uid, $data);
        if($ret){
            return $this->rebuildAccountInfoCache($uid);
        }else{
            return false;
        }
    }
    
    public function _db_update_account_info_by_uid($uid, $data){
        $db = self::$container['db_w'];
        return $db->update('cmibank.cmibank_account', $data, array('uid' => $uid));
    }
    
    public function delUserCache($uid, $phone){
        $key = _KEY_REDIS_ACCOUNT_INFO_PREFIX_ . $uid;
        #删除key
        self::$container['redis_account_w']->delete($key);
        $key  = _KEY_REDIS_ACCOUNT_UID_PREFIX_ . $phone;
        self::$container['redis_account_w']->delete($key);
        echo 'OK';
    }
    
    public function rebuildAccountInfoCache($uid){
        $key = _KEY_REDIS_ACCOUNT_INFO_PREFIX_ . $uid;
        #删除key
        self::$container['redis_account_w']->delete($key);
        return $this->getAccountInfo($uid);
    }
    
    public function _db_update_account_pwd_by_uid($uid, $passworld){
        $db = self::$container['db_w'];
        $updateParams = array(
            'pwd' => $passworld
        );
        return $db->update('cmibank.cmibank_account', $updateParams, array('uid' => $uid));
    }
    
    
    public function setLoginPwdCode($account, $code){
        $key = _KEY_REDIS_LOGINPWD_PREFIX_ . $account;
        return self::$container['redis_account_w']->save($key, $code, VALIDATECODE_TTL);
    }
    
    public function getLoginPwdCode($account){
        $key = _KEY_REDIS_LOGINPWD_PREFIX_ . $account;
        return self::$container['redis_account_w']->get($key);
    }
    
    public function moveLoginPwdCode($account){
        $key = _KEY_REDIS_LOGINPWD_PREFIX_ . $account;
        return self::$container['redis_account_w']->delete($key);
    }
    
    public function setValidateCode($uid, $code){
        $key = _KEY_REDIS_VALIDATECODE_PREFIX_ . $uid;
        return self::$container['redis_account_w']->save($key, $code, VALIDATECODE_TTL);
    }
    
    public function moveValidateCode($uid){
        $key = _KEY_REDIS_VALIDATECODE_PREFIX_ . $uid;
        return self::$container['redis_account_w']->delete($key);
    }
    
    public function getBindBankCode($uid){
        $key = _KEY_REDIS_BINDBANK_PREFIX_ . $uid;
        return self::$container['redis_account_r']->get($key);
    }
    
    public function setBindBankCode($uid, $code){
        $key = _KEY_REDIS_BINDBANK_PREFIX_ . $uid;
        return self::$container['redis_account_w']->save($key, $code, VALIDATECODE_TTL);
    }
    
    public function getBindBankPhone($uid){
        $key = _KEY_REDIS_BINDBANK_PHONE_PREFIX_ . $uid;
        return self::$container['redis_account_r']->get($key);
    }
    
    public function setBindBankPhone($uid, $phone){
        $key = _KEY_REDIS_BINDBANK_PHONE_PREFIX_ . $uid;
        return self::$container['redis_account_w']->save($key, $phone, VALIDATECODE_TTL);
    }
    
    public function getValidateCode($uid){
        $key = _KEY_REDIS_VALIDATECODE_PREFIX_ . $uid;
        return self::$container['redis_account_r']->get($key);
    }
    
    public function getCode($uid){
        $key = _KEY_REDIS_VALIDATECODE_PREFIX_ . $uid;
        echo $key;
        return self::$container['redis_account_r']->get($key);
    }
    
    public function setSMSCode($uid, $code){
        $key = _KEY_REDIS_SMSTPWD_PREFIX_ . $uid;
        return self::$container['redis_account_w']->save($key, $code, VALIDATECODE_TTL);
    }
    
    public function getSMSCode($uid){
        $key = _KEY_REDIS_SMSTPWD_PREFIX_ . $uid;
        return self::$container['redis_account_r']->get($key);
    }
    
    public function setModifyTpwdCode($uid, $code){
        $key = _KEY_REDIS_MOBIFYTPWD_PREFIX_ . $uid;
        return self::$container['redis_account_w']->save($key, $code, 180);
    }
    
    public function getModifyTpwdCode($uid){
        $key = _KEY_REDIS_MOBIFYTPWD_PREFIX_ . $uid;
        return self::$container['redis_account_r']->get($key);
    }
    
    public function moveModifyTpwdCode($uid){
        $key = _KEY_REDIS_MOBIFYTPWD_PREFIX_ . $uid;
        return self::$container['redis_account_r']->delete($key);
    }
    
    public function getAccountInfo($uid, $flag = true){
        $self = $this;
        $key  = _KEY_REDIS_ACCOUNT_INFO_PREFIX_ . $uid;
        $cacheParams = array();
        $return = $this->remember($key,  0 , function() use($uid, $self , $cacheParams , $flag) {
            $info = $self->_db_get_account_info_by_uid($uid, $flag);
            if(empty($info)) return false;
            return json_encode($info);
        } , _REDIS_DATATYPE_STRING , self::$container['redis_account_r'] , self::$container['redis_account_w']);
        if (empty($return)) {
            return false;
        } else {
            return json_decode($return, true);
        }
        self::$container['redis_account_w']->save($key, json_encode($info));
    }
    
    public function _db_get_account_info_by_uid($uid, $flag){
        $db = (false == $flag) ? self::$container['db_r'] : self::$container['db_w'];
        $data = $db->select('*')
        ->from("cmibank.cmibank_account")
        ->where('uid', $uid)
        ->get()
        ->row_array();
        
        if(isset($data['uid'])){
            return $data;
        }else{
            return false;
        }
    }
    
    public function createAccount($info){
        $uid = $this->_db_insert_account($info);
        $info['uid'] = $uid;
        return $info;
    }

    public function createAccountRedis($info){
        if(!isset($info['uid']) || !$info['uid']){
            return false;
        }
        $key = _KEY_REDIS_ACCOUNT_INFO_PREFIX_. $info['uid'];
        self::$container['redis_account_w']->save($key, json_encode($info));
    }
    
    private function _db_insert_account($info) {
        self::$container['db_w']->insert('cmibank.cmibank_account', $info);
        return self::$container['db_w']->insert_id();
    }
  
    public function _db_get_uid_by_account($account, $flag = true){
       $db = (false == $flag) ? self::$container['db_r'] : self::$container['db_w'];
       $data = $db->select('uid')
       ->from("cmibank.cmibank_account")
       ->where('account', $account)
       ->get()
       ->row_array();
       if(isset($data['uid'])){
           return $data['uid'];
       }else{
           return false;
       }
    }
     
    public function getUidByAccount($account, $flag = true){
        $self = $this;
        $key  = _KEY_REDIS_ACCOUNT_UID_PREFIX_ . $account;
        $cacheParams = array();
        $return = $this->remember($key,  0 , function() use($account, $self , $cacheParams , $flag) {
            $uid = $self->_db_get_uid_by_account($account, $flag);
            if(empty($uid)) return false;
            return $uid;
        } , _REDIS_DATATYPE_STRING , self::$container['redis_account_r'] , self::$container['redis_account_w']);
        if (empty($return)) {
            return false;
        } else {
            return $return;
        }
    }
    
    public function _db_get_uid_by_mobile($mobile, $flag = true){
        $db = (false == $flag) ? self::$container['db_r'] : self::$container['db_w'];
        $data = $db->select('uid')
        ->from("cmibank.cmibank_account")
        ->where('mobile', $mobile)
        ->get()
        ->row_array();
        if(isset($data['uid'])){
           return $data['uid'];
        }else{
           return false;
        }
    }
    
    public function getUidByMobile($mobile, $flag = true){
        $self = $this;
        $key  = _KEY_REDIS_ACCOUNT_MOBILE_PREFIX_ . $mobile;
        $cacheParams = array();
        $return = $this->remember($key,  0 , function() use($mobile, $self , $cacheParams , $flag) {
            $uid = $self->_db_get_uid_by_mobile($mobile, $flag);
            if(empty($uid)) return false;
            return $uid;
        } , _REDIS_DATATYPE_STRING , self::$container['redis_account_r'] , self::$container['redis_account_w']);
    
        if (empty($return)) {
            return false;
        } else {
            return $return;
        }
    }
    
    public function getAccountInfoByPhones($phones){
        $ret = $this->selectDataListSql('cmibank.cmibank_account', array('account' => $phones));
        return $ret;
    }
    
    public function getAccuntList(){
        $ret = $this->selectDataListSql('cmibank.cmibank_account', array('ctime <' => 1448899200, 'ctime > ' => 1445788800), null, null, true);
        return $ret;
    }
    
    public function get_account_with_phone($phone){
        $data = $this->selectDataSql('cmibank.cmibank_account', array('account' => $phone));
        return $data;
    }
    
    public function incrLoginPwdCode($account){
    	$key = _KEY_REDIS_LOGINPWD_COUNT_PREFIX_ . $account;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 1800);
    	return $rtn;
    }
    
    public function incrValidateCode($phone){
    	$key = _KEY_REDIS_VALIDATECODE_COUNT_PREFIX_.$phone;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 1800);
    	return $rtn;
    }
    
    public function incrModifyTpwdCode($uid){
    	$key = _KEY_REDIS_MOBIFYTPWD_COUNT_PREFIX_ . $uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 1800);
    	return $rtn;
    }
    
    public function incrPayCode($phone){
    	$key = _KEY_REDIS_PAYCODE_COUNT_PREFIX_.$phone;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 1800);
    	return $rtn;
    }
    public function getPayCode($phone){
    	$key = _KEY_REDIS_PAYCODE_PREFIX_ . $phone;
    	return self::$container['redis_account_r']->get($key);
    }

    public function setPayCode($phone, $code){
    	$key = _KEY_REDIS_PAYCODE_PREFIX_ . $phone;
    	return self::$container['redis_account_w']->save($key, $code, VALIDATECODE_TTL);
    }
}
