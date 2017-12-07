<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_emailmanage_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_emailmanage';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function addemail($data){
         return $this->insertDataSql($data, $this->_table);
     }
     
     public function getEmailmanageList($where,$order,$limit){
         return $this->selectDataListSql($this->_table, $where,$order,$limit);
     }
     
     public function getEmailmanageBycorid($corid){
         return $this->selectDataSql($this->_table, array('corid' => $corid));
     }
     public function delEmailmanageBycorid($corid){
         return $this->deleteDataSql($this->_table, array('corid' => $corid));
     }
     public function updatEmailmanage($corid, $data){
         return $this->updateDataSql($this->_table, $data, array('corid' => $corid));
     }
}