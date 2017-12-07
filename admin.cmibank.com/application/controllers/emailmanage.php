<?php
/**
 * 清算-催债
 * * */
class emailmanage extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '清算-催债') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_emailmanage_model', 'emailmanage');
        $this->load->model('admin_corporation_model','corporation');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        } else {
            
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;

            $emailmanage = $this->emailmanage->getEmailmanageList('','',array($psize, $offset));
            
            $data['list'] = $emailmanage;
            $count = count($this->emailmanage->getEmailmanageList('','',''));
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'contract/index?page=' . $page;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1105');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $this->load->view('/emailmanage/v_index',$data);
        }
    }
    
    public function addemail(){
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        }else{
            if($this->input->request('op') == 'addemail'){
                $corid = trim($this->input->post('corid'));
                
				
				 $ret = $this->emailmanage->getEmailmanageBycorid($corid);
				 if(!empty($ret)){
					 echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'已经添加过了', array(), '今日催款');
					 exit;
				 }
				
                $corporation = $this->corporation->getCorporationByCid($corid);
                $corname = $corporation['cname'];
                
                $address = trim($this->input->post('address'));
                $copyaddress = trim($this->input->post('copyaddress'));
            
                $data['corid'] = $corid;
                $data['corname'] = $corname;
                $data['address'] = $address;
                $data['copyaddress'] = $copyaddress;
                $data['ctime'] = time();
            
                $ret = $this->emailmanage->addemail($data);
                $log = $this->op->actionData($this->getSession('name'), '清算-催债', '', '添加右键地址', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加成功', array(), '添加邮箱账户 ', 'forward', OP_DOMAIN.'/emailmanage'));
            }else{
                $corporation = $this->corporation->getAllCorporation();
                $data['corporation'] = $corporation;
                $this->load->view('/emailmanage/v_addemail',$data);
            }
        }
    }
    public function detailemail($corid){
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        }else{
            $emailmanage = $this->emailmanage->getEmailmanageBycorid($corid);
            $data['detail'] = $emailmanage;
        
            $corporation = $this->corporation->getAllCorporation();
            $data['corporation'] = $corporation;
        
            $this->load->view('/emailmanage/v_detailemail',$data);
        
        }
    }
    
    public function editemail(){
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        }else{
            if($this->input->request('op') == 'editemail'){
               
                $corid = trim($this->input->post('corid'));
                $corname = trim($this->input->post('corname'));
                $address = trim($this->input->post('address'));
                $copyaddress = trim($this->input->post('copyaddress'));
                
                $data['corid'] = $corid;
                $data['corname'] = $corname;
                $data['address'] = $address;
                $data['copyaddress'] = $copyaddress;

                $ret = $this->emailmanage->updatEmailmanage($corid,$data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '修改收件账户', '', '修改收件账户', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改收件账户成功', array(), '修改收件账户 ', 'forward', OP_DOMAIN.'/emailmanage'));
            }else{
                $corid = $this->uri->segment(3);
                $emailmanage = $this->emailmanage->getEmailmanageBycorid($corid);
                $data['detail'] = $emailmanage;
                $corporation = $this->corporation->getAllCorporation();
                $data['corporation'] = $corporation;
                
                $this->load->view('/emailmanage/v_editemail',$data);
            }
            
            
        }
    }
    
    public function delemail($corid){
        $flag = $this->op->checkUserAuthority('今日催款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '今日催款');
        }else{
            $ret = $this->emailmanage->delEmailmanageBycorid($corid);
            
            $log = $this->op->actionData($this->getSession('name'), '邮箱账户管理', '', '删除收件账户', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除收件账户', array(), '删除收件账户 ', 'forward', OP_DOMAIN.'/emailmanage'));
        }
    }

    


    
    

}