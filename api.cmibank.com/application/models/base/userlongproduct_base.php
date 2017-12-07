<?php

require_once 'basemodel.php'; 

class userlongproduct_base extends Basemodel{

    private $_table = 'cmibank.cmibank_userlongproduct_';
 
    private $longproduct_tpl;
    
    public function addUserLongProductInfo($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['buytime'] = NOW;
        $lastInsertId = $this->insertDataSql($data, $table);
        $data['id'] = $lastInsertId;
        $key = _KEY_REDIS_USER_LONGPRODUCT_PREFIX_ . $uid;
        $userProductList = self::$container['redis_app_w']->delete($key);
        return $lastInsertId;
    }
    
    public function _get_db_userLongProduct($uid, $status = 0, $format = true){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid);
        $data = $this->selectDataListSql($table, $where);
        return $data;
    }
    
    public function count_longproduct_money($uid, $time, $dx = '>'){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) FROM " . $table . " WHERE uid = $uid and buytime $dx $time";
        $data = $this->executeSql($sql);
        return isset($data[0]['money']) ? $data[0]['money']: 0;
    }

    
    //用户所有产品
    public function getAllLongProductInfo($uid){
        $data = $this->_get_db_userLongProduct($uid, 1);
        return $data;
    }
    
    
    public function getUserLongProductInfo($uid){
        $key = _KEY_REDIS_USER_LONGPRODUCT_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userlongproductInfo = $self->_get_db_userLongProduct($uid);
            if(empty($userlongproductInfo)) return false;
            return json_encode($userlongproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function rebuildUserLongProductInfo($uid){
        $key = _KEY_REDIS_USER_LONGPRODUCT_PREFIX_ . $uid;
        self::$container['redis_app_w']->delete($key);
        return $this->getUserProductInfo($uid);
    }
    
    public function get_tpl_longproduct_info($lid){
        if(!isset($this->longproduct_tpl[$lid])){
            $this->load->model('base/longproduct_base', 'longproduct');
            $info = $this->longproduct->getLongProductDetail($lid);
            $this->longproduct_tpl[$lid] = $info;
        }
        return $this->longproduct_tpl[$lid];
    }
    
    public function set_user_longproduct_max($uid, $pid, $var){
        $key = _KEY_REDIS_USER_LONGPRODUCT_MAX_PREFIX_ . '_' . date('Y-m-d') . '_' . $uid;
        return self::$container['redis_app_w']->hashInc($key, $pid, $var);
    }
    
    public function get_user_longproduct_max($uid, $pid){
        $key = _KEY_REDIS_USER_LONGPRODUCT_MAX_PREFIX_ . '_' . date('Y-m-d') . '_' . $uid;
        $val = self::$container['redis_app_w']->hashGet($key, $pid);
        return $val ? $val : 0;
    }
    
    public function getSumUserLongProduct($odate){
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
    
    public function getAllMoney($uid){
    	$_table_index = $uid % 16;
    	$table = $this->_table . $_table_index;
    	$sql = "SELECT SUM(money) as totalmoney,count(*) as totalcount  FROM ".$table." WHERE `uid` =  ".$uid;
    	$ret =  $this->executeSql($sql);
    	return $ret;
    }
//     public function setProfitDetailCache($uid, $data){
//         $key = _KEY_REDIS_USER_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
//         foreach ($data as $index => $_data){
// //             echo strtotime($index) . '<br />' . json_encode($_data);
//             $_data = array($index => $_data);
//             self::$container['redis_app_w']->setAdd($key, json_encode($_data), 1, strtotime($index));
//         }
//         self::$container['redis_app_w']->expire($key, 86400);
//         return true;
//     }
    
//     public function getProfitDetailCache($uid, $start, $end){
//         $key = _KEY_REDIS_USER_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
//         return self::$container['redis_app_r']->setRange($key,$start,$end,1);
//     }
    
    
}
