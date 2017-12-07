<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_luckmoney_log_model extends Basemodel {
    
     private $_table = 'cmibank_activity.cmibank_luckmoney_log';
     
     public function __construct() {
         parent::__construct();
     }
     
    
     public function getLuckmoneyLogList($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     public  function getluckmoneyLogCount(){
         return $this->selectDataCountSql($this->_table);
     }
     
     
}