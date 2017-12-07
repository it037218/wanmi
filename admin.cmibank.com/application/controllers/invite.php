<?php
/**
 * invite管理
 * * */
class invite extends Controller{
    
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
        $this->load->model('admin_invite_model', 'invite');
        $this->load->model('admin_useridentity_model','useridentity');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('邀请好友',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'邀请好友');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $searchtitle = trim($this->input->post('searchtitle'));
            $inviteduser = trim($this->input->post('inviteduser'));
            if($this->input->request('op') == "search"){
            	$where = array();
            	if($inviteduser && $inviteduser != '请输入搜索内容'){
            		$where['u_account']=$inviteduser;
            	}
            	if($searchtitle && $searchtitle != '请输入搜索内容'){
            		$where['invite_account']=$searchtitle;
            	}
                $invite = $this->invite->getInviteList($where,'itime desc',array($psize, $offset));
                $rtnuid = array();
                $rtn = array();
                $this->load->model('user_log_base', 'user_log_base');
                foreach ($invite as $key=>$value){
                    $rtnuid[] = $this->useridentity->getUseridentityByUid($value['uid']);
                	if(!empty($value['buytime'])){
                		$money = $this->user_log_base->getFirstBuyMoney($value['uid']);
                		$invite[$key]['money'] = $money;
                	}
                }
                foreach ($rtnuid as $key=>$val){
                    if(!empty($val)){
                        $rtn[$val['uid']] = $val['realname'];
                    }
                }
                $data['user'] = $rtn;
                $data['list'] = $invite;
                $data['searchtitle'] = $searchtitle;
                $data['inviteduser'] = $inviteduser;
                $count = count($this->invite->getInviteList($where,'',''));
            }else{
              /* $invite = $this->invite->getInviteList('','itime desc',array($psize, $offset));
               //print_r($invite);
               $rtnuid = array();
               $rtn = array();
               $this->load->model('user_log_base', 'user_log_base');
               foreach ($invite as $key=>$value){
                   $rtnuid[] = $this->useridentity->getUseridentityByUid($value['uid']);
                   if(!empty($value['buytime'])){
                   	$money = $this->user_log_base->getFirstBuyMoney($value['uid']);
                   	$invite[$key]['money'] = $money;
                   }
               }
               foreach ($rtnuid as $key=>$val){
                   if(!empty($val)){
                       $rtn[$val['uid']] = $val['realname'];
                   }
               }
               $data['user'] = $rtn;
               $data['list'] = $invite; 
               $count = $this->invite->getInviteCount();*/
            	$data['user'] = $rtn;
            	$data['list'] = array();
            	$count = 0;
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'useridentity/index?page=' . $page;
                if(!empty($searchtitle)){
                    $data['rel'] .= '&title=' . $searchtitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
                $data['pageNum']    = 0;
                $data['numPerPage'] = 0;
            }
            $this->load->view('/invite/v_index', $data);
        }
    }
    
    
 
   
}