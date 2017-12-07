<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

//审核
class admin_repayment_auditing_model extends Basemodel {

    private $_table = 'cmibank.cmibank_repayment_auditing';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function addRepayment_auditing($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function getTodayRepayment_auditing_list(){
        $sql = 'select * from ' . $this->_table . " where ctime > " . mktime(0,0,0);
        return $this->executeSql($sql);
    }
    
    public function getRepayment_auditing_buy_orderid($orderid){
        return $this->selectDataSql($this->_table, array('orderid' => $orderid));
    }
    
    public function updateRepayment_auditing_buy_orderid($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }
    
}
