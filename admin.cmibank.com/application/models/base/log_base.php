<?php

class log_base extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
    }
    
	public function back_contract_log($data){
	    $msg = date("Y-m-d H:i:s") . '####' . json_encode($data);
	    if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
	        $logFile = './back_contract.log'.date("Y-m-d");
	    }else{
	        $logFile = '/tmp/back_contract.log';
	    }
	    $fp = fopen($logFile, 'a');
	    $isNewFile = !file_exists($logFile);
	    if (flock($fp, LOCK_EX)) {
	        if ($isNewFile) {
	            chmod($logFile, 0666);
	        }
	        fwrite($fp, $msg . "\n");
	        flock($fp, LOCK_UN);
	    }
	    fclose($fp);
	}
    
}


   
