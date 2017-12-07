<?php

require_once 'basemodel.php'; 

class userproduct_base extends Basemodel{

    private $_table = 'cmibank.cmibank_userproduct_';
 
    private $product_tpl;
    
    public function addUserProductInfo($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['buytime'] = NOW;
        $lastInsertId = $this->insertDataSql($data, $table);
        $data['id'] = $lastInsertId;
        $key = _KEY_REDIS_USER_PRODUCT_PREFIX_ . $uid;
        self::$container['redis_app_w']->delete($key);
        return $lastInsertId;
    }
    
    /**
     * 0 末结算
     * 1 所有
     * @param unknown $uid
     * @param number $type
     * @param string $format
     * @return unknown|Ambigous <multitype:, unknown>
     */
    public function _get_db_userProduct($uid, $type = 0){
        $table = $this->getTableIndex($uid, $this->_table);
        $where = array('uid' => $uid);
        if($type == 0){
            $where['status'] = 0;
        }
        $data = $this->selectDataListSql($table, $where, null, array(1000,0));
        return $data;
    }
    
    
    public function getPidsByStatus($uid, $status){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = 'select `pid` from ' . $table . ' where uid = ' . $uid . ' and status = ' . $status;
        $data = $this->executeSql($sql);
        return $data;
    }
    
    //用户所有产品
    public function getAllProductInfo($uid){
        $data = $this->_get_db_userProduct($uid , 1);
        return $data;
    }
    
    
    public function getUserProductInfo($uid){
        $key = _KEY_REDIS_USER_PRODUCT_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userproductInfo = $self->_get_db_userProduct($uid);
            if(empty($userproductInfo)) return false;
            return json_encode($userproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return, true);
    }
    
    public function rebuildUserProductInfo($uid){
        $key = _KEY_REDIS_USER_PRODUCT_PREFIX_ . $uid;
        self::$container['redis_app_w']->delete($key);
        return $this->getUserProductInfo($uid);
    }
    
    public function updateUserProductStatus($data, $where){
        if(empty($where['uid'])){
            return false;
        }
        $tableName = $this->getTableIndex($where['uid'], $this->_table);
        $this->updateDataSql($tableName, $data, $where);
        $this->rebuildUserProductInfo($where['uid']);
        return true;
    }
    
    
    public function get_tpl_product_info($pid){
        if(!isset($this->product_tpl[$pid])){
            $this->load->model('base/product_base', 'product');
            $info = $this->product->getProductDetail($pid);
            $this->product_tpl[$pid] = $info;
        }
        return $this->product_tpl[$pid];
    }
    
    //计算单条产品收益
    public function count_product_profit($user_product, $product_tpl){
        //用户购买的天数
        $product_tpl['uietime'] = strtotime($product_tpl['uietime']);
        $profit_time = $product_tpl['uietime'] > NOW ? NOW : $product_tpl['uietime'];   //结算时间
        $days = $this->diff_days(strtotime($product_tpl['uistime']), $profit_time);
//        echo $days;
//         if($product_tpl['pid'] == 100180){
//             echo strtotime($product_tpl['uistime']);
//             echo "<br />";
//             echo $profit_time;
//             echo "<br />";
//             echo $days;
//             echo "<br />";
//         }
        $profit = ($product_tpl['income']) / 360 * $days * $user_product['money'] * 0.01;
        $rtn['profit'] = sprintf("%.2f",substr(sprintf("%.3f", $profit), 0, -1));
        $rtn['days'] = $days;
        return $rtn;
    }
    
    public function diff_days($start, $now , $diffday = 0){
        if($now < $start){
            return 0;
        }
        $a_dt=getdate($start);
        $b_dt=getdate($now);
        $a_new=mktime(12,0,0,$a_dt['mon'],$a_dt['mday'],$a_dt['year']);
        $b_new=mktime(12,0,0,$b_dt['mon'],$b_dt['mday'],$b_dt['year']);
        //这里后面要改
        //负的就是0 不能转为正整数
        return (($b_new - $a_new)/86400) + $diffday;
    }
    
    public function setProfitDetailCache($uid, $type, $data){
        if($type == 0){
            $key = _KEY_REDIS_USER_NOT_SQUARE_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
        }else{
            $key = _KEY_REDIS_USER_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
        }
        foreach ($data as $index => $_data){
//             echo strtotime($index) . '<br />' . json_encode($_data);
            $_data = array($index => $_data);
            self::$container['redis_app_w']->setAdd($key, json_encode($_data), 1, strtotime($index));
        }
        $ttl_time = 86400;
        if(NOW < mktime(2, 0, 0) ){
            $ttl_time = mktime(2, 0, 0) - NOW;
        }
        self::$container['redis_app_w']->expire($key, $ttl_time);
        return true;
    }
    
    public function getProfitDetailCache($uid,$type, $start, $end, $withScore){
        if($type == 0){
            $key = _KEY_REDIS_USER_NOT_SQUARE_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
        }else{
            $key = _KEY_REDIS_USER_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
        }
        if($withScore == true){
            return self::$container['redis_app_r']->setRevRangeBySorce($key,$start,$end,1);
        }
        return self::$container['redis_app_r']->setRange($key,$start,$end,1);
    }
    
    public function getSumUserProduct($odate){
        $sum_money = 0;
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        for($i = 0 ; $i <= 15; $i++){
            $sql = "SELECT sum(money) as sum_money FROM ". $this->_table . $i . " WHERE buytime >= $start_time AND buytime < $end_time AND status = 0";
            $data = $this->executeSql($sql);
            $sub_money = $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
            $sum_money += $sub_money;
        }
        return $sum_money;
    }
    
    public function getAllSumUserProduct($odate){
        $sum_money = 0;
        $time = strtotime($odate);
        $end_time = $time + 86400;
        for($i = 0 ; $i <= 15; $i++){
            $sql = "SELECT sum(money) as sum_money FROM ". $this->_table . $i . " WHERE buytime < $end_time and status = 0";
            $data = $this->executeSql($sql);
            $sub_money = $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
            $sum_money += $sub_money;
        }
        return $sum_money;
    }
    
    public function getMoneyLimt($pname, $s, $e){
        $num = 0;
        for($i = 0 ; $i <= 15 ; $i++){
            $sql = 'select count(*) as num from ' . $this->_table . $i .' where money >= '.$s.' and money < '.$e.' and pname like "%'.$pname.'%" and buytime < 1440604800';
            $ret = $this->executeSql($sql);
            $count = $ret[0]['num'];
            $num += $count;
        }
        echo $pname . ':' . $num;
    }
    
    
    public function moveUserSumProductMoney($uid){
        $key = _KEY_REDIS_USER_PRODUCT_MONEY_PREFIX_ . $uid;
        return self::$container['redis_app_w']->delete($key);
    }
    
    
    public function getUserSumProductMoney($uid){
        $key = _KEY_REDIS_USER_PRODUCT_MONEY_PREFIX_ . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $userproductmoney = $self->_db_userproduct_money($uid);
            if(empty($userproductmoney)) return false;
            return $userproductmoney;
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return $return;
    }
    
    public function _db_userproduct_money($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(money) as sum_money FROM ". $table . " WHERE `status` = 0 and `uid` = " . $uid;
        $ret = $this->executeSql($sql);
        $count = $ret[0]['sum_money'];
        return $count ? $count : 0;
    }

    public function getAllMoney($uid){
    	$_table_index = $uid % 16;
    	$table = $this->_table . $_table_index;
    	$sql = "SELECT SUM(money) as totalmoney,count(*) as totalcount  FROM ".$table." WHERE `uid` =  ".$uid." and status=0 ";
    	$ret =  $this->executeSql($sql);
    	return $ret;
    }
    
    public function getUserProductByid($uid,$id){
    	$key = _KEY_REDIS_USER_PRODUCT_DETAIL_PREFIX_ . $uid.':'.$id;
    	$self = $this;
    	$return = $this->remember($key, 0 , function() use($self , $uid, $id) {
    		$userProductDetail = $self->_get_db_userproduct_detail($uid,$id);
    		if(empty($userProductDetail)) return false;
    		return json_encode($userProductDetail);
    	} , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
    		return json_decode($return , true);
    }
    
    public function _get_db_userproduct_detail($uid,$id){
    	$tableName = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * from $tableName where id=".$id;
    	$ret=  $this->executeSql($sql);
    	return $ret[0]?$ret[0]:false;
    }
}
