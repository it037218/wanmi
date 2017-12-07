<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class redbag extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/redbag_logic', 'redbag_logic');
        $this->load->model('logic/login_logic', 'login_logic');
        $this->load->model('base/redbag_base' , 'redbag_base');
    }
    
    public function getRedBag(){
    	if(defined('RED_BAG') && RED_BAG == true){
    		$code = $this->input->post('code');
    		$phone = $this->input->post('phone');
    		if(!$this->_check_mobile($phone)){
    			$response = array('error'=> 1, 'msg'=>'号码格式错误');
    		}else{
	    		$response = $this->redbag_logic->getRedBag($code,$phone);
    		}
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 1, 'msg' => '红包活动已结束');
    		$this->out_print($response);
    	}
    }
    
    public function showRedbagDetail(){
    	$money = $this->input->request('money');
    	$isnew = $this->input->request('isnew');
    	$account = $this->input->request('account');
    	$code = $this->input->request('code');
    	$data['money'] = $money;
    	$data['isnew'] = $isnew;
    	$data['account'] = $account;
    	$list = $this->redbag_base->get_user_redbag_list($code);
    	$data['list'] = $list;
    	$this->load->view('v_redbagResult', $data);
    }
    
    public function ini_red_bag(){
    	$code = $this->input->request('code');
    	$data = $this->redbag_logic->init_red_bag($code);
    	$data['code'] = $code;
    	$this->load->view('v_redbag', $data);
    }
}
