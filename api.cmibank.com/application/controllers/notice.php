<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class notice extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('base/notice_base', 'notice_base');
        //$this->check_login();
    }

    
    public function jj_notice(){
    	usleep(1900000);
    	$uid = $this->getCookie('uid');
    	$this->load->model('base/pay_redis_base', 'pay_redis_base');
    	$sent = $this->pay_redis_base->getAndoridVersion($uid);
    	if($sent=='1'){
	        $this->config->load('cfg/jingji_notice', true, true);
	        $notice = $this->config->item('cfg/jingji_notice');
	        $rtn = array();
	        if($notice){
	            if(strtotime($notice['stime']) < NOW &&  strtotime($notice['etime']) > NOW){
	                $rtn = $notice;
	            }
	        }
	        $data['notice'] = $rtn;
	        $response = array('error'=> 0, 'data'=> $data);
	        $this->out_print($response);
    	}else{
    		$data['notice'] = array();
    		$response = array('error'=> 0, 'data'=> $data);
    		$this->out_print($response);
    	}
    }
    
    public function index(){
        $page = max(1, intval($this->input->post('page')));
        $psize = 20;
        $start = ($page - 1) * $psize;
        $end = $start + $psize - 1;
        $rtn = array();
        $noticelist = $this->notice_base->getNoticelistCode($start, $end);
        $data['list'] = $noticelist;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */