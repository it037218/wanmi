<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class expmoney extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        $this->load->model('logic/expmoney_logic', 'expmoney_logic');
        $this->load->model('base/user_jifeng_duihuan_base', 'user_jifeng_duihuan_base');
    }

    public function getExpmoneyBalance(){
        $expmoney = $this->expmoney_logic->get_expmoney($this->uid);
        $response = array('error'=> 0, 'data' => array('expmoney' => $expmoney));
        $this->out_print($response);
    }
    
    public function getUserExpMoneyInfo(){
        //余额
        $balance_expmoney = $this->expmoney_logic->get_expmoney($this->uid);
        //在投
        $in_expmoney = $this->expmoney_logic->getUserExpProductCount($this->uid);
        //所有投过金额
        $all_expmoney = $this->expmoney_logic->getUserAllExpProductCount($this->uid);
        //总共
        $return_data = array();
        $return_data['balance'] = $balance_expmoney;                    //体验金余额
        $return_data['use'] = $in_expmoney;                             //在投体验金余额
        $return_data['all'] = $balance_expmoney + $all_expmoney;        //累计获得体验金总额
        $response = array('error'=> 0, 'data' => $return_data);
        $this->out_print($response);
    }
    
    public function getExpmoneyLog(){
        $page = $this->input->post('page');
        $page = max(1, $page);
        $start = ($page - 1) * 10;
        $offset = $start + 9;
        $log_data = $this->expmoney_logic->getLog($this->uid, $start, $offset);
        $response = array('error'=> 0, 'data' => $log_data);
        $this->out_print($response);
    }
    
    public function buy(){
        $activity_money = array(1288, 2688);
        $money = $this->input->post('money');
        if($money <= 0){
            $response = array('error'=> 4034, 'msg'=>'金额错误');
            $this->out_print($response);
        }
        $income = 8;
        $this->load->model('logic/expmoney_logic', 'expmoney_logic');
        $exp_money = $this->expmoney_logic->get_expmoney($this->uid);
        if($exp_money < $money){
            $response = array('error'=> 4035, 'msg'=>'体验金余额不足!');
            $this->out_print($response);
        }
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $userIdentity = $this->user_identity_base->getUserIdentity($this->uid);
        if(!in_array($money, $activity_money)){
            if(!$userIdentity || $userIdentity['isnew'] == 1){
                $response = array('error'=> 4037, 'msg'=>'购买过定期产品后方可使用!');
                $this->out_print($response);
            }
        }
        $ret = $this->expmoney_logic->cost_expmoney($this->uid, $money);
        if($exp_money < $money){
            $response = array('error'=> 4036, 'msg'=>'体验金余额不足!');
            $this->out_print($response);
        }
        
        $trxId = date('Ymds'). $this->uid . mt_rand(100, 999) . 'bep';
        $product_data = array(
            'uid' => $this->uid,
            'money' => $money,
            'trxId' => $trxId,
            'uietime' => date('Y-m-d', strtotime('+6 day')),
            'status' => 0,
            'income' => $income,
        );
        $ret = $this->expmoney_logic->addUserExpProduct($this->uid, $product_data);
        if(!$ret){
            $response = array('error'=> 4037, 'msg'=>'购买失败,请重新尝试!');
            $this->out_print($response);
            //$this->expmoney_logic->add_expmoney($this->uid, $money);
        }
        $balance = $this->expmoney_logic->get_expmoney($this->uid);
        
        $this->expmoney_logic->addExpMoney_using($this->uid, $money);
        $this->load->model('base/expmoney_using_base', 'expmoney_using_base');
        $data = array(
            'uid' => $this->uid,
            'ctime' => NOW,
            'log_desc' => '使用体验金',
            'money' => $money,
            'balance' => $balance,
            'exp_using' => $this->expmoney_using_base->get_user_expmoney_using($this->uid),
            'trxId' => $trxId,
            'action' => EXPMONEY_LOG_COST,
        );
        $log_data = $this->expmoney_logic->addLog($this->uid, $data);
        $return_data = array();
        $return_data['money'] = $money;
        $return_data['trxId'] = $trxId;
        $return_data['exp_balance'] = $balance;
        $response = array('error'=> 0, 'data'=> $return_data);
        $this->out_print($response);
    }
    
    public function tiyan(){
    	$this->load->model('base/user_expmoney_base' , 'user_expmoney_base');
    	$eid = trim($this->input->post('eid'));
    	if(empty($eid)){
    		$response = array('error'=> 4066, 'msg'=>'未找到体验金记录');
    		$this->out_print($response);
    	}
    	$expmoneyDetail = $this->user_expmoney_base->getUserExpmoneyDetail($this->uid,$eid);
    	if(empty($expmoneyDetail)){
    		$response = array('error'=> 6065, 'msg'=>'未找到体验金记录');
    		$this->out_print($response);
    	}
    	if($expmoneyDetail['etime']<NOW){
    		$response = array('error'=> 6061, 'msg'=>'该体验金已过期');
    		$this->out_print($response);
    	}
    	if(!empty($expmoneyDetail['utime'])){
    		$response = array('error'=> 6063, 'msg'=>'该体验金已体验');
    		$this->out_print($response);
    	}
    	if(!empty($expmoneyDetail['status'])){
    		$response = array('error'=> 6063, 'msg'=>'该体验金已体验');
    		$this->out_print($response);
    	}
        $identity = $this->user_expmoney_base->getAccount($this->uid);
        if(!$identity){
            $response = array('error'=> 6067, 'msg'=>'请绑定银行卡');
            $this->out_print($response);
        }

        if($identity && $identity['isnew'] == 1){
            $response = array('error'=> 6068, 'msg'=>'活动期间需投资后才能使用');
            $this->out_print($response);
        }
        
    	$expmoney_update['utime']=NOW;
    	$expmoney_update['status']=1;
    	$expmoney_update['uietime']= strtotime(date('Y-m-d',time()))+$expmoneyDetail['days']*86400+86399;
    	$ret = $this->user_expmoney_base->updateExpmoney($expmoney_update,$eid,$this->uid);
    	if($expmoneyDetail['type']==4){
    		@$this->user_jifeng_duihuan_base->useDuihuan($eid);
    	}
    	
    	if($ret){
    		$response = array('error'=> 0, 'msg'=>'成功使用体验金');
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 6064, 'msg'=>'体验金使用失败');
    		$this->out_print($response);
    	}
    }
    
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */