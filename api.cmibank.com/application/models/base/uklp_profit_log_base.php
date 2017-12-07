<?php

require_once 'basemodel.php'; 

class uklp_profit_log_base extends Basemodel{

    public $_table = 'cmibank.cmibank_uklp_profit_log_';
    
    public function add_uklp_profit_log($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $this->insertDataSql($data, $table);
    }
    
    public function  _get_db_user_klproduct_profit($uid, $starttime, $endtime){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT * FROM " . $table . " WHERE `uid` = ". $uid ." and `time` >=" . $starttime . " and `time` <= ". $endtime . ' order by time desc';
//         echo $sql;
        return $this->executeSql($sql);
    }
    
    public function _get_user_klproduct_profit($uid, $starttime, $endtime){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_LOG_PROFIT_LIST_PREFIX_ . date('Ymd', $starttime) .'-'. date('Ymd', $endtime) .':'. $uid;
        //         echo $key;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid, $starttime, $endtime) {
            $user_longprofit_log = $self->_get_db_user_klproduct_profit($uid, $starttime, $endtime);
            if(empty($user_longprofit_log)) return false;
            return json_encode($user_longprofit_log);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        $ttl_time = 86400;
        if(NOW < mktime(2, 0, 0) ){
            $ttl_time = mktime(2, 0, 0) - NOW;
        }
        self::$container['redis_default']->expire($key , $ttl_time);
        $exp_log = json_decode($return , true);
        return $exp_log;
    }
    
    public function _count_user_klproduct_profit($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(profit) as countprofit FROM " . $table . " WHERE `uid` =  " . $uid;
        $data =  $this->executeSql($sql);
        if($data[0]['countprofit']){
            return $data[0]['countprofit'];
        }else{
            return 0;
        }
    }
    
    public function get_all_profit_with_odate($odate){
        $sum_profit = 0;
        $odate = strtotime($odate);
        for($i = 0 ; $i <= 15; $i++){
            $sql = "SELECT sum(profit) as p_sum FROM " . $this->_table . $i . " WHERE time = '$odate'";
            $data = $this->executeSql($sql);
            $sum_profit += $data[0]['p_sum'];
        }
        return $sum_profit;
    }
    
    
    public function get_all_klmoney_with_odate($odate){
        $sum_profit = 0;
        $odate = strtotime($odate);
        for($i = 0 ; $i <= 15; $i++){
            $sql = "SELECT sum(f_klmoney) as p_sum FROM " . $this->_table . $i . " WHERE time = '$odate'";
            $data = $this->executeSql($sql);
            $sum_profit += $data[0]['p_sum'];
        }
        return $sum_profit;
    }
    
    public function get_uklplist_with_odate($index, $odate){
        $sql = "SELECT * FROM " . $this->_table . $index . " WHERE time = '$odate'";
        return $this->executeSql($sql);
    }
    
    public function update_uklp_data_by_id($index, $data, $where){
        return $this->updateDataSql($this->_table . $index, $data, $where);
    }
    
}
