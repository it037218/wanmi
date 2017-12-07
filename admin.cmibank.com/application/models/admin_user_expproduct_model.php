<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_user_expproduct_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_user_expproduct_';
     
     public function __construct() {
         parent::__construct();
     }

     public function getUserExpProductUid($uid){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $sql = "select id FROM $tableName where uid=$uid and status=0";
         $aa = $this->executeSql($sql);
         if(!empty($aa)){
             foreach ($aa as $val){
                 $bb[] = $val['id'];
             }
             return implode(',', $bb);
         }else{
             return null;
         }
         
     }
     
     public function getUserExpProduct($index){
         $tableName = $this->_table . $index;
         $sql = "select id FROM $tableName where status=0";
         $aa = $this->executeSql($sql);
         if(!empty($aa)){
             foreach ($aa as $val){
                 $bb[] = $val['id'];
             }
             return implode(',', $bb);
         }else{
             return null;
         }   
     }
     public function getExpProductByPid($uid,$id){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         return $this->selectDataSql($tableName, array('id' => $id));
     }
     
     public function countExpmoney($uid){
         $index = $uid%16;
         $tableName = $this->_table . $index;
         $sql = "select sum(money) FROM ".$tableName." where uid = $uid";
         return $this->executeSql($sql);
     }
     
     
     
     
}