<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class llpay_wap extends Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/user_logic', 'user_logic');
        $this->load->model('logic/llpay_wap_logic', 'llpay_wap_logic');
//         $this->check_link();
        $this->check_login();

    }

    public function bulidPayHtml(){ 
        error_reporting(E_ALL);
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all'); 

        $this->load->model('base/user_base', 'user_base');
        $user_account_info = $this->user_base->getAccountInfo($this->uid);
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error' => 11999, 'msg' => '请先进行绑卡操作!');
            $this->out_print($response);
        }
        $tpwd = trim($this->input->post('pwd'));
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        if($identity_result['tpwd'] != $tpwd){
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $acct_name = $identity_result['realname'];                  //银行卡用户名
        $id_no = $identity_result['idCard'];                        //身份证号
        $card_no = $identity_result['cardno'];                      //银行卡号

        $money = $this->input->request('money_order');              //充值金额
        $ordid = date('YmdHis') . $this->uid . mt_rand(1000,9999);
        $id_no = (string)$id_no;

        $risk_item = array(
            'frms_ware_category' => 2009,
            'user_info_mercht_userno' => $this->uid,
            'user_info_bind_phone' => $user_account_info['account'],
            'user_info_dt_register' => date("YmdHis",$user_account_info['ctime']),
            'user_info_full_name' => $acct_name,
            'user_info_id_type' => 0,
            'user_info_id_no' => $id_no,
            'user_info_identify_state' => 0,
            'user_info_identify_type' => 4,
        );
        $risk_item_json = json_encode($risk_item);
        $risk_item_json = addslashes($risk_item_json);

        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
        $cd_data['pay']--;
        $this->cd_logic->setUserCd($this->uid, $cd_data);
        
        $parameter = array (
            "user_id" => $this->uid,
            "busi_partner" => '101001',
            "no_order" => "$ordid",
            "dt_order" => date('YmdHis'),
            "name_goods" => "投资币",
            "info_order" => '充值',
            "money_order" => $money,
            "card_no" => $card_no,
            "acct_name" => $acct_name,
            "id_no" => $id_no,
            "risk_item" => $risk_item_json,
            "valid_order" => '30',
        );
        $return_data = $this->llpay_wap_logic->bulidRequestForm($parameter);

        //创建购买订单
        $pay_log = array(
            'uid' => $this->uid,
            'ordid' => $ordid,
            'amt' => $money,
            'platform' => 'llpay',
            'curcode' => 'RMB',
            'ctime' => NOW,
            'status' => 0,
        );
        $return_data = json_decode($return_data, true);
        $this->load->model('base/pay_log_base' , 'pay_log');
        $this->pay_log->createOrder($pay_log);
        $response = array('error'=> 0, 'data' => $return_data);
        $this->out_print($response);
    }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */