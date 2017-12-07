<?php
require_once 'basemodel.php';
class qs_log_base extends Basemodel {

    private $_table = 'cmibank_yunying.cmibank_qs_log';
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
    }
    
	public function add($data){
	    return $this->insertDataSql($data, $this->_table);
	}
    
	public function update($data, $where){
	    $ret =  $this->updateDataSql($this->_table, $data, $where, TRUE);
	    exit;
	    return $ret;
	}
	
}


   
