<?php
require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';

class admin_backcontract_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_backcontract';
     
     public function addbackcontract($data){
         return $this->insertDataSql($data, $this->_table);
     }
     
     public function editbackcontract($bid, $data){
         return $this->updateDataSql($this->_table, $data, array('bid'=>$bid));
     }
     
     public function getbackcontractList($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     
     public  function getbackcontractCount(){
         return $this->selectDataCountSql($this->_table);
     }
     
     public function getbackcontractByBid($bid){
         return $this->selectDataSql($this->_table, array('bid' => $bid));
     }
     
     public function delbackcontract($bid){
         return $this->deleteDataSql($this->_table, array('bid' => $bid));
     }
     
     
     
    
}


