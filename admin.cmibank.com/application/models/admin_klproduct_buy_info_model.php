<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_klproduct_buy_info_model extends Basemodel {

    private $_table = 'cmibank.cmibank_klproduct_buy_info_';
    
    public function __construct() {
        parent::__construct();
    }

    public function getKlProductBuyInfoByPid($where, $order, $limit=null){
        $tableName = $this->getTableIndex($where['pid'], $this->_table);
        return $this->selectDataListSql($tableName, $where, $order, $limit);
    }
    
    public function sumKlProductByIos($index,$uids){
        $sql = "SELECT sum(money) FROM `cmibank_klproduct_buy_info_$index`";
        return $this->executeSql($sql);
    }
    

    
}
