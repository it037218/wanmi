<?php

require_once 'basemodel.php'; 

class invite_first_buy_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_invite_first_buy_log';
    
    public function getLogByOrdid($ordid){
        $result = $this->selectDataSql($this->_table, array('ordid' => $ordid));
        return !empty($result) ? $result : false;
    }
    
    public function createLog($data){
        return $this->insertDataSql($data, $this->_table);
    }

    public function getLogListByCtime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $sql = "SELECT sum(money) as sum_money FROM " . $this->_table . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time;
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'];
    }
        
}
