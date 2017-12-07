<?php

class count_test extends baseController{

    public function __construct(){
        parent::__construct();
        $this->load->model('admin_useridentity_model', 'useridentity');
    }

    public function test(){
        $count = $this->useridentity->getUseridentityCount();
        $psize = 500;
        $max_page = ceil($count/$psize);
        $return = array();
        for($page = 1; $page <= $max_page; $page++){
            $offset = ($page - 1) * $psize;
            $data = $this->useridentity->getUseridentityList(null, null, array($psize, $offset));
            foreach ($data as $_d){
                $key = substr($_d['idCard'], 6, 4);
                if(!isset($return[$key])){
                    $return[$key] = 1;
                }else{
                    $return[$key]++;
                }
            }
        }
        ksort($return);
        //print_r($return);
        foreach ($return as $key => $v){
            $return[$key] = $v / $count * 100 . '%';
        }
        print_r($return);
    }
    
}