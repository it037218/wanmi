<?php
/**
 * couponsend活动管理
 * * */
class couponsend extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '发放抵用券') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_couponsend_model', 'couponsend');
        $this->load->model('admin_coupon_model', 'coupon');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('发放抵用券', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '发放抵用券');
            exit;
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->couponsend->getCouponSendList('', 'ctime desc', array($psize, $offset));
            if(!empty($list)){
            	foreach ($list as $index=>$_val){
            		$cids = explode(",",$_val['cids']);
            		$cnames = "";
            		foreach ($cids as $cid){
            			$coupon = $this->coupon->getCouponById($cid);
            			if(!empty($coupon)){
	            			$cnames = $cnames.$coupon[0]['name'].',';
            			}
            		}
            		$cnames = substr($cnames,0,-1);
            		$list[$index]['cnames'] = $cnames;
            	}
            }
            
            $count = $this->couponsend->getCouponSendCount('');

         	$data = array();
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['list'] = $list;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1250');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '发放抵用券', '', '发放抵用券', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/couponsend/v_index', $data);
        }
    }
    
    public function addCouponSend(){
        $flag = $this->op->checkUserAuthority('抵用券列表', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '发放抵用券');
        } else {
            if($this->input->request('op') == 'addcouponsend'){
                $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $accounts = trim($this->input->post('accounts'));
                $cids = trim($this->input->post('cids'));
                $data = array();
                $data['name'] = $name;
                $data['type'] = $type;
                $data['cids'] = $cids;
                $data['accounts'] = $accounts;
                $data['status'] = 1;
                $data['ctime'] = NOW;
                $ret = $this->couponsend->addCouponSend($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加抵用券活动失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '发放抵用券', '', '发放抵用券', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加抵用券活动成功', array(), '发放抵用券 ', 'forward', OP_DOMAIN.'/couponsend'));
            }else{
            	$couponList = $this->coupon->getAvailableCoupon();
                $this->load->view('/couponsend/v_addCouponSend',array('couponList'=>$couponList));
            }
        }
    }
    public function editCouponSend(){
        $flag=$this->op->checkUserAuthority('发放抵用券',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '发放抵用券');
        }else{
            if($this->input->request('op') == 'editcouponsend'){
               $id = $this->input->post('id');    
               if(!$id){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               } 
               $id = trim($this->input->post('id'));
               $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $accounts = trim($this->input->post('accounts'));
                $cids = trim($this->input->post('cids'));
                $data = array();
                $data['name'] = $name;
                $data['type'] = $type;
                $data['cids'] = $cids;
                $data['accounts'] = $accounts;
               $ret = $this->couponsend->updateCouponSendById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '发放抵用券', '', '修改抵用券列表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改抵用券列表 ', 'forward', OP_DOMAIN.'/couponsend'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $sendList = $this->couponsend->getCouponSendById($id);
                $this->load->model('admin_coupon_model', 'coupon');
                $couponList = $this->coupon->getAvailableCoupon();
                $data['detail'] =$sendList[0];
                $data['couponList'] =$couponList;
                $this->load->view('/couponsend/v_editCouponSend', $data);
            }  
        }
    }
    
    public function detail(){
    			$id = $this->uri->segment(3);
    			if($id < 0 || !is_numeric($id)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$sendList = $this->couponsend->getCouponSendById($id);
    			$this->load->model('admin_coupon_model', 'coupon');
    			$couponList = $this->coupon->getAvailableCoupon();
    			$data['detail'] =$sendList[0];
    			$data['couponList'] =$couponList;
    			$this->load->view('/couponsend/v_detail', $data);
    }
    
    public function delCouponSend(){
        $flag=$this->op->checkUserAuthority('发放抵用券',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '发放抵用券');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->couponsend->delCouponSendById($id);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '发放抵用券', '', '删除抵用券', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除抵用券', 'forward', OP_DOMAIN.'/couponsend'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('发放抵用券',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '发放抵用券');
    	}else{
    		$id = $this->uri->segment(3);
    		$couponsendList = $this->couponsend->getCouponSendById($id);
    		$couponsend = array();
    		if(empty($couponsendList)){
    			return;
    		}else{
    			$couponsend =  $couponsendList[0];
    		};
    		$this->load->model('admin_user_coupon_model' , 'admin_user_coupon_model');
    		$sendtimes = $this->admin_user_coupon_model->incr($id);
    		if($sendtimes!=1){
    			return;
    		}
    		$this->load->model('admin_account_model', 'account');
    		$this->load->model('admin_user_notice_model', 'admin_user_notice_model');
    		$couponList = $this->couponsend->getCouponByCids($couponsend['cids']);
    		$usertotal = 0 ;
    		$coupontotal = 0 ;
    		if($couponsend['type']==1){
	    		$psize = 100;
	    		$count = $this->account->getAccountCount();
	    		$max_page = ceil($count/$psize);
	    		for($page = 1; $page <= $max_page; $page++){
	    			$offset = ($page - 1) * $psize;
	    			$accountList = $this->account->getAccountUidList($offset, $psize);
	    			if(!empty($accountList)){
	    				foreach ($accountList as $account){
	    					$singlcount = 0;
	    					$moneycount = 0;
	    					foreach ($couponList as $value){
	    						if(!empty($value['etime'])|| !empty($value['days'])){
	    							$data['id']= date('YmdHis') . mt_rand(1000,9999);
	    							$data['name']=$value['name'];
	    							$data['uid']=$account['uid'];
	    							$data['account']=$account['account'];
	    							$data['ctime'] = NOW;
	    							$data['sendmoney'] = $value['sendmoney'];
	    							$data['type'] = 5;
	    							$data['ptids'] = $value['ptids'];
	    							$data['pnames'] = $value['pnames'];
	    							$data['minmoney'] = $value['minmoney'];
	    							if($value['days']==0){
		    							if(empty($value['stime'])){
		    								$data['stime'] = NOW;
		    							}else{
			    							$data['stime'] = $value['stime'];
		    							}
	    								$data['etime'] = $value['etime'];
	    							}else{
	    								$data['stime'] = NOW;
	    								$data['etime'] = strtotime(date('Y-m-d',time()))+$value['days']*86400-1;
	    							}
	    							$this->admin_user_coupon_model->addCoupon($account['uid'],$data);
	    							$coupontotal++;
	    							$singlcount++;
	    							$moneycount = $moneycount+$value['sendmoney'];
	    						}
	    					}
	    					if($singlcount>0 && $moneycount>0){
 	    						$this->admin_user_coupon_model->send_coupon_msg($account, $singlcount,$moneycount);
								$notice_data = array(
        							'uid' => $account['uid'],
        							'title' => '抵用券获得提醒',
        							'content' => "恭喜您获得了".$singlcount."张共价值".$moneycount."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！",
        							'ctime' => NOW
        						);
        						$this->admin_user_notice_model->addNotice($account['uid'],$notice_data);
	    					}
	    				}
	    			}
	    			$usertotal += count($accountList);
	    		}
    		}else{
    			$accountList = explode(",", $couponsend['accounts']);
    			foreach ($accountList as $account){
    				$uid = $this->account->getUidByAccount($account);
    				if(!empty($uid)){
    					$singlcount = 0;
    					$moneycount = 0;
	    				foreach ($couponList as $value){
	    					if(!empty($value['etime'])|| !empty($value['days'])){
	    						$data['id']= date('YmdHis') . mt_rand(1000,9999);
	    						$data['name']=$value['name'];
	    						$data['uid']=$uid[0]['uid'];
	    						$data['account']=$account;
	    						$data['ctime'] = NOW;
	    						$data['sendmoney'] = $value['sendmoney'];
	    						$data['type'] = 5;
	    						$data['ptids'] = $value['ptids'];
	    						$data['pnames'] = $value['pnames'];
	    						$data['minmoney'] = $value['minmoney'];
	    						if($value['days']==0){
	    							if(empty($value['stime'])){
	    								$data['stime'] = NOW;
	    							}else{
		    							$data['stime'] = $value['stime'];
	    							}
	    							$data['etime'] = $value['etime'];
	    						}else{
	    							$data['stime'] = NOW;
	    							$data['etime'] = strtotime(date('Y-m-d',time()))+$value['days']*86400-1;
	    						}
	    						$this->admin_user_coupon_model->addCoupon($uid[0]['uid'],$data);
	    						$coupontotal++;
	    						$singlcount++;
	    						$moneycount = $moneycount+$value['sendmoney'];
	    					}
	    				}
	    				if($singlcount>0 && $moneycount>0){
	    					$this->admin_user_coupon_model->send_coupon_msg($account, $singlcount,$moneycount);
							$notice_data = array(
        							'uid' => $uid[0]['uid'],
        							'title' => '抵用券获得提醒',
        							'content' => "恭喜您获得了".$singlcount."张共价值".$moneycount."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！",
        							'ctime' => NOW
        					);
        					$this->admin_user_notice_model->addNotice($uid[0]['uid'],$notice_data);
	    				}
    				$usertotal++;
    				}
    			}
    		}
    		$updatedata['status'] = 2;
    		$updatedata['usertotal'] = $usertotal;
    		$updatedata['coupontotal'] = $coupontotal;
    		$updatedata['stime'] = NOW;
    		$ret = $this->couponsend->updateCouponSendById($id,$updatedata);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发放失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '发放抵用券', '', '发放抵用券', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发放成功', array(), '发放抵用券', 'forward', OP_DOMAIN.'/couponsend'));
    }
}