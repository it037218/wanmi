<?php
/**
 * Coupon活动管理
 * * */
class couponactivity extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '抵用券活动列表') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_couponactivity_model', 'couponactivity');
        $this->load->model('admin_coupon_model', 'coupon');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('抵用券活动列表', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '抵用券活动列表');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            
            $couponActivityList = $this->couponactivity->getCouponActivityList(null,'stime desc',array($psize, $offset));
            if(!empty($couponActivityList)){
            foreach ($couponActivityList as $index=>$_val){
               $cids = explode(",",$_val['cids']);
               $cnames = "";
               foreach ($cids as $cid){
	               $coupon = $this->coupon->getCouponById($cid);
	               if(!empty($coupon)){
		               $cnames = $cnames.$coupon[0]['name'].',';
	               }
               }
               $cnames = substr($cnames,0,-1);
               $couponActivityList[$index]['cnames'] = $cnames;
               if($_val['etime']<NOW){
               		if($_val['status'] != 3){
	               		$_data['status'] = 3;
	               		$ret = $this->couponactivity->updateCouponActivityById($_val['id'],$_data);
	               		$couponActivityList[$index]['status'] = 3;
               		}
               }
            }
            }
            $count = $this->couponactivity->getCouponActivityCount();
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $couponActivityList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1194');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '抵用券活动列表', '', '抵用券活动列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/couponactivity/v_index', $data);
        }
    }
    
    public function addCouponActivity(){
        $flag = $this->op->checkUserAuthority('抵用券列表', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '抵用券列表');
        } else {
            if($this->input->request('op') == 'addcouponactivity'){
                $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $buymoney = trim($this->input->post('buymoney'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $cids = trim($this->input->post('cids'));
                $data = array();
                $data['name'] = $name;
                $data['buymoney'] = $buymoney;
                $data['stime'] = strtotime($stime);
                $data['etime'] = strtotime($etime);
                $data['cids'] = $cids;
                $data['type'] = $type;
                $data['status'] = 1;
                $ret = $this->couponactivity->addCouponActivity($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加抵用券活动失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '抵用券活动列表', '', '抵用券活动列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加抵用券活动成功', array(), '抵用券活动列表 ', 'forward', OP_DOMAIN.'/couponactivity'));
            }else{
            	$this->load->model('admin_coupon_model', 'coupon');
            	$couponList = $this->coupon->getAvailableCoupon();
                $this->load->view('/couponactivity/v_addCouponActivity',array('couponList'=>$couponList));
            }
        }
    }
    public function editCouponActivity(){
        $flag=$this->op->checkUserAuthority('抵用券列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '抵用券列表');
        }else{
            
            if($this->input->request('op') == 'editcouponactivity'){
               $id = $this->input->post('id');    
               if(!$id){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               } 
               $id = trim($this->input->post('id'));
               $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $buymoney = trim($this->input->post('buymoney'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $cids = trim($this->input->post('cids'));
                $data = array();
                $data['name'] = $name;
                $data['buymoney'] = $buymoney;
                $data['stime'] = strtotime($stime);
                $data['etime'] = strtotime($etime);
                $data['cids'] = $cids;
                $data['type'] = $type;
               $ret = $this->couponactivity->updateCouponActivityById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '修改抵用券列表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改抵用券列表 ', 'forward', OP_DOMAIN.'/couponactivity'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $activityList = $this->couponactivity->getCouponActivityById($id);
                $this->load->model('admin_coupon_model', 'coupon');
                $couponList = $this->coupon->getAvailableCoupon();
                $data['detail'] =$activityList[0];
                $data['couponList'] =$couponList;
                $this->load->view('/couponactivity/v_editCouponActivity', $data);
            }  
        }
    }
    public function delCouponActivity(){
        $flag=$this->op->checkUserAuthority('抵用券列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '抵用券列表');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->couponactivity->delCouponActivityById($id);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '删除抵用券', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除抵用券', 'forward', OP_DOMAIN.'/couponactivity'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('抵用券列表',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '抵用券列表');
    	}else{
	    		$id = $this->uri->segment(3);
	    		$activityList = $this->couponactivity->getCouponActivityById($id);
	    		if($activityList[0]['etime']<NOW){
	    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'活动已过期')));
	    		}
// 	    		$couponActivityList = $this->couponactivity->getOnlineCouponActivity($activityList[0]['type']);
// 	    		if(!empty($couponActivityList)){
// 	    			foreach ($couponActivityList as $couponActivity){
// 	    				if($couponActivity['etime']>NOW){
// 	    					if(!($activityList[0]['etime']<$couponActivity['stime']||$activityList[0]['stime']>$couponActivity['etime'])){
// 		    					exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'与已有活动时间冲突')));
// 	    					}
// 	    				}
// 	    			}
// 	    		}
	    		$data['status'] = 2;
	    		$ret = $this->couponactivity->updateCouponActivityById($id,$data);
	    		if(!$ret){
	    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发布失败')));
	    		}
    		}
    	$log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '发布抵用券', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布抵用券', 'forward', OP_DOMAIN.'/couponactivity'));
    }
    public function downLine(){
    	$flag=$this->op->checkUserAuthority('抵用券列表',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '抵用券列表');
    	}else{
    		$id = $this->uri->segment(3);
    		$data['status'] = 3;
    		$ret = $this->couponactivity->updateCouponActivityById($id,$data);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'下架失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '抵用券列表', '', '下架抵用券', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '下架成功', array(), '下架抵用券', 'forward', OP_DOMAIN.'/couponactivity'));
    }
}