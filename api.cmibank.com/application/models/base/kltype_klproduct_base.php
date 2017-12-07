<?php

//活期分类
require_once 'basemodel.php';
class kltype_klproduct_base extends Basemodel {

    private $_table = 'cmibank.cmibank_kltype_klproduct';

    public function __construct() {
        parent::__construct();
    }

     
    public function updateKltypeKlProduct($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }

    public function deleteKltypeKlProduct($ptid, $odate){
        $where = array();
        $where['ptid'] = $ptid;
        $where['odate'] = $odate;
        return $this->deleteDataSql($this->_table, $where);
    }

    public function deleteKltypeKlProductByOdate($odate){
        $where = array();
        $where['odate'] = $odate;
        return $this->deleteDataSql($this->_table, $where);
    }
    
}
