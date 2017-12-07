<?php
class withdrawshenghe extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '用户管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
    }
    
    
    public function index() {
        $flag = $this->op->checkUserAuthority('用户取现审核', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户取现审核');
        } else {
        	$searchparam = array();
        	$data=array();
        	if($this->input->request('op') == 'search'){
        		$status = trim($this->input->post('status'));
        		if($status!=3){
        			$searchparam['status']=$status;
        		}
					$data['status']=$status;
        		$sqstime = trim($this->input->post('sqstime'));
        		$sqetime = trim($this->input->post('sqetime'));
        		if(!empty($sqstime)){
        			$searchparam['sqstime'] = strtotime($sqstime);
        			$data['sqstime'] = $sqstime;
        		}else {
        			$searchparam['sqstime'] = strtotime(date('Y-m-d',NOW));
        			$data['sqstime'] = date('Y-m-d',NOW);
        		}
        		if(!empty($sqetime)){
        			$searchparam['sqetime'] = strtotime($sqetime)+86400;
        			$data['sqetime'] = $sqetime;
        			 
        		}else{
        			$searchparam['sqetime'] = NOW;
        			$data['sqetime'] = date('Y-m-d',NOW);
        		}
        		
        		$ckstime = trim($this->input->post('ckstime'));
        		$cketime = trim($this->input->post('cketime'));
        		
        		if(!empty($ckstime)){
        			$searchparam['ckstime'] = strtotime($ckstime);
        			$data['ckstime'] = $ckstime;
        		}
        		if(!empty($cketime)){
        			$searchparam['cketime'] = strtotime($cketime)+86400;
        			$data['cketime'] = $cketime;
        		}
        	}else{
        		$data['status']=0;
        		$searchparam['status']=0;
        		$searchparam['sqstime'] = strtotime(date('Y-m-d',strtotime('-5 day')));
        		$data['sqstime'] = date('Y-m-d',strtotime('-5 day'));
        		$searchparam['sqetime'] = NOW;
        		$data['sqetime'] = date('Y-m-d',NOW);
        	}
            $this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
            $this->load->model('admin_useridentity_model', 'useridentity');
            $weeList = $this->weehour_withdraw->getWeeHourWithDraw($searchparam);
            $yanshiArray = $this->weehour_withdraw->getYanshi();
            if(!empty($weeList)){
            	$names = array();
            	$phones = array();
            	$bankCode = array();
            	foreach ($weeList as $val){
            		if(!array_key_exists($val['uid'],$names)){
            			$user = $this->useridentity->getUseridentityByUid($val['uid']);
            			if(!empty($user)){
            				$names[$val['uid']] = $user['realname'];
            				$phones[$val['uid']] = $user['phone'];
            				$bankCode[$val['uid']] = trim($user['bankcode']);
            			}
            		}
            	}
            	$data['names'] = $names;
            	$data['phones'] = $phones;
            	$data['bankcode'] = $bankCode;
            }
            $data['yanshi'] = $yanshiArray;
            $total = $this->weehour_withdraw->sumWeeHourWithDraw($searchparam);
            $totalToBe = $this->weehour_withdraw->sumToBeWithDraw();
            $totalToBeJYT = $this->weehour_withdraw->sumToBeWithDrawJYT();
            $totalToBeBaofoo = $this->weehour_withdraw->sumToBeWithDrawBaofoo();
            $defaultWithdraw = $this->weehour_withdraw->getDefaultWithdraw();
            $data['defaultWithdraw'] = $defaultWithdraw;
            $data['weehour_order'] = $weeList;
            $data['total'] = $total;
            $data['totalToBe'] = $totalToBe;
            $data['totalToBeJYT'] = $totalToBeJYT;
            $data['totalToBeBaofoo'] = $totalToBeBaofoo;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1600');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '用户取现审核', '', '用户取现审核', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/withdrawshenghe/v_index', $data);
        }
    }
    
    public function setYanshi(){
    	$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    	$uid = $this->uri->segment(3);
    	$yanshiArray = $this->weehour_withdraw->getYanshi();
    	array_push($yanshiArray, $uid);
    	$ret = $this->weehour_withdraw->setYanshi($yanshiArray);
    	if($ret){
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '操作成功', array(), '用户取现审核 ', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}else{
    		exit($this->ajaxDataReturn(self::AJ_RET_FAIL,  '操作失败', array(), '用户取现审核 ', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}
    }
    
    public function cancel($id){
    	$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    	$ret = $this->weehour_withdraw->updateShengHeWeeHourWithDrawById($id,0);
    	if($ret){
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '操作成功', array(), '用户取现审核 ', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}else{
    		exit($this->ajaxDataReturn(self::AJ_RET_FAIL,  '操作失败', array(), '用户取现审核 ', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}
    }
    
    public function removeYanshi(){
    	$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    	$uid = $this->uri->segment(3);
    	$yanshiArray = $this->weehour_withdraw->getYanshi();
    	$location = array_search($uid, $yanshiArray);
    	array_splice($yanshiArray, $location, 1);
    	$ret = $this->weehour_withdraw->setYanshi($yanshiArray);
    	if($ret){
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '操作成功', array(), '用户取现审核 ', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}else{
    		exit($this->ajaxDataReturn(self::AJ_RET_FAIL,  '操作失败', array(), '用户取现审核 ', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}
    }
    
    public function weehourshenghe($id){
        $flag = $this->op->checkUserAuthority('用户取现审核', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户取现审核');
        } else {
            $data = array();
            $this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
            $data = $this->weehour_withdraw->getWeeHourWithDrawById($id);
            if($data['shenghe'] == 1){
                exit($this->ajaxDataReturn(self::AJ_RET_FAIL, '已审核过', array(), '用户取现审核'));
            }
            $this->weehour_withdraw->updateShengHeWeeHourWithDrawById($id,1);
            $log = $this->op->actionData($this->getSession('name'), '审核取现订单', '', '审核取现订单', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '审核通过', array(), '用户取现审核'));
        }
    }
    public function weehourshenghetuihui($id){
    	$flag = $this->op->checkUserAuthority('用户取现审核', $this->getSession('uid'));        //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户取现审核');
    	} else {
    		$data = array();
    		$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    		$data = $this->weehour_withdraw->getWeeHourWithDrawById($id);
    		if($data['shenghe'] == 2){
    			exit($this->ajaxDataReturn(self::AJ_RET_FAIL, '已退回', array(), '用户取现审核'));
    		}
    		$this->weehour_withdraw->updateShengHeWeeHourWithDrawById($id,2);
    		$log = $this->op->actionData($this->getSession('name'), '审核取现订单', '', '审核取现订单', $this->getIP(), $this->getSession('uid'));
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '审核已退回', array(), '审核取现订单', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}
    }
    
    public function changePlat($id){
    	$flag = $this->op->checkUserAuthority('用户取现审核', $this->getSession('uid'));        //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户取现审核');
    	} else {
    		$data = array();
    		$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    		$data = $this->weehour_withdraw->getWeeHourWithDrawById($id);
    		if($data['status'] == 1){
    			exit($this->ajaxDataReturn(self::AJ_RET_FAIL, '已出款订单不能修改', array(), '用户取现审核'));
    		}
    		if(empty($data['plat'])){
	    		$this->weehour_withdraw->changePlatByid($id,1);
    		}else{
    			$this->weehour_withdraw->changePlatByid($id,0);
    		}
    		$log = $this->op->actionData($this->getSession('name'), '切换渠道', '', '切换渠道', $this->getIP(), $this->getSession('uid'));
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '切换成功', array(), '审核取现订单', 'forward', OP_DOMAIN.'/withdrawshenghe'));
    	}
    }
    
    public function withdrawToBaofoo(){
    	$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    	$this->weehour_withdraw->toBaofoo();
    	$ret = $this->weehour_withdraw->withdrawToBaofoo();
    	if($ret){
    		exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'切换至宝付成功')));
    	}else{
    		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'切换至金运通失败')));
    	}
    }
    
    public function withdrawToJYT(){
    	$this->load->model('admin_weehour_withdraw_model', 'weehour_withdraw');
    	$this->weehour_withdraw->toJYT();
    	$ret = $this->weehour_withdraw->withdrawToJYT();
    	if($ret){
    		exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'切换至金运通成功')));
    	}else{
    		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'切换至金运通失败')));
    	}
    }
}