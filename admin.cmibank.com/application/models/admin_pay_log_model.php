<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_pay_log_model extends Basemodel {
    //cmibank_pay_log_2015_27
    private $_table= 'cmibank_log.cmibank_pay_log_';
    private $_tabletwo = 'cmibank_log.cmibank_pay_log_2016_';
    private $_tablethree = 'cmibank_log.cmibank_pay_log_2017_';
   
    
//     public function getPayLoglist($where, $order_by = NULL ,$limit = NULL){
//         return $this->selectDataListSql($this->_table,$where,$order_by,$limit);
//     }
    
    public function getPayLog($trxId){
        $tableName = $this->getTablePagLog($trxId,$this->_table);
        return $this->selectDataListSql($tableName,array('trxId' => $trxId));
    }
    
    public function getPayLogbyordid($ordid){
        $tableName = $this->getTablePagLog($ordid,$this->_table);
        return $this->selectDataListSql($tableName,array('ordid' => $ordid));
    }
    public function getPayLoglistlikeUid($uid,$num,$year,$errormsg,$status,$limit = ''){
       $num = str_pad($num,2,"0",STR_PAD_LEFT);
       if($num == 52){
           $year = 2016;
       }
       if($uid =="请输入搜索内容"){
           $uid = '';
       }
       $sql= "SELECT * FROM cmibank_log.cmibank_pay_log_" .$year."_".$num ." where uid like '%$uid%' and errormsg !=''";
       if($errormsg !=false){
           $sql= "SELECT * FROM cmibank_log.cmibank_pay_log_" .$year."_".$num ." where errormsg =''";
       }
       if($status !=false){
           $sql .= ' and status = 1';
       }else{
           $sql .= ' and status in(0,1)';
       }
       if(!empty($limit)){
           $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
       }
       $sql .= ' ORDER BY ctime DESC';
       return $this->executeSql($sql);
    }
    //新加用户充值购买记录M
    public function getuserpaylogbycondition($searchparam,$offset,$psize){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT * FROM ( ".$subSql." ) as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['trxId'])){
    		$sql = $sql." and tt.trxId like '%".$searchparam['trxId']."%'";
    	}
    	if(isset($searchparam['status'])){
    		$sql = $sql." and tt.status=1 ";
    	}else{
    		$sql = $sql." and tt.status=0 ";
    	}
    	if(isset($searchparam['errormsg'])){
    		$sql = $sql." and (tt.errormsg is null or LENGTH(trim(tt.errormsg))=0) ";
    	}else{
    		$sql = $sql." and (tt.errormsg is not null or LENGTH(trim(tt.errormsg))>0) ";
    	}
    	$sql .= ' order by ctime desc limit '.$offset.','.$psize;
    	return $this->executesql($sql);
    }
    
    public function countuserpaylogbycondition($searchparam){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT count(*) as counts FROM ( ".$subSql." ) as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['trxId'])){
    		$sql = $sql." and tt.trxId like '%".$searchparam['trxId']."%'";
    	}
    	if(isset($searchparam['status'])){
    		$sql = $sql." and tt.status=1 ";
    	}else{
    		$sql = $sql." and tt.status=0 ";
    	}
    	if(isset($searchparam['errormsg'])){
    		$sql = $sql." and (tt.errormsg is null or LENGTH(trim(tt.errormsg))=0) ";
    	}else{
    		$sql = $sql." and (tt.errormsg is not null or LENGTH(trim(tt.errormsg))>0) ";
    	}
    	$ret = $this->executesql($sql);
    	return $ret[0]['counts'];
    }
    
    public function sumuserpaylogbycondition($searchparam){
    	$subSql = $this->getSubSql($searchparam);
    	$sql = "SELECT sum(amt) as moneys FROM ( ".$subSql." ) as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime'];
    	if(isset($searchparam['uid'])){
    		$sql = $sql." and tt.uid=".$searchparam['uid'];
    	}
    	if(isset($searchparam['trxId'])){
    		$sql = $sql." and tt.trxId like '%".$searchparam['trxId']."%'";
    	}
    	if(isset($searchparam['status'])){
    		$sql = $sql." and tt.status=1 ";
    	}else{
    		$sql = $sql." and tt.status=0 ";
    	}
    	if(isset($searchparam['errormsg'])){
    		$sql = $sql." and (tt.errormsg is null or LENGTH(trim(tt.errormsg))=0) ";
    	}else{
    		$sql = $sql." and (tt.errormsg is not null or LENGTH(trim(tt.errormsg))>0) ";
    	}
    	$ret = $this->executesql($sql);
    	return $ret[0]['moneys'];
    }
    
    public function getSubSql($searchParam){
    	$sql = "";
    	$fromWeek = 1;
    	$toWeek = 1;
    	$fromYear = '2016';
    	$toYear = '2017';
    	if(!empty($searchParam['stime'])){
    		$fromWeek = date('W',$searchParam['stime']);
    		$fromYear = date('Y',$searchParam['stime']);
    		if($fromWeek==52){
    			$fromYear='2016';
    		}
    	}
    	if(!empty($searchParam['etime'])){
    		$toWeek = date('W',$searchParam['etime']);
    		$toYear = date('Y',$searchParam['etime']);
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
    
    
    public function editPayLogbyordid($ordid, $data){
        $tableName = $this->getTablePagLog(date('Ymd',time()),$this->_table);
        return $this->updateDataSql($tableName , $data, array('ordid'=>$ordid));
    }
    public function getPayLogbyordidthis($ordid){
        $tableName = $this->getTablePagLog(date('Ymd',time()),$this->_table);
        return $this->selectDataListSql($tableName,array('ordid' => $ordid));
    }
    public function addpaylog($data){
        $tableName = $this->getTablePagLog(date('Ymd',time()),$this->_table);
        return $this->insertDataSql($data, $tableName);
    }
    public function delpaylog($ordid){
      $tableName = $this->getTablePagLog($ordid,$this->_table);
      return $this->deleteDataSql($tableName, array('ordid' => $ordid));
    }
}