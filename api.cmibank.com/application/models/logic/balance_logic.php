<?php
class balance_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类h
        parent::__construct();
        $this->load->model('base/balance_base' , 'balance_base');
    }
    
    public function get_balance($uid){
        return $this->balance_base->get_user_balance($uid);
    }
    
    public function add_user_balance($uid, $balance){
        return $this->balance_base->add_user_balance($uid, $balance);
    }
    
}


   
