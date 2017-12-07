<?php
/**
 * aboutus管理
 * * */
class business extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '关于我们'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_business_model', 'business');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('每日交易情况',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'每日汇总');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $count = count($this->business->getBusiness('','',''));
            $list = $this->business->getBusiness('','id desc',array($psize, $offset));
            $data['list'] = $list;
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            
            $this->load->view('/qs_log/v_business', $data);
            
        }
    }
    




}