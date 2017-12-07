<?php
/**
 * 活期用户购买记录
 * * */
class userlongproduct extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '用户管理'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_userlongproduct_model', 'userlongproduct');
    }

}