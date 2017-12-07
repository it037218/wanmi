<?php

require_once 'basemodel.php'; 

class balance_base extends Basemodel{

    private $_table = 'cmibank.cmibank_balance';
    
    public function get_user_balance($uid){
        $db = self::$container['db_r'];
        $data = $db->select('balance')
        ->from("cmibank.cmibank_balance")
        ->where('uid', $uid)
        ->get()
        ->row_array();
        
        if(empty($data)){
            $data = $this->init_user_balance($uid);
        }
        if(isset($data['balance'])){
           return $data['balance'];
        }else{
           return 0;
        }
    }
    
    public function init_user_balance($uid){
        $data = array('uid' => $uid, 'balance' => 0);
        $this->insertDataSql($data, $this->_table);
        return $data;
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
    
    public function add_user_balance($uid, $balance, $log = false){
        $sql = "INSERT INTO  " . $this->_table . " (`uid`, `balance`)  VALUE ($uid, '$balance')  ON DUPLICATE KEY UPDATE  `balance` = `balance` + '" . $balance ."'";
        if($log){
            $msg = array();
            $msg['uid'] = $uid;
            $msg['money'] = $balance;
            $this->balance_back_log($msg);
        }
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function balance_back_log($msg){
        if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
            $logFile = './crontab_run_log.log.'.date("Y-m-d");
        }else{
            $logFile = '/tmp/crontab_run_log.log.'.date("Y-m-d");;
        }
        $msg = json_encode($msg);
        $fp = fopen($logFile, 'a');
        $isNewFile = !file_exists($logFile);
        if (flock($fp, LOCK_EX)) {
            if ($isNewFile) {
                chmod($logFile, 0666);
            }
            fwrite($fp, $msg . "\n");
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
    
    public function count_balance(){
        $sql = "select sum(balance) as sum_balance from " . $this->_table;
        $data = $this->executeSql($sql);
        return $data[0]['sum_balance'] ? $data[0]['sum_balance'] : 0;
    }
    
}
