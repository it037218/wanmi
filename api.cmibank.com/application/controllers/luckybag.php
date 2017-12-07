<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class luckybag extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/luckybag_logic', 'luckybag_logic');
        $this->load->model('logic/login_logic', 'login_logic');
    }
    
    public function iniLuckybag(){
    	$code = $this->input->get('code');
    	$lid = $this->input->get('lid');
    	$data['code'] = $code;
    	$uid = $this->decode_invite($code);
    	$data['lid'] = $lid;
    	$luckybagDetail = $this->luckybag_logic->getLuckybagDetailByid($uid,$lid);
    	if($luckybagDetail){
    		if($luckybagDetail['status']==0){
    			$data['error']=0;
    		}else{
    			$data['error']=1;
    		}
    		$data['money']=$luckybagDetail['money'];
    	}else{
    		$data['error']=1;
    	}
    	$data['luckybagDetail'] = $luckybagDetail;
    	$this->load->view('luckybag', $data);
    }
}
