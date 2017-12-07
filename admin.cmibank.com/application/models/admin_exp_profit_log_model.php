<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_exp_profit_log_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_expmoney_profit_';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function getExpProfitList($where,$order,$limit){
         $index = $where['uid']%16;
         $tableName = $this->_table . $index;
         return $this->selectDataListSql($tableName,$where,$order,$limit);
     }
     
     //当个用户累计体验金收益
     public function getCountExpProfit($uid){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $sql = "select sum(profit) FROM $tableName where uid=$uid";
         return $this->executeSql($sql);
     }
     //当个用户累计体验金收益
     public function getCountExpmoney($uid){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $sql = "select sum(money) FROM $tableName where uid=$uid";
         return $this->executeSql($sql);
     }
     //当个用户昨日累计体验金收益
     public function getYestExpProfit($uid){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $odate = date('Y-m-d',time()-84600);
         $sql = "select sum(profit) FROM $tableName where uid=$uid and odate = '$odate'";
         return $this->executeSql($sql);
     }
     //单个用户当前体验收益
     public function getNowtExpProfit($uid,$ue_id){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $sql = "select sum(profit) FROM $tableName where eid in ($ue_id)";
         return $this->executeSql($sql);
     }

     
     //累计体验金利息
     public function geTotalExpProfit($index,$ue_id){
         $tableName = $this->_table . $index;
         $sql = "select sum(profit) FROM $tableName";
         if(!empty($ue_id)){
             $sql .= " where eid  in ('$ue_id')";
         }
         return $this->executeSql($sql);
     }
     //昨日体验金利息
     public function getTotalYestExpProfit($index){
         $tableName = $this->_table . $index;
         $odate = date('Y-m-d',time()-84600);
         $sql = "select sum(profit) FROM $tableName where odate = '$odate'";
         return $this->executeSql($sql);
     }
   
     
     public function get_all_profit_with_odate($odate){
     	$sum_profit = 0;
     	for($i = 0 ; $i <= 15; $i++){
     		$sql = "SELECT sum(profit) as p_sum FROM " . $this->_table . $i . " WHERE odate = '$odate'";
     		$data = $this->executeSql($sql);
     		$sum_profit += $data[0]['p_sum'];
     	}
     	return $sum_profit;
     }
     
     
     
}