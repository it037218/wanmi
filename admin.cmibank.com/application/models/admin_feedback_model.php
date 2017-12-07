<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_feedback_model extends Basemodel {
    
     private $_table = 'cmibank_log.cmibank_feedback';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function getFeedbackList($where,$order,$limit=null){
         return $this->selectDataListSql($this->_table, $where,$order,$limit);
     }
     
     public function updateFeedback($id, $data){
         return $this->updateDataSql($this->_table, $data, array('id' => $id));
     }
}