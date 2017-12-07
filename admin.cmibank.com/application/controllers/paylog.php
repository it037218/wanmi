<?php
/**
 * paylog管理
 * * */
class paylog extends Controller{
    
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
        $this->load->model('admin_pay_log_model', 'paylog');
    }
    
    public function index(){
    	$flag = $this->op->checkUserAuthority('用户充值购买记录',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户充值购买记录');
    	}else{
    		$count = 0;
    		$sum = 0;
    		$page = max(1, intval($this->input->request('pageNum')));
    		$psize = max(20, intval($this->input->request('numPerPage')));
    		$data = array();
    		$offset = ($page - 1) * $psize;
    		$payloglist = array();
    		if($this->input->request('op') == "search"){
	    			$uid = trim($this->input->post('uid'));
	    			$trxId = trim($this->input->post('trxId'));
	    			$phone = trim($this->input->post('phone'));
	    			$stime = trim($this->input->post('stime'));
	    			$etime = trim($this->input->post('etime'));
	    			$status = trim($this->input->post('status'));//购买状态
	    			$errormsg = trim($this->input->post('errormsg'));//错误消息
	    			
	    			if(!empty($phone)){
	    				$this->load->model('admin_account_model', 'account');
	    				$uid = $this->account->getUidByAccount($phone);
	    				if(!empty($uid)){
	    					$uid = $uid[0]['uid'];
	    				}
	    				$data['phone'] = $phone;
	    			}
	    			$searchparam = array();
	    			if(!empty($uid)){
	    				$searchparam['uid'] = $uid;
			    		$data['uid'] = $uid;
	    			}
	    			if(!empty($trxId)){
	    				$searchparam['trxId'] = $trxId;
	    				$data['trxId'] = $trxId;
	    			}
	    			if(!empty($stime)){
	    				$searchparam['stime'] = strtotime($stime);
	    				$data['stime'] = $stime;
	    			}else {
	    				$searchparam['stime'] = 1462781437;
	    			}
	    			if(!empty($etime)){
	    				$searchparam['etime'] = strtotime($etime)+86400;
	    				$data['etime'] = $etime;
	    				
	    			}else{
	    				$searchparam['etime'] = NOW;
	    			}
	    			if(!empty($status)){
		    			$searchparam['status'] = $status;
		    			$data['status'] = $status;
	    			}
	    			if(!empty($errormsg)){
		    			$searchparam['errormsg'] = $errormsg;
		    			$data['errormsg'] = $errormsg;
	    			}
	    			
	    			$payloglist= $this->paylog->getuserpaylogbycondition($searchparam,$offset,$psize);
	    			if(!empty($payloglist)){
	    				$count = $this->paylog->countuserpaylogbycondition($searchparam);
	    				$sum = $this->paylog->sumuserpaylogbycondition($searchparam);
	    			}
    		}else{
    			$data['status'] = 1;
    			$data['errormsg'] = 1;
    			$data['stime']=date('Y-m-d',NOW);
    			$data['etime']=date('Y-m-d',NOW);
    		}
    		if($count>0){
    			$data['pageNum']    = $page;
    			$data['numPerPage'] = $psize;
    			$data['count'] = $count;
    			$data['sum'] = $sum;
    			$data['list'] = $payloglist;
    		}else{
    			$data['list'] = $data['page'] = '';
    			$data['pageNum']    = 0;
    			$data['numPerPage'] = 0;
    			$data['count'] =  0;
    			$data['sum'] =  0;
    		}
    		$this->load->view('/paylog/v_index',$data);
    
    		 
    	}
    	 
    }
    
    public function editpaylog(){
        $flag = $this->op->checkUserAuthority('用户充值购买记录', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户充值购买记录');
        }else{
            if($this->input->request('op') == 'saveedit'){
                $ordid = $this->input->post('ordid');
                $errormsg = $this->input->post('errormsg');
                
                $data['ordid'] = $ordid;
                $data['errormsg'] = $errormsg;
                $ret = $this->paylog->editPayLogbyordid($ordid,$data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '用户管理', '', '修改用户充值购买记录', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改用户充值购买记录 ', 'forward', OP_DOMAIN.'/paylog'));
            }else{
                $ordid = $this->uri->segment(3);
                $paylog = $this->paylog->getPayLogbyordidthis($ordid);
                $data['detail'] = $paylog; 
                $this->load->view('/paylog/v_editpaylog',$data);
            }
        }
    }
    public function backtotpaylog($ordid){
        $flag = $this->op->checkUserAuthority('用户充值购买记录',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户充值购买记录');
        }else{
            $data = array();
            //$ordid = $this->uri->segment(3);
            $paylog_list = $this->paylog->getPayLogbyordid($ordid);
            
            $data['ordid'] = $paylog_list[0]['ordid'];
            $data['trxId'] = $paylog_list[0]['trxId'];
            $data['uid'] = $paylog_list[0]['uid'];
            $data['operid'] = $paylog_list[0]['operid'];
            $data['curcode']= $paylog_list[0]['curcode'];
            $data['amt'] = $paylog_list[0]['amt'];
            $data['platform'] = $paylog_list[0]['platform'];
            $data['status'] = $paylog_list[0]['status'];
            $data['ctime'] = time();
            $data['isback'] = $paylog_list[0]['isback'];
            $data['errormsg'] = $paylog_list[0]['errormsg'];
            $data['errorcode'] = $paylog_list[0]['errorcode'];
            $ret = $this->paylog->getPayLogbyordidthis($ordid);
            if($ret){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'本周已经有了哦')));
            }
            $ret = $this->paylog->addpaylog($data);
            $ret = $this->paylog->delpaylog($data['ordid']);
            if(!$ret){
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'加入本周失败')));
            }
            $log = $this->op->actionData($this->getSession('name'), '用户管理', '', '用户充值购买记录', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '加入本周成功', array(), '用户充值购买记录','forward', OP_DOMAIN.'/paylog'));
        }
        
    }
    public function handle($ordid = ''){
    	$paylogList = $this->paylog->getPayLogbyordidthis($ordid);
    	if(!empty($paylogList)){
    		$paylog = $paylogList[0];
    		if($paylog['isback']==0){
    			$this->load->model('admin_balance_model', 'admin_balance_model');
    			$balance = $this->admin_balance_model->get_user_balance($paylog['uid']);
    			$balance += $paylog['amt'];
    			$user_log_data = array(
    					'uid' => $paylog['uid'],
    					'pid' => 0,
    					'pname' => '充值',
    					'paytime' => NOW,
    					'money' => $paylog['amt'],
    					'balance' => $balance,
    					'orderid' => $ordid,
    					'action' => USER_ACTION_PAY
    			);
    			$this->load->model('admin_user_log_model', 'admin_user_log_model');
    			$this->admin_user_log_model->addUserLog($paylog['uid'], $user_log_data);
    			$ret = $this->admin_balance_model->add_user_balance($paylog['uid'], $paylog['amt']);
    			$log_data = array();
    			$log_data['isback'] = 1;
    			$log_data['status'] = 1;
    			$log_data['errormsg'] = '';
    			$log_data['errorcode'] = '';
    			$log_data['trxid'] = $ordid;
    			$this->paylog->editPayLogbyordid($ordid, $log_data);
    			exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'用户充值购买记处理 成功')));
    		}
    	}else{
    		
    	}
    }
    

}