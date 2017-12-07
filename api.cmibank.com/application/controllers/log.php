<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class log extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('base/pay_error_log_base', 'pay_error_log_base');
    }
    
    

    public function save_pay_error_log(){
//         error_reporting(E_ALL);
        $uid = $this->input->post('uid');
        $orderid = $this->input->post('orderid');
        $ret_code = $this->input->post('ret_code');
        $ret_msg = $this->input->post('ret_msg');
        
        $data = array(
            'uid' => $uid,
            'orderid' => $orderid,
            'ret_code' => $ret_code,
            'ret_msg' => $ret_msg
        );
        return $this->pay_error_log_base->createOrder($data);
    }
    
}



/* End of file test.php */
/* Location: ./application/controllers/test.php */