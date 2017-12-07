<?php

require_once APPPATH. 'models/base/basemodel.php';

class admin_user_expmoney_model extends Basemodel{

    private $_table = 'cmibank.cmibank_user_expmoney_';
    private $table_sql = ' (SELECT * FROM cmibank.cmibank_user_expmoney_0 UNION 
SELECT * FROM cmibank.cmibank_user_expmoney_1 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_2 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_3 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_4 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_5 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_6 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_7 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_8 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_9 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_10 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_11 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_12 UNION
SELECT * FROM cmibank.cmibank_user_expmoney_13 UNION 
SELECT * FROM cmibank.cmibank_user_expmoney_14 UNION 
SELECT * FROM cmibank.cmibank_user_expmoney_15) ';
    
    private $send_key = 'expmoney:send:';

    public function addExpmoney($uid, $data){
    	$table = $this->getTableIndex($uid, $this->_table);
        $insertid = $this->insertDataSql($data, $table);
        if($insertid){
	        $key = _KEY_REDIS_USER_EXPMONEY_PREFIX_ . $uid;
	        self::$container['redis_default']->delete($key);
        }
        return $insertid;
    }
    
    public function updateExpmoney($data, $id, $uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$ret = $this->updateDataSql($table, $data, array('id' => $id));
    	if($ret){
	    	$key = _KEY_REDIS_USER_EXPMONEY_PREFIX_ . $uid;
	    	self::$container['redis_default']->delete($key);
    	}
    	return $ret;
    }
    
    public function get_user_expmoney_list($uid){
    	$key = _KEY_REDIS_USER_EXPMONEY_PREFIX_ . $uid;
    	$expmoneyList = self::$container['redis_default']->setRange($key, 0, -1, 1);
    	if(empty($expmoneyList)){
	    	$data = $this->getUserExpmoneyList($uid);
	    	if($data){
	    		foreach ($data as $value){
	    			self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['etime']);
	    		}
	    		self::$container['redis_default']->expire($key, 86400);
	    	}
	    	return $data;
    	}else{
    		$rtn = array();
    		foreach ($expmoneyList as $key => $value){
    			$rtn[$key] = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    
    //得到所有未过期且未使用的，或者正在使用但未结算的体验金
    public function getUserExpmoneyList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where status<2 and uietime >".NOW." and uid= $uid order by uietime desc";
    	return $this->executeSql($sql);
    }
    
    public function getUserExpmoneyDetail($uid,$id){
    	$table = $this->getTableIndex($uid, $this->_table);
    	return $this->selectDataSql($table, array('id' => $id));
    }
    
    public function getUserExpmoneyByCondition($queryparam,$offset,$psize){
    	if(!empty($queryparam['uid'])){
    		$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    		$sql = "SELECT * FROM ".$table." as c where  c.uid=".$queryparam['uid'];
    	}else{
    		$sql = "SELECT * FROM ".$this->table_sql." as c where  1=1 ";
    	}
    		if($queryparam['status']<4){
    			$sql = $sql." and ";
    			switch ($queryparam['status']){
    				case 0:$sql = $sql." utime=0 and etime>".NOW;break;
    				case 1:$sql = $sql." status=1 ";break;
    				case 2:$sql = $sql." status=2 ";break;
    				case 3:$sql = $sql." utime=0 and etime<".NOW;break;
    			}
    		}
    		if(isset($queryparam['type'])){
    			$sql = $sql." and type=".$queryparam['type'];
    		}
    		
    		if(!empty($queryparam['money'])){
    			$sql = $sql." and money=".$queryparam['money'];
    			 
    		}
    		if(isset($queryparam['days'])){
    			$end = strtotime(date('Y-m-d',time()))+86400*($queryparam['days']+1);
    			$start = strtotime(date('Y-m-d',time()))+86400*$queryparam['days'];
    			$sql = $sql." and etime<".$end." and etime>".$start;
    		
    		}
    		if(!empty($queryparam['stime'])){
    			$stime = strtotime($queryparam['stime']);
    			$sql = $sql." and ctime>".$stime;
    		
    		}
    		if(!empty($queryparam['etime'])){
    			$etime = strtotime($queryparam['etime'])+86400;
    			$sql = $sql." and ctime<".$etime;
    		
    		}
    		$sql = $sql.' order by ctime desc limit '.$offset.','.$psize;
    		$ret = $this->executeSql($sql);
    		return $ret;
    }
    
    public function getTotalNotExpired(){
    	$sql = "SELECT count(*) as count , sum(money) as totalmoney FROM ".$this->table_sql." as c where utime=0 and etime>".NOW;
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotalUsing(){
    	$sql = "SELECT count(*) as count , sum(money) as totalmoney FROM ".$this->table_sql." as c where status=1";
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotalBacked(){
    	$sql = "SELECT count(*) as count , sum(money) as totalmoney FROM ".$this->table_sql." as c where status=2";
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotalExpired(){
    	$sql = "SELECT count(*) as count , sum(money) as totalmoney FROM ".$this->table_sql." as c where etime<".NOW." and utime=0";
    	$ret = $this->executeSql($sql);
    	return $ret[0];
    }
    public function getTotalProfit(){
    	$sql = "SELECT sum(profit) as totalprofit FROM ".$this->table_sql." as c ";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['totalprofit'];
    }
    public function countUserExpmoneyByCondition($queryparam){
    	if(!empty($queryparam['uid'])){
    		$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    		$sql = "SELECT count(id) as count FROM ".$table." as c where  c.uid=".$queryparam['uid'];
    	}else{
    		$sql = "SELECT count(*) as count FROM ".$this->table_sql." as c where  1=1 ";
    	}
    		if($queryparam['status']<4){
    			$sql = $sql." and ";
    			switch ($queryparam['status']){
    				case 0:$sql = $sql." utime=0 and etime>".NOW;break;
    				case 1:$sql = $sql." status=1 ";break;
    				case 2:$sql = $sql." status=2 ";break;
    				case 3:$sql = $sql." utime=0 and etime<".NOW;break;
    			}
    		}
    		if(isset($queryparam['type'])){
    			$sql = $sql." and type=".$queryparam['type'];
    		}
    		
    		if(!empty($queryparam['money'])){
    			$sql = $sql." and money=".$queryparam['money'];
    			 
    		}
    		if(isset($queryparam['days'])){
    			$end = strtotime(date('Y-m-d',time()))+86400*($queryparam['days']+1);
    			$start = strtotime(date('Y-m-d',time()))+86400*$queryparam['days'];
    			$sql = $sql." and etime<".$end." and etime>".$start;
    		
    		}
    		if(!empty($queryparam['stime'])){
    			$stime = strtotime($queryparam['stime']);
    			$sql = $sql." and ctime>".$stime;
    		
    		}
    		if(!empty($queryparam['etime'])){
    			$etime = strtotime($queryparam['etime'])+86400;
    			$sql = $sql." and ctime<".$etime;
    		
    		}
    		$ret = $this->executeSql($sql);
    		return $ret[0]['count'];
    }
    
    public function sumUserExpmoneyProfitByCondition($queryparam){
    	if(!empty($queryparam['uid'])){
    		$table = $this->getTableIndex($queryparam['uid'], $this->_table);
    		$sql = "SELECT sum(profit) as sum_profit FROM ".$table." as c where  c.uid=".$queryparam['uid'];
    	}else{
    		$sql = "SELECT sum(profit) as sum_profit FROM ".$this->table_sql." as c where  1=1 ";
    	}
    	if(isset($queryparam['type'])){
    		$sql = $sql." and type=".$queryparam['type'];
    	}
    	if(!empty($queryparam['money'])){
    		$sql = $sql." and money=".$queryparam['money'];
    
    	}
    	if(isset($queryparam['days'])){
    			$end = strtotime(date('Y-m-d',time()))+86400*($queryparam['days']+1);
    			$start = strtotime(date('Y-m-d',time()))+86400*$queryparam['days'];
    			$sql = $sql." and etime<".$end." and etime>".$start;
    		
    		}
    	if(!empty($queryparam['stime'])){
    		$stime = strtotime($queryparam['stime']);
    		$sql = $sql." and ctime>".$stime;
    
    	}
    	if(!empty($queryparam['etime'])){
    		$etime = strtotime($queryparam['etime'])+86400;
    		$sql = $sql." and ctime<".$etime;
    
    	}
    	$ret = $this->executeSql($sql);
    	return $ret[0]['sum_profit'];
    }
    
    
    public function countUserExpmoneyList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT count(id) as count FROM ".$table." where utime is null and uid=".$uid." and etime >".NOW." order by etime desc";
    	$ret = $this->executeSql($sql);
    	return $ret[0]['count'];
    }
    
    public function incr($id){
    	$key = $this->send_key . $id;
    	$rtn = self::$container['redis_default']->incr($key);
    	self::$container['redis_default']->expire($key , $ttl = 600);
    	return $rtn;
    }
    
    public function send_expmoney_msg($phone, $count,$money){
    	try {
	    	include(APPPATH . 'libraries/submail.lib.php');
	    	$submail = new submail();
	    	$values = array('count' => $count,'money' => $money);
	    	$rtn = $submail->send_msg($phone, $values, 'TA8WE4');
	    	$rtn = json_decode($rtn, true);
	    	if($rtn['status'] == 'error'){
	    		return false;
	    	}
	    	return true;
    		
    	} catch (Exception $e) {
    		return false;
    	}
    }
    
    public function sumExpmoneyMoneyByUid($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "select sum(money) as sum_money from $table where uid=$uid and utime is not null";
    	$ret=$this->executeSql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
    
    public function sumUnusedExpmoney(){
    	$sql = "SELECT sum(money) as totalmoney FROM ".$this->table_sql." as c where utime is null and etime>".NOW;
    	$ret = $this->executeSql($sql);
    	return $ret[0]['totalmoney']?$ret[0]['totalmoney']:0;
    }
}
