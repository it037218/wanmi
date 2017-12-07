<?php
require_once 'basemodel.php';
class ptype_product_base extends Basemodel {

    private $_table = 'cmibank.cmibank_ptype_product';
    
    public function __construct() {
        parent::__construct();
    }

   
    public function updatePtypeProduct($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }
    
    public function deletePtypeProduct($ptid, $odate){
        $where = array();
        $where['ptid'] = $ptid;
        $where['odate'] = $odate;
        return $this->deleteDataSql($this->_table, $where);
    }
    
    public function getPtypeProductByPtid($ptid, $odate){
        $where = array('ptid' => $ptid, 'odate' => $odate);
        return $this->selectDataListSql($this->_table, $where, null, array(1000,0));
    }
    
}
