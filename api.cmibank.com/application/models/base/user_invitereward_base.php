<?php

require_once 'basemodel.php'; 

class user_invitereward_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_invitereward_';
    
    public function _db_get_invitereward($uid){
        $tableName = $this->getTableIndex($uid, $this->_table);
        $sql = "select buytime,rewardmoney,account from $tableName where uid=$uid";
        return $this->executeSql($sql);
    }
    
    public function _count_db_user_inviterward($uid){
        $tableName = $this->getTableIndex($uid, $this->_table);
        $sql = "SELECT sum(rewardmoney) as s_money FROM " . $tableName . " WHERE uid = " . $uid ;
        $data = $this->executeSql($sql);
        return $data[0]['s_money'] ? $data[0]['s_money'] : 0;
    }
    
    public function count_user_inviterward($uid){
        $key = _KEY_REDIS_MY_INVITE_REWARD_COUNT . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $money = $self->_count_db_user_inviterward($uid);
            if(empty($money)) return false;
            return $money;
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return $return;
    }
    
    public function get_user_inviterward($uid, $start, $end){
        $key = _KEY_REDIS_MY_INVITE_REWARD . $uid;
        return self::$container['redis_default']->setRange($key, $start, $end, 1);
    }
    
    public function init_user_invitereward($uid){
        $key = _KEY_REDIS_MY_INVITE_REWARD . $uid;
        $data = $this->_db_get_invitereward($uid);
        foreach ($data as $_da){
            self::$container['redis_default']->setAdd($key, json_encode($_da), 1, $_da['buytime']);
        }
        return true;
    }
    
    public function add_user_invitereward($uid, $data){
        $tableName = $this->getTableIndex($uid, $this->_table);
        $lastid = $this->insertDataSql($data, $tableName);
        $data['id'] = $lastid;
        $key = _KEY_REDIS_MY_INVITE_REWARD . $uid;
        $size = self::$container['redis_default']->setSize($key, 1);
        if($size > 0){
            $add_data = array();
            $add_data['buytime'] = $data['buytime'];
            $add_data['rewardmoney'] = $data['rewardmoney'];
            $add_data['account'] = $data['account'];
            $ret = self::$container['redis_default']->setAdd($key, json_encode($add_data), 1, $add_data['buytime']);
            $count_key = _KEY_REDIS_MY_INVITE_REWARD_COUNT . $uid;
            self::$container['redis_default']->delete($count_key);
        }
        return $lastid;
    }
    
    public function sum_rewardmoney($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $sum = 0;
        for($i = 0; $i <= 15; $i++){
            $sql = "SELECT sum(rewardmoney) as sum_money FROM " . $this->_table . $i . " WHERE buytime >= $start_time and buytime < $end_time ";
            $data = $this->executeSql($sql);
            $money = $data[0]['sum_money'] ? $data[0]['sum_money'] : 0;
            $sum += $money;
        }
        return $sum;
    }
    
    
}
