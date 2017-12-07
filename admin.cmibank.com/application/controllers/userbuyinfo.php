<?php

class userbuyinfo extends Controller {
    
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
        $this->load->model('admin_ptype_model', 'ptype');
        $this->load->model('admin_userbuyinfo_model', 'userbuyinfo');
        $this->load->model('admin_account_model', 'account');
    }
    
    public function index() {
        $flag = $this->op->checkUserAuthority('购买产品查询',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'购买产品查询');exit;
        }else{
            $data = array();
            $data['types'] = $this->ptype->getPtypeList();
            
            $page = max(1, intval($this->input->post('pageNum')));
            $psize = max(20, intval($this->input->post('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            
            if($this->input->post('op') == "search"){
                $i = 0;
                foreach ($this->input->post() as $key => $value) {
                    if($key != 'op' && $value != ''){
                        $i++;
                    }
                }
//                if($i === 0){
//                    exit($this->ajaxDataReturn(self::AJ_RET_FAIL,'请选择要搜索的参数！'));
//                }
                $params = array();
                if($type = $this->input->post('type')){
                    $params['ptid'] = $type;
                }
                if($timestart = $this->input->post('timestart')){
                    $params['timestart'] = strtotime($timestart);
                }
                if($timeend = $this->input->post('timeend')){
                    $params['timeend'] = strtotime($timeend);
                }
                if($amountmin = $this->input->post('amountmin')){
                    $params['amountmin'] = $amountmin;
                }
                if($amountmax = $this->input->post('amountmax')){
                    $params['amountmax'] = $amountmax;
                }
                $uid = '';
                if($account= $this->input->post('account')){
                    $uidArr = $this->account->getUidByAccount($account);
                    if($uidArr){
                        $uid = $uidArr[0]['uid'];
                    }else{
                        exit($this->ajaxDataReturn(self::AJ_RET_FAIL,'没有此用户'));
                    }
                    $params['uid'] = $uid;
                }
                $counts = $this->userbuyinfo->getCount($uid,$params);
                if($uid){
                    $data['list'] = $this->userbuyinfo->getBuyInfoProductListByUid($uid,$params,$offset,$psize);
                } else {
                    $data['list'] = $this->userbuyinfo->getBuyInfoProductListAll($params,$offset,$psize);
                }
                
                if(empty($data['list'])){
                    exit($this->ajaxDataReturn(self::AJ_RET_FAIL,'没有搜索到结果！'));
                }
//                print_r($data['list']);exit;
            }
            $data['type'] = $this->input->post('type') ? $this->input->post('type') : ''; 
            $data['timestart'] = $this->input->post('timestart') ? $this->input->post('timestart') : ''; 
            $data['timeend'] = $this->input->post('timeend') ? $this->input->post('timeend') : ''; 
            $data['amountmin'] = $this->input->post('amountmin') ? $this->input->post('amountmin') : ''; 
            $data['amountmax'] = $this->input->post('amountmax') ? $this->input->post('amountmax') : ''; 
            $data['account'] = $this->input->post('account') ? $this->input->post('account') : ''; 
            
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize; 
            $data['count'] = isset($counts[1]) ? $counts[1] : 0; 
            $data['total'] = isset($counts[0]) ? $counts[0] : 0; 
        }
        
        $this->op->actionData($this->getSession('name'), '用户管理', '', '购买产品查询', $this->getIP(), $this->getSession('uid'));
        $this->load->view('/userbuyinfo/index',$data);
    }
    
    public function userBuyInfos() {
        
    }
    
    public function detail() {
        $data = array('type' => 2);
        $this->load->view('/userbuyinfo/detail',$data);
    }

}