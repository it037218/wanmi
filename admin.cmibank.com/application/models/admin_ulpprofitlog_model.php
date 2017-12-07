<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_ulpprofitlog_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_ulp_profit_log_';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function getUlpProfitLogUid($uid){
         $_table_index = $uid % 16;
         $table = $this->_table . $_table_index;
         return $this->selectDataListSql($table, array('uid' => $uid));
     }
     
     //统计 累计活期收益
     public function Total_Count_ulp_profit(){
         $a = array();
         $sum = 0;
         for($i = 0 ; $i <= 15; $i++){
             $a[] =$this->Count_ulp_profit($i);
         }
         foreach ($a as $val){
             $sum += $val[0]['sum(profit)'];
         }
         return $sum;
     }
     public function Count_ulp_profit($index){
         $sql = "SELECT sum(profit) FROM $this->_table".$index;
         return $this->executeSql($sql);
     }
     
     public function total_yesy_ulp_profit(){
         $a = array();
         $sum = 0;
         for($i = 0 ; $i <= 15; $i++){
             $a[] =$this->yesy_ulp_profit($i);
         }
         foreach ($a as $val){
             $sum += $val[0]['sum(profit)'];
         }
         return $sum;
     }
     public function yesy_ulp_profit($index){
         $t = mktime(0,0,0);
         $sql = "SELECT sum(profit) FROM $this->_table".$index." where time ='".$t."'";
         return $this->executeSql($sql);
     }
     
     public function  sum_user_longproduct_profit($uid){
     	$table = $this->getTableIndex($uid, $this->_table);
     	$sql = "SELECT sum(profit) as sumprofit FROM " . $table . " WHERE `uid` = ". $uid;
     	$data =  $this->executeSql($sql);
     	return $data[0]['sumprofit'];
     }
}