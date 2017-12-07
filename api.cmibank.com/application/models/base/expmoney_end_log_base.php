<?php

require_once 'basemodel.php'; 

class expmoney_end_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_expmoney_end_log';
    
    public function addLog($data){
        $table = $this->getTableIndex();
        return $this->insertDataSql($data, $table);
    }
    
    public function getTableIndex($data_y = '', $data_w = ''){
        $data_y = $data_y ? $data_y : date("Y");
        $data_w = $data_w ? $data_w : date("W");
        if($data_w == 52){
            $data_y = '2016';
        }
        return $this->_table . '_' . $data_y . '_' . $data_w;
    }
    
    public function getLogListBySucctime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $search_date = date('Ymd', $start_time);
        $tableName = $this->_table . '_' . date("Y", $time) . '_' . date('W', $time);
        $sql = "SELECT sum(money) as sum_money FROM " . $tableName . " WHERE orderid like '%" . $search_date . "%'";
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
    }
    
}
