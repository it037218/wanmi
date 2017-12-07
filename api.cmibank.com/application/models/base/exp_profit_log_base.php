<?php

require_once 'basemodel.php'; 
//定期利息
class exp_profit_log_base extends Basemodel{

    public $_table = 'cmibank_log.cmibank_exp_profit_log_';
    
    private $_fix = 16;
    
    public function add_exp_profit_log($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $this->insertDataSql($data, $table, true);
    }
    
    public function get_exp_profit_buy_uid($uid){
        $tableName = $this->getTableIndex($uid, $this->_table, $this->_fix);
        return $this->selectDataListSql($tableName, array('uid' => $uid), 'odate desc');
    }
    
    public function get_exp_profit_buy_uid_and_pids($uid, $pids){
        $tableName = $this->getTableIndex($uid, $this->_table, $this->_fix);
        return $this->selectDataListSql($tableName, array('uid' => $uid, 'pid' => $pids), 'odate desc');
    }

    public function  _get_db_user_expproduct_profit($uid, $starttime, $endtime, $ue_ids){
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $sql = "SELECT * FROM " . $table . " WHERE `uid` = ". $uid ." and `time` >=" . $starttime . " and `time` <= ". $endtime ;
        if($ue_ids){
            $sql .= ' AND ue_id in(' . implode(',', $ue_ids) . ') ';
        }
        $sql .= ' order by time desc';
        return $this->executeSql($sql);
    }
    
    public function _get_user_expproduct_profit($uid, $starttime, $endtime, $ue_ids = array()){
        $type = 1;
        if(!empty($ue_ids)){
            $type = 0;
        }

        $key = _KEY_REDIS_SYSTEM_EXPMONEY_LOG_PROFIT_LIST_PREFIX_ . date('Ymd', $starttime) .'-'. date('Ymd', $endtime) .':'. $uid . $type;

        
//         echo $key;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid, $starttime, $endtime, $ue_ids) {
            $user_exp_log = $self->_get_db_user_expproduct_profit($uid, $starttime, $endtime, $ue_ids);
            if(empty($user_exp_log)) return false;
            return json_encode($user_exp_log);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        $ttl_time = 86400;
        if(NOW < mktime(2, 0, 0) ){
            $ttl_time = mktime(2, 0, 0) - NOW;
        }
        self::$container['redis_default']->expire($key , $ttl_time);
        $exp_log = json_decode($return , true);
        return $exp_log;
    }

    public function get_product_count_profit_from_db($uid){
        $table = $this->getTableIndex($uid, $this->_table, $this->_fix);
        $sql = "SELECT sum(`profit`) as count_profit FROM " . $table . " WHERE `uid` = " . $uid;
        $data = $this->executeSql($sql);
        return $data[0]['count_profit'] ? $data[0]['count_profit'] : 0;
    }
    
    public function get_product_count_profit($uid){
        $key = _KEY_REDIS_SYSTEM_COUNT_EXPMONEY_PROFIT_PREFIX_ . date('Y-m-d') . '_' . $uid;
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
        $sql = "SELECT sum(`profit`) as sum_profit FROM " . $table . " WHERE `uid` = " . $uid . " AND odate = '" . $odate . "'"; 
        $data = $this->executeSql($sql);
        return $data[0]['sum_profit'] ? $data[0]['sum_profit'] : 0;
    }
    
    public function get_yesterday_profit($uid){
        $key = _KEY_REDIS_SYSTEM_YESTERDAY_EXPMONEY_PROFIT_PREFIX_ . date('Y-m-d') . '::' . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $productInfo = $self->get_yesterday_profit_from_db($uid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        return $return ? json_decode($return , true) : 0;
    }
    
    public function _count_user_expproduct_profit($uid, $ue_ids){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(profit) as countprofit FROM " . $table . " WHERE `uid` =  " . $uid ;
        if($ue_ids){
            $sql .= " AND ue_id in (" . implode(',', $ue_ids) . ")";
        }
//         echo $sql;
        $data =  $this->executeSql($sql);
        if($data[0]['countprofit']){
            return $data[0]['countprofit'];
        }else{
            return 0;
        }
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
    
    
}
