<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_up_profit_log_model extends Basemodel {
    
     private $_table = 'cmibank_log.cmibank_up_profit_log_';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function getUpProfitLog($index,$pids){
       $sql = "SELECT sum(profit) FROM $this->_table".$index." where pid in ($pids)";     
       return $this->executeSql($sql);
     }
     public function getUpProfitLogUid($uid){
         $_table_index = $uid % 32;
         $table = $this->_table . $_table_index;
         return $this->selectDataListSql($table, array('uid' => $uid));
     }
     
     public function getUpProfitLogUidList($uid,$where=null,$order_by=null,$limit = NULL){
         $_table_index = $uid % 32;
         $table = $this->_table . $_table_index;
         return $this->selectDataListSql($table,$where,$order_by,$limit);
     }
     //统计 总共累计定期收益
     public function TotalCount_up_profit(){
         $a = array();
         $sum = 0;
         for($i = 0 ; $i <= 31; $i++){
            $a[] =$this->Count_up_profit($i);
         }
         foreach ($a as $val){
           $sum += $val[0]['sum(profit)'];
         }
         return $sum;
     }
     
     public function Count_up_profit($index){
       $sql = "SELECT sum(profit) FROM $this->_table".$index;     
       return $this->executeSql($sql);
       
     }
     //统计总共 昨日定期收益
     public function TotalYest_up_proift(){
         $a = array();
         $sum = 0;
         for($i = 0 ; $i <= 31; $i++){   
            $a[] = $this->Yest_up_proift($i);
         }
         
         foreach ($a as $val){
             $sum += $val[0]['sum(profit)'];
         }
         return $sum;
     }
     public function Yest_up_proift($index){
        $t = date('Y-m-d',time()-84600);
        $sql = "SELECT sum(profit) FROM $this->_table".$index." where odate ='".$t."'";
        return $this->executeSql($sql);  
     }
     
    public function getupprofit($uid,$start,$end,$limit){
        $_table_index = $uid % 32;
        $table = $this->_table . $_table_index;
	    $sql = "SELECT * FROM $table WHERE `odate` BETWEEN '$start' and '$end' and uid = '$uid' order by `pid` desc";
	    if(!empty($limit)){
	        $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
	    }
	    return $this->executeSql($sql);
	} 
	//单个用户累计收益
	public function oneTotal_up_profit($uid){
	    $_table_index = $uid % 32;
	    $table = $this->_table . $_table_index;
	    $sql ="SELECT sum(profit) FROM $table where uid =$uid";
	    return $this->executeSql($sql);
	}
	
	public function get_sum_profit_by_pids($uid, $pids){
		$_table_index = $uid % 32;
	    	$table = $this->_table . $_table_index;
		$sql = "SELECT sum(profit) as sum_profit FROM " . $table . " WHERE `uid` = " . $uid . " and `pid` in (" . implode(',', $pids) . ")";
		$data = $this->executeSql($sql);
		return $data[0]['sum_profit'];
	}

}