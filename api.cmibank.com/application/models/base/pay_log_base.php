<?php

require_once 'basemodel.php'; 

class pay_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_pay_log';
 
    
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
        $sql = "SELECT sum(amt) as sum_amt FROM " . $tableName . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time . " AND status = 1";
        $data = $this->executeSql($sql);
        return $data[0]['sum_amt'];
    }
    
    public function getLogListForPlatByCtime($odate){
    	$time = strtotime($odate);
    	$start_time = $time;
    	$end_time = $time + 86400;
    	$data_y = date("Y", $time);
    	$data_w = date('W', $time);
    	if($data_w == 52){
    		$data_y = '2016';
    	}
    	$tableName = $this->_table . '_' . $data_y . '_' . $data_w;
    	$sql = "SELECT platform,sum(amt) as sum_amt FROM " . $tableName . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time . " AND status = 1 group by platform order by platform";
    	$data = $this->executeSql($sql);
    	return $data;
    }
    
    public function getLogByOrdid($ordid){
        $table = $this->getPayTableIndex();
        $result = $this->selectDataSql($table, array('ordid' => $ordid));
        return !empty($result) ? $result : false;
    }
    
    public function updateOrder($ordid, $data){
        if(!$ordid){
            return false;
        }
        $table = $this->getPayTableIndex();
        return $this->updateDataSql($table, $data, array('ordid' => $ordid));
    }

//     public function setUserBankInfo($uid, $cardno, $bankid, $provid, $areaid){
//         $data = array('cardno' => $cardno, 'bankid' => $bankid, 'provid' => $provid, 'areaid' => $areaid);
//         $where = array('uid' => $uid);
//         $result = $this->updateDataSql($this->_table, $data, $where);
//         return $result;
//     }
    
    public function createOrder($data){
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
    
}
