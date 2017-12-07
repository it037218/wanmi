<?php

require_once 'basemodel.php'; 

class userklproduct_base extends Basemodel{

    private $_table = 'cmibank.cmibank_userklproduct_';
 
    private $klproduct_tpl;
    
    public function addUserKlProductInfo($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['buytime'] = NOW;
        $lastInsertId = $this->insertDataSql($data, $table);
        $data['id'] = $lastInsertId;
        $key = _KEY_REDIS_USER_KLPRODUCT_PREFIX_ . $uid;
        $userProductList = self::$container['redis_app_w']->delete($key);
        return $lastInsertId;
    }
    
    public function _get_db_userKlProduct($uid, $status = 0, $format = true){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid);
        $data = $this->selectDataListSql($table, $where);
        return $data;
    }
    
    public function count_klproduct_money($uid, $time, $dx = '>'){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) FROM " . $table . " WHERE uid = $uid and buytime $dx $time";
        $data = $this->executeSql($sql);
        return isset($data[0]['money']) ? $data[0]['money']: 0;
    }

    
    //用户所有产品
    public function getAllKlProductInfo($uid){
        $data = $this->_get_db_userklProduct($uid, 1);
        return $data;
    }
    
    
    public function getUserKlProductInfo($uid){
        $key = _KEY_REDIS_USER_KLPRODUCT_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userklproductInfo = $self->_get_db_userKlProduct($uid);
            if(empty($userklproductInfo)) return false;
            return json_encode($userklproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function rebuildUserKlProductInfo($uid){
        $key = _KEY_REDIS_USER_KLPRODUCT_PREFIX_ . $uid;
        self::$container['redis_app_w']->delete($key);
        return $this->getUserProductInfo($uid);
    }
    
    public function get_tpl_klproduct_info($lid){
        if(!isset($this->klproduct_tpl[$lid])){
            $this->load->model('base/klproduct_base', 'klproduct_base');
            $info = $this->klproduct_base->getKlProductDetail($lid);
            $this->klproduct_tpl[$lid] = $info;
        }
        return $this->klproduct_tpl[$lid];
    }
    
    
    
    public function getSumUserKlProduct($odate){
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
