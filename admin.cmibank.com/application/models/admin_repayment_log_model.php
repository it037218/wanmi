<?php

//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_repayment_log_model extends Basemodel{

    private $_table = 'cmibank_log.cmibank_product_repayment_log';
 
    public function createLog($data){
        $table = $this->getTableIndex(NOW);
        return $this->insertDataSql($data, $table);
    }
    
    public function getTableIndex($createtime){
        if(date('W',$createtime) == 52){
            $data_y = '2016';
        }else{
            $data_y = date("Y",$createtime);
        }
        return $this->_table . '_' . $data_y . '_' . date('W',$createtime);
    }
    
    public function getLogsLimitByPid($pid, $createtime,$psize, $offset){
        $table = $this->getTableIndex($createtime);
        return $this->selectDataListSql($table, array('pid' => $pid), null, array($psize, $offset));
    }
    
    public function updateLogsStatusByPid($pid,$createtime, $data){
        $table = $this->getTableIndex($createtime);
        return $this->updateDataSql($table, $data, array('pid' => $pid));
    }
    
    public function countLogsByPid($pid,$createtime){
        $table = $this->getTableIndex($createtime);
        return $this->selectDataCountSql($table, array('pid' => $pid));
    }
    
    public function sumMoneyLogsByPid($pid,$createtime){
        $table = $this->getTableIndex($createtime);
        $sql = "SELECT sum(`money`) as sum_money  FROM " . $table . " WHERE pid = " . $pid;
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
    }
    
    public function sumProfitLogsByPid($pid,$createtime){
        $table = $this->getTableIndex($createtime);
        $sql = "SELECT sum(`profit`) as sum_profit  FROM " . $table . " WHERE pid = " . $pid;
        $data = $this->executeSql($sql);
        return $data[0]['sum_profit'] ? $data[0]['sum_profit'] : 0;
    }
    
}
