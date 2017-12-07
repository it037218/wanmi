<?php

require_once 'basemodel.php'; 

class user_jifeng_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_jifeng_log_';

    public function addJifeng($uid, $data){
    	$table = $this->getTableIndex($uid, $this->_table);
        $insertid = $this->insertDataSql($data, $table);
        if($insertid){
        	self::$container['redis_default']->delete(_KEY_REDIS_USER_JIFENG_LOG_PREFIX_ . $uid);
        	self::$container['redis_default']->delete(_KEY_REDIS_USER_QIANDAO_LIST_PREFIX_ . $uid);
        	self::$container['redis_default']->delete(_KEY_REDIS_USER_QIANDAO_MONTH_PREFIX_. $uid);
        }
        return $insertid;
    }
    
    public function incr($uid){//连续次数,明天晚上0点过期
    	$key =  _KEY_REDIS_USER_QIANDAO_LIANXU_COUNT_PREFIX_.$uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	$time = strtotime(date('Y-m-d',time()))+172800-time();
    	self::$container['redis_app_w']->expire($key ,$time);
    	return $rtn;
    }
    public function getCount($uid){
    	$key =  _KEY_REDIS_USER_QIANDAO_LIANXU_COUNT_PREFIX_.$uid;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    public function incrDay($uid){//当日次数，今天晚上0点过期
    	$key =  _KEY_REDIS_USER_QIANDAO_SINGLE_COUNT_PREFIX_.$uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	$time = strtotime(date('Y-m-d',time()))+86400-time();
    	self::$container['redis_app_w']->expire($key , $time);
    	return $rtn;
    }
    
    public function getDay($uid){
    	$key =  _KEY_REDIS_USER_QIANDAO_SINGLE_COUNT_PREFIX_.$uid;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    public function incrMonth($uid){//当月次数
    	$key =  _KEY_REDIS_USER_QIANDAO_MONTH_COUNT_PREFIX_.date('Ym').':'.$uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	return $rtn;
    }
    public function getMonth($uid){
    	$key =  _KEY_REDIS_USER_QIANDAO_MONTH_COUNT_PREFIX_.date('Ym').':'.$uid;
    	$rtn = self::$container['redis_app_w']->get($key);
    	return $rtn;
    }
    
    
    public function get_user_jifeng_list($uid,$page){
    	$key = _KEY_REDIS_USER_JIFENG_LOG_PREFIX_ . $uid;
    	$psize = 20;
    	$offset = ($page-1)*$psize;
    	$max = $page*$psize;
    	$jifengList = self::$container['redis_default']->setRange($key, $offset,$max,1);
    	if(empty($jifengList)){
    		$data = $this->getUserJifengList($uid,array($psize, $offset));
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['ctime']);
    			}
    			self::$container['redis_default']->expire($key, 86400);
    		}
    		$jifengList = self::$container['redis_default']->setRange($key, $offset,$max,1);
    	}
    	
    	if(empty($jifengList)){
    		return null;
    	}else{
    		$rtn = array();
    		foreach ($jifengList as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    
    
    public function getUserJifengList($uid,$limit){
    	$table = $this->getTableIndex($uid, $this->_table);
    	return $this->selectDataListSql($table, array('uid'=>$uid), 'id desc', $limit, '');
    }
    
    public function get_user_qiandao_list($uid,$page){
    	$key = _KEY_REDIS_USER_QIANDAO_LIST_PREFIX_ . $uid;
    	$psize = 20;
    	$offset = ($page-1)*$psize;
    	$max = $page*$psize;
    	$jifengList = self::$container['redis_default']->setRange($key, $offset,$max,1);
    	if(empty($jifengList)){
    		$data = $this->getUserQiandaoList($uid,array($psize, $offset));
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['ctime']);
    			}
    			self::$container['redis_default']->expire($key, 86400);
    		}
    		$jifengList = self::$container['redis_default']->setRange($key, $offset,$max,1);
    	}
    	 
    	if(empty($jifengList)){
    		return null;
    	}else{
    		$rtn = array();
    		foreach ($jifengList as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    public function getUserQiandaoList($uid,$limit){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where  uid= $uid and action in (6,7) order by ctime desc limit $limit[1],$limit[0]";
    	return $this->executeSql($sql);
    }
    
    public function get_user_qiandao_list_by_month($uid,$stime,$etime){
    	$key = _KEY_REDIS_USER_QIANDAO_MONTH_PREFIX_ . $uid;
    	$jifengList = self::$container['redis_default']->setRangeBySorce($key, $stime,$etime);
    	if(empty($jifengList)){
    		$data = $this->getUserQiandaoListbyMonth($uid);
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['ctime']);
    			}
    			self::$container['redis_default']->expire($key, 86400);
    		}
    		$jifengList = self::$container['redis_default']->setRangeBySorce($key, $stime,$etime);
    	}
    
    	if(empty($jifengList)){
    		return null;
    	}else{
    		$rtn = array();
    		foreach ($jifengList as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    		}
    		return $rtn;
    	}
    }
    public function getUserQiandaoListbyMonth($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where  uid= $uid and action=6 limit 500";
    	return $this->executeSql($sql);
    }
    
    
    public function sumUserQiandao($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT sum(value) as total FROM ".$table." where  uid= $uid and action in (6,7)";
    	$ret = $this->executeSql($sql);
    	return empty($ret)?0:$ret[0]['total'];
    }
    
    public function set_total_Qiandao($uid, $value){
    	$key = _KEY_REDIS_SYSTEM_TOTAL_JIFENG_PREFIX_ ;
    	return self::$container['redis_default']->setScore($key, $uid, $value);
    }
    
    public function get_total_Qiandao($uid){
    	$key = _KEY_REDIS_SYSTEM_TOTAL_JIFENG_PREFIX_ ;
    	return self::$container['redis_default']->setScore($key, $uid);
    }
}
