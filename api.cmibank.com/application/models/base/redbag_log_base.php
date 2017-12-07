<?php

require_once 'basemodel.php'; 

class redbag_log_base extends Basemodel{

    private $_table = 'cmibank.cmibank_redbag_log';
    
    public function create_redbag_log($data){
    	return $this->insertDataSql($data, $this->_table);
    }
    
    public function count_by_date($odate){
    	$time = strtotime($odate);
    	$start_time = $time;
    	$end_time = $time + 86400;
    	$sql = "SELECT sum(money) as sum_money FROM " . $this->_table . " WHERE utime is not null and utime >= " . $start_time . " AND utime < " . $end_time;
    	$data = $this->executeSql($sql);
    	return $data[0]['sum_money'];
    }
    
    public function update_redbag_log($data, $id){
        $ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
        return $ret;
    }
    
}
