<?php

require_once 'basemodel.php'; 

class withdraw_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_withdraw_log';
    
    public function addLog($data){
        $table = $this->getPayTableIndex();
        return $this->insertDataSql($data, $table);
    }
    
    public function updateDrawLog($data, $where, $data_y = '', $data_w = ''){
        $tableName = $this->getPayTableIndex($data_y, $data_w);
        return $this->updateDataSql($tableName, $data, $where);
    }
    
    public function getPayTableIndex($data_y = '', $data_w = ''){
        $data_y = $data_y ? $data_y : date("Y");
        $data_w = $data_w ? $data_w : date("W");
        if($data_w == 52){
            $data_y = '2016';
        }
        return $this->_table . '_' . $data_y . '_' . $data_w;
    }
    
    public function getDrawLogTableList($where = NULL, $data_y = '', $data_w = ''){
        $tableName = $this->getPayTableIndex($data_y, $data_w);
        return $this->selectDataListSql($tableName, $where, null, array(10000,0));
    }
    
    public function getLogByOrderId($orderid, $year, $week){
        $tableName = $this->getPayTableIndex($year, $week);
        return $this->selectDataSql($tableName, array('orderid' => $orderid));
    }
    
    public function getLogListBySucctime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $search_date = date('Ymd', $start_time);
        $data_y = date("Y", $time);
        $data_w = date('W', $time);
        if($data_w == 52){
            $data_y = '2016';
        }
        $tableName = $this->_table . '_' . $data_y . '_' . $data_w;
        $sql = "SELECT sum(money) as sum_money FROM " . $tableName . " WHERE orderid like '%" . $search_date . "%' AND back_status = 'SUCCESS'";

        $data = $this->executeSql($sql);
        return $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
    }
    
    public function getBaofooRequest(){
    	$tableName = $this->getPayTableIndex('', '');
    	$sql = "SELECT * FROM " . $tableName . " WHERE status=0 and plat='baofoo' ";
    	return $this->executeSql($sql);
    }
    
}
