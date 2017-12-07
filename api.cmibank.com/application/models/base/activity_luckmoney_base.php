<?php

require_once 'basemodel.php'; 

class activity_luckmoney_base extends Basemodel{

    private $_table = 'cmibank_activity.cmibank_luckmoney_list';
    
    
    private $_sub_user_cd = array(
        'u_rate' => 20,
        'lucktime' => 0,
    );
    
    public function get_db($lmid){
        return $this->selectDataSql($this->_table, array('lmid' => $lmid));
    }
    
    public function get_luckmoney_detail($lmid){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_ . $lmid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self, $lmid) {
            $luckmoney = $self->get_db($lmid);
            if(empty($luckmoney)) return false;
            return json_encode($luckmoney);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function update_luckmoney_db_detail($data, $lmid){
        $ret = $this->updateDataSql($this->_table, $data, array('lmid' => $lmid));
        if($ret){
            $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_ . $lmid;
            self::$container['redis_default']->delete($key);
        }
        return $ret;
    }
    
    public function getLuckMoneyRedisList($time){
        $zerotime = mktime(0,0,0);
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_LIST_PREFIX_ . date('Ymd');
        $data = self::$container['redis_default']->setRangeBySorce($key, $zerotime, $time);
        if(empty($data)){
            return false;
        }
        return array_pop($data);
    }
    
    //单个红包所有用户排名
    /**
     * @param unknown $lmid int
     * @param unknown $where array array()
     */
    public function get_luckmoney_rank_by_lmid($lmid, $start, $end){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_ . $lmid;
        return self::$container['redis_default']->setRange($key, $start, $end, 1, 1);
    }
    
    //设置单个用户数值
    public function set_luckmoney_rank_by_lmid($lmid, $uid, $value){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_ . $lmid;
        return self::$container['redis_default']->setScore($key, $uid, $value);
    }
    
    /*******************************************************
     *************     用户REDIS数据            ***********************
     *******************************************************/
    
    public function get_user_luckmoney_cd($uid, $lmid, $ltoweight){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_USERCD_PREFIX_ . $lmid . ':' . $uid;
        $data = self::$container['redis_default']->get($key);
        if(!$data){
            $this->_sub_user_cd['u_rate'] = $ltoweight;
            $this->set_user_luckmoney_cd($uid, $lmid, $this->_sub_user_cd);
            return $this->_sub_user_cd;
        }
        return json_decode($data, true);
    }
    
    public function set_user_luckmoney_cd($uid, $lmid, $data){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_USERCD_PREFIX_ . $lmid . ':' . $uid;
        return self::$container['redis_default']->save($key, json_encode($data), 86400);
    }
    
    /***************************************************************
     *************     红包额度REDIS数据            ****************************
     ***************************************************************/
    public function get_luckmoney_money_incr($lmid){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_INCR_PREFIX_ . $lmid;
        return self::$container['redis_default']->get($key);
    }
    
     public function set_luckmoney_money_incr($lmid, $value){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_INCR_PREFIX_ . $lmid;
        return self::$container['redis_default']->incrByFloat($key, $value);
    }
    
    
    /***************************************************************
     *************     红包用户手气列表REDIS数据            ***********************
     ***************************************************************/
    //818总排名
    public function set_luckmoney_rank_with_lmid($lmid, $uid, $value){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_ . $lmid;
        return self::$container['redis_default']->setScore($key, $uid, $value);
    }
    
    //取排名
    public function get_luckmoney_rank_with_lmid($lmid, $start = 0, $end = 9){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_ . $lmid;
        return self::$container['redis_default']->setRange($key, $start, $end, 1, 1);
    }
    
    
    /***************************************************************
     *************     红包参与人数REDIS数据            ***********************
     ***************************************************************/
    
    public function set_luckmoney_join_with_lmid($lmid, $uid, $value){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_JOIN_PREFIX_ . $lmid;
        return self::$container['redis_default']->setScore($key, $uid, $value);
    }
    
}
