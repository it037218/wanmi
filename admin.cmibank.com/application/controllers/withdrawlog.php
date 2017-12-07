<?php
/**
 *用户取现记录
 * * */
class withdrawlog extends Controller{ 
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '用户取现记录'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_withdraw_log_model', 'withdraw_log');
        $this->load->model('user_log_base', 'user_log');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_withdraw_failed_log_model', 'failed_log');
    }
    public function index(){
    	$flag = $this->op->checkUserAuthority('用户取现记录',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户取现记录');
    	}else{
    		$count = 0;
    		$page = max(1, intval($this->input->request('pageNum')));
    		$psize = max(20, intval($this->input->request('numPerPage')));
    		$data = array();
    		$offset = ($page - 1) * $psize;
    		$withdrawloglist = array();
    		if($this->input->request('op') == "search"){
    		        $phone = trim($this->input->post('phone'));
	    			$uid = trim($this->input->post('uid'));
	    			$ssucctime = trim($this->input->post('ssucctime'));
	    			$esucctime = trim($this->input->post('esucctime'));	
	    			$failed = trim($this->input->post('failed'));	
	    			$orderid = trim($this->input->post('orderid'));
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
	    		       $user = $this->useridentity->getUseridentityByUid($uid);
	    		       $data['realname'] = $user['realname'];
	    			   $searchparam['uid'] = $uid;
			    	   $data['uid'] = $uid;
	    			}
	    			if(!empty($ssucctime)){
	    				$searchparam['ssucctime'] = strtotime($ssucctime);
	    				$data['ssucctime'] = $ssucctime;
	    			}else {
	    				$searchparam['ssucctime'] = strtotime(date('Y-m-d',NOW));
	    				$data['ssucctime'] = date('Y-m-d',NOW);
	    			}
	    			if(!empty($esucctime)){
	    				$searchparam['esucctime'] = strtotime($esucctime)+86400;
	    				$data['esucctime'] = $esucctime;
	    				
	    			}else{
	    				$searchparam['esucctime'] = NOW;
	    				$data['esucctime'] = date('Y-m-d',NOW);
	    			} 
	    			if(!empty($failed)){
	    				$searchparam['failed'] = 1;
	    				$data['failed'] = $failed;
	    			}
	    			if(!empty($orderid)){
	    				$searchparam['orderid'] = $orderid;
	    				$data['orderid'] = $orderid;
	    			}
	    			$withdrawloglist= $this->withdraw_log->getuserwithdrawlogcondition($searchparam,$offset,$psize);
	    			if(!empty($withdrawloglist)){
	    				$names = array();
	    				$this->load->model('admin_useridentity_model', 'useridentity');
	    				foreach ($withdrawloglist as $withdrawlog){
		    				if(!array_key_exists($withdrawlog['uid'],$names)){
			            		$user = $this->useridentity->getUseridentityByUid($withdrawlog['uid']);
			            		if(!empty($user)){
				            		$names[$withdrawlog['uid']] = $user['realname'];
			            		}
			            	}
	    				}
	    				$count = $this->withdraw_log->countuserwithdrawlogbycondition($searchparam);
	    				$data['names'] = $names;
	    				$sum_money = $this->withdraw_log->sumuserwithdrawlogbycondition($searchparam);
	    				$data['sum_money'] = $sum_money;
	    				$sum_money_jyt = $this->withdraw_log->sumuserwithdrawlogbyconditionJYT($searchparam);
	    				$data['sum_money_jyt'] = $sum_money_jyt;
	    				$sum_money_baofoo = $this->withdraw_log->sumuserwithdrawlogbyconditionBaofoo($searchparam);
	    				$data['sum_money_baofoo'] = $sum_money_baofoo;
	    			}
    		}
    		if($count>0){
    			$data['pageNum']    = $page;
    			$data['numPerPage'] = $psize;
    			$data['count'] = $count;
    			$data['list'] = $withdrawloglist;
    		}else{
    			$data['list'] = $data['page'] = '';
    			$data['pageNum']    = 0;
    			$data['numPerPage'] = 0;
    			$data['count'] =  0;
    		}
    		
    		$restrict = $this->withdraw_log->getWithdraw();
    		$data['restrict'] = $restrict;
    		$edatable = $this->op->getEditable($this->getSession('uid'),'1460');
    		if(!empty($edatable)){
    			$data['editable'] = $edatable[0]['editable'];
    		}else{
    			$data['editable']=0;
    		}
    		$this->load->view('/withdrawlog/v_index',$data);
    	}   	 
    }
    public function backtotwithdrawlog($uid){
        $flag = $this->op->checkUserAuthority('用户取现记录',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户取现记录');
        }else{
            $data = array();
            $withdrawlog_list = $this->withdraw_log->getWithDrawloglistlikeUid($uid);
            $data['id'] =   $withdrawlog_list[0]['id'];
            $data['orderid'] =  $withdrawlog_list[0]['orderid'];
            $user = $this->useridentity->getUseridentityByUid($uid);
            $data['realname'] = $user['realname'];
            $data['status_code']= $withdrawlog_list[0]['status_code'];
            $data['status'] =  $withdrawlog_list[0]['status'];
            $data['logid'] =  $withdrawlog_list[0]['logid'];
            $data['money'] = $withdrawlog_list[0]['money'];
            //$data['back_status'] = $withdrawlog_list[0]['back_status'];
            $data['succtime'] = $withdrawlog_list[0]['succtime']; 
        } 
    }
    public function returnmoney(){
    	$flag = $this->op->checkUserAuthority('用户取现记录',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户取现记录');
    	}else{
	    	$orderid = $this->uri->segment(3);
	    	$uid = $this->uri->segment(4);
	    	$withdrawloglist = $this->withdraw_log->getWithDrawlog($orderid);
	    	if(!empty($withdrawloglist)){
	    		$withdrawlog=$withdrawloglist[0];
	    		if($withdrawlog['back_status']!='SUCCESS'){
	    			$isfind = strpos($withdrawlog['logid'], ',');
           			if($isfind){
                		$updatelogArray = explode(',', $withdrawlog['logid']);
                		$update_logid = $updatelogArray[0];
            		}else{
                		$update_logid = $withdrawlog['logid'];
            		}
	    			$userloglist = $this->user_log->getUserLogByid($withdrawlog['uid'],$update_logid);
	    			if(!empty($userloglist)){
	    				$userlog = $userloglist[0];
	    				if($userlog['pname']=='取现处理中' && $userlog['action']==2){
		    					$update_data = array();
		    					$update_data['pname']='取现失败，2小时内将会退还至余额账户';
		    					$update_data['paytime']=NOW;
		    					$update_data['action']=20;
		    					$userlog_ret = $this->user_log->updateUserLogOnlyWithDraw($withdrawlog['uid'],'',$update_data,array('id'=>$update_logid));
		    					if($userlog_ret){
		    						$withdraw_update = array();
		    						$withdraw_update['status']=1;
		    						$withdraw_update['succtime'] = NOW;
		    						$withdraw_update['back_status'] = 'NB_FAILD';
		    						$withdraw_ret = $this->withdraw_log->updateDrawLog($withdraw_update,$orderid,array('id' => $withdrawlog['id']));
		    						if($withdraw_ret){
		    							$useridentity = $this->useridentity->getUseridentityByUid($uid);
		    							$this->config->load('cfg/banklist', true, true);
		    							$bankCfg = $this->config->item('cfg/banklist');
		    							
		    							$faild_log_data = array(
		    									'uid' => $uid,
		    									'orderid' => $orderid,
		    									'money' => $withdrawlog['money'],
		    									'realname' => $useridentity['realname'],
		    									'bankname' => $bankCfg[$useridentity['bankcode']]['name'],
		    									'bankcode' => $useridentity['bankcode'],
		    									'cardNo' => $useridentity['cardno'],
		    									'back_code' => '后台退款',
		    									'back_msg' =>  '后台退款',
		    									'logid' => $withdrawlog['logid'],
		    									'plat' => 'jyt',
		    									'ctime' => NOW
		    							);
		    							$failed_ret = $this->failed_log->addFailedLog($faild_log_data);
		    							if($failed_ret){
		    								exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'成功生成退款订单')));
		    							}else{
		    								exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'生成退款订单失败')));
		    							}
		    						}else{
		    							exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'修改取现订单失败')));
		    						}
		    					}else{
		    						exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'修改用户日志失败')));
		    					}
	    				}else{
	    					exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'非取现订单')));
	    				}
	    			}else{
	    				$user_log_data = array(
	    						'uid' => $withdrawlog['uid'],
	    						'pid' => 0,
	    						'pname' => '取现失败，2小时内将会退还至余额账户',
	    						'orderid' => $orderid,
	    						'money' => $withdrawlog['money'],
	    						'balance' => 0,
	    						'action' => USER_ACTION_WITHDRAWFAILED
	    				);
		    					$userlog_ret = $this->user_log->addUserLog($withdrawlog['uid'],$user_log_data);
		    					if($userlog_ret){
		    						$withdraw_update = array();
		    						$withdraw_update['status']=1;
		    						$withdraw_update['succtime'] = NOW;
		    						$withdraw_update['back_status'] = 'NB_FAILD';
		    						$withdraw_ret = $this->withdraw_log->updateDrawLog($withdraw_update,$orderid,array('id' => $withdrawlog['id']));
		    						if($withdraw_ret){
		    							$useridentity = $this->useridentity->getUseridentityByUid($uid);
		    							$this->config->load('cfg/banklist', true, true);
		    							$bankCfg = $this->config->item('cfg/banklist');
		    							
		    							$faild_log_data = array(
		    									'uid' => $uid,
		    									'orderid' => $orderid,
		    									'money' => $withdrawlog['money'],
		    									'realname' => $useridentity['realname'],
		    									'bankname' => $bankCfg[$useridentity['bankcode']]['name'],
		    									'bankcode' => $useridentity['bankcode'],
		    									'cardNo' => $useridentity['cardno'],
		    									'back_code' => '后台退款',
		    									'back_msg' =>  '后台退款',
		    									'logid' => $withdrawlog['logid'],
		    									'plat' => 'jyt',
		    									'ctime' => NOW
		    							);
		    							$failed_ret = $this->failed_log->addFailedLog($faild_log_data);
		    							if($failed_ret){
		    								exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'成功生成退款订单')));
		    							}else{
		    								exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'生成退款订单失败')));
		    							}
		    						}else{
		    							exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'修改取现订单失败')));
		    						}
		    					}else{
		    						exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'修改用户日志失败')));
		    					}
	    			}
	    		}else{
	    			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'订单已成功处理，不能退款！')));
	    		}
	    	}
    	}
    }
    
    public function handle(){
    	$flag = $this->op->checkUserAuthority('用户取现记录',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户取现记录');
    	}else{
	    	$orderid = $this->uri->segment(3);
	    	$uid = $this->uri->segment(4);
	    	$withdrawloglist = $this->withdraw_log->getWithDrawlog($orderid);
	    	if(!empty($withdrawloglist)){
	    		$withdrawlog=$withdrawloglist[0];
	    		if($withdrawlog['back_status']!='SUCCESS'){
	    			$isfind = strpos($withdrawlog['logid'], ',');
           			if($isfind){
                		$updatelogArray = explode(',', $withdrawlog['logid']);
                		$update_logid = $updatelogArray[0];
            		}else{
                		$update_logid = $withdrawlog['logid'];
            		}
	    			$userloglist = $this->user_log->getUserLogByid($withdrawlog['uid'],$update_logid);
	    			if(!empty($userloglist)){
	    				$userlog = $userloglist[0];
	    				if($userlog['action']==20){
		    					$update_data = array();
		    					$update_data['pname']='提现成功';
		    					$update_data['action']=2;
		    					$userlog_ret = $this->user_log->updateUserLogOnlyWithDraw($withdrawlog['uid'],'',$update_data,array('id'=>$update_logid));
		    					if($userlog_ret){
		    						$withdraw_update = array();
		    						$withdraw_update['status']=2;
		    						$withdraw_update['back_status'] = 'SUCCESS';
		    						$withdraw_update['status_code'] = '';
		    						$withdraw_ret = $this->withdraw_log->updateDrawLog($withdraw_update,$orderid,array('id' => $withdrawlog['id']));
		    						exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'处理成功')));
		    					}else{
		    						exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'修改用户日志失败')));
		    					}
	    				}
	    			}
	    		}else{
	    			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'订单已成功处理，不能退款！')));
	    		}
	    	}
    	}
    }
    
    public function stopWithdraw(){
    	$ret = $this->withdraw_log->stopWithdraw();
    	if($ret){
    		exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'停止取现成功')));
    	}else{
    		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'停止取现失败')));
    	}
    }
    
    public function startWithdraw(){
    	$ret = $this->withdraw_log->startWithdraw();
    	if($ret){
    		exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message'=>'开启取现成功')));
    	}else{
    		exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'开启取现失败')));
    	}
    }
}