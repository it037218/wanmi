<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_repayment_model extends Basemodel {

    private $_table = 'cmibank.cmibank_repayment_log';
    
    public function __construct() {
        parent::__construct();
    }

    
    public function addRepaymentLog($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function updateOrderStatus($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }
    
    
    
}
