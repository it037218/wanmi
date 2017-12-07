<?php

require_once 'basemodel.php';
class goods_base extends Basemodel {

    private $_table = 'cmibank.cmibank_goods';
    
    public function __construct() {
        parent::__construct();
    }

    public function getGoodsByCid($id){
        $key = _KEY_REDIS_SYSTEM_GOODS_DETAIL_ . $id;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $id) {
            $goodsInfo = $self->_get_db_goods_detail($id);
            if(empty($goodsInfo)) return false;
            return json_encode($goodsInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_goods_detail($id){
      return $this->DBR->select('*')->from($this->_table)->where('id',$id)->get()->row_array();
    }
    
    public function getOnlineGoodsList($type){//0:虚拟，1：实物
    	$key = _KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.$type;
    	$jifengList = self::$container['redis_default']->setRange($key, 0,-1);
    	if(empty($jifengList)){
    		$data = $this->getDBOnlineGoodsList($type);
    		if($data){
    			foreach ($data as $value){
    				self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['rank']);
    			}
    			self::$container['redis_default']->expire($key, 86400);
    		}
    		$jifengList = self::$container['redis_default']->setRange($key, 0,-1);
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
    public function getDBOnlineGoodsList($type){
    	$sql = "SELECT * FROM $this->_table where  status=1 and deleted=0 and stock>sold ";
    	if($type==1){
    		$sql = $sql." and type=4";
    	}else{
    		$sql = $sql." and type<4";
    	}
    	return $this->executeSql($sql);
    }
    
    public function incrSold($id,$count){//添加销售
    	$key =  _KEY_REDIS_SYSTEM_TOTAL_GOODS_SOLD_COUNT_PREFIX_.$id;
    	$rtn = self::$container['redis_default']->incrWithValue($key,$count);
    	return $rtn;
    }
    public function addLock($id){
    	return self::$container['redis_default']->incr(_KEY_REDIS_SYSTEM_TOTAL_GOODS_SOLD_LOCK_PREFIX_.$id);
    	self::$container['redis_default']->expire(_KEY_REDIS_SYSTEM_TOTAL_GOODS_SOLD_LOCK_PREFIX_.$id, 5);
    }
    public function releaseLock($id){
    	return self::$container['redis_default']->delete(_KEY_REDIS_SYSTEM_TOTAL_GOODS_SOLD_LOCK_PREFIX_.$id);
    }
    
    public function updateGoodsById($id, $data){
    	$ret = $this->updateDataSql($this->_table, $data, array('id' => $id));
    	if($ret){
    		self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_GOODS_DETAIL_.$id);
    		self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.'1');
    		self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.'0');
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public function addSold($id, $count){
        if($count < 0){
            die('error balance');
        }
        $sql = "UPDATE " . $this->_table . " SET `sold` = `sold` + " . $count . " WHERE `id` = " . $id;
        $this->DBW->query($sql);
        if($this->DBW->affected_rows() >= 1){
        	self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_GOODS_DETAIL_.$id);
        	self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.'1');
	    	self::$container['redis_app_w']->delete(_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_.'0');
            return true;
        }else{
            return false;
        }
    }
}
