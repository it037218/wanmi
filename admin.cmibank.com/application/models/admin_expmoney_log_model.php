<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_expmoney_log_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_expmoney_log_';
     
     public function __construct() {
         parent::__construct();
     }
     //获取时间段的体验金
     public function getexpmoney_log_list($index,$where,$order,$limit=''){
         $tableName ="cmibank.cmibank_expmoney_log_$index";
         $sql = "SELECT SUM(money) FROM $tableName where action = 1 and ctime BETWEEN  '1448899200' and '1450886399'";
         $aa = $this->executeSql($sql);
         return $aa[0]['SUM(money)'];
     }
     //获取时间段内人数
     public function getexpmoney_log_num($index,$where,$order,$limit=''){
         $tableName ="cmibank.cmibank_expmoney_log_$index";
         $sql = "SELECT count(distinct(uid)) FROM $tableName where action = 1 and ctime BETWEEN  '1448899200' and '1450886399'";
         $aa = $this->executeSql($sql);
         return $aa[0]['count(distinct(uid))'];
     }
     
     
     public function getExpmoneLogyByUid($where,$order,$limit){
         $index = $where['uid']%16;
         $tableName = $this->_table . $index;
         return $this->selectDataListSql($tableName,$where,$order,$limit);
     }

     //此方法目前不用
     public function countExpmoney($uid){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $sql = "select sum(money) FROM ".$tableName." where action=1";
         if(!empty($uid)){
             $sql .= " and uid=$uid";
         }
         return $this->executeSql($sql);
     }
     
     
     
}