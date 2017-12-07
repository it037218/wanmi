<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 凌晨取现
 */
class yee_query_balance_crontab extends Controller {

    public function __construct()
    {
        parent::__construct();
        if(!$this->input->is_cli_request()){
            die('only cli model can run!');
        }
    }
    
    public function run(){
        $this->config->load('cfg/jytpay_config', true, true);
        $jytpay_config = $this->config->item('cfg/jytpay_config');
        $orderid = $jytpay_config['merchant_id'] . date('YmdHis') . rand(100000,999999);    //请根据商户系统自行定义订单号
        $this->load->model('logic/jytpay_logic', 'jytpay_logic');
        $data = $this->jytpay_logic->queryWithDrawBalance($orderid);
        $balance = (float)$data->body->balance;
        echo date('Y-m-d h:i:s') . ' : ' . $balance . '\r\n';
        if($balance < 2000000){
            $this->load->model('logic/msm_logic', 'msm_logic');
            $this->msm_logic->send_query_balance_msg('18019710907', $balance);
            $this->msm_logic->send_query_balance_msg('18621871289', $balance);
        }
        echo 'ok';
    }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */