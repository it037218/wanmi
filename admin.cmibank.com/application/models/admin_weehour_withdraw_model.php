<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_weehour_withdraw_model extends Basemodel {
    private $_table= 'cmibank_log.cmibank_weehours_withdraw_log';
    
    private $key = 'yanshi';
    
    public function setYanshi($data){
    	return self::$container['redis_app_w']->save($this->key, json_encode($data), 86400*160);
    }
    
    public function getYanshi(){
    	$rtn = self::$container['redis_app_w']->get($this->key);
    	return $rtn?json_decode($rtn):array();
    }
    
    public function getWeeHourWithDraw($searchparam){
    	$sql = "SELECT * FROM ". $this->_table. " where ctime>".$searchparam['sqstime']." and ctime<".$searchparam['sqetime'];
    	if(isset($searchparam['status'])){
    		$sql = $sql." and shenghe=".$searchparam['status'];
    	}
    	if(isset($searchparam['ckstime'])){ 
    		$sql = $sql." and utime>".$searchparam['ckstime'];
    	}
    	if(isset($searchparam['cketime'])){
    		$sql = $sql." and utime<".$searchparam['cketime'];
    	}
    	$sql .= ' order by ctime desc,id desc ';
    	return $this->executesql($sql);
    }
    
    public function sumWeeHourWithDraw($searchparam){
    	$sql = "SELECT sum(money) as sum_money FROM ". $this->_table. " where ctime>".$searchparam['sqstime']." and ctime<".$searchparam['sqetime'];
    	if(isset($searchparam['status'])){
    		$sql = $sql." and shenghe=".$searchparam['status'];
    	}
    	if(isset($searchparam['ckstime'])){
    		$sql = $sql." and utime>".$searchparam['ckstime'];
    	}
    	if(isset($searchparam['cketime'])){
    		$sql = $sql." and utime<".$searchparam['cketime'];
    	}
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
    
    public function sumToBeWithDraw(){
    	$sql = "SELECT sum(money) as sum_money FROM $this->_table  where shenghe=1 and status=0";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
    public function sumToBeWithDrawJYT(){
    	$sql = "SELECT sum(money) as sum_money FROM $this->_table  where shenghe=1 and status=0 and plat=0";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
    public function sumToBeWithDrawBaofoo(){
    	$sql = "SELECT sum(money) as sum_money FROM $this->_table  where shenghe=1 and status=0 and plat=1";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money']?$ret[0]['sum_money']:0;
    }
    
    public function getWeeHourWithDrawById($id){
        return $this->selectDataSql($this->_table, array('id' => $id));
    }
    
    public function updateShengHeWeeHourWithDrawById($id,$status){
        return $this->updateDataSql($this->_table, array('shenghe' => $status), array('id' => $id));
    }
    
    public function changePlatByid($id,$plat){
    	return $this->updateDataSql($this->_table, array('plat' => $plat), array('id' => $id));
    }
    public function toJYT(){
    	return $this->updateDataSql($this->_table, array('plat' => 0), array('status' => 0));
    }
    public function toBaofoo(){
    	return $this->updateDataSql($this->_table, array('plat' => 1), array('status' => 0));
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
    public function getDefaultWithdraw(){
    	$key = _KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
}