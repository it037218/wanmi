<?php

class coupon_activity_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/coupon_activity_base' , 'coupon_activity_base');
        $this->load->model('base/user_coupon_base' , 'user_coupon_base');
        $this->load->model('base/user_notice_base', 'user_notice_base');
    }
    
    public function sendCoupon($type,$buymoney,$uid,$account){
    	$singlcount = 0;
    	$moneycount = 0;
    	try {
    		if($type==COUPON_ACTIVITY_BUY){
    			$times = $this->coupon_activity_base ->incr_coupon_send_flag($uid);
    			if($times>1){
    				return array('singlcount'=>$singlcount,'moneycount'=>$moneycount);
    			}
    		}
	    	$activityInfo = $this->coupon_activity_base ->getCouponActivityInfo($type);
	    	if($activityInfo){
	    		foreach ($activityInfo as $activity){
		    		if($activity['etime']>NOW && $activity['stime']<NOW){
		    			if($type==COUPON_ACTIVITY_BUY||$type==COUPON_ACTIVITY_FIRSTBUY){//购买,首购赠送时判断购买金额
			    			if($buymoney<$activity['buymoney']){
			    				continue;
			    			}
		    			}
		    			$activityDetail = $this->coupon_activity_base ->getCouponActivityDetail($type,$activity['id'],$activity['cids']);
		    			if($activityDetail){
		    				foreach ($activityDetail as $value){
		    					if(!empty($value['etime'])|| !empty($value['days'])){
		    						$data['id']= date('YmdHis') . mt_rand(1000,9999);
		    						$data['name']=$value['name'];
			    					$data['uid']=$uid;
			    					$data['account']=$account;
			    					$data['ctime'] = NOW;
			    					$data['sendmoney'] = $value['sendmoney'];
			   						$data['type'] = $type;
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
			   						$this->user_coupon_base->addCoupon($uid,$data);
			   						$singlcount++;
			   						$moneycount = $moneycount+$value['sendmoney'];
		    					}
		    				}
		    			}
		    		}
	    		}
	    	}
    	}catch (Exception $e){
    		
    	}
    	return array('singlcount'=>$singlcount,'moneycount'=>$moneycount);
    }
    
    public function sendJifengCoupon($uid,$id,$account){
    	$couponDetail = $this->coupon_activity_base ->getCouponDetail($id);
    	if($couponDetail){
    		$data['id']= date('YmdHis') . mt_rand(1000,9999);
    		$data['name']=$couponDetail['name'];
    		$data['uid']=$uid;
    		$data['account']=$account;
    		$data['ctime'] = NOW;
    		$data['sendmoney'] = $couponDetail['sendmoney'];
    		$data['type'] = COUPON_ACTIVITY_JIFENG;
    		$data['ptids'] = $couponDetail['ptids'];
    		$data['pnames'] = $couponDetail['pnames'];
    		$data['minmoney'] = $couponDetail['minmoney'];
    		if($couponDetail['days']==0){
    			if(empty($couponDetail['stime'])){
    				$data['stime'] = NOW;
    			}else{
    				$data['stime'] = $couponDetail['stime'];
    			}
    			$data['etime'] = $couponDetail['etime'];
    		}else{
    			$data['stime'] = NOW;
    			$data['etime'] = strtotime(date('Y-m-d',time()))+$couponDetail['days']*86400-1;
    		}
    		$insertid = $this->user_coupon_base->addCoupon($uid,$data);
    		if($insertid){
    			$notice_data = array(
    					'uid' => $uid,
    					'title' => '抵用券获得提醒',
    					'content' => "恭喜您获得了 1 张共价值".$couponDetail['sendmoney']."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去看看吧！",
    					'ctime' => NOW
    			);
    			$this->user_notice_base->addNotice($uid,$notice_data);
    			return array('insertid'=>$data['id'],'realmoney'=>$couponDetail['sendmoney'],'money'=>$couponDetail['sendmoney']);
    		}else{
    			return false;
    		}
    	}else{
    		return false;
    	}
    }
}
