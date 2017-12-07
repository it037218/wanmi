<?php
require_once APPPATH. 'models/base/basemodel.php';
class qs_log extends Basemodel {

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
	public function getHistoryStockMoney(){
		$sql = "SELECT SUM(lp_buy)-SUM(ltob)-sum(invite_reward)-SUM(l_profit) as moneySum  FROM $this->_table ";
		$data = $this->executeSql($sql);
		return $data[0]['moneySum'];
	}
}


   
