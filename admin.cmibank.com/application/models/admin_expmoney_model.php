<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_expmoney_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_expmoney';
     
     public function __construct() {
         parent::__construct();
     }

     public function getCanExpmoney($uid){
         $sql = "select expmoney FROM $this->_table where uid=$uid";
         return $this->executeSql($sql);
     }
     
     public function getSumExpmoney(){
         $sql = "select sum(expmoney) FROM $this->_table";
         return $this->executeSql($sql);
     }
     
     
}