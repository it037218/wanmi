<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_longmoney_income_log_model extends Basemodel {
      
     private $_table = 'cmibank_log.cmibank_longmoney_income_log';
     private $_longProductContractTable = 'cmibank.cmibank_longproductcontract';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function getLongMoneyIncomeLogList($start){
         $sql ="SELECT income FROM $this->_table where ctime ='$start'";
         $aa = $this->executeSql($sql);
         return $aa[0]['income'];
     }

     public function getLongIncome(){
        $sql ="SELECT income FROM $this->_longProductContractTable LIMIT 1";
        $aa = $this->executeSql($sql);
         return $aa[0]['income'];
     }
}