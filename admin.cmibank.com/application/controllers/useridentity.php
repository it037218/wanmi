<?php
/**
 * 用户基本信息管理
 * * */
class useridentity extends Controller{
    
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
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_account_model', 'account');
        $this->load->model('admin_balance_model', 'balance');
        $this->load->model('admin_longmoney_model', 'longmoney');
    }
    public function index(){
    	$flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
        	$page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data = array();
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $count=0;
            if($this->input->request('op') == "search"){
            	$type = trim($this->input->post('type'));
            	$bangka = trim($this->input->post('bangka'));
            	$searchtitle = trim($this->input->post('searchtitle'));
				$searchparam = array();
				$searchparam['type']=$type;
				$searchparam['bangka']=$bangka;
				$searchparam['searchtitle']=$searchtitle;
				$data['type'] = $type;
				$data['bangka'] = $bangka;
				$data['searchtitle'] = $searchtitle;
            	$useridentitylist = $this->useridentity ->getUseridentityListByLike($searchparam,$offset,$psize);
            	if (!empty($useridentitylist)){
            		$this->config->load('cfg/banklist', true, true);
            		$bankCfg = $this->config->item('cfg/banklist');
            		$data['banklist'] = $bankCfg;
            		$count = $this->useridentity->countUseridentityListByLike($searchparam);
            	}	
				if($count>0){
	                $data['count'] = $count;
	                $data['list'] = $useridentitylist;
	            }else{
	                $data['count'] = 0;
	                $data['list'] = $data['page'] = '';
	            }
        	}else{
        		$data['type'] = 1;
        		$data['bangka'] = 1;
        	}
	            $edatable = $this->op->getEditable($this->getSession('uid'),'1040');
	            if(!empty($edatable)){
	            	$data['editable'] = $edatable[0]['editable'];
	            }else{
	            	$data['editable']=0;
	            }
        	
        	$useriNumber = count($this->useridentity->getUseridentityList(array('isnew'=>'0'),'',''));
        	$userallNumber = $this->useridentity->getAllDealUser();
        	$accountNumber = $this->account->getAccountCount();
        	$totalValidate = $this->useridentity->getUseridentityCount();
        	$totalBalance = $this->balance->getSumBalanceMoney();
        	$totalLongMoney = $this->longmoney->getSumLongMoney();
        	$data['totalBalance'] = $totalBalance[0]['sum(balance)'];
        	$data['totalLongMoney'] = $totalLongMoney[0]['sum(money)'];
        	$data['accountNumber'] = $accountNumber;
        	$data['useriNumber'] = $useriNumber;
        	$data['userallNumber'] = $userallNumber;
        	$data['totalValidate'] = $totalValidate;
	        $log = $this->op->actionData($this->getSession('name'), '用户管理', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
        	$this->load->view('/useridentity/v_index',$data);
   		}	
    }
   	
    public function getBuyList(){
    	$flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
    	}else{
    		$data = array();
    		$count=0;
    		$useridentitylist=array();
    		if($this->input->request('op') == "search"){
    			$type = trim($this->input->post('type'));
    			$stime = trim($this->input->post('stime'));
    			$etime = trim($this->input->post('etime'));
    			$plat = trim($this->input->post('plat'));
    			if(!empty($plat)){
    				$data['plat']= $plat;
    				$searchparam['plat']=$plat;
    			}
    			$searchparam = array();
    			$searchparam['type']=$type;
    			$searchparam['stime']=strtotime($stime);
    			$searchparam['etime']=strtotime($etime)+86400;
    			$searchparam['plat']=$plat;
    			$data['type'] = $type;
    			$data['stime'] = $stime;
    			$data['etime'] = $etime;
    			$data['plat'] = $plat;
    		}else{
    			$type = trim($this->uri->segment(3));
    			$date = trim($this->uri->segment(4));
    			$plat = trim($this->uri->segment(5));
    			$searchparam = array();
    			$searchparam['type']=$type;
    			$data['type']= $type;
    			if(!empty($plat)){
    				$data['plat']= $plat;
    				$searchparam['plat']=$plat;
    			}
    			if(strlen($date)>18){
    				$datelist = explode("--", $date);
    				$searchparam['stime']=strtotime($datelist[0]);
    				$searchparam['etime']=strtotime($datelist[1]);
    				$data['stime']= $datelist[0];
    				$data['etime']= $datelist[1];
    			}else{
    				$searchparam['stime']=strtotime($date);
    				$searchparam['etime']=strtotime($date)+86400;
    				$year = substr( $date, 0, 4);
    				$num =date('W',strtotime($date));
    				$table_index = $year.'_'.$num;
    				$data['stime']= $date;
    				$data['etime']= $date;
    				$searchparam['table_index']= $table_index;
    			}
    		}
    
    		$useridentitylist = $this->useridentity ->getUserBuyListByTyppeLike($searchparam);
    		$count = count($useridentitylist);
    		if($count>0){
    			$data['count'] = $count;
    			$data['list'] = $useridentitylist;
    			$this->config->load('cfg/banklist', true, true);
    			$bankCfg = $this->config->item('cfg/banklist');
    			$data['banklist'] = $bankCfg;
    		}else{
    			$data['count'] = 0;
    			$data['list'] = $data['page'] = '';
    		}
    		$edatable = $this->op->getEditable($this->getSession('uid'),'1040');
    		if(!empty($edatable)){
    			$data['editable'] = $edatable[0]['editable'];
    		}else{
    			$data['editable']=0;
    		}
    		$useriNumber = count($this->useridentity->getUseridentityList(array('isnew'=>'0'),'',''));
    		$accountNumber = $this->account->getAccountCount();
    		$totalValidate = $this->useridentity->getUseridentityCount();
    		$data['accountNumber'] = $accountNumber;
    		$data['useriNumber'] = $useriNumber;
    		$data['totalValidate'] = $totalValidate;
    		$log = $this->op->actionData($this->getSession('name'), '用户管理', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
    		$this->load->view('/useridentity/v_productBuyInfo',$data);
    	}
    }
    public function getSomeUsers(){
    	$flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
    	}else{
    		$data = array();
    		$count=0;
    		$useridentitylist=array();
    		$searchparam = array();
    		if($this->input->request('op') == "search"){
    			$type = trim($this->input->post('type'));
    			$stime = trim($this->input->post('stime'));
    			$etime = trim($this->input->post('etime'));
    			$plat = trim($this->input->post('plat'));
    			if(!empty($plat)){
    				$data['plat']= $plat;
    				$searchparam['plat']=$plat;
    			}
    			$searchparam['type']=$type;
    			$searchparam['stime']=strtotime($stime);
    			$searchparam['etime']=strtotime($etime)+86400;
    			$data['type'] = $type;
    			$data['stime'] = $stime;
    			$data['etime'] = $etime;
    			$data['plat'] = $plat;
    		}else{
	    		$type = trim($this->uri->segment(3));
	    		$date = trim($this->uri->segment(4));
	    		$plat = trim($this->uri->segment(5));
	    		$searchparam['type']=$type;
	    		$data['type']= $type;
	    		if(!empty($plat)){
		    		$data['plat']= $plat;
		    		$searchparam['plat']=$plat;
	    		}
	    		if(strlen($date)>18){
	    			$datelist = explode("--", $date);
	    			$searchparam['stime']=strtotime($datelist[0]);
	    			$searchparam['etime']=strtotime($datelist[1]);
	    			$data['stime']= $datelist[0];
	    			$data['etime']= $datelist[1];
	    		}else{
	    			$searchparam['stime']=strtotime($date);
	    			$searchparam['etime']=strtotime($date)+86400;
	    			$year = substr( $date, 0, 4);
	    			$num =date('W',strtotime($date));
	    			$table_index = $year.'_'.$num;
	    			$data['stime']= $date;
	    			$data['etime']= $date;
	    			$searchparam['table_index']= $table_index;
	    		}
    		}
    		
    		$useridentitylist = $this->useridentity ->getUseridentityListByTyppeLike($searchparam);
    		$count = count($useridentitylist);
    		if($count>0){
    			$data['count'] = $count;
    			$data['list'] = $useridentitylist;
    			$this->config->load('cfg/banklist', true, true);
    			$bankCfg = $this->config->item('cfg/banklist');
    			$data['banklist'] = $bankCfg;
    		}else{
    			$data['count'] = 0;
    			$data['list'] = $data['page'] = '';
    		}
    		$edatable = $this->op->getEditable($this->getSession('uid'),'1040');
    		if(!empty($edatable)){
    			$data['editable'] = $edatable[0]['editable'];
    		}else{
    			$data['editable']=0;
    		}
    		$useriNumber = count($this->useridentity->getUseridentityList(array('isnew'=>'0'),'',''));
    		$accountNumber = $this->account->getAccountCount();
    		$totalValidate = $this->useridentity->getUseridentityCount();
    		$data['accountNumber'] = $accountNumber;
    		$data['useriNumber'] = $useriNumber;
    		$data['totalValidate'] = $totalValidate;
    		$log = $this->op->actionData($this->getSession('name'), '用户管理', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
    		$this->load->view('/useridentity/v_userlist',$data);
    	}
    }
    
    public function detail(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            $data = array();
            $uid = $this->uri->segment(3);
            //判断开户银行编号
            $this->config->load('cfg/banklist', true, true);
            $data['banklist'] = $bankCfg = $this->config->item('cfg/banklist');
            
            $data['detail'] = $this->useridentity->getUseridentityByUid($uid);
            $this->load->view('/useridentity/v_detail',$data);
        }    
    }
    
    public function againUseridentity($uid){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            $ret = $this->useridentity->updateUseridentity($uid,1);
            if($ret){
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重新绑定成功', array(),'用户基本信息管理', 'forward', OP_DOMAIN.'/useridentity'));
            }
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'重新绑定成功失败',array(),'用户基本信息管理');
        }
    }
    public function resetUseridentity($uid){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
        	$this->load->model('admin_balance_model', 'balance');
        	$balance = $this->balance->get_user_balance($uid);
        	
        	//活期
        	$this->load->model('admin_longmoney_model', 'longmoney_model');
        	$longmoney = $this->longmoney_model->getUserLongMoney($uid);
        	if($balance + $longmoney > 0){
        		exit($this->ajaxDataReturn(self::AJ_RET_FAIL,  '解绑失败，用户账户内尚有资金，不允许解绑卡！', array(), '用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
        	}
            $ret = $this->useridentity->updateUseridentity($uid,0);
            if($ret){
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '解绑成功', array(), '用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
            }
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'解绑失败',array(),'用户基本信息管理');
        }
    }
    
    public function editUseridentity(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            if($this->input->request('op') == 'saveedit'){
                $uid = trim($this->input->post('uid'));
                $phone = trim($this->input->post('phone'));
                $bankcode = trim($this->input->post('bankCode'));      //银行编号
                $cardno = trim($this->input->post('cardno'));      //银行卡号
                $idCard = trim($this->input->post('idCard'));
                $realname = trim($this->input->post('realname'));
                
                $data['phone'] = $phone;
                $data['bankcode'] = $bankcode;
                $data['cardno'] = $cardno;
               // $data['idCard'] = $idCard;  身份证号码不能修改
                $data['ischeck'] = 0;
                $data['isvalidate'] = 0;
                $data['realname'] = $realname;
                
                $ret = $this->useridentity->editUseridentity($uid,$data);
                if($ret){
                    $log = $this->op->actionData($this->getSession('name'), '修改用户信息', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
                }
            }else{
                $uid = $this->uri->segment(3);
                if($uid < 0 || !is_numeric($uid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $this->config->load('cfg/banklist', true, true);
                $data['banklist'] = $bankCfg = $this->config->item('cfg/banklist');
                $data['detail'] = $this->useridentity->getUseridentityByUid($uid);
                $this->load->view('/useridentity/editUseridentity',$data);
            }
        }
    }
    //汇付天下
//     public function editUseridentity(){
//         $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
//         if($flag == 0){
//            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
//         }else{
//             if($this->input->request('op') == 'saveedit'){
                
//                 $uid = trim($this->input->post('uid'));
//                 $OperId = trim($this->input->post('pnr_usrid'));       //开户时传的MerUsrId
//                 $openAcctid = trim($this->input->post('cardno'));      //银行卡号
//                 $name = trim($this->input->post('realname'));          //银行卡用户名
//                 $OpenBankCode = trim($this->input->post('bankcode'));      //银行编号
//                 $OpenProvId = trim($this->input->post('provid'));      //省份地区编码
//                 $OpenAreaId = trim($this->input->post('areaid'));      //省份地区编码
//                 $phone = trim($this->input->post('phone'));
//                 $idCard = trim($this->input->post('idCard'));          //身份证编号
//                 $openAcctid_old = trim($this->input->post('cardno_old'));
                
//                 $data['uid'] = $uid;
//                 $data['pnr_usrid'] = $OperId;
//                 $data['cardno'] = $openAcctid;
//                 $data['realname'] = $name;
//                 $data['bankcode'] = $OpenBankCode;
//                 $data['phone'] = $phone;
//                 $data['idCard'] = $idCard;
              
//                 $this->load->model('admin_pnr_model', 'pnr');
//                 //取现绑卡
//                 $message=$this->pnr->SDPBindCard($OperId, $openAcctid, $name, $OpenBankCode, $OpenProvId, $OpenAreaId);
//                 //echo '取现绑卡--'.$message['ErrMsg'].'<br/>';
//                 //解绑充值银行卡
//                 $message=$this->pnr->WHCancelBindCard($OperId, $openAcctid_old);
                
//                 //echo '解绑充值银行卡--'.$message['ErrMsg'].'<br/>';
//                 //充值绑卡  WHBindCard
//                 $message=$this->pnr->WHBindCard($OperId, $openAcctid,  $name, $OpenBankCode, $idCard, $phone);
//                 if($message['RespCode'] !="000000"){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>$message['ErrMsg'])));
//                 }
//                 //echo '充值绑卡--'.$message['ErrMsg'].'<br/>';
                
//                 $ret = $this->useridentity->editUseridentity($uid,$data);
//                 if(!$ret){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
//                 }
//                 $log = $this->op->actionData($this->getSession('name'), '用户管理', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
//                 exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
               
//             }else{
//                 $uid = $this->uri->segment(3);
//                 if($uid < 0 || !is_numeric($uid)){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
//                 }
//                 $this->config->load('cfg/banklist', true, true);
//                 $data['banklist'] = $bankCfg = $this->config->item('cfg/banklist');
//                 $data['detail'] = $this->useridentity->getUseridentityByUid($uid);
//                 $this->load->view('/useridentity/editUseridentity',$data);
//             }
//         }
//     }
    
    
    
    //重置交易密码
    public function ReseTpwd(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            $uid = $this->uri->segment(3);
            $userinfo = $this->useridentity->getUseridentityByUid($uid);
            
            $tpwd = $this->useridentity->random(6);
            $data['tpwd']= $tpwd;
            
            $ret = $this->useridentity->editUseridentity($uid,$data);
            
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'重置交易密码失败')));
            }
            //发送短信
            $phone = $userinfo['phone'];
            $msg_tpl_num ='PEhDN3';
            $ret=$this->useridentity->send_msg($phone, $tpwd,$msg_tpl_num);
            $rn = array();
            $rn =json_decode($ret,true);
            if($rn ['status'] == 'success'){
                 exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重置交易密码成功', array(), '用户基本信息管理解绑银行卡 ', 'no', OP_DOMAIN.'/useridentity'));
            }else{
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重置交易密码失败', array(), '用户基本信息管理解绑银行卡 ', 'no', OP_DOMAIN.'/useridentity'));
            }
        
        }

    }
    //重置登录密码
    public function ResePwd(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            $data = array();
            $uid = $this->uri->segment(3);
            $accountinfo = $this->account->getAccountByUid($uid);
            $data['pwd'] = $this->useridentity->random(6);
            $ret = $this->account->editAccount($uid,$data);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'重置登录密码失败')));
            }
            //发送短信
            $phone = $accountinfo['account'];
            $pwd = $data['pwd'];
            $msg_tpl_num ='uqRK8';
            $ret=$this->useridentity->send_msg($phone,$pwd,$msg_tpl_num);
            $rn  = array();
            $rn =json_decode($ret,true);
            if($rn ['status'] == 'success'){
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重置登录密码成功', array(), '用户基本信息管理解绑银行卡 ', 'no', OP_DOMAIN.'/useridentity'));
            }else{
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重置登录密码失败', array(), '用户基本信息管理解绑银行卡 ', 'no', OP_DOMAIN.'/useridentity'));
            }
        }
    }
    
    public function setforbidden($uid){
        $data = array();
        $data['forbidden'] = 1;
        $this->load->model('admin_account_model', 'account');
        $ret = $this->account->editAccount($uid, $data);
        if($ret){
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '注销成功', array(), '注销用户 ', 'no', OP_DOMAIN.'/useridentity'));
        }else{
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '注销失败', array(), '注销用户 ', 'no', OP_DOMAIN.'/useridentity'));
        }
    }
    
    public function resetforbidden($uid){
        $data = array();
        $data['forbidden'] = 0;
        $this->load->model('admin_account_model', 'account');
        $ret = $this->account->editAccount($uid, $data);
        if($ret){
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '注销成功', array(), '注销用户 ', 'no', OP_DOMAIN.'/useridentity'));
        }else{
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '注销失败', array(), '注销用户 ', 'no', OP_DOMAIN.'/useridentity'));
        }
    }
    
    public function restWithDraw(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            $uid = $this->uri->segment(3);
            $ret = $this->useridentity->restWithDraw($uid);
            $log = $this->op->actionData($this->getSession('name'), '重置取现次数', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重置取现次数成功', array(), '用户基本信息管理 ', 'no', OP_DOMAIN.'/useridentity'));
            
        }
    }
      
    public function updateUserRegisterPhone(){
        $flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
        }else{
            if($this->input->request('op') == 'saveedit'){
                $uid = trim($this->input->post('uid'));
                $phone = trim($this->input->post('new_phone'));
                $old_phone = trim($this->input->post('old_phone'));
                $data = array();
                $data['account'] = $phone;
                if(strlen($phone) != 11){
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'手机号码位数不足11，请检查',array(),'用户基本信息管理');
                    exit;
                }
                $this->load->model('admin_account_model', 'account');
                $result = $this->account->getAccountInfoByPhones($phone);
                if($result){
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'要修改的账号已注册',array(),'用户基本信息管理');
                    exit;
                }
                $this->account->delUserCache($uid, $old_phone);
                $ret = $this->account->editAccount($uid, $data);
                if($ret){
                    $log = $this->op->actionData($this->getSession('name'), '修改注册手机号', '', $old_phone . '->' . $phone, $this->getIP(), $this->getSession('uid'));
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
                }
            }else{
                $uid = $this->uri->segment(3);
                $this->load->model('admin_account_model', 'account');
                $data = $this->account->getAccountByUid($uid);
                $this->load->view('/useridentity/editUseraccount', $data);
            }
        }
    }
    
    public function addFengKong(){
    	$flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
    	}else{
    		$uid = $this->uri->segment(3);
    		$account = trim($this->uri->segment(4));
    		$data = array('fengkong'=>1);
    		$ret = $this->useridentity->editUseridentity($uid,$data);
    		if($ret){
    			$this->useridentity->send_fengkong_msg($account);
    		}
    		$log = $this->op->actionData($this->getSession('name'), '列为风控', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '列为风控成功', array(), '用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
    
    	}
    }
    
    public function removeFengKong(){
    	$flag = $this->op->checkUserAuthority('用户基本信息管理',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'用户基本信息管理');
    	}else{
    		$uid = $this->uri->segment(3);
    		$data = array('fengkong'=>0);
    		$ret = $this->useridentity->editUseridentity($uid,$data);
    		$log = $this->op->actionData($this->getSession('name'), '移除风控', '', '用户基本信息管理', $this->getIP(), $this->getSession('uid'));
    		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '移除风控成功', array(), '用户基本信息管理 ', 'forward', OP_DOMAIN.'/useridentity'));
    
    	}
    }
    
}