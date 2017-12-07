<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_balance_model extends Basemodel {
    
    private $_table = 'cmibank.cmibank_balance';
    
    public function getBalanceList(){
        return $this->selectDataListSql($this->_table, null);
    }
    
    public function getBalanceByUid($uid){
        return $this->selectDataListSql($this->_table, array('uid'=>$uid));
    }
    
    //获取总账户余额
    public function getSumBalanceMoney(){
        $sql = "SELECT sum(balance) FROM $this->_table";
        return $this->executeSql($sql);
    }

    public function get_user_balance($uid){
        $db = self::$container['db_r'];
        $data = $db->select('balance')
        ->from("cmibank.cmibank_balance")
        ->where('uid', $uid)
        ->get()
        ->row_array();
        if(isset($data['balance'])){
            return $data['balance'];
        }else{
            return 0;
        }
    }
    
    public function add_user_balance($uid, $balance){
        $sql = "INSERT INTO  " . $this->_table . " (`uid`, `balance`)  VALUE ($uid, '$balance')  ON DUPLICATE KEY UPDATE  `balance` = `balance` + '" . $balance ."'";
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            return true;
        }else{
            return false;
        }
    }
    public function cost_user_balance($uid, $money){
    	if($money < 0){
    		die('error balance');
    	}
    	$sql = "UPDATE " . $this->_table . " SET `balance` = `balance` - " . $money . " WHERE `uid` = " . $uid . " AND `balance` >= " . $money;
    	$this->DBW->query($sql);
    	if($this->DBW->affected_rows() >= 1){
    		return true;
    	}else{
    		return false;
    	}
    }
}