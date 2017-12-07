<?php

class activity_luckmoney_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/activity_luckmoney_base' , 'activity_luckmoney_base');
    }
    
    public function getluckmoney(){
        $lmid = $this->activity_luckmoney_base->getLuckMoneyRedisList(NOW);
        if(!$lmid){
            return array();
        }
        //取红包详情
        $luckmoney_detail = $this->activity_luckmoney_base->get_luckmoney_detail($lmid);
        return $luckmoney_detail;
    }
    
    //红包队列
    public function getLuckMoneyUserList($lmid){
        $luckmoney_detail = $this->activity_luckmoney_base->get_luckmoney_rank_by_lmid($lmid);
        
    }
    
    //单个用户数据
    public function getLuckMoneyUserCd($uid, $lmid, $ltoweight){
        return $this->activity_luckmoney_base->get_user_luckmoney_cd($uid, $lmid, $ltoweight);
    }
    
    public function setLuckMoneyUserCd($uid, $lmid, $user_cd){
        return $this->activity_luckmoney_base->set_user_luckmoney_cd($uid, $lmid, $user_cd);
    }
    
    
    //红包额度数据
    public function get_luckmoney_money_incr($lmid){
        return $this->activity_luckmoney_base->get_luckmoney_money_incr($lmid);
    }
    
    public function set_luckmoney_money_incr($lmid, $value){
        return $this->activity_luckmoney_base->set_luckmoney_money_incr($lmid, $value);
    }
    
    public function update_luckmoney_db_info($update_data, $lmid){
        return $this->activity_luckmoney_base->update_luckmoney_db_detail($update_data, $lmid);
    }
    
    public function set_luckmoney_rank_with_lmid($lmid, $uid, $value){
        return $this->activity_luckmoney_base->set_luckmoney_rank_with_lmid($lmid, $uid, $value);
    }
    
    public function get_luckmoney_rank_with_lmid($lmid){
        return $this->activity_luckmoney_base->get_luckmoney_rank_with_lmid($lmid);
    }
    
    public function set_luckmoney_join_with_lmid($lmid, $uid, $value){
        return $this->activity_luckmoney_base->set_luckmoney_join_with_lmid($lmid, $uid, $value);
    }
    
}


   
