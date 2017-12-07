<?php

require_once 'basemodel.php'; 

class product_repayment_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_product_repayment_log';
 
    public function createLog($data){
        $table = $this->getTableIndex();
        return $this->insertDataSql($data, $table);
    }
    
    public function getTableIndex(){
        $data_y = date("Y");
        $data_w = date("W");
        if($data_w == 52){
            $data_y = '2016';
        }
        return $this->_table . '_' . $data_y . '_' . $data_w;
    }
    
    public function getLogsWithStatus($status){
        $table = $this->getTableIndex();
        $data = self::$container['db_r']->select('*')
        ->from($table)
        ->where('status', $status)
        ->get()
        ->result_array();
        return $data;
    }
    
    public function updateLogs($data, $where){
        $table = $this->getTableIndex();
        return $this->updateDataSql($table, $data, $where);
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
        $sql = "SELECT sum(money) as sum_amt FROM " . $tableName . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time ;
        $data = $this->executeSql($sql);
        $sum_amt = $data[0]['sum_amt'] ? $data[0]['sum_amt'] : 0;
        $sql = "SELECT sum(profit) as sum_profit FROM " . $tableName . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time ;
        $data = $this->executeSql($sql);
        $sum_profit = $data[0]['sum_profit'] ? $data[0]['sum_profit'] : 0;
        return array('money' => $sum_amt, 'profit' => $sum_profit);
    }
    
}
