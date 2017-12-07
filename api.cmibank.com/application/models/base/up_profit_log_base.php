<?php

require_once 'basemodel.php'; 
//定期利息
class up_profit_log_base extends Basemodel{

    public $_table = 'cmibank_log.cmibank_up_profit_log_';
    
    private $_fix = 32;
    
    public function add_up_profit_log($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $this->insertDataSql($data, $table, true);
    }
    
    public function get_up_profit_buy_uid($uid){
        $tableName = $this->getTableIndex($uid, $this->_table, $this->_fix);
        return $this->selectDataListSql($tableName, array('uid' => $uid), 'odate desc');
    }
    
    public function get_up_profit_buy_uid_and_pids($uid, $pids){
        $tableName = $this->getTableIndex($uid, $this->_table, $this->_fix);
        return $this->selectDataListSql($tableName, array('uid' => $uid, 'pid' => $pids), 'odate desc');
    }


    public function get_product_count_profit_from_db($uid){
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $sql = "SELECT sum(`profit`) as count_profit FROM " . $table . " WHERE `uid` = " . $uid;
        $data = $this->executeSql($sql);
        return $data[0]['count_profit'] ? $data[0]['count_profit'] : 0;
    }
    
    public function get_product_count_profit($uid){
        $key = _KEY_REDIS_SYSTEM_COUNT_PROFIT_PREFIX_ . date('Y-m-d') . '_' . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $productInfo = $self->get_product_count_profit_from_db($uid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    
    public function get_yesterday_profit_from_db($uid){
        $odate = date('Y-m-d', strtotime("-1 day"));
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $sql = "SELECT sum(`profit`) as yesterday_profit FROM " . $table . " WHERE `uid` = " . $uid . " AND odate = '" . $odate . "'"; 
        $data = $this->executeSql($sql);
        return !empty($data[0]['yesterday_profit']) ? $data[0]['yesterday_profit'] : 0;
    }
    
    public function get_yesterday_profit($uid){
        $key = _KEY_REDIS_SYSTEM_YESTERDAY_PROFIT_PREFIX_ . date('Y-m-d') . '_' . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $productInfo = $self->get_yesterday_profit_from_db($uid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        
        return $return ? json_decode($return , true) : 0;
    }
    
    public function get_sum_profit_by_pids($uid, $pids){
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $sql = "SELECT sum(profit) as sum_profit FROM " . $table . " WHERE `uid` = " . $uid . " and `pid` in (" . implode(',', $pids) . ")";
        $data = $this->executeSql($sql);
        return $data[0]['sum_profit'];
    }
    
    
    
    public function get_all_profit_with_odate($odate){
        $sum_profit = 0;
        for($i = 0 ; $i <= 31; $i++){
            $sql = "SELECT sum(profit) as p_sum FROM " . $this->_table . $i . " WHERE odate = '$odate'";
            $data = $this->executeSql($sql);
            $sum_profit += $data[0]['p_sum'];
        }
        return $sum_profit;
    }
    
//     public function  _get_user_longproduct_profit($uid, $starttime, $endtime){
//         $table = $this->getTableIndex($uid, $this->_table);
//         $sql = "SELECT * FROM " . $table . " WHERE `uid` = ". $uid ." and `time` >=" . $starttime . " and `time` <= ". $endtime . ' order by time desc';
//         //echo $sql;
//         return $this->executeSql($sql);
//     }
    
//     public function _count_user_longproduct_profit($uid){
//         $table = $this->getTableIndex($uid, $this->_table);
//         $sql = "SELECT sum(profit) as countprofit FROM " . $table . " WHERE `uid` =  " . $uid;
//         $data =  $this->executeSql($sql);
//         if($data[0]['countprofit']){
//             return $data[0]['countprofit'];
//         }else{
//             return 0;
//         }
//     }
    
}
