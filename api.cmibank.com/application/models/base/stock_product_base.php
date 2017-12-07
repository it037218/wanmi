<?php

require_once 'basemodel.php'; 

class stock_product_base extends Basemodel{

    public $_table = 'cmibank.cmibank_stock_product';
    
    public function sum_stock_money($cid){
        $sql = 'select sum(stockmoney) as stockmoney from ' . $this->_table . " where `cid` = '$cid' and `status` = 0 ";
        $data = $this->executeSql($sql);
        return $data[0]['stockmoney'];
    }
    
    
}
