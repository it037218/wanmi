<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_userlongproduct_model extends Basemodel {
    
    private $_table = 'cmibank.cmibank_userlongproduct_';
    
    public function __construct() {
        parent::__construct();
    }
    public function getUserLongProductInUid($index,$uids,$odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT * FROM $this->_table$index where uid in ($uids) and buytime >= $start_time  AND buytime <  $end_time";
        return $this->executeSql($sql);
    }
    //获取新注册用户数目
    public function getNewLongUserNumber($index,$uids,$odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT distinct(uid) FROM $this->_table$index where uid in ($uids) and buytime >= $start_time  AND buytime <  $end_time";
        $aa = $this->executeSql($sql);
        if(!empty($aa)){
            return $aa;
        }
    }
    public function getUserLongProductlistByUid($uid,$where=null,$order_by=null,$limit = NULL){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        return $this->selectDataListSql($table,$where,$order_by,$limit);
    }
}