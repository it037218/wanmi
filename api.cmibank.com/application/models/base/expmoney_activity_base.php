<?php

require_once 'basemodel.php'; 

class expmoney_activity_base extends Basemodel{

    public $_table = 'cmibank.cmibank_expmoney_activity';
   
    public function getExpmoneyActivityDetail($type){
    	switch ($type){
    		case EXPMONEY_ACTIVITY_REGEDIT:$key= _KEY_REDIS_EXPMONEY_REG_DETAIL_PREFIX_;break;
    	}
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $type) {
            $activityInfo = $self->_get_db_expmoneyActivity_info($type);
            if(empty($activityInfo)) return false;
            return json_encode($activityInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_expmoneyActivity_info($type){
    	$sql = "SELECT * FROM ".$this->_table." where status=2 and type=$type and etime >".NOW." and stime<".NOW;
    	$ret = $this->executeSql($sql);
    	if(!empty($ret)){
    		return $ret;
    	}else{
    		return null;
    	}
    }
}
