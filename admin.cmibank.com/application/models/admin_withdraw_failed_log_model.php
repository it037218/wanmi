<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_withdraw_failed_log_model extends Basemodel {

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
    
    
}
