<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_useridentity_model extends Basemodel {
    
	private $_table_account = 'cmibank.cmibank_account';
    private $_table = 'cmibank.cmibank_user_identity';
     
     public function __construct() {
         parent::__construct();
     }
     
     public function getUserInofManageList(){
         return $this->selectDataListSql($this->_table, null);
     }
     public function addUseridentity($data){
         if(!$this->insertDataSql($data, $this->_table)){
             return false;
         }
         $this->rebuildUseridentityListRedisCache();
         return true;
     }
     public function getUseridentityList($where,$order = null,$limit = null){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     public  function getUseridentityCount(){
         return $this->selectDataCountSql($this->_table);
     }
     public function getUseridentityByUid($uid){
         return $this->selectDataSql($this->_table, array('uid' => $uid));
     }
     public function editUseridentity($uid, $data){
         $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
         self::$container['redis_default']->delete($key);
         return $this->updateDataSql($this->_table, $data, array('uid'=>$uid));
     }
     public function updateUseridentity($uid,$ischeck){
        $key = _KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_ . $uid;
        self::$container['redis_default']->delete($key);
        if(!$this->updateDataSql($this->_table, array('ischeck' =>$ischeck, 'isvalidate' =>$ischeck,  'tpwd' => ''), array('uid' => $uid))){
            return false;
        }
        return true;
     }
     
     public function getAllDealUser(){
     	$sql = "select count(*) as count from " . $this->_table . " where isnew=0 or h_isnew=0 ";
     	$data = $this->executeSql($sql);
     	return $data[0]['count'];
     }
 	//用户充值购买记录添加M
     public function getUidByPhone($phone){
     	$sql = "select uid from " . $this->_table . " where phone=".$phone;
     	$data = $this->executeSql($sql);
     	return $data;
     }
     
     public function getUseridentityListByLike($searchParam, $offset,$psize){
     	$sql="select acc.uid,acc.account,acc.ctime,acc.ltime,acc.plat,acc.forbidden,iden.bankcode,iden.phone,iden.realname,iden.idCard,iden.cardno,iden.isnew,iden.ischeck,iden.fengkong,iden.ctime as bankatime from ";
     	if($searchParam['type'] == 1){
     		$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.account like '%".$searchParam['searchtitle']."%' ";
     	}else if($searchParam['type'] == 2){
     		$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where iden.realname like '%".$searchParam['searchtitle']."%' ";
     	}else if($searchParam['type'] == 3){
     		$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.uid=".$searchParam['searchtitle'];
     	}else if($searchParam['type'] == 4){
     		$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where iden.phone like '%".$searchParam['searchtitle']."%' ";
     	}else if($searchParam['type'] == 5){
     		$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where iden.idCard like '%".$searchParam['searchtitle']."%' ";
     		}
     	if(!empty($searchParam['bangka'])){
     		if($searchParam['bangka']==1){
     			$sql = $sql." and iden.idCard is not null ";
     		}else if($searchParam['bangka']==2){
     			$sql = $sql." and iden.idCard is null ";
     		}
     	}
     	$sql = $sql.' order by acc.ctime desc limit '.$offset.','.$psize;
     	return $this->executesql($sql);
     }
     
     public function countUseridentityListByLike($searchParam){
     	$sql="select count(*) as count from";
     	if($searchParam['type'] == 1){
     		$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.account like '%".$searchParam['searchtitle']."%' ";
     	}else if($searchParam['type'] == 2){
     		$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where iden.realname like '%".$searchParam['searchtitle']."%' ";
     	}else if($searchParam['type'] == 3){
     		$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.uid=".$searchParam['searchtitle'];
     	}else if($searchParam['type'] == 4){
     		$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where iden.phone like '%".$searchParam['searchtitle']."%' ";
     	}else if($searchParam['type'] == 5){
     		$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where iden.idCard like '%".$searchParam['searchtitle']."%' ";
     		}
     	if(!empty($searchParam['bangka'])){
     		if($searchParam['bangka']==1){
     			$sql = $sql." and iden.idCard is not null ";
     		}else if($searchParam['bangka']==2){
     			$sql = $sql." and iden.idCard is null ";
     		}
     	}	
     	$ret =$this->executesql($sql);
     	return $ret[0]['count'];
     }
     
     public function getUserBuyListByTyppeLike($searchParam, $offset,$psize){
     	$sql="select DISTINCT buylog.*,acc.account,iden.realname,iden.idCard from ";
     	if($searchParam['type'] == 1){//定期
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='p' and acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='p' where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 2){//活期
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='lp' and  acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='lp' where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 3){//总
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}
     	$sql = $sql.' order by buylog.ctime desc ';
     	return $this->executesql($sql);
     }
     
     public function countUserBuyListByTyppeLike($searchParam){
     	$sql="select count(*) from ";
     	if($searchParam['type'] == 1){//定期
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='p' and acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='p' where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 2){//活期
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='lp' and  acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ptype='lp' where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 3){//总
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}
     	return $this->executesql($sql);
     }
     
     public function getUseridentityListByTyppeLike($searchParam){
     	$sql="select DISTINCT  acc.uid,acc.account,acc.ctime,acc.ltime,acc.plat,acc.forbidden,iden.bankcode,iden.phone,iden.realname,iden.idCard,iden.cardno,iden.isnew,iden.ischeck,iden.requestid from ";
     	if($searchParam['type'] == 1){//注册
     		if(isset($searchParam['plat'])){
	     		$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.plat='".$searchParam['plat']."' and acc.ctime>=".$searchParam['stime']." and acc.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.ctime>=".$searchParam['stime']." and acc.ctime<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 2){//绑卡
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where acc.plat='".$searchParam['plat']."' and SUBSTRING(iden.requestid,1,10)>=".$searchParam['stime']." and SUBSTRING(iden.requestid,1,10)<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where SUBSTRING(iden.requestid,1,10)>=".$searchParam['stime']." and SUBSTRING(iden.requestid,1,10)<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 3){//购买
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
	     		$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where  acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}
     	$sql = $sql.' order by acc.ctime desc';
     	return $this->executesql($sql);
     }
     
     public function countUseridentityListByTyppeLike($searchParam){
     	$sql="select count(*) as count from ";
     	if($searchParam['type'] == 1){
     		if(isset($searchParam['plat'])){
	     		$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.plat='".$searchParam['plat']."' and acc.ctime>=".$searchParam['stime']." and acc.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $this->_table_account acc left join $this->_table iden on acc.uid=iden.uid  where acc.ctime>=".$searchParam['stime']." and acc.ctime<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 2){
     		if(isset($searchParam['plat'])){
     			$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where acc.plat='".$searchParam['plat']."' and SUBSTRING(iden.requestid,1,10)>=".$searchParam['stime']." and SUBSTRING(iden.requestid,1,10)<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $this->_table iden left join $this->_table_account acc on acc.uid=iden.uid  where SUBSTRING(iden.requestid,1,10)>=".$searchParam['stime']." and SUBSTRING(iden.requestid,1,10)<".$searchParam['etime'];
     		}
     	}else if($searchParam['type'] == 3){//购买
     		$buy_table = "cmibank_log.cmibank_buy_log_".$searchParam['table_index'];
     		if(isset($searchParam['plat'])){
	     		$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid where acc.plat='".$searchParam['plat']."' and buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}else{
     			$sql = $sql." $buy_table buylog left join  $this->_table_account acc on buylog.uid=acc.uid left join $this->_table iden on acc.uid=iden.uid  where buylog.ctime>=".$searchParam['stime']." and buylog.ctime<".$searchParam['etime'];
     		}
     	}
     	$ret =$this->executesql($sql);
     	return $ret[0]['count'];
     }
     
     public function rebuildUseridentityListRedisCache() {
         return true;
     }
     
     public function send_msg($phone, $code,$msg_tpl_num){
         include(APPPATH . 'libraries/submail.lib.php');
         $submail = new submail();
         $rtn = $submail->send_msg($phone,$code,$msg_tpl_num);
         return $rtn;    
     }
     //生成一个随机数
     function random($length, $numeric = 0) {
         PHP_VERSION < '4.2.0' ? mt_srand ( ( double ) microtime () * 1000000 ) : mt_srand ();
         $seed = base_convert ( md5 ( print_r ( $_SERVER, 1 ) . microtime () ), 16, $numeric ? 10 : 35 );
         $seed = '012340567890';    
         $hash = '';
         $max = strlen ( $seed ) - 1;
         for($i = 0; $i < $length; $i ++) {
             $hash .= $seed [mt_rand ( 0, $max )];
         }
         return $hash;
     }
     public function searchbk($uids){
         $sql = "SELECT uid FROM $this->_table where uid in($uids)";
         $aa = $this->executeSql($sql);
         $bb = array();
         foreach ($aa as $key=>$val){
            $index = $val['uid']%16;
            $bb[$index][] = $val['uid'];
         }
         return $bb; 
   
     }
     public function bangkashu($uids){
         $sql = "SELECT uid FROM $this->_table where uid in($uids)";
         $aa = $this->executeSql($sql);
         $bb = count($aa);
         return $bb;
     }
     
     private $key = 'cd:';
     public function restWithDraw($uid){
         $key = $this->key . $uid;
         $data = self::$container['redis_app_r']->get($key);
         $data = json_decode($data, true);
        
         $rdata['longmoneyToBalance'] = $data['longmoneyToBalance'];
         $rdata['withDraw'] = 3;
         $rdata['free_withDraw'] = 1;
         $rdata['pay'] = $data['pay'];
         $rdata['t'] = $data['t'];
         self::$container['redis_app_w']->save($key, json_encode($rdata), 86400);
         return true;
     }
     
     public function send_fengkong_msg($phone){
     	try {
     		include(APPPATH . 'libraries/submail.lib.php');
     		$submail = new submail();
     		$rtn = $submail->send_msg($phone, '','WF9vZ2');
     		$rtn = json_decode($rtn, true);
     		if($rtn['status'] == 'error'){
     			return false;
     		}
     		return true;
     
     	} catch (Exception $e) {
     		return false;
     	}
     }
     
}