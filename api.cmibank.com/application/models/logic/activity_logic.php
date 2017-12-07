<?php

class activity_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/activity_base' , 'activity_base');
        $this->load->model('base/user_jifeng_base' , 'jifeng_base');
    }
    
    public function getUserActivity($uid, $actid){
        return $this->activity_base->get($uid, $actid);
    }
    
    public function addtUserActivity($uid, $actid, $data){
        return $this->activity_base->add($uid, $actid, $data);
    }
    
    //添加用户积分
    public function checkAndAddUserIntegral($account, $money, $ptid, $uid,$pid=0,$pname=''){
        $actid = 2;
        $this->config->load('cfg/activity_time', true, true);
        $activityCfg = $this->config->item('cfg/activity_time');
        if(!isset($activityCfg[$actid])){
            return false;
        }
        $activityCfg = $activityCfg[$actid];
        $ret = $this->check_give_money_activity_time($activityCfg);     //判断活动开始时间
        if(!$ret){
            return false;
        }
        if(!isset($activityCfg['rate'][$ptid])){
            return false;
        }
        $rate = $activityCfg['rate'][$ptid];
        $integral = round($rate * $money*0.01);
        $rank_ret = $this->activity_base->set_activity_rank_with_actid($actid, $account, $integral);
        if($rank_ret){
        	$jifeng_data = array(
        			'uid' => $uid,
        			'name' => '购买产品-'.$pname,
        			'action' => JIFENG_BUY_PRODUCT,
        			'value' => $integral,
        			'pid' => $pid,
        			'ctime' => NOW
        	);
        	$this->jifeng_base->addJifeng($uid,$jifeng_data);
        }
//         $this->activity_base->set_activity_weekRank_with_actid($actid, $account, $integral);
        return true;
    }
    
    public function addUserIntegral($account,$integral, $uid,$action){
    	$rank_ret = $this->activity_base->set_activity_rank_with_actid(2, $account, $integral);
    	$name='';
    	switch ($action)
    	{
    		case 2:
    			$name='注册';
    			break;
    		case 3:
    			$name='绑卡';
    			break;
    		case 4:
    			$name='首购';
    			break;
    		case 5:
    			$name='奖励积分';
    			break;
    		case 6:
    			$name='签到';
    			break;
    		case 7:
    			$name='累计签到奖励';
    			break;
    		case 51:
    			$name='积分兑换';
    			break;
    	}
    	if($rank_ret){
    		$jifeng_data = array(
    				'uid' => $uid,
    				'name' => $name,
    				'action' => $action,
    				'value' => abs($integral),
    				'ctime' => NOW
    		);
    		$this->jifeng_base->addJifeng($uid,$jifeng_data);
    	}
    	return true;
    }
    
    
    public function qiandao($account,$uid){
    	
    }
    
    public function checkAndSendUserGiveMoneyActivity($uid, $name, $phone, $pid, $money){
        $this->config->load('cfg/activity_time', true, true);
        $activityCfg = $this->config->item('cfg/activity_time');
        $actid = ACTIVITY_GIVE_MONEY;                                   //送钱活动ID
        $activityCfg = $activityCfg[$actid];
        $ret = $this->check_give_money_activity_time($activityCfg);     //判断活动开始时间

        if(!$ret){
//            echo 1;
            return false;
        }
//         $pid = $productDetail['pid'];
//         if(!in_array($productDetail['ptid'], $activityCfg['ptids'])){
//             echo 2;
//             return false;
//         }
//         $ptid = $productDetail['ptid'];
//         if($activityCfg['money_limit'][$ptid] != $productDetail['startmoney']){ //标起购金额达不到活动金额
//             echo 3;
//             return false;
//         }
        $add_activity_money = $money * 0.01;
//         if($add_activity_money > 200){
//             $add_activity_money = 200;
//         }
        
//         $add_activity_money = 0;
//         $stageCfg = $activityCfg['stage'];
//         foreach ($stageCfg as $_stage => $_v){
//             list($_min, $_max) = explode('-', $_stage);
//             if($money >= $_min && $money < $_max){
//                 $add_activity_money = $_v;
//                 break;
//             }
//         }
        //var_dump($add_activity_money);
//         $today = date('Y-m-d');
        $this->load->model('base/activity_log_base', 'activity_log_base');
//         $data = $this->getUserActivity($uid, $actid);
//         if($data){
//             //echo 4;
//             return false;
//         }
//         $nowMoney = $this->activity_log_base->getTodayActivity($today);
//         if($nowMoney >= $activityCfg['day_limit']){
//             echo 7;
//             return false;
//         }
//         $count_money = $this->activity_log_base->IncrTodayActivity($today, $add_activity_money);
//         if($count_money > $activityCfg['day_limit']){
// //            echo 6;
//             return false;
//         }
		$content['pid'] = $pid;
		$content['money'] = $money;
        $activity_data = array('content' => json_encode($content));
        $ret = $this->addtUserActivity($uid, $actid, $activity_data);
        if(!$ret){
            // echo 5;
            return false;
        }
        
        $this->load->model('base/balance_base' , 'balance_base');
        $this->balance_base->add_user_balance($uid, $add_activity_money);
        $this->load->model('logic/msm_logic' , 'msm_logic');
        $ret = $this->msm_logic->send_activity_msg($uid, $name, $phone, $add_activity_money);
        $balance = $this->balance_logic->get_balance($uid);
        $user_log_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => '购买送现金活动',
            'orderid' => 0,
            'money' => $add_activity_money,
            'balance' => $balance,
            'action' => USER_ACTION_ACTIVITY
        );
        $this->load->model('base/user_log_base', 'user_log_base');
        $this->user_log_base->addUserLog($uid, $user_log_data);
        //活动账目日志
        $activity_log_data = array();
        $activity_log_data['actid'] = ACTIVITY_GIVE_MONEY;
        $activity_log_data['uid'] = $uid;
        $activity_log_data['content'] = json_encode(array('givemoney' => $add_activity_money, 'pid' => $pid));
        $activity_log_data['ctime'] = time();
        $this->activity_log_base->createLog($activity_log_data);
        return $add_activity_money;
    }
    

    public function check_give_money_activity_time($activityCfg){
        if(!$activityCfg){
            return false;
        }
        if(!isset($activityCfg['starttime']) || !isset($activityCfg['endtime'])){
            return false;
        }
        if(strtotime($activityCfg['starttime']) > NOW || strtotime($activityCfg['endtime']) < NOW){
            return false;
        }
        return true;
    }
    
    
    public function invite_activity($uid, $account, $money, $productInfo,$isnew){
        $this->load->model('logic/msm_logic', 'msm_logic');
        $this->load->model('logic/invite_logic', 'invite_logic');
        $this->load->model('base/balance_base' , 'balance_base');
        $this->load->model('base/user_notice_base', 'user_notice_base');
        $invite_my = $this->invite_logic->get_invite_my($uid);
        $invite_account = array();
        if(!empty($invite_my)){
            $invite_uid = $invite_my['invite_uid'];
            $cfg = $this->invite_logic->getCfg();
            if($money < $cfg['min_money']){
                return ;
            }
            $invite_balance = $this->balance_base->get_user_balance($invite_uid);
            $first_buy_money = $cfg['first_buy_reward'];
            if($invite_my['buytime'] == 0 && $isnew==1){//自己是否有首次购买定期时间，自己是否是新人
//            if($isnew==0){//自己是否有首次购买定期时间，自己是否是新人
//             	if(($productInfo['ptid'] != 45)&&($productInfo['ptid'] != 47)){
                if(isset($cfg['reward_money'])){
                    $invite_my['buytime'] = NOW;
                    //给邀请我的人首次购买红包
                    $reward_money = $cfg['reward_money'];
                    if(strtotime($cfg['buff_stime']) <= NOW && strtotime($cfg['buff_etime']) >= NOW){
                        $reward_money = $cfg['buff_reward_money'];
                            if(isset($cfg['stage_3']) && isset($cfg['stage_4'])){
                                if(in_array($invite_uid, $cfg['ad_master'])){
                                    foreach ($cfg['stage_4'] as $_stage => $cfg_arr){
                                        list($_min, $_max) = explode('-', $_stage);
                                        if($money >= $_min && $money <= $_max){
                                            $reward_money = $cfg_arr['buff_reward_money'];
                                            $first_buy_money = isset($cfg_arr['first_buy_reward']) ? $cfg_arr['first_buy_reward'] : 0;
                                            break;
                                        }
                                    }
                                }elseif(in_array($invite_uid, $cfg['channel'])){
                                    foreach ($cfg['stage_5'] as $_stage => $cfg_arr){
                                        list($_min, $_max) = explode('-', $_stage);
                                        if($money >= $_min && $money <= $_max){
                                            $reward_money = (strpos($cfg_arr['buff_reward_money'],'%') !== false) ? ((float)$cfg_arr['buff_reward_money']/100)*$money : $cfg_arr['buff_reward_money'];
                                            $first_buy_money = (strpos($cfg_arr['first_buy_reward'],'%') !== false) ? ((float)$cfg_arr['first_buy_reward']/100)*$money : $cfg_arr['first_buy_reward'];
                                            break;
                                        }
                                    }
                                }else{
                                    foreach ($cfg['stage_3'] as $_stage => $cfg_arr){
                                        list($_min, $_max) = explode('-', $_stage);
                                        if($money >= $_min && $money <= $_max){
                                            $reward_money = $cfg_arr['buff_reward_money'];
                                            $first_buy_money = isset($cfg_arr['first_buy_reward']) ? $cfg_arr['first_buy_reward'] : 0;
                                            break;
                                        }
                                    }
                                }
                            
                            	//限活动时间内的总量
                                $this->load->model('base/invite_limit_base', 'invite_limit_base');
                                $invite_num = $this->invite_limit_base->incr();
                                if($cfg['invite_limit'] && $cfg['invite_limit'] > 0 && $invite_num > $cfg['invite_limit']){
                                    $reward_money = $cfg['reward_money'];//超过
                                }
                                
                                //限个人量
//                                 $this->load->model('base/invite_max_reward_base', 'invite_max_reward');
//                                 $invite_num = $this->invite_max_reward->incrSignal($invite_uid);
//                                 if($invite_num > 20){
//                                 	$this->invite_logic->update_my_buytime($uid, $invite_my['invite_uid'], 0);
//                                 	return;
//                                 }
                        }
                        
                        if($cfg['be_invite_limit'] && $cfg['be_invite_limit']>0 && $first_buy_money>0 && $invite_num<($cfg['be_invite_limit']+1)){
                            $user_balance = $this->balance_base->get_user_balance($uid);
                            $ret = $this->balance_base->add_user_balance($uid, $first_buy_money);
                            $log_data = array(
                                'uid' => $uid,
                                'pid' => 0,
                                'paytime' => NOW,
                                'pname' => '邀请首次购买红包',
                                'money' => $first_buy_money,
                                'balance' => $user_balance+$first_buy_money,
                                'action' => USER_ACTION_BE_INVITE,
                                'orderid' => 'yq'.$uid.date('YmdHis').mt_rand(100,999)
                            );

                            $this->user_log_base->addUserLog($uid, $log_data);
                            $this->msm_logic->send_invite_buy_msg($uid, $first_buy_money, $account);
                            
                            $notice_data = array(
                                            'uid' => $uid,
                                            'title' => '邀请首次购买红包提醒',
                                            'content' => "恭喜您获得".$first_buy_money."元的邀请首次购买红包，可在资产余额里面查看。赶紧把这个好消息告诉你的小伙伴吧！",
                                            'ctime' => NOW
                            );
                            
                            $this->user_notice_base->addNotice($uid,$notice_data);
                            
                            $log_data = array();
                            $log_data['uid'] = $uid;
                            $log_data['money'] = $first_buy_money;
                            $log_data['ctime'] = NOW;
                            $this->load->model('base/invite_first_buy_log_base' , 'invite_first_buy_log_base');
                            $this->invite_first_buy_log_base->createLog($log_data);
                        }
                    }
                    
                    //限总量
                    $this->load->model('base/invite_limit_base', 'invite_limit_base');
                    $invite_num = $this->invite_limit_base->incr2();
                    if($cfg['invite_limit'] && $cfg['invite_limit']>0 && $invite_num <($cfg['invite_limit']+1)){//邀请限制人数+1
                            $notice_data = array(
                                'uid' => $invite_uid,
                                'title' => '邀请红包获得提醒',
                                'content' => "恭喜您获得".$reward_money."元的邀请好友活动现金红包，可在资产余额里面查看。赶紧把这个好消息告诉你的小伙伴吧！",
                                'ctime' => NOW
                            );
                            
                            $this->user_notice_base->addNotice($invite_uid,$notice_data);
                        
                            $this->invite_logic->update_my_buytime($uid, $invite_my['invite_uid'], $reward_money,$first_buy_money,$money);
                            
                            $ret = $this->balance_base->add_user_balance($invite_uid, $reward_money);
                            $invite_balance += $reward_money;
                            $invite_log_data = array(
                                'uid' => $invite_uid,
                                'pid' => 0,
                                'paytime' => NOW,
                                'pname' => '邀请好友红包',
                                'money' => $reward_money,
                                'balance' => $invite_balance,
                                'action' => USER_ACTION_INVITE,
                                'orderid' => 'yq'.$invite_uid.date('YmdHis').mt_rand(100,999)
                            );
                            $this->user_log_base->addUserLog($invite_uid, $invite_log_data);
                            $this->load->model('base/user_base' , 'user_base');
                            $invite_account = $this->user_base->getAccountInfo($invite_uid);
                            if(!in_array($invite_uid, $cfg['ad_master'])){
                                $this->msm_logic->send_invite_msg($invite_uid, substr($account, -4), $reward_money, $invite_account['account']);
                            }
                    }
                }
//             	}
            }

            $this->load->model('base/user_identity_base', 'user_identity_base');
            $profit=0;
            if(NOW>$invite_my['itime'] && (NOW-$invite_my['itime'])<$cfg['days']){
	            $profit = $this->countProductProfit(strtotime($productInfo['uistime']), strtotime($productInfo['uietime']), $money, $productInfo['income']);
	            $add_money = $profit * $cfg['transaction_scale'];
	            $add_money = sprintf("%.2f",substr(sprintf("%.3f", $add_money), 0, -1));
	           // 给邀请我的人发购买利息百分比奖励
	            if($add_money >= $cfg['min_yongjing']){
	                $invite_balance += $add_money;
	                $ret = $this->balance_base->add_user_balance($invite_uid, $add_money);
	                $this->load->model('base/user_log_base', 'user_log_base');
	                $invite_log_data = array(
	                    'uid' => $invite_uid,
	                    'pid' => 0,
	                    'paytime' => NOW,
	                    'pname' => '好友佣金奖励',
	                    'money' => $add_money,
	                    'balance' => $invite_balance,
	                    'action' => USER_ACTION_INVITE
	                );
	                $this->user_log_base->addUserLog($invite_uid, $invite_log_data);
	                //添加好友购买利息返现记录
	                $this->load->model('base/user_invitereward_base', 'user_invitereward_base');
	                $inviterewardData = array();
	                $inviterewardData['uid'] = $invite_uid;
	                $inviterewardData['f_uid'] = $uid;
	                $inviterewardData['account'] = $account;
	                $inviterewardData['pid'] = $productInfo['pid'];
	                $inviterewardData['buymoney'] = $money;
	                $inviterewardData['buytime'] = NOW;
	                $inviterewardData['rewardmoney'] = $add_money;
	                $this->user_invitereward_base->add_user_invitereward($invite_uid, $inviterewardData);
	                $inviteuserIdentity = $this->user_identity_base->getUserIdentity($invite_uid);
	                if(!empty($inviteuserIdentity)){
	 	                $this->msm_logic->send_invite_reward_msg($inviteuserIdentity['realname'], $add_money, $invite_my['invite_account']);
	                }else{
	                	$this->msm_logic->send_invite_reward_msg(substr($invite_my['invite_account'], -4), $add_money, $invite_my['invite_account']);
	                }
	            }
	           $invite_invite_my = $this->invite_logic->get_invite_my($invite_my['invite_uid']);
	           if((NOW-$invite_invite_my['itime'])<$cfg['days']){
		           $invite_invite_uid = $invite_invite_my['invite_uid'];
		           $invite_invite_balance = $this->balance_base->get_user_balance($invite_invite_uid);
		           $add_money_invite = $profit * $cfg['second_transaction_scale'];
		           $add_money_invite = sprintf("%.2f",substr(sprintf("%.3f", $add_money_invite), 0, -1));
		           	// 给邀请我的人发购买利息百分比奖励
		           if($add_money_invite >= $cfg['min_yongjing']){
		           		$invite_invite_balance += $add_money_invite;
		           		$ret = $this->balance_base->add_user_balance($invite_invite_uid, $add_money_invite);
		           		$this->load->model('base/user_log_base', 'user_log_base');
		           		$invite_log_data = array(
		           				'uid' => $invite_invite_uid,
		           				'pid' => 0,
		           				'paytime' => NOW,
		           				'pname' => '好友佣金奖励',
		           				'money' => $add_money_invite,
		           				'balance' => $invite_invite_balance,
		           				'action' => USER_ACTION_INVITE
		           		);
		           		$this->user_log_base->addUserLog($invite_invite_uid, $invite_log_data);
		           		//添加好友购买利息返现记录
	           			$this->load->model('base/user_invitereward_base', 'user_invitereward_base');
	           			$inviterewardData = array();
	           			$inviterewardData['uid'] = $invite_invite_uid;
	           			$inviterewardData['f_uid'] = $uid;
	           			$inviterewardData['account'] = $account;
	           			$inviterewardData['pid'] = $productInfo['pid'];
	           			$inviterewardData['buymoney'] = $money;
	           			$inviterewardData['buytime'] = NOW;
	           			$inviterewardData['rewardmoney'] = $add_money_invite;
	           			$this->user_invitereward_base->add_user_invitereward($invite_invite_uid, $inviterewardData);
		           		$inviteinviteuserIdentity = $this->user_identity_base->getUserIdentity($invite_invite_uid);
	               	 	if(!empty($inviteinviteuserIdentity)){
	 	                	$this->msm_logic->send_invite_reward_msg($inviteinviteuserIdentity['realname'], $add_money_invite, $invite_invite_my['invite_account']);
	                	}else{
	                		$this->msm_logic->send_invite_reward_msg(substr($invite_invite_my['invite_account'], -4), $add_money_invite, $invite_invite_my['invite_account']);
	                	}
		           	}
	           }
            }
        }
    }
    
    public function invite_activity_forY($uid, $account, $money, $productInfo){
    	$this->load->model('logic/invite_logic', 'invite_logic');
    	$cfg = $this->invite_logic->getCfg();
    	if(strtotime($cfg['buff_stime']) <= NOW && strtotime($cfg['buff_etime']) >= NOW){
    		$invite_my = $this->invite_logic->get_invite_my($uid);
    		if(!empty($invite_my)){
    			if($invite_my['buytime'] == 0){
	    			$invite_uid = $invite_my['invite_uid'];//邀请人
	    			$this->load->model('base/invite_max_reward_base', 'invite_max_reward');
	    			$invite_num = $this->invite_max_reward->incr_yangmao($invite_uid);
	    			$reward_money = 0;
	    			if (2==$invite_num){
	    				$reward_money = 50;
	    			}elseif (4==$invite_num){
	    				$reward_money = 100;
	    			}elseif (50==$invite_num){
	    				$reward_money = 350;
	    			}elseif (100==$invite_num){
	    				$reward_money = 1000;
	    			}elseif (300==$invite_num){
	    				$reward_money = 4500;
	    			}elseif (500==$invite_num){
	    				$reward_money = 4000;
	    			}
	    			
	    			$this->invite_logic->update_my_buytime($uid, $invite_my['invite_uid'], $reward_money);
	    			
	    			if($reward_money!=0){
	    				$invite_balance = $this->balance_base->get_user_balance($invite_uid);
	    				$ret = $this->balance_base->add_user_balance($invite_uid, $reward_money);
	    				$invite_balance += $reward_money;
	    				$invite_log_data = array(
	    						'uid' => $invite_uid,
	    						'pid' => 0,
	    						'paytime' => NOW,
	    						'pname' => '邀请好友超过' . $invite_num .'位奖励',
	    						'money' => $reward_money,
	    						'balance' => $invite_balance,
	    						'action' => USER_ACTION_INVITE
	    				);
	    				$this->user_log_base->addUserLog($invite_uid, $invite_log_data);
	    			}
    			}
    		}
    	}else{
    		return;
    	}
    }
    
    private function countProductProfit($start, $end, $money, $income){
        $days = (($end - $start)/86400) + 1;
        $profit = $income/100/365 * $money * $days;
        return $profit;
    }
    
}


   
