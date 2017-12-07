<?php
require_once 'basemodel.php';
class equalptype_equalproduct extends Basemodel {

    private $_table = 'cmibank.cmibank_equalptype_equalproduct';
    
    public function __construct() {
        parent::__construct();
    }

   
    public function updateEqualPtypeEqualProduct($data, $where){
        return $this->updateDataSql($this->_table, $data, $where);
    }
    
    public function deleteEqualPtypeEqualProduct($ptid, $odate){
        $where = array();
        $where['ptid'] = $ptid;
        $where['odate'] = $odate;
        return $this->deleteDataSql($this->_table, $where);
    }
    
    public function getEqualPtypeEqualProductByPtid($ptid, $odate){
        $where = array('ptid' => $ptid, 'odate' => $odate);
        return $this->selectDataListSql($this->_table, $where, null, array(1000,0));
    }
    
    public function getminrindex($ptid, $odate){
        $odate = $odate ? $odate : date("Y-m-d");
        $sql = "select min(rindex) as minrindex from " . $this->_table . " where odate = '$odate'  and stype = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
}
