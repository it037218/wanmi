<?php

class tg_user_identity_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/tg_user_identity_base' , 'tg_user_identity_base');
    }

    public function initUserIdentity($data){
        return $this->tg_user_identity_base->initUserIdentity($data);
    }
    
    public function getUserIdentity($uid){
        return $this->tg_user_identity_base->getUserIdentity($uid);
    }

    public function getUserIdentityByColumn($where){
        return $this->tg_user_identity_base->getUserIdentityByColumn($where);
    }
    
    public function updateUserIdentity($uid, $data){
        return $this->tg_user_identity_base->updateUserIdentity($data, array('uid' => $uid));
    }
    
    public function set_isnew($uid, $is_new = 0){
        return $this->tg_user_identity_base->set_isnew($uid, $is_new);
    }
    
}


   
