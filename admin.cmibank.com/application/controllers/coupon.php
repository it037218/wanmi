<?php
/**
 * Coupon管理
 * * */
class coupon extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '抵用券列表') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_coupon_model', 'coupon');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('抵用券列表', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '抵用券列表');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            
            $couponList = $this->coupon->getCouponList(array('deleted'=>0),'ctime desc',array($psize, $offset));
            $count = $this->coupon->getCouponCount(array('deleted'=>0));
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $couponList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1190');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '抵用券列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/coupon/v_index', $data);
        }
    }
    
    public function addcoupon(){
        $flag = $this->op->checkUserAuthority('抵用券列表', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '抵用券列表');
        } else {
            if($this->input->request('op') == 'addcoupon'){
                $name = trim($this->input->post('name'));
                $sendmoney = trim($this->input->post('sendmoney'));
                $minmoney = trim($this->input->post('minmoney'));
                $days = trim($this->input->post('days'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $ptids=$this->input->post('ptids');
                $pnames=$this->input->post('pnames');
                $data = array();
                $data['name'] = $name;
                $data['sendmoney'] = $sendmoney;
                $data['minmoney'] = $minmoney;
                $data['days'] = $days;
                $data['ctime'] = NOW;
                if(!empty($stime)){
	                $data['stime'] = strtotime($stime);
                }
            	if(!empty($etime)){
	                $data['etime'] = strtotime($etime)+86399;
                }
                $data['ptids'] = $ptids;  
                $data['pnames'] = $pnames;  
                $ret = $this->coupon->addCoupon($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加抵用券失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '抵用券列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加抵用券成功', array(), '抵用券列表 ', 'forward', OP_DOMAIN.'/coupon'));
            }else{
            	$this->load->model('admin_ptype_model', 'ptype');
            	$typeList = $this->ptype->getPtypeList();
                $this->load->view('/coupon/v_addCoupon',array('ptypeList'=>$typeList));
            }
        }
    }
    public function editCoupon(){
        $flag=$this->op->checkUserAuthority('抵用券列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '抵用券列表');
        }else{
            
            if($this->input->request('op') == 'editCoupon'){
            	$id = trim($this->input->post('id'));
                $name = trim($this->input->post('name'));
                $sendmoney = trim($this->input->post('sendmoney'));
                $minmoney = trim($this->input->post('minmoney'));
                $days = trim($this->input->post('days'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $ptids=$this->input->post('ptids');
                $pnames=$this->input->post('pnames');
                $data = array();
                if(!empty($stime)){
                	$data['stime'] = strtotime($stime);
                }
                if(!empty($etime)){
                	$data['etime'] = strtotime($etime)+86399;
                }
                $data['name'] = $name;
                $data['sendmoney'] = $sendmoney;
                $data['minmoney'] = $minmoney;
                $data['days'] = $days;
                $data['ptids'] = $ptids;  
                $data['pnames'] = $pnames;  
               $ret = $this->coupon->updateCouponById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '修改抵用券列哦表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改抵用券列哦表 ', 'forward', OP_DOMAIN.'/coupon'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                 $rec= $this->coupon->getCouponById($id);
                 $data['detail'] = $rec[0];
                $this->load->view('/coupon/v_editCoupon', $data);
            }  
        }
    }
    public function delCoupon(){
        $flag=$this->op->checkUserAuthority('抵用券列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '抵用券列表');
        }else{
            $id = $this->uri->segment(3);
            $data['deleted'] = 1;
            $ret = $this->coupon->updateCouponById($id, $data);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '删除抵用券', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除抵用券', 'forward', OP_DOMAIN.'/coupon'));
    }
}