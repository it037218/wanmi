<?php

require_once 'basemodel.php'; 

class user_jifeng_duihuan_base extends Basemodel{

    private $_table = 'cmibank.cmibank_duihuang';

    public function addDuihuang($uid, $data){
        $insertid = $this->insertDataSql($data, $this->_table);
        if($insertid){
        	$key = _KEY_REDIS_SYSTEM_TOTAL_DUIHUAN_LIST_PREFIX_ . $uid;
        	self::$container['redis_default']->delete($key);
        }
        return $insertid;
    }
    
    
    public function useDuihuan($id){
    	$this->updateDataSql($this->_table, array('status' => 1), array('wid' => $id));
    }
    
    public function get_user_duihuan_list($uid,$page){
    	$key = _KEY_REDIS_SYSTEM_TOTAL_DUIHUAN_LIST_PREFIX_ . $uid;
    	$psize = 20;
    	$offset = ($page-1)*$psize;
    	$max = $page*$psize;
    	$jifengList = self::$container['redis_default']->setRange($key, $offset,$max,1);
    	if(empty($jifengList)){
    		$data = $this->getUserDuihuanList($uid,array($psize, $offset));
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
    public function getUserDuihuanList($uid,$limit){
    	$sql = "SELECT * FROM $this->_table where  uid= $uid  order by ctime desc limit $limit[1],$limit[0]";
    	return $this->executeSql($sql);
    }
    
}
