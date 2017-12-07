<?php

require_once 'basemodel.php'; 

class withdraw_sxf_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_withdraw_sxf_log';
 
    
    public function createLog($data){
        //$table = $this->getSxfTableIndex();//取消分月
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function getSxfTableIndex($year = '', $date_m = ''){
        $year = $year ? $year : date('Y');
        $date_m = $date_m ? $date_m : date("m");
        return $this->_table . '_' .$year. '_' .$date_m;
    }
    
    public function getSxfByDay($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $sql = "SELECT sum(sxf) as sum_sxf FROM " . $this->_table . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time;
        $data = $this->executeSql($sql);
        return $data[0]['sum_sxf'];
    }
    
    public function getAll(){
    	return $this->selectDataListSql($this->_table, null, null, null);
    }
}
