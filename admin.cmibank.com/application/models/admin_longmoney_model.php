<?php
/****
 * 活期
 * **/
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_longmoney_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_longmoney';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getLongMoneyList(){
        return $this->selectDataListSql($this->_table, null);
    }
    
    public function getLongMoneyByUid($uid){
        return $this->selectDataListSql($this->_table, array('uid'=>$uid));
    }
    
    public function getUserLongMoney($uid){
    	$where = array('uid' => $uid);
    	$res = $this->selectDataSql($this->_table, $where);
    	if($res){
    		return $res['money'];
    	}
    	return 0;
    }
    public function getSumLongMoney(){
        $sql = "SELECT sum(money) FROM $this->_table";
        return $this->executeSql($sql);
    }
}