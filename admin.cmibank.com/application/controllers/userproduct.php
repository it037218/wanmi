<?php
/**
 * 定期用户购买记录管理
 * * */
class userproduct extends Controller{
    
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
        $this->load->model('admin_userproduct_model', 'userproduct');
    }
    
    public function index(){
        $flag = $this->op->checkUserAuthority('定期用户购买记录',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'定期用户购买记录');
        }else{
           $uid = 119213;
           $aa= $this->userproduct->getUserProductlistByPid($uid);
           print_r($aa);
          
        }
    }
}