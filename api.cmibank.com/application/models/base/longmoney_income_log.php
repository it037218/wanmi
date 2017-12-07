<?php

require_once 'basemodel.php'; 
//活期每日年化利率记录
class longmoney_income_log extends Basemodel{

    private $_table = 'cmibank_log.cmibank_longmoney_income_log';
    
    private $_longProductContractTable = 'cmibank.cmibank_longproductcontract';
    
    public function add($income, $ctime){
        return $this->insertDataSql(array('income' => $income, 'ctime' => $ctime), $this->_table);
    }
    
    public function get_longmoney_income_log($days){
        $endtime = mktime(0,0,0) - $days * 86400;
        $sql = "SELECT * FROM " . $this->_table . " WHERE `ctime` >" . $endtime;
        return $this->executeSql($sql);
    }
    
    public function getLongmoneyIncomeLogCache($days){
        $key = _KEY_REDIS_LONGMONEY_INCOME_LOG_PREFIX_ . date('Y-m-d') . '_' . $days ;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $days) {
            $longmoney_income = $self->get_longmoney_income_log($days);
            if(empty($longmoney_income)) return false;
            return json_encode($longmoney_income);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        self::$container['redis_default']->expire($key, 86400);
        return json_decode($return , true);
    }
    
    public function _getDbIncome(){
        $sql = "SELECT * FROM " . $this->_table;
        $data = $this->executeSql($sql);
        $return_data = array();
        foreach ($data as $_d){
            $return_data[$_d['ctime']] = $_d['income'];
        }
        return $return_data;
    }
    
    public function getALLLongMoneyIncomeLog($data = ''){
        $odata = empty($data) ? date('Y-m-d') : $data;
        $key = _KEY_REDIS_LONGMONEY_INCOME_ALL_LOG_PREFIX_ . $odata;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self) {
            $longmoney_income = $self->_getDbIncome();
            if(empty($longmoney_income)) return false;
            return json_encode($longmoney_income);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        self::$container['redis_default']->expire($key, 86400);
        return json_decode($return , true);
    }
    
    public function getLongIncome(){
        $sql ="SELECT income FROM $this->_longProductContractTable LIMIT 1";
        $aa = $this->executeSql($sql);
        return $aa[0]['income'];
    }
}
