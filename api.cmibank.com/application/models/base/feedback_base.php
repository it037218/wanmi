<?php

//用户反馈
require_once 'basemodel.php'; 

class feedback_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_feedback';
    

    public function add($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    
}
