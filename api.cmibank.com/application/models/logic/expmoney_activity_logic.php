<?php

class expmoney_activity_logic extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('base/expmoney_activity_base' , 'expmoney_activity_base');
        $this->load->model('base/user_notice_base', 'user_notice_base');
    }
    
    public function sendExpmoney($type,$uid){
    	try {
	    	$activityDetailList = $this->expmoney_activity_base ->getExpmoneyActivityDetail($type);
	    	if($activityDetailList){
	    		$activityInfo=$activityDetailList[0];
		    	if($activityInfo['etime']>NOW && $activityInfo['stime']<NOW){
		    		$this->load->model('base/user_expmoney_base' , 'user_expmoney_base');
		    		$data['id']= date('YmdHis') . mt_rand(1000,9999);
		    		$data['uid']=$uid;
		    		$data['name']=$activityInfo['expname'];
		    		$data['type'] = $type;
		    		$data['money'] = $activityInfo['money'];
		    		$data['ctime'] = NOW;
		    		$data['etime'] = strtotime(date('Y-m-d',time()))+7*86400+86399;
		    		$data['uietime'] = strtotime(date('Y-m-d',time()))+7*86400+86399;
		    		$data['days'] = $activityInfo['days'];
		    		$data['income'] = $activityInfo['income'];
		    		$this->user_expmoney_base->addExpmoney($uid,$data);
		    		
		    		$notice_data = array(
		    				'uid' => $uid,
		    				'title' => '体验金获得提醒',
		    				'content' => "恭喜您获得了".$activityInfo['money']."元体验金，赶快去【我的资产-体验金券】看看吧！",
		    				'ctime' => NOW
		    		);
		    		
		    		$this->user_notice_base->addNotice($uid,$notice_data);
		    		return true;
	    		}
	    	}
    	}catch (Exception $e){
    	}
    	return false;
    }
    
    public function sendJifengExpmoney($type,$uid,$money){
    				$this->load->model('base/user_expmoney_base' , 'user_expmoney_base');
    				$data['id']= date('YmdHis') . mt_rand(1000,9999);
    				$data['uid']=$uid;
    				$data['name']='积分兑换体验金';
    				$data['type'] = $type;
    				$data['money'] = $money;
    				$data['ctime'] = NOW;
    				$data['etime'] = strtotime(date('Y-m-d',time()))+7*86400+86399;
    				$data['uietime'] = strtotime(date('Y-m-d',time()))+7*86400+86399;
    				$data['days'] = 7;
    				$data['income'] = 8;
    				$insertid = $this->user_expmoney_base->addExpmoney($uid,$data);
    				if($insertid){
	    				$notice_data = array(
	    						'uid' => $uid,
	    						'title' => '体验金获得提醒',
	    						'content' => "恭喜您获得了".$money."元体验金，赶快去【我的资产-体验金券】看看吧！",
	    						'ctime' => NOW
	    				);
	    
	    				$this->user_notice_base->addNotice($uid,$notice_data);
	    				return array('insertid'=>$data['id'],'money'=>$money,'realmoney'=>round($money * 56/ 36500, 2));
    				}else{
    					return false;
    				}
    }
}
