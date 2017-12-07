<?php

//活期分类
require_once 'basemodel.php';
class ltype_longproduct_base extends Basemodel {

    private $_table = 'cmibank.cmibank_ltype_longproduct';

    public function __construct() {
        parent::__construct();
    }

     
    public function updateLtypeLongProduct($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }

    public function deleteLtypeLongProduct($ptid, $odate){
        $where = array();
        $where['ptid'] = $ptid;
        $where['odate'] = $odate;
        return $this->deleteDataSql($this->_table, $where);
    }

    public function deleteLtypeLongProductByOdate($odate){
        $where = array();
        $where['odate'] = $odate;
        return $this->deleteDataSql($this->_table, $where);
    }
    
}
