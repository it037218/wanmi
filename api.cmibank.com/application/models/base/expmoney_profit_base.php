<?php

require_once 'basemodel.php'; 

class expmoney_profit_base extends Basemodel{

    public $_table = 'cmibank.cmibank_expmoney_profit_';
    
    public function add_exp_profit_log($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        return $this->insertDataSql($data, $table, true);
    }
    

    public function  _get_db_user_expmoney_profit($uid, $starttime, $endtime, $eids){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT * FROM " . $table . " WHERE `uid` = ". $uid ." and `ctime` >=" . $starttime . " and `ctime` <= ". $endtime ;
        if($eids){
            $sql .= ' AND eid in(' . implode(',', $eids) . ') ';
        }
        $sql .= ' order by ctime desc';
        return $this->executeSql($sql);
    }
    
    public function _get_user_expmoney_profit($uid, $starttime, $endtime, $eids = array()){
        $type = 1;
        if(!empty($eids)){
            $type = 0;
        }

        $key = _KEY_REDIS_EXPMONEY_LOG_PROFIT_LIST_PREFIX_ . date('Ymd', $starttime) .'-'. date('Ymd', $endtime) .':'. $uid.':' . $type;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid, $starttime, $endtime, $eids) {
            $user_exp_log = $self->_get_db_user_expmoney_profit($uid, $starttime, $endtime, $eids);
            if(empty($user_exp_log)) return false;
            return json_encode($user_exp_log);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        $ttl_time = 86400;
        if(NOW < mktime(2, 0, 0) ){
            $ttl_time = mktime(2, 0, 0) - NOW;
        }
        self::$container['redis_default']->expire($key , $ttl_time);
        if($return){
	        self::$container['redis_default']->setAdd(_KEY_REDIS_EXPMONEY_LOG_PROFIT_SET_PREFIX_, $key, 0);
        }
        $exp_log = json_decode($return , true);
        return $exp_log;
    }

    public function get_product_count_profit_from_db($uid,$eids){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(`profit`) as sum_profit FROM " . $table . " WHERE `uid` = " . $uid;
        if($eids){
        	$sql .= ' AND eid in(' . implode(',', $eids) . ') ';
        }
        $data = $this->executeSql($sql);
        if(empty($data[0]['sum_profit'])){
        	return 0;
        }else{
        	return $data[0]['sum_profit'];
        }
    }
    
    public function get_product_count_profit($uid,$eids= array()){
    	$type = 1;
    	if(!empty($eids)){
    		$type = 0;//在投体验金
    	}
        $key = _KEY_REDIS_TOTAL_EXPMONEY_PROFIT_PREFIX_ . date('Y-m-d') . '_' . $uid.':'.$type;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid,$eids) {
            $productInfo = $self->get_product_count_profit_from_db($uid,$eids);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        return $return ? json_decode($return , true) : 0;
    }
    
    
    public function get_yesterday_profit_from_db($uid){
        $odate = date('Y-m-d', strtotime("-1 day"));
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(`profit`) as sum_profit FROM " . $table . " WHERE `uid` = " . $uid . " AND odate = '" . $odate . "'"; 
        $data = $this->executeSql($sql);
        return empty($data[0]['sum_profit']) ? 0:$data[0]['sum_profit'];
    }
    
    public function get_yesterday_profit($uid){
        $key = _KEY_REDIS_YESTERDAY_EXPMONEY_PROFIT_PREFIX_ . date('Y-m-d') . ':' . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $productInfo = $self->get_yesterday_profit_from_db($uid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        return $return ? json_decode($return , true) : 0;
    }
    
    public function _count_user_expmoney_profit($uid, $ue_ids){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(profit) as countprofit FROM " . $table . " WHERE `uid` =  " . $uid ;
        if($ue_ids){
            $sql .= " AND eid in (" . implode(',', $ue_ids) . ")";
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
    public function cleanCache(){
    	$set = self::$container['redis_default']->setMembers(_KEY_REDIS_EXPMONEY_LOG_PROFIT_SET_PREFIX_);
    	if($set){
    		$ret = self::$container['redis_default']->delete($set);
    		$ret = self::$container['redis_default']->delete(_KEY_REDIS_EXPMONEY_LOG_PROFIT_SET_PREFIX_);
    	}
    }

}
