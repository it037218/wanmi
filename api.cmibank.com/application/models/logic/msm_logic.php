<?php

include_once (APPPATH . 'libraries/submail.lib.php');
include_once (APPPATH . 'libraries/cpunc.lib.php');
class msm_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
    }
    
    public function fix_sp($phone){
        $sub_phone = substr($phone, 0, 3);
        $yd = array(134, 135, 136, 137, 138, 139, 147, 150, 151, 152, 157, 158, 159, 178, 182, 183, 184, 187, 188);
        $lt = array(130, 131, 132, 145, 155, 156, 176, 185, 186);
        $dx = array(133, 153, 177, 180, 181, 189);
        $sp_arr = array(        //正常运营商
            'yd' => $yd,
            'lt' => $lt,
            'dx' => $dx
        );
        if($sub_phone == 170){
            $sub_phone = substr($phone, 0, 4);
            $sp_arr = array(     //京东运营商
                'yd' => array(1700),
                'lt' => array(1705),
                'dx' => array(1709)
            );
        }
        foreach ($sp_arr as $sp => $phone_number){
            if(in_array($sub_phone, $phone_number)){
                return $sp;
            }
        }
        return 'yd';
    }
    
    public function select_msm_plat($sp){
        $msm_plat = array(
            'submail' => array('yd'),
            'cpunc' => array('dx', 'lt')
        );
        foreach ($msm_plat as $plat => $_sps){
            if(in_array($sp, $_sps)){
                return $plat;
            }
        }
        return 'submail';
        
    }
    
    public function send_activity_msg($uid, $name, $phone, $money){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('uname' => $name, 'money' => $money);
            $rtn = $submail->send_msg($phone, $values, 'cgutq1');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
            return true;
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】亲爱的'.$name.',恭喜您获得“庆上线，千万现金送不停”活动奖励：现金'.$money.'元，已充入您的余额账户。陪你不止十年！ ';
            $submail->send_msg($phone, $content);
        }
    }
    
    public function send_repayment_msg($uid, $uname, $pname, $money, $phone){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('uname' => $uname, 'pname' => $pname, 'money' => $money);
            $rtn = $submail->send_msg($phone, $values, 'cua802');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
            return true;
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】亲爱的' . $uname . '，您投资的“' . $pname . '”回款共计' . $money . '元，现已转入您的余额账户。如有疑问请致电 400-080-5611. ';
            $submail->send_msg($phone, $content);
        }
    }

    public function send_tiqian_repayment_msg($uid, $uname, $pname, $money, $phone){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$values = array('uname' => $uname, 'pname' => $pname, 'money' => $money);
    		$rtn = $submail->send_msg($phone, $values, 'J6Uat3');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    		return true;
    	}else{
    		$submail = new cpunc();
    		$content = '【易米融理财】亲爱的' . $uname . '，您投资的“' . $pname . '”回款共计' . $money . '元，现已转入您的余额账户。如有疑问请致电 400-080-5611. ';
    		$submail->send_msg($phone, $content);
    	}
    }
    
    public function send_phone_code($phone, $code){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $rtn = $submail->send_msg($phone, $code);
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】尊敬的客户您好，这是您本次的验证码' . $code . '，请填写验证码进行验证 （10分钟内有效）。';
            $submail->send_msg($phone, $content);
        }
        $this->load->model('base/user_base', 'user_base');
        return $this->user_base->setValidateCode($phone, $code);
    }
    
    public function send_pay_code($phone,$values){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$rtn = $submail->send_msg($phone, $values, 'aFglL3');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    	}else{
    		$submail = new cpunc();
    		$content = '【易米融理财】尊敬的客户您好，这是您本次的验证码' . $code . '，请填写验证码进行验证 （10分钟内有效）。';
    		$submail->send_msg($phone, $content);
    	}
    	$this->load->model('base/user_base', 'user_base');
    	return $this->user_base->setPayCode($phone, $values['code']);
    }
    
    //校验码@var(code),您正在万米财富管理有限公司绑定一张@var(bankname)储蓄卡。(工作人员不会向您索取任何密码信息，请勿泄露)
    public function send_bindBank_code($phone, $value){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $rtn = $submail->send_msg($phone, $value, 'sSnLY1');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{

        }
        return $this->load->model('base/user_base', 'user_base');
    }
    
    public function send_loginPwd_code($phone, $code){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $rtn = $submail->send_msg($phone, $code, 'oZHWH1');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】尊敬的客户您好，你重置的登录密码操作的验证码为： ' . $code . '，（10分钟内有效）。';
            $submail->send_msg($phone, $content);
        }
        $this->load->model('base/user_base', 'user_base');
        return $this->user_base->setLoginPwdCode($phone, $code);
    }
    
    
    public function send_tpwd_check_code($uid, $code, $phone){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $rtn = $submail->send_msg($phone, $code, '8lOId1');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
           
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】尊敬的客户您好，你重置的交易密码操作的验证码为： '. $code.'，（10分钟内有效）。';
            $submail->send_msg($phone, $content);
        }
        $this->load->model('base/user_base', 'user_base');
        return $this->user_base->setModifyTpwdCode($uid, $code);
    }
    
    public function send_invite_msg($uid, $phone_tail, $rewardmoney , $phone){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('phone' => $phone_tail, 'rewardmoney' => $rewardmoney);
            $rtn = $submail->send_msg($phone, $values, '4FdyZ3');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】手机尾号' . $phone_tail . '的好友已完成首笔定期投资，' . $rewardmoney . '元奖励已发放至您的余额账户，请查收。';
            $submail->send_msg($phone, $content);
        }
        return true;
    }
    
    public function send_invite_buy_msg($uid, $rewardmoney , $phone){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('rewardmoney' => $rewardmoney);
            $rtn = $submail->send_msg($phone, $values, '1J8qL2');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】恭喜您通过好友邀请方式成功注册且完成首笔定期投资，'.$rewardmoney.'元奖励已发放至您的账户余额。';
            $submail->send_msg($phone, $content);
        }
        return true;
    }
    

    
    public function send_pay_msg($phone, $money){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('amount' => $money);
            $rtn = $submail->send_msg($phone, $values, 'Dq0nT');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】您已充值' . $money . '元，如有疑问，请联系400-8179-299';
            $submail->send_msg($phone, $content);
        }
        return true;
    }
    
    public function send_coupon_msg($phone,$count,$money){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$values = array('count' => $count,'money' => $money);
    		$rtn = $submail->send_msg($phone, $values, 'TA8WE4');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    	}else{
    		$submail = new cpunc();
    		$content = '【易米融理财】亲爱的'.$phone.',恭喜您获得'.$count.'抵用券，请查收。如有疑问，请联系400-8179-299';
    		$submail->send_msg($phone, $content);
    	}
    	return true;
    }
    
    public function send_coupon_user_msg($phone,$uname,$count,$money){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$values = array('uname' => $uname,'count' => $count,'money' => $money);
    		$rtn = $submail->send_msg($phone, $values, 'eYFyJ4');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    	}else{
    		$submail = new cpunc();
    		$content = '【易米融理财】亲爱的'.$phone.',恭喜您获得'.$count.'抵用券，请查收。如有疑问，请联系400-8179-299';
    		$submail->send_msg($phone, $content);
    	}
    	return true;
    }
    
    public function send_invite_reward_msg($uname,$rewardmoney , $phone){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$values = array('uname'=>$uname,'money' => $rewardmoney);
    		$rtn = $submail->send_msg($phone, $values, 'WEGFF1');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    	}else{
    		$submail = new cpunc();
    		$content = '【快点理财】恭喜您获得一笔' . $rewardmoney .  '元的佣金奖励，已发放至余额账户，请查收！';
    		$submail->send_msg($phone, $content);
    	}
    	return true;
    }
    
    public function send_notify_luckybag_msg($phone,$account,$money){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$values = array('account' => $account,'money' => $money);
    		$rtn = $submail->send_msg($phone, $values, 'RRE8n3');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    	}else{
    		$submail = new cpunc();
    		$content = '【易米融理财】亲爱的'.$phone.',恭喜您获得'.$count.'抵用券，请查收。如有疑问，请联系400-8179-299';
    		$submail->send_msg($phone, $content);
    	}
    	return true;
    }
    
    public function send_luckybag_reve_msg($phone,$counts,$money,$uname){
    	$isp = $this->fix_sp($phone);
    	$plat = $this->select_msm_plat($isp);
    	$plat = 'submail';
    	if($plat == 'submail'){
    		$submail = new submail();
    		$values = array('counts' => $counts,'money' => $money,'uname' => $uname);
    		$rtn = $submail->send_msg($phone, $values, 'qwhDh');
    		$rtn = json_decode($rtn, true);
    		if($rtn['status'] == 'error'){
    			return false;
    		}
    	}else{
    		$submail = new cpunc();
    		$content = '【易米融理财】亲爱的'.$phone.',恭喜您获得'.$count.'抵用券，请查收。如有疑问，请联系400-8179-299';
    		$submail->send_msg($phone, $content);
    	}
    	return true;
    }

    /**
     * 双十一 非要请用户首次投资奖励
     * @param $name
     * @param $phone
     * @param $money
     * @return bool
     */
    public function send_double_eleven_msg($name, $phone, $money){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('firstbuyreward' => $money);
            $rtn = $submail->send_msg($phone, $values, 'ld3Zg4');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】亲爱的'.$name.',恭喜您获得“双11迎新 易触即发”活动奖励：现金'.$money.'元，已充入您的余额账户。请查收！ ';
            $submail->send_msg($phone, $content);
        }
        return true;
    }

    public function send_bina_card_reward_msg($name, $phone, $money){
        $isp = $this->fix_sp($phone);
        $plat = $this->select_msm_plat($isp);
        $plat = 'submail';
        if($plat == 'submail'){
            $submail = new submail();
            $values = array('invitebindreward' => $money);
            $rtn = $submail->send_msg($phone, $values, 'ut40b4');
            $rtn = json_decode($rtn, true);
            if($rtn['status'] == 'error'){
                return false;
            }
        }else{
            $submail = new cpunc();
            $content = '【易米融理财】亲爱的'.$name.',恭喜您获得'.$money.'的好友注册绑卡奖励，可在资产余额里面查看。赶紧把这个好消息告诉你的更多小伙伴吧！';
            $submail->send_msg($phone, $content);
        }
        return true;
    }
}

