<?php
require_once APPPATH. 'models/base/basemodel.php';
class admin_luckybag_model extends Basemodel {
	
    private $accepted_table= 'cmibank.cmibank_user_luckybag_accepted';
    private $luckybag_table= 'cmibank.cmibank_luckybag';
    private $user_table= 'cmibank.cmibank_user_luckybag_';
    private $table_sql = ' (SELECT * FROM cmibank.cmibank_user_luckybag_0 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_1 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_2 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_3 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_4 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_5 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_6 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_7 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_8 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_9 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_10 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_11 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_12 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_13 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_14 UNION
SELECT * FROM cmibank.cmibank_user_luckybag_15) ';
    
    public function getLuckyBagByCondition($searchparam,$offset,$psize){
    	if(isset($searchparam['uid'])){
	    	$tableName = $this->getTableIndex($searchparam['uid'],$this->user_table);
	    	$sql = "SELECT * FROM $tableName as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime']." and tt.uid=".$searchparam['uid'];
    	}else{
    		$sql = "SELECT * FROM $this->table_sql as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime'];
    	}
        if(isset($searchparam['status'])){
        	$sql = $sql." and status=".$searchparam['status'];
        }
        if(isset($searchparam['type'])){
        	$sql = $sql." and type=".$searchparam['type'];
        }
    	$sql .= ' order by ctime desc limit '.$offset.','.$psize;
    	return $this->executesql($sql);
    }
    public function countLuckyBagByCondition($searchparam){
    	if(isset($searchparam['uid'])){
	    	$tableName = $this->getTableIndex($searchparam['uid'],$this->user_table);
	    	$sql = "SELECT count(*) as counts FROM $tableName as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime']." and tt.uid=".$searchparam['uid'];
    		
    	}else{
    		$sql = "SELECT count(*) as counts FROM $this->table_sql as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime'];
    	}
        if(isset($searchparam['status'])){
        	$sql = $sql." and status=".$searchparam['status'];
        }
    	$ret = $this->executesql($sql);
    	return $ret[0]['counts']; 
    }
    public function sumLuckyBagByCondition($searchparam){
    	if(isset($searchparam['uid'])){
	    	$tableName = $this->getTableIndex($searchparam['uid'],$this->user_table);
	    	$sql = "SELECT sum(money) as sum_money FROM $tableName as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime']." and tt.uid=".$searchparam['uid'];
    	}else{
    		$sql = "SELECT sum(money) as sum_money FROM $this->table_sql as tt where tt.ctime<".$searchparam['etime']." and tt.ctime>".$searchparam['stime'];
    	}
        if(isset($searchparam['status'])){
        	$sql = $sql." and status=".$searchparam['status'];
        }
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }
    
    public function sumLuckyBagByUid($uid){
    	$sql = "SELECT sum(money) as sum_money FROM $this->accepted_table where uid=$uid or uuid=$uid";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
    
    public function getInvitedUserLuckybagList($uuid){
    	$sql = "SELECT * FROM $this->accepted_table where uuid = $uuid ";
    	return $this->executeSql($sql);
    }
    public function getLuckybagList($where,$order,$limit){
    	return $this->selectDataListSql($this->luckybag_table,$where, $order, $limit);
    }
    public function getLuckybagCount($where){
    	return $this->selectDataCountSql($this->luckybag_table,$where);
    }
    public function updateLuckybagById($id, $data){
    	$ret = self::$container['redis_default']->delete(_KEY_REDIS_LUCKYBAG_BUY_INFO_DETAIL_PREFIX_.$id);
	    return $this->updateDataSql($this->luckybag_table, $data, array('id' => $id));
    }
    public function addLuckybag($data){
    	if(!$this->insertDataSql($data, $this->luckybag_table)){
    		return false;
    	}
    	return true;
    }
    public function getLuckybagById($id){
    	return $this->selectDataListSql($this->luckybag_table, array('id'=>$id));
    }
    
    public function getAvailableLuckybag(){
    	$sql = "SELECT * from $this->luckybag_table where deleted=0";
    	return $this->executeSql($sql);
    }
}