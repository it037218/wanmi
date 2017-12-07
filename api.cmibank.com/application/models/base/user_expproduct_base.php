<?php

require_once 'basemodel.php'; 

class user_expproduct_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_expproduct_';
    
    public function addUserExpProductInfo($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['buytime'] = NOW;
        $lastInsertId = $this->insertDataSql($data, $table);
        $data['id'] = $lastInsertId;
        $this->rebuildUserExpProductInfo($uid);
        return $lastInsertId;
    }
    
    public function _get_db_userExpProduct($uid, $status = 0){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid, 'status' => $status);
        $data = $this->selectDataListSql($table, $where, NULL, array(2000, 0));
        return $data;
    }
    
    public function count_expproduct_money($uid, $time, $dx = '>'){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) FROM " . $table . " WHERE uid = $uid and buytime $dx $time";
        $data = $this->executeSql($sql);
        return isset($data[0]['money']) ? $data[0]['money']: 0;
    }
    
    public function sum_expproduct_money($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) as sum_money FROM " . $table . " WHERE `uid` = '$uid' and `status` = 0";
        $data = $this->executeSql($sql);
        return isset($data[0]['sum_money']) ? $data[0]['sum_money']: 0;
    }

    //用户在投产品金额
    public function getSumUserExpProductMoney($uid){
        $key = _KEY_REDIS_USER_SUM_EXPPRODUCT_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $sum_userExpMoney = $self->sum_expproduct_money($uid);
            return $sum_userExpMoney;
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return $return;
    }
    
    public function sum_user_all_expproduct_money($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) as sum_money FROM " . $table . " WHERE `uid` = '$uid'";
        $data = $this->executeSql($sql);
        return isset($data[0]['sum_money']) ? $data[0]['sum_money']: 0;
    }
    
    public function getSumUserAllExpProductMoney($uid){
        $key = _KEY_REDIS_USER_SUM_ALL_EXPPRODUCT_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $sum_userExpMoney = $self->sum_user_all_expproduct_money($uid);
            return $sum_userExpMoney;
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return $return;
    }
    
    //用户所有产品
    public function getUserExpProductInfo($uid){
        $key = _KEY_REDIS_USER_EXPPRODUCT_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userlongproductInfo = $self->_get_db_userExpProduct($uid);
            if(empty($userlongproductInfo)) return false;
            return json_encode($userlongproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function rebuildUserExpProductInfo($uid){
        $key = _KEY_REDIS_USER_EXPPRODUCT_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        $key = _KEY_REDIS_USER_SUM_EXPPRODUCT_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        $key = _KEY_REDIS_USER_SUM_ALL_EXPPRODUCT_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        return $this->getUserExpProductInfo($uid);
    }
    
    public function sumUserExpProductMoneyWithOdate($uid, $odate){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) as sum_money FROM " . $table . " WHERE `uid` = $uid AND `uietime` = '$odate' AND `status` = 0 ";
        $data = $this->executeSql($sql);
        $sub_money = $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
        return $sub_money;
    }
    
    public function updateUserExpProductStatus($uid, $today){
        $table = $this->getTableIndex($uid, $this->_table);
        $ret = $this->updateDataSql($table, array('status' => 1), array('uid' => $uid, 'uietime <=' => $today));
        if($ret){
            $key = _KEY_REDIS_USER_EXPPRODUCT_PREFIX_ . $uid;
            self::$container['redis_app_w']->delete($key);
            $key = _KEY_REDIS_USER_SUM_EXPPRODUCT_PREFIX_ . $uid;
            self::$container['redis_default']->delete($key);
            $key = _KEY_REDIS_USER_SUM_ALL_EXPPRODUCT_PREFIX_ . $uid;
            self::$container['redis_default']->delete($key);
        }
        return $ret;
    }
    
    public function getSumUserExpProduct($odate){
        $sum_money = 0;
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        for($i = 0 ; $i <= 15; $i++){
            $sql = "SELECT sum(money) as sum_money FROM ". $this->_table . $i . " WHERE buytime >= $start_time AND buytime < $end_time";
            $data = $this->executeSql($sql);
            $sub_money = $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
            $sum_money += $sub_money;
        }
        return $sum_money;
    }
    
    
}
