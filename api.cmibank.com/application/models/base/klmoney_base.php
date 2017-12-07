<?php

require_once 'basemodel.php'; 

class klmoney_base extends Basemodel{

    public $_table = 'cmibank.cmibank_klmoney';
    
    public function get_db_klmoney(){
        return $this->DBR->select('*')->from($this->_table)->get()->result_array();
    }
    
    public function add_klmoney($uid, $money){
        $sql = "INSERT INTO  " . $this->_table . " (`uid`, `money`, `ctime`)  VALUE ($uid, $money, " . NOW . ")  ON DUPLICATE KEY UPDATE  `money` = `money` + " . $money . ", updatetime = " . NOW ;
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function getUserKlMoney($uid){
        $where = array('uid' => $uid);
        $res = $this->selectDataSql($this->_table, $where);
        if($res){
            return $res['money'];
        }
        return 0;
    }
    
    
    public function countKlmoney(){
        $sql = "select count(*) as countnum from " . $this->_table . " where `money` > 0";
        $data = $this->executeSql($sql);
        return $data[0]['countnum'];
    }
    
    public function getKlmoneyList($offset, $psize){
        $sql = "select * from " . $this->_table . " WHERE money > 0 limit $offset, $psize";
        $data = $this->executeSql($sql);
        return $data;
    }
    
    public function updateUserKlmoney($data, $where){
        $this->updateDataSql($this->_table, $data, $where);
    }
    
    public function cost($uid, $money){
        if($money < 0){
            die('error longmoney');
        }
        $sql = "UPDATE " . $this->_table . " SET `money` = `money` - $money , `updatetime` = " . NOW . " WHERE `uid` = $uid and `money` >= $money";
        return $this->executeSql($sql);
    }
    
    public function sumKlmoney(){
        $sql = "SELECT sum(money) as sum_money FROM " . $this->_table ;
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'];
    }
    
}
