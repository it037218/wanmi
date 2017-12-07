<?php
/****
 * 代金券
 * **/
require_once APPPATH. 'models/base/basemodel.php';

class admin_jifeng_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_user_jifeng_log_';
    private $duihuan_table = 'cmibank.cmibank_duihuang';
    public function __construct() {
        parent::__construct();
    }
    
    public function getUserJifengByCondition($queryparam,$limit=null){
    	$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    	$sql = "SELECT * FROM  $table  where uid = ".$queryparam['uid'];
    	if(isset($queryparam['type'])){
    		if($queryparam['type']==1){
	    		$sql = $sql." and action in (6,7) ";
    		}else if($queryparam['type']==2){
    			$sql = $sql." and action in (2,3,4) ";
    		}else if($queryparam['type']==3){
    			$sql = $sql." and action=1 ";
    		}else if($queryparam['type']==4){
    			$sql = $sql." and action=51 ";
    		}
    	}
    	if(isset($queryparam['stime'])){
    		$sql = $sql." and ctime>".$queryparam['stime'];
    	}
    	if(isset($queryparam['etime'])){
    		$sql = $sql." and ctime<".$queryparam['etime'];
    	}
    	if(!empty($limit)){
	    	$sql .= ' order by id desc limit '.$limit[0].','.$limit[1];
    	}
    	return $this->executeSql($sql);
    }
    public function getTotalJifeng($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT sum(value) as total FROM  $table  where uid = $uid and action<50";
    	$ret = $this->executeSql($sql);
    	return empty($ret)?0:$ret[0]['total'];
    }
    public function getTotalUsedJifeng($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT sum(value) as total FROM  $table  where uid = $uid and action>50";
    	$ret = $this->executeSql($sql);
    	return empty($ret)?0:$ret[0]['total'];
    }
    
    public function getTotaljine($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT sum(b.money)as totalMoney,sum(b.realmoney) as totalRealMoney FROM $table as a left join $this->$duihuan_table as b  on a.id=b.logid where a.uid = $uid and action=51";
    	return $this->executeSql($sql);
    }
    
    public function addJifeng($uid, $data){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$insertid = $this->insertDataSql($data, $table);
    	if($insertid){
    		self::$container['redis_default']->delete(_KEY_REDIS_USER_JIFENG_LOG_PREFIX_ . $uid);
    	}
    	return $insertid;
    }
}