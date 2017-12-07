<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_longproduct_buy_info_model extends Basemodel {

    private $_table = 'cmibank.cmibank_longproduct_buy_info_';
    
    public function __construct() {
        parent::__construct();
    }

    public function getLongProductBuyInfoByPid($where, $order, $limit=null){
        $tableName = $this->getTableIndex($where['pid'], $this->_table);
        return $this->selectDataListSql($tableName, $where, $order, $limit);
    }
    
    public function sumLongProductByIos($index,$uids){
        $sql = "SELECT sum(money) FROM `cmibank_longproduct_buy_info_$index`";
        return $this->executeSql($sql);
    }
    

    
}
