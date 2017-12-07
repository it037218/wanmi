<?php

require_once 'basemodel.php'; 

class user_expmoney_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_expmoney_';
    private $identity = 'cmibank.cmibank_user_identity';

    private $exp_expire_key = 'exp:notice:';
    
    private $table_sql = ' (SELECT * FROM cmibank.cmibank_user_expmoney_0 where status=2 UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_1 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_2 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_3 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_4 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_5 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_6 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_7 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_8 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_9 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_10 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_11 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_12 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_13 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_14 where status=2  UNION
							SELECT * FROM cmibank.cmibank_user_expmoney_15 where status=2 ) ';

    public function getExpKey($uid){
    	$key = $this->exp_expire_key . $uid;
    	return self::$container['redis_default']->get($key);
    }
    
    public function setExpKey($uid){
    	$key = $this->exp_expire_key . $uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400*3);
    	return $rtn;
    }
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
    	$min = NOW;
    	$max = NOW+86400*100;
    	$expmoneyList = self::$container['redis_default']->setRangeBySorce($key, $min,$max);
    	$flag = $this->getExpKey($uid);
    	$etime = strtotime(date('Y-m-d',time()))+259199;
    	if(empty($expmoneyList)){
	    	$data = $this->getUserExpmoneyList($uid);
	    	if($data){
	    		foreach ($data as $value){
	    			self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['uietime']);
	    			if(empty($flag) && $value['etime']<=$etime && $value['etime']>NOW && empty($value['utime'])){
	    				$this->load->model('base/user_notice_base', 'user_notice_base');
	    				$days = floor(($value['etime']-NOW)/86400);
	    				$temp_days = '今天';
	    				if($days>=1){
	    					$temp_days = $days.'天后';
	    				}
	    				$notice_data = array(
	    						'uid' => $uid,
	    						'title' => '体验金即将过期提醒',
	    						'content' => "您有".$value['money']."元体验金将于".$temp_days."过期，赶快去使用吧！",
	    						'ctime' => NOW
	    				);
	    				$this->user_notice_base->addNotice($uid,$notice_data);
	    				$this->setExpKey($uid);
	    			}
	    		}
	    		self::$container['redis_default']->expire($key, 86400);
	    	}
	    	return $data;
    	}else{
    		$rtn = array();
    		foreach ($expmoneyList as $key => $value){
    			$rtn[$key]  = $temp= json_decode($value, true);
    			if(empty($flag)&&$temp['etime']<=$etime && $temp['etime']>NOW && empty($temp['utime'])){
    				$this->load->model('base/user_notice_base', 'user_notice_base');
    				$days = floor(($temp['etime']-NOW)/86400);
    				$temp_days = '今天';
    				if($days>=1){
    					$temp_days = $days.'天后';
    				}
    				$notice_data = array(
    						'uid' => $uid,
    						'title' => '体验金即将过期提醒',
    						'content' => "您有".$temp['money']."元体验金将于".$temp_days."过期，赶快去使用吧！",
    						'ctime' => NOW
    				);
    				$this->user_notice_base->addNotice($uid,$notice_data);
    				$this->setExpKey($uid);
    			}
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
    
    public function getUsingExpmoneyList($tableIndex,$offset,$psize){
    	$table = $this->_table.$tableIndex;
    	$uietime = mktime(0,0,0);
    	$sql = "SELECT * FROM ".$table." where status=1 and uietime > $uietime order by id asc limit $offset,$psize";
    	return $this->executeSql($sql);
    }
    
    public function getUserAllExpmoneyList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where uid= $uid";
    	return $this->executeSql($sql);
    }
    public function sumUserExpProfitByDate($odate){
    	$time = strtotime(date('Y-m-d'));
    	$end_time = $time + 86399;
    	$sql = "SELECT sum(profit) as total_profit FROM ".$this->table_sql." as c where uietime=".$end_time;
    	$data = $this->executeSql($sql);
    	return $data[0]['total_profit'];
    }

    /**
     * 查询身份信息
     * @param $uid
     * @return mixed
     */
    public function getAccount($uid){
        return $this->selectDataSql($this->identity, array('uid' => $uid));
    }

}
