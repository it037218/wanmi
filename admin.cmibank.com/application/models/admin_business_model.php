<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_business_model extends Basemodel {
    
     private $_table = 'cmibank_yunying.cmibank_business';
     
     public function __construct() {
         parent::__construct();
     }
       
     
     public function addBusiness($data){
         return $this->insertDataSql($data, $this->_table);
     }
     
     public function getBusiness($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
}