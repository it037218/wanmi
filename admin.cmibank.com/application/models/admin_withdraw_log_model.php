<?php
require_once APPPATH. 'models/base/basemodel.php';
class admin_withdraw_log_model extends Basemodel {
    private $_table= 'cmibank_log.cmibank_withdraw_log_';
    private $_tabletwo = 'cmibank_log.cmibank_withdraw_log_2016_';
    private $_tablethree = 'cmibank_log.cmibank_withdraw_log_2017_';
    
    public function getWithdrawTable($orderid){
    	if(strlen($orderid)>30){
	    	$trxId = substr( $orderid, 12, 8);
	    	$year = substr( $orderid, 12, 4);
	    	$num =date('W',strtotime($trxId));
	    	return $this->_table .$year.'_'.$num;
    	}else{
    		$trxId = substr( $orderid, 2, 8);
    		$year = substr( $orderid, 2, 4);
    		$num =date('W',strtotime($trxId));
    		return $this->_table .$year.'_'.$num;
    	}
    }
    public function getWithDrawlog($orderid){  //根据用户订单
        $tableName = $this->getWithdrawTable($orderid);
        return $this->selectDataListSql($tableName,array('orderid' => $orderid));
    }
    public function getWithDrawlogbyordid($ybdrawflowid){  //平台流水号
        $tableName = $this->getTablePagLog($ybdrawflowid,$this->_table);
        return $this->selectDataListSql($tableName,array('ybdrawflowid' => $ybdrawflowid));
    }
    public function getWithDrawloglistlikeUid($uid, $num,$year){
		$num = str_pad($num,2,"0",STR_PAD_LEFT);
       	if($num == 23){
       		$year = 2015;
      	}                                                     
      	$sql= "SELECT id,orderid,realname,status_code,status,logid,money,succtime FROM cmibank_log.cmibank_withdraw_log_".$year."_".$num."  where uid like '%$uid%' ";
       	$sql .= ' ORDER BY succtime DESC';
       	return $this->executeSql($sql);
    }
    public function getWithDrawlogtime( $succtime ,$star,$end,$limit=''){
        $num = str_pad($num,2,"0",STR_PAD_LEFT);
        if($num == 23){
          $year = 2015;
        }
     $sql = "SELECT id,orderid,realname,status_code,status,logid,money FROM cmibank_log.cmibank_withdraw_log_".$year."_".$num."  where succtime like '%$succtime%' and $ssucctime BETWEEN '$esucctime' and  '$ssucctime' order by succtime asc";
     $sql .= ' ORDER BY succtime DESC';
    return $this->executeSql($sql);
    }
    public function getuserwithdrawlogcondition($searchparam,$offset,$psize){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT * FROM ( ".$subSql." ) as tt where tt.succtime<".$searchparam['esucctime']." and tt.succtime>".$searchparam['ssucctime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
        }
        if(isset($searchparam['failed'])){
        	$sql = $sql." and status<2 or tt.succtime=0";
        }else{
        	$sql = $sql." and status=2";
        }
        if(isset($searchparam['orderid'])){
        	$sql = $sql." and tt.orderid like '%".$searchparam['orderid']."%'";
        }
    	$sql .= ' order by succtime desc,id desc limit '.$offset.','.$psize;
    	return $this->executesql($sql);
    }
    public function countuserwithdrawlogbycondition($searchparam){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT count(*) as counts FROM ( ".$subSql." ) as tt where tt.succtime<".$searchparam['esucctime']." and tt.succtime>".$searchparam['ssucctime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['failed'])){
    		$sql = $sql." and status<2 or tt.succtime=0";
    	}else{
        	$sql = $sql." and status=2";
        }
    	$ret = $this->executesql($sql);
    	return $ret[0]['counts']; 
    }
    public function sumuserwithdrawlogbycondition($searchparam){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT sum(money) as sum_money FROM ( ".$subSql." ) as tt where tt.succtime<".$searchparam['esucctime']." and tt.succtime>".$searchparam['ssucctime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['failed'])){
        	$sql = $sql." and status<2 or tt.succtime=0";
        }else{
        	$sql = $sql." and status=2";
        }
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }
    public function sumuserwithdrawlogbyconditionJYT($searchparam){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT sum(money) as sum_money FROM ( ".$subSql." ) as tt where tt.orderid like '2900601000%' and  tt.succtime<".$searchparam['esucctime']." and tt.succtime>".$searchparam['ssucctime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['failed'])){
    		$sql = $sql." and status<2 or tt.succtime=0";
    	}else{
    		$sql = $sql." and status=2";
    	}
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }
    public function sumuserwithdrawlogbyconditionBaofoo($searchparam){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT sum(money) as sum_money FROM ( ".$subSql." ) as tt where tt.orderid like 'bf%' and  tt.succtime<".$searchparam['esucctime']." and tt.succtime>".$searchparam['ssucctime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['failed'])){
    		$sql = $sql." and status<2 or tt.succtime=0";
    	}else{
    		$sql = $sql." and status=2";
    	}
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }
    public function getSubSql($searchParam){
    	$sql = "";
    	$fromWeek = 1;
    	$fromYear = '2016';
    	$toWeek = 1;
    	$toYear = '2017';
    	if(!empty($searchParam['ssucctime'])){
    		$fromWeek = date('W',$searchParam['ssucctime']);
    		$fromYear = date('Y',$searchParam['ssucctime']);
    		if($fromWeek==52){
    			$fromYear='2016';
    		}
    	}
    	if(!empty($searchParam['esucctime'])){
    		$toWeek = date('W',$searchParam['esucctime']);
    		$toYear = date('Y',$searchParam['esucctime']);
    	}else{
    		$toWeek=date('W',NOW);
    	}
    	if($fromYear==$toYear){
	    	for($index=$fromWeek;$index<=$toWeek;$index++){
	    		$temp = str_pad($index, 2, "0", STR_PAD_LEFT);  
	    		if($fromYear=='2016'){
		    		$sql = $sql."SELECT * FROM $this->_tabletwo".$temp." UNION ";
	    		}else{
	    			$sql = $sql."SELECT * FROM $this->_tablethree".$temp." UNION ";
	    		}
	    	} 
    	}else{
    		for($index=$fromWeek;$index<=52;$index++){
    			$sql = $sql."SELECT * FROM $this->_tabletwo".$index." UNION ";
    		}
    		for($index=1;$index<=$toWeek;$index++){
    			$temp = str_pad($index, 2, "0", STR_PAD_LEFT);
	    		$sql = $sql."SELECT * FROM $this->_tablethree".$temp." UNION ";
	    	} 
    	}
   	    $sql = substr($sql,0,-6);
    	return $sql;
    }          
    public function editWithDrawlogbyordid($orderid, $data){
        $tableName = $this->getTablePagLog(date('Ymd',time()),$this->_table);
        return $this->updateDataSql($tableName , $data, array('orderid'=>$orderid));
    }
    public function getWithDrawlogbyordidthis($orderid){
        $tableName = $this->getTablePagLog(date('Ymd',time()),$this->_table);
        return $this->selectDataListSql($tableName,array('orderid' => $orderid));
    }
    public function addwithdrawlog($data){
        $tableName = $this->getTablePagLog(date('Ymd',time()),$this->_table);
        return $this->insertDataSql($data, $tableName);
    }
    public function delwithdrawlog($orderid){
      $tableName = $this->getTablePagLog($orderid,$this->_table);
      return $this->deleteDataSql($tableName, array('orderid' => $orderid));
    }
    
    public function updateDrawLog($data, $orderid, $where){
    	$tableName = $this->getWithdrawTable($orderid);
    	return $this->updateDataSql($tableName, $data, $where);
    }
    
    public function stopWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_RESTRICT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	return $rtn;
    }
    
    public function startWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_RESTRICT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->delete($key);
    	return $rtn;
    }
    public function getWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_RESTRICT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    public function getDefaultWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    public function withdrawToBaofoo(){
    	$key = _KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	return $rtn;
    }
    
    public function withdrawToJYT(){
    	$key = _KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->delete($key);
    	return $rtn;
    }
}