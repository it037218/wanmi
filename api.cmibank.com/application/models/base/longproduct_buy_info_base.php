<?php

require_once 'basemodel.php'; 

class longproduct_buy_info_base extends Basemodel{

    private $_table = 'cmibank.cmibank_longproduct_buy_info_';
    
    public function CountLongProductBuyMoney($pid){
        $_table_index = $pid % 16;
        $table = $this->_table . $_table_index;
        $result = $this->DBR->select('sum(money) as money')->from($table)->where('pid', $pid)->get()->row_array();
        if($result['money']){
            return $result['money'];
        }else{
            return 0;
        }
    }
    
    public function getBuyUserByPid($pid){
        $_table_index = $pid % 16;
        $table = $this->_table . $_table_index;
        $data['ctime'] = NOW;
        return $this->selectDataListSql($table, array('pid' => $pid));
    }
    
    public function addLongProductBuyInfo($pid, $data){
        $_table_index = $pid % 16;
        $table = $this->_table . $_table_index;
        $data['ctime'] = NOW;
        return $this->insertDataSql($data, $table);
    }
    
    
    
}
