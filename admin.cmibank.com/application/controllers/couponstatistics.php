<?php
/**
 * Coupon管理
 * * */
class couponstatistics extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '抵用券统计') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_user_coupon_model', 'usercoupon');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_product_model', 'product');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('抵用券统计', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '抵用券统计');
            exit;
        } else {
        	if($this->input->request('op') == 'searchconponbycondition'){
	            $data = array();
	            $page = max(1, intval($this->input->request('pageNum')));
	            $psize = max(20, intval($this->input->request('numPerPage')));
	            $offset = ($page - 1) * $psize;
	            
	            $account = trim($this->input->post('account'));
	            $type = trim($this->input->post('type'));
	            $ptid = trim($this->input->post('ptid'));
	            $status = trim($this->input->post('status'));
	            $sendmoney = trim($this->input->post('sendmoney'));
	            $days= trim($this->input->post('days'));
	            $stime = trim($this->input->post('stime'));
	            $etime = trim($this->input->post('etime'));
	            $queryparam = array();
	            if(!empty($account)){
	            	$this->load->model('admin_account_model', 'account');
	            	$uid = $this->account->getUidByAccount($account);
	            	if(!empty($uid)){
	            		$queryparam['uid'] = $uid[0]['uid'];
	            	}
	            }
	            if(strlen($days)>0){
		            $queryparam['days'] = $days;
		            $data['days'] = $days;
	            }
	            $queryparam['type'] = $type;
	            $queryparam['ptid'] = $ptid;
	            $queryparam['status'] = $status;
	            $queryparam['sendmoney'] = $sendmoney;
	            $queryparam['stime'] = $stime;
	            $queryparam['etime'] = $etime;
	            
	            $data['account'] = $account;
	            $data['type'] = $type;
	            $data['ptid'] = $ptid;
	            $data['status'] = $status;
	            $data['sendmoney'] = $sendmoney;
	            $data['stime'] = $stime;
	            $data['etime'] = $etime;
	            
	            $couponList = $this->usercoupon->getUserConponByCondition($queryparam,$offset,$psize);
	            $names = array();
	            $pnames = array();
	            $couponsum = array('sum_sendmoney'=>0,'sum_buymoney'=>0);
	            if(!empty($couponList)){
	            	$couponsum = $this->usercoupon->sumUserConponByCondition($queryparam);
	            	foreach ($couponList as $coupon){
		            	if(!array_key_exists($coupon['uid'],$names)){
		            		$user = $this->useridentity->getUseridentityByUid($coupon['uid']);
		            		if(!empty($user)){
			            		$names[$coupon['uid']] = $user['realname'];
		            		}
		            	}
		            	if(!array_key_exists($coupon['pid'],$pnames)){
		            		$product = $this->product->getProductByPid($coupon['pid']);
		            		if(!empty($product)){
		            			$pnames[$coupon['pid']] = $product['pname'];
		            		}
		            	}
	            	}
	            }
	            $count = $this->usercoupon->countUserConponByCondition($queryparam);
	            $totalNotExpired = array('count'=>0,'totalmoney'=>0);
	            $totalUsed = array('count'=>0,'totalmoney'=>0);
	            $totalExpired = array('count'=>0,'totalmoney'=>0);
	            $total = array('count'=>0,'totalmoney'=>0);
	            $data['totalNotExpired']    = $totalNotExpired;
	            $data['totalUsed']    = $totalUsed;
	            $data['totalExpired']    = $totalExpired;
	            $data['total']    = $total;
                $data['couponsum'] = $couponsum;
	            if($count>0){
	                $data['pageNum']    = $page;
	                $data['numPerPage'] = $psize;
	                $data['count'] = $count;
	                $data['list'] = $couponList;
	                $data['names'] = $names;
	                $data['pnames'] = $pnames;
	            }else{
	                $data['pageNum'] = 1;
	                $data['numPerPage'] = 0;
	                $data['count'] = 0;
	                $data['list'] = $data['page'] = '';
	            }
	            $log = $this->op->actionData($this->getSession('name'), '抵用券统计', '', '抵用券统计', $this->getIP(), $this->getSession('uid'));
	            $this->load->view('/couponstatistics/v_index', $data);
        	}else{
        		$totalNotExpired = $this->usercoupon->getTotalNotExpired();
        		$totalUsed = $this->usercoupon->getTotalUsed();
        		$totalExpired = $this->usercoupon->getTotalExpired();
        		$total = $this->usercoupon->getTotal();
        		$couponsum = array('sum_sendmoney'=>0,'sum_buymoney'=>0);
        		$data = array();
        		$data['totalNotExpired']    = $totalNotExpired;
        		$data['totalUsed']    = $totalUsed;
        		$data['totalExpired']    = $totalExpired;
        		$data['total']    = $total;
        		$data['type'] = 0;
        		$data['ptid'] = 0;
        		$data['status'] = 0;
        		
        		$page = max(1, intval($this->input->request('pageNum')));
        		$psize = max(20, intval($this->input->request('numPerPage')));
        		$offset = ($page - 1) * $psize;
        		
        		$queryparam['type'] = 0;
        		$queryparam['ptid'] = 0;
        		$queryparam['status'] = 0;
        		$queryparam['sendmoney'] = 0;
        		$queryparam['stime'] = 0;
        		$queryparam['etime'] = 0;
        		$couponsum = array('sum_sendmoney'=>0,'sum_buymoney'=>0);
        		$data['couponsum'] = $couponsum;
        		$data['pageNum'] = 1;
        		$data['numPerPage'] = 0;
        		$data['count'] = 0;
				$data['list'] = $data['page'] = '';
        		$this->load->view('/couponstatistics/v_index', $data);
        	}
        }
    }
    
    public function getusercouponDetails(){
    	$page = max(1, intval($this->input->request('pageNum')));
    	$psize = max(20, intval($this->input->request('numPerPage')));
    	$offset = ($page - 1) * $psize;
    	
    	$uid = $this->uri->segment(3);
    	$queryparam['uid'] = $uid;
    	$queryparam['status'] = 1;
    	$couponList = $this->usercoupon->getUserConponByCondition($queryparam,$offset,$psize);
    	$user = $this->useridentity->getUseridentityByUid($uid);
    	if(!empty($user)){
	    	$names[$uid] = $user['realname'];
    	}
    	
    	$totalNotExpired = $this->usercoupon->getTotalNotExpired();
    	$totalUsed = $this->usercoupon->getTotalUsed();
    	$totalExpired = $this->usercoupon->getTotalExpired();
    	$total = $this->usercoupon->getTotal();
    	$data['totalNotExpired']    = $totalNotExpired;
    	$data['totalUsed']    = $totalUsed;
    	$data['totalExpired']    = $totalExpired;
    	$data['total']    = $total;
    	$data['type'] = 0;
    	$data['ptid'] = 0;
    	$data['status'] = 0;
    	$data['pageNum'] = 1;
    	$data['numPerPage'] = $psize;
    	$data['count'] = count($couponList);
    	$data['list'] = $couponList;
    	
    	$data['account'] = $user['phone'];
    	$this->load->view('/couponstatistics/v_index', $data);
    }
    
    public function getuserCouponShouyiDetails(){
    	$page = max(1, intval($this->input->request('pageNum')));
    	$psize = max(20, intval($this->input->request('numPerPage')));
    	$offset = ($page - 1) * $psize;
    	 
    	$uid = $this->uri->segment(3);
    	$queryparam['uid'] = $uid;
    	$queryparam['status'] = 2;
    	$couponList = $this->usercoupon->getUserConponByCondition($queryparam,$offset,$psize);
    	$names = array();
    	$pnames = array();
    	$couponsum = array('sum_sendmoney'=>0,'sum_buymoney'=>0);
    	if(!empty($couponList)){
    		$couponsum = $this->usercoupon->sumUserConponByCondition($queryparam);
    		foreach ($couponList as $coupon){
    			if(!array_key_exists($coupon['pid'],$pnames)){
    				$product = $this->product->getProductByPid($coupon['pid']);
    				if(!empty($product)){
    					$pnames[$coupon['pid']] = $product['pname'];
    				}
    			}
    		}
    	}
    	$user = $this->useridentity->getUseridentityByUid($uid);
    	if(!empty($user)){
    		$names[$uid] = $user['realname'];
    	}
    	 
    	$totalNotExpired = 0;
    	$totalUsed = 0;
    	$totalExpired = 0;
    	$total = 0;
    	$data['totalNotExpired']    = $totalNotExpired;
    	$data['totalUsed']    = $totalUsed;
    	$data['totalExpired']    = $totalExpired;
    	$data['total']    = $total;
    	$data['type'] = 0;
    	$data['ptid'] = 0;
    	$data['status'] = 0;
    	$data['pageNum'] = 1;
    	$data['numPerPage'] = $psize;
    	$data['count'] = count($couponList);
    	$data['list'] = $couponList;
    	$data['names'] = $names;
    	$data['pnames'] = $pnames;
    	$data['account'] = $user['phone'];
    	$data['couponsum'] = $couponsum;
    	$this->load->view('/couponstatistics/v_index', $data);
    }
}
