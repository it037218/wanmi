<?php

class cd_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/cd_base' , 'cd_base');
    }

    function getUserCd($uid){
        return $this->cd_base->get($uid);
    }
    
    function setUserCd($uid, $data){
        return $this->cd_base->set($uid, $data);
    }
    
    
    
}


   
