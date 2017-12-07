<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_product_buy_model extends Basemodel {

    private $_table = 'cmibank.cmibank_product_buy_info_';
    
    public function __construct() {
        parent::__construct();
    }

    
    public function getProductBuyInfoByPid($where, $order = null, $limit = null){
        $tableName = $this->getTableIndex($where['pid'], $this->_table);
        return $this->selectDataListSql($tableName, $where, $order, $limit);
    }
    
    public function updateProductBuyInfoByPid($pid, $data, $where){
        $tableName = $this->getTableIndex($pid, $this->_table);
        return $this->updateDataSql($tableName, $data, $where);
    }
    
    
}
