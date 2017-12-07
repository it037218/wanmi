<?php
require_once 'basemodel.php'; 

class buchang_base extends Basemodel {

    private $_table = 'cmibank_log.cmibank_buchang';
    
    public function __construct() {
        parent::__construct();
    }

    public function addBuchange($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        return true;
    }
    
    public function updateBuchange($bid, $data){
        $this->updateDataSql($this->_table, $data, array('bid' => $bid));
        return true;
    }
    
    public function deleteBuchange($bid){
        $this->deleteDataSql($this->_table, array('bid' => $bid));
        return true;
    }
    
    public function getBuchangeByBid($bid){
        $where = array('bid' => $bid);
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getBuchangeList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function getBuchangeCount($where){
        return $this->selectDataCountSql($this->_table, $where);
    }
    
    public function getBuchangByDay($odate){
    	$time = strtotime($odate);
    	$start_time = $time;
    	$end_time = $time + 86400;
    	$sql = "SELECT sum(money) as sum_money FROM " . $this->_table . " WHERE status=1 and sh_time >= " . $start_time . " AND sh_time < " . $end_time;
    	$data = $this->executeSql($sql);
    	return $data[0]['sum_money'];
    }
    
}
