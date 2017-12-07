<?php

require_once 'basemodel.php'; 

class expmoney_base extends Basemodel{

    private $_table = 'cmibank.cmibank_expmoney';
    
    public function get_user_expmoney($uid){
        $db = self::$container['db_r'];
        $data = $db->select('expmoney')
        ->from("cmibank.cmibank_expmoney")
        ->where('uid', $uid)
        ->get()
        ->row_array();
        
        if(empty($data)){
            $data = $this->init_user_expmoney($uid);
        }
        if(isset($data['expmoney'])){
           return $data['expmoney'];
        }else{
           return 0;
        }
    }
    
    public function init_user_expmoney($uid){
        $data = array('uid' => $uid, 'expmoney' => 0);
        $this->insertDataSql($data, $this->_table);
        return $data;
    }
    
    
    public function cost_user_expmoney($uid, $expmoney){
        $sql = "UPDATE " . $this->_table . " SET `expmoney` = `expmoney` - " . $expmoney . " WHERE `uid` = " . $uid . " AND `expmoney` >= " . $expmoney;
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function add_user_expmoney($uid, $expmoney, $log = false){
        $sql = "INSERT INTO  " . $this->_table . " (`uid`, `expmoney`)  VALUE ($uid, '$expmoney')  ON DUPLICATE KEY UPDATE  `expmoney` = `expmoney` + '" . $expmoney ."'";
        if($log){
            $msg = array();
            $msg['uid'] = $uid;
            $msg['expmoney'] = $expmoney;
            $this->expmoney_back_log($msg);
        }
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
            return true;
        }else{
            return false;
        }
    }
    
    public function expmoney_back_log($msg){
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
    
    public function count_expmoney(){
        $sql = "select sum(expmoney) as sum_expmoney from " . $this->_table;
        $data = $this->executeSql($sql);
        return $data[0]['sum_expmoney'] ? $data[0]['sum_expmoney'] : 0;
    }
    
}
