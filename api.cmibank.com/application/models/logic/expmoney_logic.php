<?php
class expmoney_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/expmoney_base' , 'expmoney_base');
        $this->load->model('base/expmoney_log_base' , 'expmoney_log_base');
        $this->load->model('base/user_expproduct_base' , 'user_expproduct_base');
    }
    
    public function get_expmoney($uid){
        return $this->expmoney_base->get_user_expmoney($uid);
    }
    
    public function add_expmoney($uid, $expmoney){
        return $this->expmoney_base->add_user_expmoney($uid, $expmoney);
    }
    
    public function cost_expmoney($uid, $expmoney){
        return $this->expmoney_base->cost_user_expmoney($uid, $expmoney);
    }
    
    public function getLog($uid, $start, $end){
        return $this->expmoney_log_base->getLog($uid, $start, $end);
    }
    
    public function addLog($uid, $log_data){
        return $this->expmoney_log_base->addLog($uid, $log_data);
    }

    public function addUserExpProduct($uid, $data){
        return $this->user_expproduct_base->addUserExpProductInfo($uid, $data);
    }
    //用户末结算体验金产品
    public function getuserExpProduct($uid){
        return $this->user_expproduct_base->getUserExpProductInfo($uid);
    }
    
    //在投金额
    public function getUserExpProductCount($uid){
        return $this->user_expproduct_base->getSumUserExpProductMoney($uid);
    }
    
    //所有投过的金额
    public function getUserAllExpProductCount($uid){
        return $this->user_expproduct_base->getSumUserAllExpProductMoney($uid);
    }
    
    public function addExpMoney_using($uid, $money){
        $this->load->model('base/expmoney_using_base', 'expmoney_using_base');
        return $this->expmoney_using_base->add_user_expmoney_using($uid, $money);
    }
    
    public function addExpEndLog($exp_log_data){
        $this->load->model('base/expmoney_end_log_base', 'expmoney_end_log_base');
        return $this->expmoney_end_log_base->addLog($exp_log_data);
    }
    
}


   
