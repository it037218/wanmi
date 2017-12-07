<?php

require_once 'basemodel.php'; 

class activity_luckmoney_log_base extends Basemodel{

    private $_table = 'cmibank_activity.cmibank_luckmoney_log';
    
    public function createOrder($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
}
