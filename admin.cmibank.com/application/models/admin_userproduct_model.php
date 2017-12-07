<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_userproduct_model extends Basemodel {
    
    private $_table = 'cmibank.cmibank_userproduct_';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getProductPid($_table_index){
        $table = $this->_table . $_table_index;
        $sql = "SELECT pid FROM ".$table." WHERE `status` = 0";
        $aa = $this->executeSql($sql);
        $bb = array();
        foreach ($aa as $val){
            $bb[] = $val['pid'];
        }
        return implode(",",$bb);
    }
    
    public function getUserProductByPid($_table_index, $pids){
        $table = $this->_table . $_table_index;
        $data = $this->selectDataListSql($table, array('pid' => $pids));
        return $data;
    }
    
    public function getUserFirstProductByUid($uid){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        $sql = "SELECT * FROM $table where uid = $uid order by buytime limit 1";
        return $this->executeSql($sql);
    }
    
    public function getUserProductInUid($index,$uids,$odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT * FROM $this->_table$index where uid in ($uids) and buytime >= $start_time  AND buytime <  $end_time";
        return $this->executeSql($sql);
    }
    //获取新注册用户数目
    public function getNewUserNumber($index,$uids,$odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT distinct(uid) FROM $this->_table$index where uid in ($uids) and buytime >= $start_time  AND buytime <  $end_time";
        $aa = $this->executeSql($sql);
        if(!empty($aa)){
            return $aa;
        }
    }
    
    public function getUserProductlistByUid($uid,$where=null,$order_by=null,$limit = NULL){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        return $this->selectDataListSql($table,$where,$order_by,$limit);
    }
     
    public function getUserProductCountByUid($uid,$where){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        return $this->selectDataCountSql($table,$where);
    }    
    
    public function updateUserProductBuyUid($uid, $data, $where){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        return $this->updateDataSql($table, $data, $where);
    }
    
    public function getUserProductTrxIdByUid($uid,$where,$limit = ''){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        $sql = "SELECT * FROM $table WHERE `trxId` like '%".$where ."%' order by `uid` desc";
        if(!empty($limit)){
           $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
        
    }
    public function getProfitDetailCache($uid, $start, $end, $withScore=FALSE){
        $key = _KEY_REDIS_USER_PROFIT_PREFIX_ . $uid . '_' . date('Y-m-d');
        if($withScore == true){
            return self::$container['redis_app_r']->setRevRangeBySorce($key,$start,$end,1);
        }
        return self::$container['redis_app_r']->setRange($key,$start,$end,1);
    }
    //已经投资定期总金额
    public function getSumProductMoney($_table_index){
        $table = $this->_table . $_table_index;
        $sql = "SELECT SUM(money) FROM ".$table." WHERE `status` = 0";
        return $this->executeSql($sql);
    }
    
    //已经投资定期金额
    public function money($uid,$limit=''){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        $sql = "SELECT SUM(money) FROM ".$table." WHERE `uid` =  ".$uid." and `status` = 0";
        if(!empty($limit)){
           $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    //已经回款
    public function repaymoney($uid,$limit=''){
        $_table_index = $uid % 16;
        $table = $this->_table . $_table_index;
        $sql = "SELECT SUM(money) FROM ".$table." WHERE `uid` =  ".$uid." and `status` = 1";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    

    public function getPidsByStatus($uid, $status){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = 'select `pid` from ' . $table . ' where uid = ' . $uid . ' and status = ' . $status;
    	$data = $this->executeSql($sql);
    	return $data;
    }
    
    public function get_finished_product_profit($uid){
    	$pid_arr = $this->getPidsByStatus($uid, 1);
    	if(empty($pid_arr)){
    		return 0;
    	}
    	$pids = array();
    	foreach ($pid_arr as $_pid){
    		$pids[] = $_pid['pid'];
    	}
    	$this->load->model('admin_up_profit_log_model', 'up_profit_log');
    	$sum_profit = $this->up_profit_log->get_sum_profit_by_pids($uid, $pids);
    	return $sum_profit;
    }
    public function getAllMoney($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT SUM(money) as totalmoney,count(*) as totalcount  FROM ".$table." WHERE `uid` =  ".$uid;
    	$ret =  $this->executeSql($sql);
    	return $ret;
    }
}