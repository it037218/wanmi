<?php

require_once 'basemodel.php'; 

class activity_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_activity_log';
 
    
    public function getLogByOrdid($ordid){
        $table = $this->getPayTableIndex();
        $result = $this->selectDataSql($table, array('ordid' => $ordid));
        return !empty($result) ? $result : false;
    }
    
    public function createLog($data){
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
    
    public function getAllActivityByActId($actid){
        $table = $this->getPayTableIndex();
        $result = $this->selectDataListSql($table, array('actid' => $actid));
        return $result;
    }
    
    public function getTodayActivity($today){
        $key = 'activity:money_limt:' . $today;
        return self::$container['redis_app_w']->get($key);
    }
    
    public function IncrTodayActivity($today, $money){
        $key = 'activity:money_limt:' . $today;
        return self::$container['redis_app_w']->incrWithValue($key, $money);
    }
    
    
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
        $sql = "SELECT content FROM " . $tableName . " WHERE ctime >= " . $start_time . " AND ctime < " . $end_time;
        $data = $this->executeSql($sql);
        $sum = 0;
        if($data){
            foreach ($data as $_v){
                if(is_numeric($_v['content'])){
                    $sum += $_v['content'];
                }else{
                    $_s = json_decode($_v['content'], true);
                    $sum += $_s['givemoney'];
                }
                
            }
        }
        return $sum;
    }
    
    
}
