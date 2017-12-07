<?php

require_once 'basemodel.php'; 

class test_base extends Basemodel{

    
    public function getproductWithCid($cid){
        return $this->selectDataListSql('cmibank.cmibank_product', array('cid' => $cid));
    }
    
    public function getUserProductMoneyByPid($pid, $index){
        $sql = "SELECT sum(money) as cnum FROM cmibank.cmibank_userproduct_".$index." WHERE pid = " . $pid;
        $return = $this->executeSql($sql);
        return $return[0]['cnum'] ? $return[0]['cnum']  : 0;
    }
    
    public function getUserProductByPid($pid, $index){
        $sql = "SELECT * FROM cmibank.cmibank_userproduct_".$index." WHERE pid = " . $pid;
        return $this->executeSql($sql);
    }
    
    public function getSumMoneyUserProductByPid($pid, $index){
        $sql = "SELECT sum(money) as s_money FROM cmibank.cmibank_userproduct_".$index." WHERE pid = " . $pid;
        $data = $this->executeSql($sql);
        return $data[0]['s_money'] ? $data[0]['s_money'] : 0;
    }
    
    public function getUserProductByTableIndex($pid, $status, $index){
        $sql = "SELECT sum(money) as countSellMoney FROM cmibank.cmibank_userproduct_".$index." WHERE pid >= " . $pid . " AND pid < 100288 AND status in(". $status .")";
        $ret = $this->executeSql($sql);
        return $ret[0]['countSellMoney'];
    }
    
    
    public function getSellmoneyProductwithCid($cid, $status){
        $sql = "SELECT sum(sellmoney) as countSellMoney FROM cmibank.cmibank_product WHERE cid = " . $cid . " AND status in (" . $status . ")";
        $ret = $this->executeSql($sql);
        return $ret[0]['countSellMoney'];
    }
    
    public function getAccount($uids){
        $sql = "SELECT `uid`, `plat`, `ctime` from `cmibank`.`cmibank_account` where uid in (" . implode(',', $uids) . ")";
        return $this->executeSql($sql);
    }
    
    public function getProductList(){
        $sql = "SELECT pid, ptid,pname FROM `cmibank`.`cmibank_product` where uistime > '2015-10-01' and uistime <= '2015-10-03' and sellmoney > 0";
        $data = $this->executeSql($sql);
        return $data;
    }
    
    public function getProductListWithOutSeven(){
        $sql = "SELECT pid, ptid,pname FROM `cmibank`.`cmibank_product` where uistime > '2015-10-01' and uistime <= '2015-10-06' and sellmoney > 0 and ptid != 11";
        $data = $this->executeSql($sql);
        return $data;
    }
    
    public function getProductByInfo($pid){
        $sql = "select * from `cmibank`.`cmibank_product_buy_info_" . $pid%16 . "` WHERE pid = " . $pid;
        $data = $this->executeSql($sql);
        return $data;
    }
    
    public function getUserProductMinTime($uid){
        $sql = "select * from `cmibank`.`cmibank_userproduct_" . $uid%16 . "` WHERE uid = " . $uid . ' order by buytime limit 1';
        $data = $this->executeSql($sql);
        return $data;
    }
    
    public function getUserProductMinBuyTime($uid){
        $sql = "select `buytime` from `cmibank`.`cmibank_userproduct_" . $uid%16 . "` WHERE uid = " . $uid . ' order by buytime limit 1';
        $data = $this->executeSql($sql);
        return $data[0]['buytime'];
    }
}
