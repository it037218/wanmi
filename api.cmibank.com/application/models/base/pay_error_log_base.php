<?php

require_once 'basemodel.php'; 

class pay_error_log_base extends Basemodel{

    private $_table = 'cmibank_yunying.cmibank_pay_error_log';
 
    
    public function createOrder($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
}