<?php

require_once 'basemodel.php'; 

class buy_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_buy_log';
    
    public function getLogByOrdid($ordid){
        $table = $this->getPayTableIndex();
        $result = $this->selectDataSql($table, array('ordid' => $ordid));
        return !empty($result) ? $result : false;
    }
    
    public function createBuyLog($data){
        $table = $this->getPayTableIndex();
        return $this->insertDataSql($data, $table);
    }
    
    public function getPayTableIndex($data_y = '', $data_w = ''){
        $data_y = $data_y ? $data_y : date("Y");
        $data_w = $data_w ? $data_w : date("W");
        if($data_w == 52){
            $data_y = '2016';
        }
        return $this->_table . '_' . $data_y . '_' . $data_w;
    }
    
    public function getLogListByCtime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $data_y = date("Y", $time);
        $data_w = date('W', $time);
        if($data_w == 52){
            $data_y = '2016';
        }
        $tableName = $this->_table . '_' . $data_y . '_' . $data_w;
        $sql = "SELECT sum(amt) as sum_amt FROM " . $tableName . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time . " AND ptype = 'p' ";
        $data = $this->executeSql($sql);
        return $data[0]['sum_amt'];
    }
    
    public function updateLog($data, $where){
        $table = $this->getPayTableIndex();
        return $this->updateDataSql($table, $data, $where);
    }
    
        
}
