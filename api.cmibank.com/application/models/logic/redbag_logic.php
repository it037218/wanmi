<?php

class redbag_logic extends CI_Model {

	private $_cfg ;
	
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/redbag_base' , 'redbag_base');
        $this->load->model('base/redbag_log_base' , 'redbag_log_base');
    	$this->load->model('logic/user_identity_logic', 'user_identity_logic');
    	$this->load->model('base/balance_base', 'balance_base');
    	$this->load->model('base/user_log_base', 'user_log_base');
    }
    
    public function getRedBag($code,$phone){
    	$cfg = $this->getCfg();
    	if(strtotime($cfg['stime']) <= NOW && strtotime($cfg['etime']) >= NOW){
	    		$redbag = $this->redbag_base->get_redbag_detail($code);
	    		if(empty($redbag)){
	    			$response = array('error'=> 1, 'msg' => '红包已领完');
	    		}else{
	    			if($redbag['status']==0){
	    				$response = array('error'=> 1, 'msg' => '红包已领完');
	    			}else{
    					$uid = $this->login_logic->getUidByAccount($phone);
    					$identity=array();
    					if($uid){
    						$identity = $this->user_identity_logic->getPublicUserIdentity($uid);
    					}
	    				if($redbag['user_type']==1){
	    					if($identity){
	    						return array('error'=> 1, 'msg'=>'此活动仅限未绑卡用户参加哦！');
	    					}
	    				}
	    				if($redbag['user_type']==2){
	    					if(empty($identity)){
	    						return array('error'=> 1, 'msg'=>'此活动仅限绑卡用户参加哦！');
	    					}
	    				}
	    				
	    				$had = $this->redbag_base ->get_user_redbag_money($phone);
	    				if(empty($had)){
	    					$times = $this->redbag_base->incr_user_redbag_money_withCode($phone,$code);
	    					if($times>1){
	    						return array('error'=> 1, 'msg'=>'您已领取过该红包！');
	    					}
		    				$counts = $this->redbag_base->incr($code);
		    				if($redbag['counts']<$counts){
		    					$response = array('error'=> 1, 'msg' => '红包已领完');
		    				}else{
	    						$money = 0;
		    					if($redbag['redbag_type']==1){
		    						$money = $redbag['money'];
		    					}else{
		    						$moneyArray = $this->redbag_base->getRedbagMoney($code);
		    						$money=$moneyArray[$counts-1];
		    					}
		    					if(empty($identity)){//新用户
			    					$log = array();
			    					$log['rid'] = $redbag['id'];
			    					$log['phone'] = $phone;
			    					$log['ctime'] = NOW;
			    					$log['money'] = $money;
			    					$res = $this->redbag_base->set_user_redbag_money($phone,$log,$cfg['expire_second']);
			    					$this->redbag_base->add_user_redbag_to_list($code,$log);
			    					if($res){
			    						if($counts==$redbag['counts']){
			    							$data = array();
			    							$data['status'] = 0;
			    							$data['dtime'] = NOW;
			    							$this->redbag_base->update_redbag_db_detail($data,$code);
			    							$this->redbag_base->remove_count($code);//清除计数器
			    						}
		    							$this->redbag_log_base->create_redbag_log($log);
			    						$response = array('error'=> 0, 'msg' => $money.'元红包已存入您的易米融账户，下载即可获取！','money' =>$money,'isnew'=>1,'account'=>$phone,'code'=>$code);
			    					}else{
			    						$response = array('error'=> 1, 'msg' => '红包已领完');
			    					}
		    					}else{
			    					$log = array();
			    					$log['rid'] = $redbag['id'];
			    					$log['phone'] = $phone;
			    					$log['ctime'] = NOW;
			    					$log['money'] = $money;
			    					$log['utime'] = NOW;
		    						if($counts==$redbag['counts']){
		    							$data = array();
		    							$data['status'] = 0;
		    							$data['dtime'] = NOW;
		    							$this->redbag_base->update_redbag_db_detail($data,$code);
		    							$this->redbag_base->remove_count($code);//清除计数器
		    						}
	    							$this->redbag_log_base->create_redbag_log($log);

	    							$balance = $this->balance_base->get_user_balance($uid);
	    							$balance += $money;
	    							
	    							//写用户日志
	    							$user_log_data = array(
	    									'uid' => $uid,
	    									'pid' => 0,
	    									'pname' => '红包',
	    									'paytime' => NOW,
	    									'money' => $money,
	    									'balance' => $balance,
	    									'orderid' => '0',
	    									'action' => USER_ACTION_ACTIVITY
	    							);
	    							$this->user_log_base->addUserLog($uid, $user_log_data);
	    							$ret = $this->balance_base->add_user_balance($uid, $money);
	    							if($ret){
	    								$this->redbag_base->add_user_redbag_to_list($code,$log);
			    						$response = array('error'=> 0, 'msg' => $money.'元红包已存入您的易米融账户，下载即可获取！','money' =>$money,'isnew'=>0,'account'=>$phone,'code'=>$code);
	    							}else{
	    								$response = array('error'=> 1, 'msg' => '红包已领完');
	    							}
		    					}
		    				}
	    				}else{
	    					$response = array('error'=> 2, 'msg' => '您已领取过红包   ','data' =>$had);
	    				}
	    			}
	    		}
    	}else{
    		$response = array('error'=> 1, 'msg' => '红包活动已结束');
    	}
    return $response;
    }
    
    public function init_red_bag($code){
    	$cfg = $this->getCfg();
    	if(strtotime($cfg['stime']) <= NOW && strtotime($cfg['etime']) >= NOW){
	    		$redbag = $this->redbag_base->get_redbag_detail($code);
	    		if(empty($redbag)){
	    			$response = array('error'=> 1, 'msg' => '红包已领完');
	    		}else{
	    			if($redbag['status']==0){
		    			$list = $this->redbag_base->get_user_redbag_list($code);
	    				$response = array('error'=> 1, 'msg' => '红包已领完','list'=>$list);
	    			}else{
	    				$money=0;
	    				if($redbag['redbag_type']==1){//固定金额
	    					$money = $redbag['money'];
	    				}else{//随机金额
	    					$isset = $this->redbag_base->getRedbagMoney($code);
	    					if(empty($isset)){
		    					$counts = $redbag['counts'];
		    					$ramdArray = array();
		    					$moneyArray = array();
		    					for($index=0;$index<$counts;$index++){
		    						$ramdArray[$index] = rand(1,200);
		    					}
		    					$totalRamd = array_sum($ramdArray);
		    					for($index=0;$index<$counts-1;$index++){
		    						$moneyArray[$index] = floor($redbag['money']*$ramdArray[$index]*100/$totalRamd)/100;
		    						if(empty($moneyArray[$index])){
		    							$moneyArray[$index]=0.01;
		    						}
		    					}
		    					$totalMoney = array_sum($moneyArray);
		    					$moneyArray[$counts-1] = $redbag['money']-$totalMoney;
		    					$this->redbag_base->setRedbagMoney($code,$moneyArray);
	    					}
	    				}
	    				$list = $this->redbag_base->get_user_redbag_list($code);
	    				$response = array('error'=> 0, 'msg' => '红包有效','money' =>$money,'list'=>$list);
	    			}
	    		}
    		}else{
    			$response = array('error'=> 1, 'msg' => '红包已领完');
    		}
    	return $response;
    }
    
    public function randomFloat($min = 0, $max = 10) {
    	return round($min + mt_rand() / mt_getrandmax() * ($max - $min),2);  
    }
    public function getCfg(){
    	if($this->_cfg){
    		return $this->_cfg;
    	}
    	$this->config->load('cfg/redbag_cfg', true, true);
    	$this->_cfg = $this->config->item('cfg/redbag_cfg');
    	return $this->_cfg;
    }
    
    public function get_redbag_detail($code){
        $redbag_detail = $this->redbag_base->get_redbag_detail($code);
        return $redbag_detail;
    }
    
    
    public function update_redbag_db_detail($data, $code){
       return $this->redbag_base->update_redbag_db_detail($data, $code);
    }
    
    //增加红包计数
    public function incr($code){
        return $this->redbag_base->incr($code);
    }
    
    public function set_user_redbag_money($account,$money,$timeout){
        return $this->redbag_base->set_user_redbag_money($account,$money,$timeout);
    }
    
}


   
