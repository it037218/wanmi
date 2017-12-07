<?php

require_once 'basemodel.php';
class download_base extends Basemodel {

    private $_table = 'cmibank.cmibank_download';
    
    public function __construct() {
        parent::__construct();
    }

    public function addDownload($data){
    	if(!$this->insertDataSql($data, $this->_table)){
    		return false;
    	}
    	return true;
    }
    
}
