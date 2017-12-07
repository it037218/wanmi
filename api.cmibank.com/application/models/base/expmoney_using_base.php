<?php

//在投体验金
require_once 'basemodel.php'; 

class expmoney_using_base extends Basemodel{

    private $_table = 'cmibank.cmibank_expmoney_using';
    
    public function get_user_expmoney_using($uid){
        $db = self::$container['db_r'];
        $data = $db->select('money')
        ->from("cmibank.cmibank_expmoney_using")
        ->where('uid', $uid)
        ->get()
        ->row_array();
        
        if(empty($data)){
            $data = $this->init_user_expmoney_using($uid);
        }
        if(isset($data['money'])){
           return $data['money'];
        }else{
           return 0;
        }
    }
    
    public function init_user_expmoney_using($uid){
        $data = array('uid' => $uid, 'money' => 0, 'ctime' => NOW);
        $this->insertDataSql($data, $this->_table);
        return $data;
    }
    
    
    public function cost_user_expmoney_using($uid, $money){
        $sql = "UPDATE " . $this->_table . " SET `money` = `money` - $money, `updatetime` = " . NOW . " WHERE `uid` = " . $uid;
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            echo 'OK';
            return true;
        }else{
            return false;
        }
    }
    
    public function add_user_expmoney_using($uid, $money){
        $sql = "INSERT INTO  " . $this->_table . " (`uid`, `money`, `ctime`)  VALUE ($uid, '$money', '" . NOW . "')  ON DUPLICATE KEY UPDATE  `money` = `money` + '" . $money ."' , `updatetime` = '" . NOW . "'";
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function count_expmoney_using(){
        $sql = "select count(uid) as count_money from " . $this->_table;
        $data = $this->executeSql($sql);
        return $data[0]['count_money'] ? $data[0]['count_money'] : 0;
    }
    
    public function sum_expmoney_using(){
        $sql = "select sum(money) as sum_money from " . $this->_table;
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
    }
    
    public function getExpMoneyUsingList($todaytime, $offset, $psize){
        $sql = "select * from " . $this->_table . " limit $offset, $psize";
        $data = $this->executeSql($sql);
        return $data;
    }
    
    public function updateUserExpMoney($data, $where){
        $this->updateDataSql($this->_table, $data, $where);
    }
    
}
