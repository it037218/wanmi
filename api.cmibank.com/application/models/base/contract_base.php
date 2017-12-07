<?php

require_once 'basemodel.php';
class contract_base extends Basemodel {

    private $_table = 'cmibank.cmibank_contract';
    private $stock_table = 'cmibank.cmibank_stock_product';
    private $cmibank_creditor_information = 'cmibank.cmibank_creditor_information';
    
    public function __construct() {
        parent::__construct();
    }

    public function getContractByCid($cid){
        $key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_ . $cid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $cid) {
            $contractInfo = $self->_get_db_contract_detail($cid);
            if(empty($contractInfo)) return false;
            return json_encode($contractInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_contract_detail($cid){
       $sql = "SELECT * from $this->_table as a left JOIN $this->cmibank_creditor_information as b on a.creid=b.id where a.cid=".$cid;
        $ret=  $this->executeSql($sql);
        return $ret[0]?$ret[0]:false;
    }
    
    public function backMoneytoContract($cid, $sellmoney){
        $sql = "UPDATE " . $this->_table . " SET  `money` = " . $sellmoney . " WHERE `cid` = " . $cid;
        $ret = $this->executeSql($sql);
        if($ret){
            $key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_ . $cid;
            self::$container['redis_app_w']->delete($key);
        }else{
            return false;
        }
        return true;
    }
    
    public function getContractByRepaymentDate($date){
    	$d= date('Y-m-d', strtotime('-1 day',strtotime($date))) ;;
    	$sql = "select con.*,stock.stockmoney,stock.ctime as stocktime,stock.status as stockstatus  from $this->_table as con left join $this->stock_table as stock on con.cid=stock.cid where con.repaymenttime='$d'";
    	return $this->executeSql($sql);
    }
}
