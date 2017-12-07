<?php

require_once 'basemodel.php'; 
/*
 * 取现错误记录，脚本回用户钱
 */
class withdraw_failed_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_withdraw_failed_log';
    
    public function addFailedLog($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function updateDrawFailedLog($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }
    
    public function getDrawFailedLogTableList($where = NULL){
        return $this->selectDataListSql($this->_table, $where, null, array(10000,0));
    }
    
    public function getFailedLogByOrderId($orderid){
        return $this->selectDataSql($this->_table, array('orderid' => $orderid));
    }
    
    public function getFailedLogListBySucctime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $search_date = date('Ymd', $start_time);
        $sql = "SELECT sum(money) as sum_money FROM " . $this->_table . " WHERE orderid like '%" . $search_date . "%'";
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
    }
    
    //得到昨天出款了但是不是昨天申请的
    public function getYesWithdraw($odate){
    	$stime = strtotime($odate);
    	$etime = $stime+86400;
    	$sql = "select sum(money) as sum_money from  ".$this->_table." where utime<$etime and utime>$stime and ctime<$stime";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_money'];
    }
    //得到昨天申请了 但是不是昨天未处理的
    public function getYestnotWithdraw($odate){
    	$stime = strtotime($odate);
    	$etime = $stime+86400;
    	$sql = "select sum(money) as sum_money from  ".$this->_table." where ctime<$etime and ctime>$stime and utime=0";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_money'];
    }
}
