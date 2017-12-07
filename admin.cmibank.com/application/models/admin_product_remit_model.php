<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

//定期还款任据
class admin_product_remit_model extends Basemodel {

    private $_table = 'cmibank.cmibank_product_remit';
    
    private $_product_arr = array();
    
    public function __construct() {
        parent::__construct();
    }

    public function getProductRemitList($where,$order,$limit=null){
        return $this->selectDataListSql($this->_table, $where,$order,$limit);
    }
    
    public function getAllReimit(){
        $sql = "SELECT * FROM " . $this->_table ;
        return $this->executeSql($sql);
        
    }
    
    public function getProductRemitByPid($pid){
        return $this->selectDataSql($this->_table, array('pid' => $pid));
    }
    
    public function getProductRemitByRid($rid){
        return $this->selectDataSql($this->_table, array('rid' => $rid));
    }
    
    public function updatePorductRemit($rid, $data){
        return $this->updateDataSql($this->_table, $data, array('rid' => $rid));
    }
    
    public function addPorductRemit($data){
        return $this->insertDataSql($data, $this->_table);
    }
	
    public function getctimebycid($cid){
    	$sql = "select remit.ctime as ctime from cmibank.cmibank_contract con left join cmibank.cmibank_product pro on con.cid=pro.cid left join $this->_table remit on pro.pid=remit.pid where pro.sellmoney>0 and con.cid=$cid";
    	return $this->executeSql($sql);
    }
	
}
