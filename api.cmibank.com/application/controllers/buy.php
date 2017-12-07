<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class buy extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/balance_logic', 'balance_logic');
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $this->load->model('base/user_jifeng_duihuan_base', 'user_jifeng_duihuan_base');
        $this->check_link();
        $this->check_login();
        
    }

    public function product(){
        if(NOW < mktime(1, 0, 0)){
            $response = array('error'=> 4018, 'msg'=>'凌晨1点开始售卖');
            $this->out_print($response);
        }
        $pid = $this->input->post('pid');
        $money = $this->input->post('money');
        $paytype = $this->input->post('paytype');   //购买方式 1为余额购买  2为银行卡购买～ 
        if(!$paytype){
            $paytype = 1;
        }
        $money = floor($money);
        $money = strval($money);
        $tpwd = $this->input->post('tpwd');
        if($money <= 0){
            $response = array('error'=> 4019, 'msg'=>'金额错误');
            $this->out_print($response);
        }
        $this->load->model('logic/product_logic', 'product_logic');
        $this->load->model('base/user_notice_base', 'user_notice_base');
        //验证交易密码
        $userIdentity = $this->user_identity_base->getUserIdentity($this->uid);
        if(!$userIdentity || !$userIdentity['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $tpwd_times = $this->pay_redis_base->getbuytpwdtimes($userIdentity['phone']);
        if((!empty($tpwd_times))&&$tpwd_times>=3){
        	$response = array('error'=> 4040, 'msg'=>'支付密码已尝试3次，请3小时后再试');
        	$this->out_print($response);
        }
        if($userIdentity['tpwd'] != $tpwd){
	        $this->pay_redis_base->incrbuytpwdtimes($userIdentity['phone']);
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->pay_redis_base->delbuytpwdtimes($userIdentity['phone']);
        $productInfo = $this->product_logic->getProductDetail($pid);
        if($productInfo['ptid'] == 43){
        	if($userIdentity['isnew'] != 1){
	            $response = array('error'=> 4121, 'msg'=>'只有新手才可以购买新手标');
	            $this->out_print($response);
        	}
        }
        
        if($productInfo['canbuyuser'] == 2){
        	if($userIdentity['isnew'] != 1){
        		$response = array('error'=> 4121, 'msg'=>'当前标的只供新用户购买');
        		$this->out_print($response);
        	}
        }else if($productInfo['canbuyuser'] == 3){
        	if($userIdentity['isnew'] == 1){
        		$response = array('error'=> 4121, 'msg'=>'当前标的只供老用户购买');
        		$this->out_print($response);
        	}
        }
        if(!$productInfo){
            $response = array('error'=> 4022, 'msg'=>'产品不存在');
            $this->out_print($response);
        }
        if($productInfo['status'] != 1 || $productInfo['uptime'] > NOW){
            $response = array('error'=> 4023, 'msg'=>'产品还末上线或已下标');
            $this->out_print($response);
        }
        $this->load->model('base/product_base', 'product_base');
        $ptid = $productInfo['ptid'];
        $productid = $this->product_base->getOnlineProductListFirstMem($ptid);
        if($productid != $pid){
            $response = array('error'=> 4124, 'msg'=>'产品还末上线或已下标');
            $this->out_print($response);
        }
        //print_r($productInfo);
        //print_r($productInfo);
        if($money < $productInfo['startmoney'] ||
           ($money - $productInfo['startmoney']) % $productInfo['money_limit'] != 0 || //累进金额不能小
           $money > $productInfo['money_max']){
            $response = array('error'=> 4024, 'msg'=>'金额错误');
            $this->out_print($response);
        }
        $sellMoney = $this->product_logic->rsyncProductSellMoney($pid);
        if($sellMoney >= $productInfo['money']){
            $response = array('error'=> 4025, 'msg'=>'产品已卖完');
            $this->out_print($response);
        }
        if($productInfo['money'] - $sellMoney < $money){
            $response = array('error'=> 4035, 'msg'=>'产品剩余金额不足');
            $this->out_print($response);
        }
        $this->load->model('base/balance_base', 'balance_base');
        $balance = $this->balance_base->get_user_balance($this->uid);
        
        $cid = $this->input->post('cid');
        $minusconpon = $money;
        if(!empty($cid)){
        	$this->load->model('base/user_coupon_base' , 'user_coupon_base');
        	$conpon = $this->user_coupon_base->getUserCouponDetail($this->uid,$cid);
        	if(empty($conpon)){
        		$response = array('error'=> 6060, 'msg'=>'抵用券不可用');
        		$this->out_print($response);
        	}
        	if($conpon['etime']<NOW){
        		$response = array('error'=> 6061, 'msg'=>'抵用券已过期');
        		$this->out_print($response);
        	}
        	if($conpon['stime']>NOW){
        		$response = array('error'=> 6062, 'msg'=>'抵用券暂不可用');
        		$this->out_print($response);
        	}
        	if(!empty($conpon['utime'])){
        		$response = array('error'=> 6063, 'msg'=>'抵用券不可用');
        		$this->out_print($response);
        	}
        	if($conpon['minmoney']>$money){
        		$response = array('error'=> 6064, 'msg'=>'未达到抵用券最小购买金额要求');
        		$this->out_print($response);
        	}
        	$ptidArray = explode(",", $conpon['ptids']);
        	if(in_array($ptid,$ptidArray)){
        		$minusconpon = $money-$conpon['sendmoney'];
        		$balance = $balance+$conpon['sendmoney'];
        		if($balance < $money){
        			$response = array('error'=> 4035, 'msg'=>'余额不足');
        			$this->out_print($response);
        		}
        		$updateconpon['utime'] = NOW;
        		$updateconpon['buymoney'] = $money;
        		$updateconpon['pid'] = $productInfo['pid'];
        		$updateconpon['ptid'] = $ptid;
        		$this->user_coupon_base->updateCoupon($updateconpon,$cid,$this->uid);
        		if($conpon['type']==6){
	        		@$this->user_jifeng_duihuan_base->useDuihuan($cid);
        		}
        		
        		$user_log_data = array(
		            'uid' => $this->uid,
		            'pid' => $pid,
		            'pname' => '抵用券抵扣',
		            'orderid' => 0,
		            'money' => $conpon['sendmoney'],
		            'balance' => $balance,
		            'action' => USER_ACTION_ACTIVITY
        		);
		        $this->load->model('base/user_log_base', 'user_log_base');
		        $this->user_log_base->addUserLog($this->uid, $user_log_data);
        	}else{
        		$response = array('error'=> 6064, 'msg'=>'抵用券不适用该产品');
        		$this->out_print($response);
        	}
        }else{
        	if($balance < $money){
        		$response = array('error'=> 4035, 'msg'=>'余额不足');
        		$this->out_print($response);
        	}
        }
        
        
        $data = array();
        //扣除余额
        $this->load->model('base/balance_base' , 'balance_base');
        $ret = $this->balance_base->cost_user_balance($this->uid, $minusconpon);
        if(!$ret){
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['pid'] = $pid;
            $err_data['ptype'] = 'product';
            $err_data['money'] = $money;
            $err_data['balance'] = $balance;
            $response = array('error'=> 3333, 'msg'=> '余额不足');
            $this->out_print($response, 'json',  true,  true, $err_data);
        }
        $account = $this->getCookie('account');
        $trxid = $this->product_logic->buy_product($this->uid, $productInfo, $userIdentity, $money, $account, $paytype, $balance);
        if(!$trxid){
            $response = array('error'=> 4026, 'msg'=>'购买失败，请重试');
            $this->out_print($response);
        }
        
        //再次同步
        $sellMoney = $this->product_logic->rsyncProductSellMoney($pid);
        if($productInfo['money'] == $sellMoney){
            $this->product_logic->setProductSellOut($productInfo['ptid'], $pid);
        }

        //送体验金
        $add_exp = false;
        $send_expmoney = 0;
        $exp_balance = 0;
        
        $activity_expmoney = true;
        $this->load->model('logic/invite_logic', 'invite_logic');
        $invite_cfg = $this->invite_logic->getCfg();
        $this->load->model('base/user_base', 'user_base');
        
        if(strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW 
            && $userIdentity['isnew'] == 1){
            $activity_expmoney = false;
        }
        
        if($productInfo['exp_buy'] > 0 && $productInfo['exp_send'] && $money >= $productInfo['exp_buy'] && $activity_expmoney){
            $this->load->model('base/exp_cd_base', 'exp_cd_base');
            $cd_info = $this->exp_cd_base->get($this->uid, $pid);
            $cd_info = false;
            if(!$cd_info){
                $this->load->model('logic/expmoney_logic','expmoney_logic');

                if(substr(trim($productInfo['exp_send']), 0, 1) == '*'){
                    $multiple = substr($productInfo['exp_send'], 1, 3);
                    if($multiple > 3){
                        $multiple = 3;
                    }
                    $send_expmoney = $money * $multiple;
                }else{
                    if($productInfo['exp_send'] == '+'){
                        $send_expmoney = $money;
                    }else{
                        $send_expmoney = $productInfo['exp_send'];
                    }
                }
                $add_exp = true;
                //添加体验金
                $this->expmoney_logic->add_expmoney($this->uid, $send_expmoney);
                $exp_balance = $this->expmoney_logic->get_expmoney($this->uid);
                //添加体验金日志
                $exp_log_data = array(
                    'uid' => $this->uid,
                    'ctime' => NOW,
                    'log_desc' => '购买'. $productInfo['pname'].'赠送',
                    'money' => $send_expmoney,
                    'action' => EXPMONEY_LOG_ADD,
                    'balance'  => $exp_balance
                );
                $log_data = $this->expmoney_logic->addLog($this->uid, $exp_log_data);
                $cd_info = $this->exp_cd_base->set($this->uid, $pid);
            }
        }
        $this->load->model('logic/activity_logic', 'activity_logic');
            //新手购买送现金活动,1月旺不参与送现金活动,且金额必须大于200元
        	if($productInfo['ptid'] == 46){
            	if($money>=100){
	                $add_activity_money = $this->activity_logic->checkAndSendUserGiveMoneyActivity($this->uid, $userIdentity['realname'], $this->account, $pid, $money);
	                if($ret){
	                    $balance += $add_activity_money;
	                }
            	}
        	}
        	
        	$luckyflag = false;
        	if($userIdentity['isnew'] == 1){
        		$this->load->model('base/luckybag_base' , 'luckybag_base');
        		$had = $this->luckybag_base ->get_cached_luckybag($account);
        		if(!empty($had)){
        			if($had['etime']>NOW){
        				$canUseFlag = false;
        				if(!empty($had['usetype'])){
        					if($had['usetype']==1){
        						if($money>=$had['goumaimoney']){
				        			$luckybagPtidArray = explode(",", $had['ptids']);
				        			if(in_array($ptid,$luckybagPtidArray)){
				        				$canUseFlag=true;
				        			}
        						}
        					}else{
        						if($money>=$had['money']*$had['goumaibeishu']){
				        			$luckybagPtidArray = explode(",", $had['ptids']);
				        			if(in_array($ptid,$luckybagPtidArray)){
				        				$canUseFlag=true;
				        			}
        						}
        					}
        				}else if($money>=$had['money']*100){
        					$canUseFlag=true;
        				}
        				if($canUseFlag){
	        				$luckybag_updatedata = array();
	        				$luckybag_updatedata['utime']=NOW;
	        				$luckybag_updatedata['uuid']=$this->uid;
	        				$luckybag_updatedata['status']=2;
	        	
	        				$had['utime']=NOW;
	        				$had['uuid']=$this->uid;
	        				$had['status']=2;
	        				$luckybagType = $had['type'];
	        	
	        				$this->luckybag_base ->update_luckybag_db_detail($had['uid'],$had,$luckybag_updatedata,$had['id']);
	        	
	        				$this->load->model('base/luckybag_accepted_base' , 'luckybag_accepted_base');
	        				unset($had['noticed']);
	        				unset($had['type']);
	        				$this->luckybag_accepted_base->add($had);
	        	
	        				$uuid_balance = $this->balance_base->get_user_balance($this->uid);
	        				$uuid_balance += $had['money'];
	        				$uuiduser_log_data = array(
	        						'uid' => $this->uid,
	        						'pid' => $had['id'],
	        						'pname' => '邀请红包',
	        						'paytime' => NOW,
	        						'money' => $had['money'],
	        						'balance' => $uuid_balance,
	        						'action' => USER_ACTION_ACTIVITY
	        				);
	        				$this->load->model('base/user_log_base', 'user_log_base');
	        				$this->user_log_base->addUserLog($this->uid, $uuiduser_log_data);
	        				$ret = $this->balance_base->add_user_balance($this->uid, $had['money']);
	        	
	        				$uid_balance = $this->balance_base->get_user_balance($had['uid']);
	        				$uid_balance += $had['money'];
	        				$uiduser_log_data = array(
	        						'uid' => $had['uid'],
	        						'pid' => $had['id'],
	        						'pname' => '邀请红包',
	        						'paytime' => NOW,
	        						'money' => $had['money'],
	        						'balance' => $uid_balance,
	        						'action' => USER_ACTION_ACTIVITY
	        				);
	        				$this->load->model('base/user_log_base', 'user_log_base');
	        				$this->user_log_base->addUserLog($had['uid'], $uiduser_log_data);
	        				$ret = $this->balance_base->add_user_balance($had['uid'], $had['money']);
	        	
	        				$this->load->model('logic/invite_logic', 'invite_logic');
	        				$this->invite_logic->update_my_buytime($this->uid, $had['uid'], 0);
        					if($luckybagType==2){
        						@$this->user_jifeng_duihuan_base->useDuihuan($had['id']);
        					}
        					$luckyflag=true;
        				}
        			}
        		}
        	}
                
            //2017双11活动
            $this->config->load('cfg/festivity_cfg', true, true);
            $festivity = $this->config->item('cfg/festivity_cfg');
            if(strtotime($festivity['buff_stime']) <= NOW && strtotime($festivity['buff_etime']) >= NOW){
                $this->load->model('logic/festivity_logic', 'festivity_logic');
                $this->festivity_logic->double_activity($this->uid, $money, $userIdentity, $festivity);
            }
            //复投活动奖励
            $this->config->load('cfg/festivity_cfg', true, true);
            $festivity = $this->config->item('cfg/festivity_cfg');
            if(strtotime($festivity['activety_fu_stime']) <= NOW && strtotime($festivity['activety_fu_etime']) >= NOW){
                $this->load->model('logic/festivity_logic', 'festivity_logic');
                $this->festivity_logic->futou_activity($this->uid, $account, $money,$productInfo, $userIdentity['isnew'], $festivity);
            }
            //复投活动奖励end
        	if(!$luckyflag){
	            //好友邀请奖励
	            if(defined('INVITE') && INVITE == 'true'){
	                $this->activity_logic->invite_activity($this->uid, $account, $money, $productInfo,$userIdentity['isnew']);
	            }
        	}
        	//双十二活动
            $this->load->model('logic/festivity_logic', 'festivity_logic');
            $this->festivity_logic->double_twelve_activity_one($this->uid, $account, $productInfo, $money);
            $this->festivity_logic->double_twelve_activity_two($this->uid, $account, $productInfo);
            //$this->festivity_logic->double_twelve_activity_three();
            //end

            $this->activity_logic->checkAndAddUserIntegral($this->account, $money, $ptid,$this->uid,$pid,$productInfo['pname']);
            //---运营数据-----  老新用户第一次购买定期
            $this->load->model('base/user_base', 'user_base');
            $account = $this->user_base->getAccountInfo($this->uid);
            if(date('Y-m-d', $account['ctime']) != date('Y-m-d') && $userIdentity['isnew'] == 1){
                $this->load->model('base/olduser_base', 'olduser_base');
                $olduser_data = array();
                $olduser_data['uid'] = $this->uid;
                $olduser_data['ctime'] = mktime(0,0,0);
                $olduser_data['plat'] = $account['plat'];

                $this->olduser_base->del_uid($this->uid);
                $this->olduser_base->add($olduser_data);
            }
            //活期转定期
            if($userIdentity['h_isnew'] == 0 && $userIdentity['isnew'] == 1){
                $this->load->model('base/hzd_olduser_base', 'hzd_olduser_base');
                $olduser_data = array();
                $olduser_data['uid'] = $this->uid;
                $olduser_data['ctime'] = mktime(0,0,0);
                $olduser_data['plat'] = $account['plat'];

                $this->hzd_olduser_base->del_uid($this->uid);
                $this->hzd_olduser_base->add($olduser_data);
            }
        
        $new_count = array('singlcount'=>0,'moneycount'=>0);
        if($userIdentity['isnew'] == 1){
        	$this->user_identity_base->set_isnew($this->uid);
        	$this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
        	$new_count = $this->coupon_activity_logic->sendCoupon(COUPON_ACTIVITY_FIRSTBUY,$money,$this->uid,$account['account']);
        	$this->activity_logic->addUserIntegral($account['account'],50,$this->uid,JIFENG_FIRSTBUY);
        }
        
        $this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
        $count = $this->coupon_activity_logic->sendCoupon(COUPON_ACTIVITY_BUY,$money,$this->uid,$account['account']);
        
        $data['couponcounts'] = $new_count['singlcount']+$count['singlcount'];
        if($data['couponcounts']>0){
        	$this->load->model('logic/msm_logic', 'msm_logic');
        	$coupontotals = $new_count['moneycount']+$count['moneycount'];
        	$this->msm_logic->send_coupon_user_msg($account['account'],$userIdentity['realname'], $data['couponcounts'],$coupontotals);
        	$notice_data = array(
        			'uid' => $this->uid,
        			'title' => '抵用券获得提醒',
        			'content' => "恭喜您获得了".$data['couponcounts']."张共价值".$coupontotals."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！",
        			'ctime' => NOW
        	);
        	$this->user_notice_base->addNotice($this->uid,$notice_data);
        }
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $this->userproduct_base->moveUserSumProductMoney($this->uid);
        
	       	$this->load->model('logic/luckybag_logic', 'luckybag_logic');
	        $this->luckybag_logic->addLuckybagForUser($ptid,$money,$this->uid,$pid,$account['account'],$userIdentity['realname']);
        //---------
        $data['balance'] = $balance - $money;
        $data['cost'] = $money;
        $data['trxid'] = $trxid;
        $data['add_exp'] = $add_exp;
        if($add_exp){
            $data['exp_add'] = $send_expmoney;
            $data['exp_balance'] =  $exp_balance;
        }
        $response = array('error'=> 0, 'data'=> $data, 'activity' => $ret);
        $this->out_print($response);
    }
    
    
    public function longproduct(){
        if(NOW < mktime(1, 0, 0)){
            $response = array('error'=> 4018, 'msg'=>'凌晨1点开始售卖');
            $this->out_print($response);
        }
        $pid = $this->input->post('pid');
        $money = $this->input->post('money');
        $paytype = $this->input->post('paytype');
        if(!$paytype){
            $paytype = 1;
        }
        $money = floor($money);
        $money = strval($money);
        $tpwd = $this->input->post('tpwd');
        $gateId = '61';
        if($money <= 0){
            $response = array('error'=> 4019, 'msg'=>'金额错误');
            $this->out_print($response);
        }
        //验证交易密码
        $userIdentity = $this->user_identity_base->getUserIdentity($this->uid);
        if(!$userIdentity || !$userIdentity['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $tpwd_times = $this->pay_redis_base->getbuytpwdtimes($userIdentity['phone']);
        if((!empty($tpwd_times))&&$tpwd_times>=3){
        	$response = array('error'=> 4040, 'msg'=>'支付密码已尝试3次，请3小时后再试');
        	$this->out_print($response);
        }
        if($userIdentity['tpwd'] != $tpwd){
        	$this->pay_redis_base->incrbuytpwdtimes($userIdentity['phone']);
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->pay_redis_base->delbuytpwdtimes($userIdentity['phone']);
        
        $this->load->model('logic/longproduct_logic', 'longproduct_logic');
        $longproductInfo = $this->longproduct_logic->getLongProductDetail($pid);
        if(!$longproductInfo){
            $response = array('error'=> 4022, 'msg'=>'产品不存在');
            $this->out_print($response);
        }
        if($longproductInfo['status'] != 1 || $longproductInfo['uptime'] > NOW){
            $response = array('error'=> 4023, 'msg'=>'产品还末上线或已下标');
            $this->out_print($response);
        }
        $ltid = $longproductInfo['ptid'];
        $longproductid = $this->longproduct_logic->getOnlineLongProductListFirstMem($ltid);
        if($longproductid != $pid){
            $response = array('error'=> 4124, 'msg'=>'产品还末上线或已下标');
            $this->out_print($response);
        }
        $this->load->model('base/userlongproduct_base', 'userlongproduct_base');
        $user_longproduct_max = $this->userlongproduct_base->get_user_longproduct_max($this->uid, $pid);
        if($user_longproduct_max + $money > $longproductInfo['money_max']){
            $response = array('error'=> 4220, 'msg'=> '超过可购买上限,限买金额:' . ($longproductInfo['money_max'] - $user_longproduct_max));
            $this->out_print($response);
        }
        if(($userIdentity['isnew'] == 0 || $userIdentity['h_isnew'] == 0)){
            if($longproductInfo['ptid'] == NEW_LONGPRODUCT_PTID){
                $response = array('error'=> 4220, 'msg' => '只有新手方可购买此产品');
                $this->out_print($response);
            }
        }
        if($money < $longproductInfo['startmoney'] ||
            ($money - $longproductInfo['startmoney']) % $longproductInfo['money_limit'] != 0 || //累进金额不能小
            $money > $longproductInfo['money_max']){
            $response = array('error'=> 4024, 'msg'=>'金额错误');
            $this->out_print($response);
        }
        $sellMoney = $this->longproduct_logic->rsyncLongProductSellMoney($pid);
        if($sellMoney >= $longproductInfo['money']){
            $response = array('error'=> 4025, 'msg'=>'产品已卖完');
            $this->out_print($response);
        }
        if($longproductInfo['money'] - $sellMoney < $money){
            $response = array('error'=> 4035, 'msg'=>'产品剩余金额不足');
            $this->out_print($response);
        }
        $this->load->model('base/balance_base', 'balance_base');
        $balance = $this->balance_base->get_user_balance($this->uid);
        if($balance < $money){
            $response = array('error' => 3000, 'msg' => '余额不足');
            $this->out_print($response);
        }
        $ret = $this->balance_base->cost_user_balance($this->uid, $money);
        if(!$ret){
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['pid'] = $pid;
            $err_data['ptype'] = 'longproduct';
            $err_data['money'] = $money;
            $err_data['balance'] = $balance;
            $response = array('error'=> 3333, 'msg'=> '余额不足');
            $this->out_print($response, 'json',  true,  true, $err_data);
        }
        
        $account = $this->getCookie('account');
        $trxid = $this->longproduct_logic->buy_longproduct($this->uid, $longproductInfo, $userIdentity, $money, $account, $paytype, $balance);
        if(!$trxid){
            $response = array('error'=> 4026, 'msg'=>'购买失败，请重试');
            $this->out_print($response);
        }
        if($userIdentity['h_isnew'] == 1){
            $this->user_identity_base->set_h_isnew($this->uid);
            $this->load->model('base/user_base', 'user_base');
            $account = $this->user_base->getAccountInfo($this->uid);
            if(date('Y-m-d', $account['ctime']) != date('Y-m-d') && $userIdentity['h_isnew'] == 1){
                $this->load->model('base/h_olduser_base', 'h_olduser_base');
                $h_olduser_data = array();
                $h_olduser_data['uid'] = $this->uid;
                $h_olduser_data['ctime'] = mktime(0,0,0);
                $h_olduser_data['plat'] = $account['plat'];
                $this->h_olduser_base->add($h_olduser_data);
            }
        }
        $data = array();
        $this->userlongproduct_base->set_user_longproduct_max($this->uid, $pid, $money);
        
        //最后一笔，产品设为售馨
        if($money == $longproductInfo['money'] - $sellMoney){
            $this->longproduct_logic->setLongProductSellOut($longproductInfo['ptid'], $pid);
        }
        $this->longproduct_logic->rsyncLongProductSellMoney($pid);
        $data['balance'] = $balance;
        $data['cost'] = $money;
        $data['trxid'] = $trxid;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function klproduct(){
        if(NOW < mktime(1, 0, 0)){
            $response = array('error'=> 4018, 'msg'=>'凌晨1点开始售卖');
            $this->out_print($response);
        }
        $pid = $this->input->post('pid');
        $money = $this->input->post('money');
        $paytype = $this->input->post('paytype');
    
        if(!$paytype){
            $paytype = 1;
        }
        $money = floor($money);
        $money = strval($money);
        $tpwd = $this->input->post('tpwd');
        if($money <= 0){
            $response = array('error'=> 4019, 'msg'=>'金额错误');
            $this->out_print($response);
        }
        //验证交易密码
        $userIdentity = $this->user_identity_base->getUserIdentity($this->uid);
        if(!$userIdentity || !$userIdentity['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        if($userIdentity['tpwd'] != $tpwd){
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->load->model('logic/klproduct_logic', 'klproduct_logic');
        $klproductInfo = $this->klproduct_logic->getKlProductDetail($pid);
        if(!$klproductInfo){
            $response = array('error'=> 4022, 'msg'=>'产品不存在');
            $this->out_print($response);
        }
        if($klproductInfo['status'] != 1 || $klproductInfo['uptime'] > NOW){
            $response = array('error'=> 4023, 'msg'=>'产品还末上线或已下标');
            $this->out_print($response);
        }
        $ltid = $klproductInfo['ptid'];
        $klproductid = $this->klproduct_logic->getOnlineklproductListFirstMem($ltid);
        if($klproductid != $pid){
            $response = array('error'=> 4124, 'msg'=>'产品还末上线或已下标');
            $this->out_print($response);
        }
        if($money < $klproductInfo['startmoney'] ||
            ($money - $klproductInfo['startmoney']) % $klproductInfo['money_limit'] != 0 || //累进金额不能小
            $money > $klproductInfo['money_max']
        ){
            $response = array('error'=> 4024, 'msg'=>'购买金额错误');
            $this->out_print($response);
        }
    
        $sellMoney = $this->klproduct_logic->rsyncklproductSellMoney($pid);
    
        if($sellMoney >= $klproductInfo['money']){
            $response = array('error'=> 4025, 'msg'=>'产品已卖完');
            $this->out_print($response);
        }
        if($klproductInfo['money'] - $sellMoney < $money){
            $response = array('error'=> 4035, 'msg'=>'产品剩余金额不足');
            $this->out_print($response);
        }
        $this->load->model('base/balance_base', 'balance_base');
        $balance = $this->balance_base->get_user_balance($this->uid);
        if($balance < $money){
            $response = array('error' => 3000, 'msg' => '余额不足');
            $this->out_print($response);
        }
        $ret = $this->balance_base->cost_user_balance($this->uid, $money);
        if(!$ret){
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['pid'] = $pid;
            $err_data['ptype'] = 'longproduct';
            $err_data['money'] = $money;
            $err_data['balance'] = $balance;
            $response = array('error'=> 3333, 'msg'=> '余额不足');
            $this->out_print($response, 'json',  true,  true, $err_data);
        }
        
        $account = $this->getCookie('account');
        $trxid = $this->klproduct_logic->buy_klproduct($this->uid, $klproductInfo, $userIdentity, $money, $account, $paytype, $balance);
        if(!$trxid){
            $response = array('error'=> 4026, 'msg'=>'购买失败，请重试');
            $this->out_print($response);
        }
        if($userIdentity['h_isnew'] == 1){
            $this->user_identity_base->set_h_isnew($this->uid);
            $this->load->model('base/user_base', 'user_base');
            $account = $this->user_base->getAccountInfo($this->uid);
            if(date('Y-m-d', $account['ctime']) != date('Y-m-d') && $userIdentity['h_isnew'] == 1){
                $this->load->model('base/h_olduser_base', 'h_olduser_base');
                $h_olduser_data = array();
                $h_olduser_data['uid'] = $this->uid;
                $h_olduser_data['ctime'] = mktime(0,0,0);
                $h_olduser_data['plat'] = $account['plat'];
                $this->h_olduser_base->add($h_olduser_data);
            }
        }
        //最后一笔，产品设为售馨
        if($money == $klproductInfo['money'] - $sellMoney){
            $this->klproduct_logic->setKlProductSellOut($klproductInfo['ptid'], $pid);
        }
        $this->klproduct_logic->rsyncKlProductSellMoney($pid);
        $data = array();
        $data['balance'] = $balance;
        $data['cost'] = $money;
        $data['trxid'] = $trxid;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
//     public function equalproduct(){
//         if(NOW < mktime(1, 0, 0)){
//             $response = array('error'=> 4018, 'msg'=>'凌晨1点开始售卖');
//             $this->out_print($response);
//         }
//         $pid = $this->input->post('pid');
//         $money = $this->input->post('money');
//         $paytype = $this->input->post('paytype');   //购买方式 1为余额购买  2为银行卡购买～
//         if(!$paytype){
//             $paytype = 1;
//         }
//         $money = floor($money);
//         $money = strval($money);
//         $tpwd = $this->input->post('tpwd');
//         if($money <= 0){
//             $response = array('error'=> 4019, 'msg'=>'金额错误');
//             $this->out_print($response);
//         }
//         $this->load->model('logic/equalproduct_logic', 'equalproduct_logic');
//         //验证交易密码
//         $userIdentity = $this->user_identity_base->getUserIdentity($this->uid);
//         if(!$userIdentity || !$userIdentity['tpwd']){
//             $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
//             $this->out_print($response);
//         }
//         if($userIdentity['tpwd'] != $tpwd){
//             $response = array('error'=> 4021, 'msg'=>'交易密码错误');
//             $this->out_print($response);
//         }
//         $productInfo = $this->equalproduct_logic->getEqualProductDetail($pid);
//         //         if($productInfo['ptid'] == NEW_USER_PTID && $userIdentity['isnew'] != 1){
//         //             $response = array('error'=> 4121, 'msg'=>'只有新手可以购买新手标');
//         //             $this->out_print($response);
//         //         }
//         if(!$productInfo){
//             $response = array('error'=> 4022, 'msg'=>'产品不存在');
//             $this->out_print($response);
//         }
//         if($productInfo['status'] != 1 || $productInfo['uptime'] > NOW){
//             $response = array('error'=> 4023, 'msg'=>'产品还末上线或已下标');
//             $this->out_print($response);
//         }
//         $this->load->model('base/equalproduct_base', 'equalproduct_base');
//         $ptid = $productInfo['ptid'];
//         $productid = $this->equalproduct_base->getOnlineEqualProductListFirstMem($ptid);
//         if($productid != $pid){
//             $response = array('error'=> 4124, 'msg'=>'产品还末上线或已下标');
//             $this->out_print($response);
//         }
//         //print_r($productInfo);
//         //print_r($productInfo);
//         if($money < $productInfo['startmoney'] ||
//             ($money - $productInfo['startmoney']) % $productInfo['money_limit'] != 0 || //累进金额不能小
//             $money > $productInfo['money_max']){
//             $response = array('error'=> 4024, 'msg'=>'金额错误');
//             $this->out_print($response);
//         }
//         $sellMoney = $this->equalproduct_logic->rsyncEqualProductSellMoney($pid);
//         if($sellMoney >= $productInfo['money']){
//             $response = array('error'=> 4025, 'msg'=>'产品已卖完');
//             $this->out_print($response);
//         }
//         if($productInfo['money'] - $sellMoney < $money){
//             $response = array('error'=> 4035, 'msg'=>'产品剩余金额不足');
//             $this->out_print($response);
//         }
//         $this->load->model('base/balance_base', 'balance_base');
//         $balance = $this->balance_base->get_user_balance($this->uid);
//         if($balance < $money){
//             $response = array('error'=> 4035, 'msg'=>'余额不足');
//             $this->out_print($response);
//         }
//         $data = array();
//         //扣除余额
//         $this->load->model('base/balance_base' , 'balance_base');
//         $ret = $this->balance_base->cost_user_balance($this->uid, $money);
//         if(!$ret){
//             $err_data = array();
//             $err_data['uid'] = $this->uid;
//             $err_data['pid'] = $pid;
//             $err_data['ptype'] = 'product';
//             $err_data['money'] = $money;
//             $err_data['balance'] = $balance;
//             $response = array('error'=> 3333, 'msg'=> '余额不足');
//             $this->out_print($response, 'json',  true,  true, $err_data);
//         }
//         $account = $this->getCookie('account');
//         $trxid = $this->equalproduct_logic->buy_equalproduct($this->uid, $productInfo, $userIdentity, $money, $account, $paytype, $balance);
//         if(!$trxid){
//             $response = array('error'=> 4026, 'msg'=>'购买失败，请重试');
//             $this->out_print($response);
//         }
//         //再次同步
//         $sellMoney = $this->equalproduct_logic->rsyncEqualProductSellMoney($pid);
//         if($productInfo['money'] == $sellMoney){
//             $this->equalproduct_logic->setEqualProductSellOut($productInfo['ptid'], $pid);
//         }
//         //送体验金
//         $add_exp = false;
//         $send_expmoney = 0;
//         $exp_balance = 0;

//         $activity_expmoney = true;
//         $this->load->model('logic/invite_logic', 'invite_logic');
//         $invite_cfg = $this->invite_logic->getCfg();
//         $this->load->model('base/user_base', 'user_base');
//         if(strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW
//             && $userIdentity['isnew'] == 1){
//             $activity_expmoney = false;
//         }
//         if($productInfo['exp_buy'] > 0 && $productInfo['exp_send'] && $money >= $productInfo['exp_buy'] && $activity_expmoney){
//             $this->load->model('base/exp_cd_base', 'exp_cd_base');
//             $cd_info = $this->exp_cd_base->get($this->uid, $pid);
//             $cd_info = false;
//             if(!$cd_info){
//                 $this->load->model('logic/expmoney_logic','expmoney_logic');
//                 if(substr(trim($productInfo['exp_send']), 0, 1) == '*'){
//                     $multiple = substr($productInfo['exp_send'], 1, 3);
//                     if($multiple > 3){
//                         $multiple = 3;
//                     }
//                     $send_expmoney = $money * $multiple;
//                 }else{
//                     if($productInfo['exp_send'] == '+'){
//                         $send_expmoney = $money;
//                     }else{
//                         $send_expmoney = $productInfo['exp_send'];
//                     }
//                 }
//                 $add_exp = true;
//                 //添加体验金
//                 $this->expmoney_logic->add_expmoney($this->uid, $send_expmoney);
//                 $exp_balance = $this->expmoney_logic->get_expmoney($this->uid);
//                 //添加体验金日志
//                 $exp_log_data = array(
//                     'uid' => $this->uid,
//                     'ctime' => NOW,
//                     'log_desc' => '购买'. $productInfo['pname'].'赠送',
//                     'money' => $send_expmoney,
//                     'action' => EXPMONEY_LOG_ADD,
//                     'balance'  => $exp_balance
//                 );
//                 $log_data = $this->expmoney_logic->addLog($this->uid, $exp_log_data);
//                 $cd_info = $this->exp_cd_base->set($this->uid, $pid);
//             }
//         }
//         $this->load->model('logic/activity_logic', 'activity_logic');
//         if($productInfo['ptid'] != NEW_USER_PTID){
//             $account_info = $this->user_base->getAccountInfo($this->uid);
//             if($userIdentity['isnew'] == 1){
//                 //新手购买送现金活动
//                 $add_activity_money = $this->activity_logic->checkAndSendUserGiveMoneyActivity($this->uid, $userIdentity['realname'], $this->account, $pid, $money);
//                 if($ret){
//                     $balance += $add_activity_money;
//                 }
//                 //                 $this->user_identity_base->set_isnew($this->uid);
//             }
//             if($userIdentity['isnew'] == 1){
//                 $this->user_identity_base->set_isnew($this->uid);
//             }
//             //好友邀请奖励
//             if(defined('INVITE') && INVITE == true){
//                 $this->activity_logic->invite_activity($this->uid, $account, $money, $productInfo);
//             }
//             //--------818积分活动  start ----------------------
//             $this->activity_logic->checkAndAddUserIntegral($this->account, $money, $ptid);
//             //-------818积分活动   end  ------------------------
//             //---运营数据-----  老新用户第一次购买
//             $this->load->model('base/user_base', 'user_base');
//             $account = $this->user_base->getAccountInfo($this->uid);
//             if(date('Y-m-d', $account['ctime']) != date('Y-m-d') && $userIdentity['isnew'] == 1){
//                 $this->load->model('base/olduser_base', 'olduser_base');
//                 $olduser_data = array();
//                 $olduser_data['uid'] = $this->uid;
//                 $olduser_data['ctime'] = mktime(0,0,0);
//                 $olduser_data['plat'] = $account['plat'];
//                 $this->olduser_base->add($olduser_data);
//             }
//         }
//         //---------
//         $data['balance'] = $balance - $money;
//         $data['cost'] = $money;
//         $data['trxid'] = $trxid;
//         $data['add_exp'] = $add_exp;
//         if($add_exp){
//             $data['exp_add'] = $send_expmoney;
//             $data['exp_balance'] =  $exp_balance;
//         }
//         $response = array('error'=> 0, 'data'=> $data, 'activity' => $ret);
//         $this->out_print($response);
//     }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */