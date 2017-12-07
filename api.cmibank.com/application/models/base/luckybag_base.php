<?php

require_once 'basemodel.php'; 

class luckybag_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_luckybag_';
    private $lucky_expire_key = 'lucky:notice:';
    private $luckybag_table= 'cmibank.cmibank_luckybag';
    
    public function getExpKey($uid){
    	$key = $this->lucky_expire_key . $uid;
    	return self::$container['redis_default']->get($key);
    }
    
    public function setExpKey($uid){
    	$key = $this->lucky_expire_key . $uid;
    	$rtn = self::$container['redis_app_w']->incr($key);
    	self::$container['redis_app_w']->expire($key , $ttl = 86400*3);
    	return $rtn;
    }
    
	public function add($uid,$data){
    	$table = $this->getTableIndex($uid, $this->_table);
        $insertid = $this->insertDataSql($data, $table);
        $listkey = _KEY_REDIS_USER_LUCKYBAG_LIST_PREFIX_ . $uid;
        self::$container['redis_default']->delete($listkey);
        return $insertid;
    }
    
    public function get_user_luckybag_list($uid){
    	$key = _KEY_REDIS_USER_LUCKYBAG_LIST_PREFIX_ . $uid;
    	$min = NOW;
    	$max = NOW+86400*30;
    	$list = self::$container['redis_default']->setRevRangeBySorce($key, $min,$max);
    	$etime = strtotime(date('Y-m-d',time()))+259199;
    	$flag = $this->getExpKey($uid);
    	if(empty($list)){
    		$data = $this->getUserLuckybagList($uid);
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['etime']);
    				if(empty($flag) && $value['etime']>NOW && $value['etime']<=$etime && $value['status']==0){
    					$this->load->model('base/user_notice_base', 'user_notice_base');
    					$days = floor(($value['etime']-NOW)/86400);
    					$temp_days = '今天';
    					if($days>=1){
    						$temp_days = $days.'天后';
    					}
    					$notice_data = array(
    							'uid' => $uid,
    							'title' => '现金红包即将过期提醒',
    							'content' => "您有一个".$value['money']."元现金红包将于".$temp_days."过期，赶快去使用吧！",
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
    		foreach ($list as $key => $value){
    			$rtn[$key] = $temp = json_decode($value, true);
    			if(empty($flag) && $temp['etime']>NOW && $temp['etime']<=$etime && $temp['status']==0){
    				$this->load->model('base/user_notice_base', 'user_notice_base');
    				$days = floor(($temp['etime']-NOW)/86400);
    				$temp_days = '今天';
    				if($days>=1){
    					$temp_days = $days.'天后';
    				}
    				$notice_data = array(
    						'uid' => $uid,
    						'title' => '现金红包即将过期提醒',
    						'content' => "您有一个".$temp['money']."元现金红包将于".$temp_days."过期，赶快去使用吧！",
    						'ctime' => NOW
    				);
    				$this->user_notice_base->addNotice($uid,$notice_data);
    				$this->setExpKey($uid);
    			}
    		}
    		return $rtn;
    	}
    }
    
    public function getUserLuckybagList($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT * FROM ".$table." where status in (0,1) and etime >".NOW." and uid= $uid order by ctime desc";
    	return $this->executeSql($sql);
    }
    
    public function getLuckybagDetailByid($uid,$id){
        $key = _KEY_REDIS_LUCKYBAG_DETAIL_PREFIX_ . $id;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self, $uid,$id) {
            $luckybag = $self->get_detail_db($uid,$id);
            if(empty($luckybag)) return false;
            return json_encode($luckybag);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function get_detail_db($uid,$id){
    	$table = $this->getTableIndex($uid, $this->_table);
    	return $this->selectDataSql($table, array('id' => $id));
    }
    
    public function update_luckybag_db_detail($uid,$data,$updatedata, $id){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$ret = $this->updateDataSql($table, $updatedata, array('id' => $id));
    	if($ret){
    		$key = _KEY_REDIS_LUCKYBAG_DETAIL_PREFIX_ . $id;
    		self::$container['redis_default']->delete($key);
    		$listkey = _KEY_REDIS_USER_LUCKYBAG_LIST_PREFIX_ . $uid;
        	self::$container['redis_default']->delete($listkey);
    		if($updatedata['status']==1){
    			$cacheKey = _KEY_REDIS_LUCKYBAG_CACHE_DETAIL_PREFIX_ .$data['uuaccount'];
    			$ttl = $data['etime']-NOW;
    			self::$container['redis_default']->save($cacheKey,json_encode($data),$ttl);
    		}else{
    			$cacheKey = _KEY_REDIS_LUCKYBAG_CACHE_DETAIL_PREFIX_ .$data['uuaccount'];
    			self::$container['redis_default']->delete($cacheKey);
    			
    			$akey_uid = _KEY_REDIS_USER_LUCKYBAG_ACCEPTED_LIST_PREFIX_.$uid;
    			self::$container['redis_default']->delete($akey_uid);
    			$akey_uuid = _KEY_REDIS_USER_LUCKYBAG_ACCEPTED_LIST_PREFIX_.$data['uuid'];
    			self::$container['redis_default']->delete($akey_uuid);
    		}
    	}
    	return $ret;
    }
    
    public function setNoticed($uid,$id){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$ret = $this->updateDataSql($table, array('noticed' => 1), array('id' => $id));
    	if($ret){
    		$key = _KEY_REDIS_LUCKYBAG_DETAIL_PREFIX_ . $id;
    		self::$container['redis_default']->delete($key);
    		$listkey = _KEY_REDIS_USER_LUCKYBAG_LIST_PREFIX_ . $uid;
    		self::$container['redis_default']->delete($listkey);
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public function get_cached_luckybag($uuaccount){
    	$cacheKey = _KEY_REDIS_LUCKYBAG_CACHE_DETAIL_PREFIX_ .$uuaccount;
    	$ret = self::$container['redis_default']->get($cacheKey);
    	return empty($ret)?false:json_decode($ret , true);
    }
    
    public function incr($id){
    	$key = _KEY_REDIS_LUCKYBAG_COUNT_PREFIX_ . $id;
    	$rtn = self::$container['redis_default']->incr($key);
    	self::$container['redis_default']->expire($key,1800);
    	return $rtn;
    }
    
    public function getLuckyForNotice($tableIndex,$offset,$psize){
    	$table = $this->_table.$tableIndex;
    	$_etime = strtotime(date('Y-m-d',time()))+172799;//明天晚上过期
    	$sql = "SELECT * FROM ".$table." where status in (0,1) and etime = $_etime order by ctime desc limit $offset, $psize ";
    	return $this->executeSql($sql);
    }
    
    public function getLuckyActivity(){
    	$activity = self::$container['redis_default']->get(_KEY_REDIS_LUCKYBAG_BUY_INFO_PREFIX_);
    	return empty($activity)?false:json_decode($activity , true);
    }
    public function getLuckyDetail($id){
    	$key = _KEY_REDIS_LUCKYBAG_BUY_INFO_DETAIL_PREFIX_ . $id;
    	$self = $this;
    	$return = $this->remember($key, 0 , function() use($self,$id) {
    		$luckybag = $self->get_buyinfo_detail_db($id);
    		if(empty($luckybag)) return false;
    		return json_encode($luckybag);
    	} , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
    		return json_decode($return , true);
    }
    
    public function get_buyinfo_detail_db($id){
    	return $this->selectDataSql($this->luckybag_table, array('id' => $id));
    }
}
