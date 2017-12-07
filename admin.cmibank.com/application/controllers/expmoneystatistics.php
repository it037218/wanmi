<?php
class expmoneystatistics extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '体验金统计') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_user_expmoney_model', 'userexpmoney');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_account_model', 'account');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('体验金统计', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '体验金统计');
            exit;
        } else {
        	if($this->input->request('op') == 'searchexpmoneybycondition'){
	            $data = array();
	            $page = max(1, intval($this->input->request('pageNum')));
	            $psize = max(20, intval($this->input->request('numPerPage')));
	            $offset = ($page - 1) * $psize;
	            
	            $account = trim($this->input->post('account'));
	            $status = trim($this->input->post('status'));
	            $type = trim($this->input->post('type'));
	            $money = trim($this->input->post('money'));
	            $days= trim($this->input->post('days'));
	            $stime = trim($this->input->post('stime'));
	            $etime = trim($this->input->post('etime'));
	            $queryparam = array();
	            if(!empty($account)){
	            	$uid = $this->account->getUidByAccount($account);
	            	if(!empty($uid)){
	            		$queryparam['uid'] = $uid[0]['uid'];
	            	}
	            }
	            if(strlen($days)>0){
		            $queryparam['days'] = $days;
		            $data['days'] = $days;
	            }
	            $queryparam['status'] = $status;
	            $queryparam['money'] = $money;
	            $queryparam['stime'] = $stime;
	            $queryparam['etime'] = $etime;
	            
	            if(!empty($type)){
	            	$queryparam['type'] = $type;
	            }
	            $data['type'] = $type;
	            $data['account'] = $account;
	            $data['status'] = $status;
	            $data['money'] = $money;
	            $data['stime'] = $stime;
	            $data['etime'] = $etime;
	            
	            $expmoneyList = $this->userexpmoney->getUserExpmoneyByCondition($queryparam,$offset,$psize);
	            $names = array();
	            $accounts = array();
	            $expmoneysum = 0;
	            if(!empty($expmoneyList)){
	            	$expmoneysum = $this->userexpmoney->sumUserExpmoneyProfitByCondition($queryparam);
	            	foreach ($expmoneyList as $expmoney){
		            	if(!array_key_exists($expmoney['uid'],$names)){
		            		$user = $this->useridentity->getUseridentityByUid($expmoney['uid']);
		            		$useraccount = $this->account->getAccountByUid($expmoney['uid']);
			            	$accounts[$expmoney['uid']] = $useraccount['account'];
		            		if(!empty($user)){
			            		$names[$expmoney['uid']] = $user['realname'];
		            		}
		            	}
	            	}
	            }
	            $count = $this->userexpmoney->countUserExpmoneyByCondition($queryparam);
	            $data['totalNotExpired']    = array('count'=>0,'totalmoney'=>0);
	            $data['totalUsing']    = array('count'=>0,'totalmoney'=>0);
	            $data['totalBacked']    = array('count'=>0,'totalmoney'=>0);
	            $data['totalExpired']    = array('count'=>0,'totalmoney'=>0);
                $data['expmoneysum'] = $expmoneysum;
	            if($count>0){
	                $data['pageNum']    = $page;
	                $data['numPerPage'] = $psize;
	                $data['count'] = $count;
	                $data['list'] = $expmoneyList;
	                $data['names'] = $names;
	                $data['accounts'] = $accounts;
	            }else{
	                $data['pageNum'] = 1;
	                $data['numPerPage'] = 0;
	                $data['count'] = 0;
	                $data['list'] = $data['page'] = '';
	            }
	            $log = $this->op->actionData($this->getSession('name'), '体验金统计', '', '体验金统计', $this->getIP(), $this->getSession('uid'));
	            $this->load->view('/expmoneystatistics/v_index', $data);
        	}else{
        		$totalNotExpired = $this->userexpmoney->getTotalNotExpired();
        		$totalUsing = $this->userexpmoney->getTotalUsing();
        		$totalExpired = $this->userexpmoney->getTotalExpired();
        		$totalBacked = $this->userexpmoney->getTotalBacked();
        		$expmoneysum = $this->userexpmoney->getTotalProfit();
        		$data = array();
        		$data['totalNotExpired']    = $totalNotExpired;
        		$data['totalUsing']    = $totalUsing;
        		$data['totalExpired']    = $totalExpired;
        		$data['totalBacked']    = $totalBacked;
        		$data['status'] = 4;
        		$data['type'] = 0;
        		$page = max(1, intval($this->input->request('pageNum')));
        		$psize = max(20, intval($this->input->request('numPerPage')));
        		$offset = ($page - 1) * $psize;
        		
        		$queryparam['status'] = 0;
        		$queryparam['sendmoney'] = 0;
        		$queryparam['stime'] = 0;
        		$queryparam['etime'] = 0;
        		$data['expmoneysum'] = $expmoneysum;
        		$data['pageNum'] = 1;
        		$data['numPerPage'] = 0;
        		$data['count'] = 0;
				$data['list'] = $data['page'] = '';
        		$this->load->view('/expmoneystatistics/v_index', $data);
        	}
        }
    }
    
    public function getuserexpmoneyDetails(){
    	$page = max(1, intval($this->input->request('pageNum')));
    	$psize = max(20, intval($this->input->request('numPerPage')));
    	$offset = ($page - 1) * $psize;
    	
    	$uid = $this->uri->segment(3);
    	$queryparam['uid'] = $uid;
    	$queryparam['status'] = 1;
    	$expmoneyList = $this->userexpmoney->getUserExpmoneyByCondition($queryparam,$offset,$psize);
    	$names = array();
    	$accounts = array();
    	$user = $this->useridentity->getUseridentityByUid($uid);
    	if(!empty($user)){
    		$useraccount = $this->account->getAccountByUid($uid);
    		$accounts[$uid] = $useraccount['account'];
	    	$names[$uid] = $user['realname'];
    	}
    	$expmoneysum = 0;
    	if(!empty($expmoneyList)){
    		$expmoneysum = $this->userexpmoney->sumUserExpmoneyProfitByCondition($queryparam);
    	}
    	$count = $this->userexpmoney->countUserExpmoneyByCondition($queryparam);
	    $data['totalNotExpired']    = array('count'=>0,'totalmoney'=>0);
	    $data['totalUsing']    = array('count'=>0,'totalmoney'=>0);
	    $data['totalBacked']    = array('count'=>0,'totalmoney'=>0);
	    $data['totalExpired']    = array('count'=>0,'totalmoney'=>0);
        $data['expmoneysum'] = $expmoneysum;
    	$data['status'] = 1;
    	$data['pageNum'] = 1;
    	$data['names'] = $names;
    	$data['accounts'] = $accounts;
    	$data['numPerPage'] = $psize;
    	$data['count'] = $count;
    	$data['list'] = $expmoneyList;
    	
    	$data['account'] = $user['phone'];
    	$this->load->view('/expmoneystatistics/v_index', $data);
    }
}
