<?php

class luckybag_logic extends CI_Model {

	private $_cfg ;
	
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/luckybag_base' , 'luckybag_base');
    }
    
    public function addLuckybagForUser($ptid,$money,$uid,$pid,$phone,$uname){
    	$luckybagActivity = $this->luckybag_base->getLuckyActivity();
    	if(!empty($luckybagActivity)){
	    	if($luckybagActivity['stime'] <= NOW && $luckybagActivity['etime'] >= NOW){
	    		if($money>=$luckybagActivity['buymoney']){
	    			$luckbagDetail = $this->luckybag_base->getLuckyDetail($luckybagActivity['lid']);
	    			if(!empty($luckbagDetail)){
			    		$bagmoney = $luckbagDetail['money'];
			    		if($luckbagDetail['type']==2){
			    			$bagmoney = round($money*$luckbagDetail['bili']/100);
			    		}
			    		if($bagmoney>=1){
			    			$bagmoney = $bagmoney>=50?50:$bagmoney;
			    			$data['money'] = $bagmoney;
			    			$data['uid'] = $uid;
			    			$data['pid'] = $pid;
			    			$data['ctime'] = NOW;
			    			$data['usetype'] = $luckbagDetail['usetype'];
			    			$data['goumaimoney'] = $luckbagDetail['goumaimoney'];
			    			$data['goumaibeishu'] = $luckbagDetail['goumaibeishu'];
			    			$data['ptids'] = $luckbagDetail['ptids'];
			    			$data['pnames'] = $luckbagDetail['pnames'];
			    			$data['etime'] = strtotime(date('Y-m-d',time()))+86400*$luckbagDetail['days']+86399;
			    			$count=1;
			    			if($ptid=='43'){
			    				$count=3;
			    			}else if($ptid=='40'||$ptid=='44'){
			    				$count=2;
			    			}
			    			for($index=1;$index<=$count;$index++){
				    			$data['id']= date('YmdHis') . mt_rand(1000,9999);
				    			$this->luckybag_base->add($data['uid'],$data);
			    			}
			    			$this->load->model('logic/msm_logic', 'msm_logic');
			    			$this->msm_logic->send_luckybag_reve_msg($phone, $count,$bagmoney,$uname);
			    			$this->load->model('base/user_notice_base', 'user_notice_base');
			    			$notice_data = array(
			    					'uid' => $uid,
			    					'title' => '现金红包获得提醒',
			    					'content' => "恭喜您获得了".$count."个".$bagmoney."元的现金红包，赶快去【我的资产-红包】看看吧！",
			    					'ctime' => NOW
			    			);
			    			$this->user_notice_base->addNotice($uid,$notice_data);
			    		}
	    			}
	    		}
	    	}
    	}
    }
    
    public function addJifengLuckybagForUser($money,$uid,$phone){
    			$data['money'] = $money;
    			$data['uid'] = $uid;
    			$data['pid'] = 0;
    			$data['ctime'] = NOW;
    			$data['type'] = 2;
    			$data['etime'] = strtotime(date('Y-m-d',time()))+86400*7+86399;
    			$data['id']= date('YmdHis') . mt_rand(1000,9999);
    			$this->luckybag_base->add($uid,$data);
    			$this->load->model('logic/msm_logic', 'msm_logic');
    			$this->load->model('base/user_notice_base', 'user_notice_base');
    			$notice_data = array(
    					'uid' => $uid,
    					'title' => '现金红包获得提醒',
    					'content' => "恭喜您获得了 1 个".$money."元的现金红包，赶快去【我的资产-红包】看看吧！",
    					'ctime' => NOW
    			);
    			$ret = $this->user_notice_base->addNotice($uid,$notice_data);
    			if($ret){
    				return $data['id'];
    			}else{
    				return false;
    			}
    }
    
    public function getLuckyBag($uid,$phone,$lid){
    	$luckybag = $this->luckybag_base->getLuckybagDetailByid($uid,$lid);
    	if(empty($luckybag)){
    		$response = array('error'=> 1, 'msg' => '红包已被领走');
    	}else{
    		if($luckybag['status']==1){
    			$response = array('error'=> 1, 'msg' => '红包已被领走');
    		}else{
    			$had = $this->luckybag_base ->get_cached_luckybag($phone);
    			if(empty($had)){
    				$counts = $this->luckybag_base->incr($lid);
    				if($counts>1){
    					$response = array('error'=> 1, 'msg' => '红包已被领走');
    				}else{
    					$updatedata = array();
    					$updatedata['stime']= NOW;
    					$updatedata['status']=1;
    					$updatedata['uuaccount']=$phone;
    	
    					$luckybag['stime']= NOW;
    					$luckybag['status']=1;
    					$luckybag['uuaccount']=$phone;
    	
    					$this->luckybag_base->update_luckybag_db_detail($uid,$luckybag,$updatedata,$lid);
    					$response = array('error'=> 0, 'msg' => $luckybag['money'].'元红包已存入您的易米融账户，下载即可获取！','money' =>$luckybag['money']);
    				}
    			}else{
    				$response = array('error'=> 2, 'msg' => '您已领取过红包   ','data' =>$had);
    			}
    		}
    	}
    	return $response;
    }
    
    public function useLuckyBag($uid){
    	
    }
    
    public function getCfg(){
    	if($this->_cfg){
    		return $this->_cfg;
    	}
    	$this->config->load('cfg/lucky_cfg', true, true);
    	$this->_cfg = $this->config->item('cfg/lucky_cfg');
    	return $this->_cfg;
    }
    
    public function getLuckybagDetailByid($uid,$id){
        $ret = $this->luckybag_base->getLuckybagDetailByid($uid,$id);
        return $ret;
    }
    
	public function setNoticed($uid,$id){
    	$ret = $this->luckybag_base->setNoticed($uid,$id);
        return $ret;
    }
    public function getUserLuckybagList($uid,$account){
    	$list = $this->luckybag_base->get_user_luckybag_list($uid);
    	$ret = $this->luckybag_base->get_cached_luckybag($account);
    	if ($ret){
    		$list[] = $ret;
    		
    	}
    	return $list;
    }
}


   
