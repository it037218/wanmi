<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_duihuan_model extends Basemodel{
    
    private $_table = 'cmibank.cmibank_duihuang';
    public function __construct() {
        parent::__construct();
    }
    
    public function getUserJifengByCondition($searchparam,$limit=null,$orderby=' ctime desc '){
    	$sql = "SELECT * FROM  $this->_table  where 1=1  ";
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['type'])){
	    	$sql = $sql." and type=".$searchparam['type'];
    	}
    	if(isset($searchparam['stime'])){
    		$sql = $sql." and ctime>".$searchparam['stime'];
    	}
    	if(isset($searchparam['etime'])){
    		$sql = $sql." and ctime<".$searchparam['etime'];
    	}
    	if(isset($searchparam['status'])){
    		$sql = $sql." and status=".$searchparam['status'];
    	}
    	$sql .= " order by $orderby ";
    	if(!empty($limit)){
	    	$sql .= '  limit '.$limit[0].','.$limit[1];
    	}
    	return $this->executeSql($sql);
    }
    public function countUserJifengByCondition($searchparam){
    	$sql = "SELECT count(*) as count FROM  $this->_table  where 1=1  ";
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['type'])){
    		$sql = $sql." and type=".$searchparam['type'];
    	}
    	if(isset($searchparam['stime'])){
    		$sql = $sql." and ctime>".$searchparam['stime'];
    	}
    	if(isset($searchparam['etime'])){
    		$sql = $sql." and ctime<".$searchparam['etime'];
    	}
    	if(isset($searchparam['status'])){
    		$sql = $sql." and status=".$searchparam['status'];
    	}
    	$ret = $this->executeSql($sql);
    	return empty($ret)?0:$ret[0]['count'];
    }
    public function getDuihuanByid($id){
    	$sql = "SELECT * FROM $this->_table where  wid=$id";
    	return $this->executeSql($sql);
    }
    public function updateDuihuanById($id, $data){
    	return $this->updateDataSql($this->_table, $data, array('wid' => $id));
    }
    
    public function sumDuihuanByUid($uid){
    	$sql = "SELECT sum(realmoney) as sum_money FROM $this->_table where uid=$uid and status=1 and type=4";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
}