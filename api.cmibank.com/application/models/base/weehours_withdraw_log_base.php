<?php

require_once 'basemodel.php'; 

class weehours_withdraw_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_weehours_withdraw_log';
    
    public function addLog($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function updateDrawLog($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }
    
    public function getDrawLog($where = NULL){
        return $this->selectDataSql($this->_table, $where, null);
    }
    
    public function getDrawLogTableList($where = NULL){
        return $this->selectDataListSql($this->_table, $where, null, array(10000,0));
        
    }
    
    public function getLogByOrderId($orderid){
        return $this->selectDataSql($this->_table, array('orderid' => $orderid));
    }
    
    public function getLogListBySucctime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $search_date = date('Ymd', $start_time);
        $sql = "SELECT sum(money) as sum_money FROM " . $this->_table . " WHERE orderid like '%" . $search_date . "%'";
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
    }
    
    public function getSumMoneyByTime(){
    	$order = 'jyt' . date('Ymd', strtotime('-1 day'));
    	$sql = "select sum(money) as sum_money from  ". $this->_table ." where nb_orderid like '".$order."%'";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_money'];
    }
    //得到昨天出款了但是不是昨天申请的
    public function getYesWithhold($odate){
    	$stime = strtotime($odate);
    	$etime = $stime+86400;
    	$sql = "select sum(money) as sum_money from  ". $this->_table ." where utime<$etime and utime>$stime and ctime<$stime";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_money'];
    }
    //得到昨天申请了 但是不是昨天出款的
    public function getYesnotWithhold($odate){
    	$stime = strtotime($odate);
    	$etime = $stime+86400;
    	$sql = "select sum(money) as sum_money from  ". $this->_table ." where ctime<$etime and ctime>$stime and utime is null";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_money'];
    }
}
