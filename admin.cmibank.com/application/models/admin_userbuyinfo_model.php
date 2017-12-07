<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_userbuyinfo_model extends Basemodel {

    private $_table = 'cmibank.won_product_buy_info_';
    
    private $_accountTable = 'cmibank.won_account';
    
    private $_productTable = 'cmibank.won_product';
    
    private $_identityTable = 'cmibank.won_user_identity';
    
    private $_typeTable = 'cmibank.won_ptype';


    public function __construct() {
        parent::__construct();
    }
    
    public function getBuyInfoProductListByUid($uid,$searchParams,$offset,$psize) {
        $buyInfoTable = $this->getTableIndex($uid, $this->_table);
        return $this->getBuyInfoProductListByTable($searchParams, $buyInfoTable,$offset,$psize);
    }
    
    public function getBuyInfoProductListAll($searchParams,$offset,$psize) {
        $list = array();
        for ($i = 0; $i<16;$i++) {
            $list = array_merge($list,$this->getBuyInfoProductListByTable($searchParams, $this->_table.$i,$offset,$psize));
        }
        return $list;
    }
    
    public function getBuyInfoProductListByTable($searchParams,$buyInfoTable,$offset,$psize,$count = false) {
        if($count === false){
            $filed = "p.*,pbi.*,a.*,i.*,pt.name AS ptname, pbi.money AS buyamount,pbi.ctime AS buytime,p.pname AS productname";
        } else {
            $filed = "count(*) AS total";
        }
        
        $sql = "SELECT $filed "
                . "FROM $buyInfoTable AS pbi "
                . "LEFT JOIN $this->_productTable AS p ON pbi.pid=p.pid "
                . "LEFT JOIN $this->_accountTable AS a ON pbi.uid=a.uid "
                . "LEFT JOIN $this->_typeTable AS pt ON p.ptid=pt.ptid "
                . "LEFT JOIN $this->_identityTable AS i ON pbi.uid=i.uid";
        
        $where = array();
        if(isset($searchParams['ptid'])){
            $where[] = "pt.ptid='".$searchParams['ptid']."'";
        }
        if(isset($searchParams['timestart'])){
            $where[] = 'pbi.ctime>='.$searchParams['timestart'];
        }
        if(isset($searchParams['timeend'])){
            $where[] = 'pbi.ctime<='.$searchParams['timeend'];
        }
        if(isset($searchParams['amountmin'])){
            $where[] = 'pbi.money>='.$searchParams['amountmin'];
        }
        if(isset($searchParams['amountmax'])){
            $where[] = 'pbi.money<='.$searchParams['amountmax'];
        }
        if(isset($searchParams['uid'])){
            $where[] = 'pbi.uid='.$searchParams['uid'];
        }
        $whereStr = '';
        if($where){
            $whereStr = ' WHERE '.implode(' AND ', $where);
        }
        $sql .= $whereStr;
        
        if($count === false){
            $sql .= " LIMIT $offset, $psize";
        }
        return $this->executeSql($sql);
    }
    
    public function getCount($uid,$searchParams) {
        if($uid){
            $buyInfoTable = $this->getTableIndex($uid, $this->_table);
            return $this->getBuyInfoProductListByTable($searchParams, $buyInfoTable,0,0,true);
        } else {
            $i = 0;
            $b = 0;
            for ($i = 0; $i<16;$i++) {
                $result = $this->getBuyInfoProductListByTable($searchParams, $this->_table.$i,0,0,true);
                $i += intval($result[0]['total']);
                $b = max($b,intval($result[0]['total']));
            }
            return array($i,$b);
        }
    }
}