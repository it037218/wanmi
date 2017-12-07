<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 凌晨取现
 */
class weehours_withdraw extends Controller {

    public function __construct($lock = true)
    {
        parent::__construct();
        if($lock == true){
            $response = array('error'=> 4001, 'msg'=>'错误的系统调用关系!');
            $this->out_print($response);
        }
        $this->check_login($lock);
    }
    
//     public function save_withdraw_order(){
//         die('error api name@');
//         $amount = trim($this->input->post('amount'));
//         if(!$amount || $amount <= 0){
//             $response = array('error'=> 4017, 'msg'=>'请输入取现金额');
//             $this->out_print($response);
//         }
//         if($amount < 1){
//             $response = array('error'=> 4017, 'msg'=>'最低取现1元');
//             $this->out_print($response);
//         }
//         //取现次数限制  测试先不用
//         $this->load->model('logic/cd_logic', 'cd_logic');
//         $cd_data = $this->cd_logic->getUserCd($this->uid);
 
//         if($cd_data['withDraw'] <= 0){
//             $response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
//             $this->out_print($response);
//         }
//         $cd_data['withDraw']--;
//         $money = $amount;
//         //交易密码 测试先不用
//         $tpwd = trim($this->input->post('tpwd'));
//         $this->load->model('base/user_identity_base', 'user_identity_base');
//         $identity_result = $this->user_identity_base->getUserIdentity($this->uid);
//         if(empty($identity_result)){
//             $response = array('error' => 4019, 'msg' => '用户信息错误');
//             $this->out_print($response);
//         }
//         if(!$identity_result || !$identity_result['tpwd']){
//             $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
//             $this->out_print($response);
//         }
//         if($identity_result['tpwd'] != $tpwd){
//             $response = array('error'=> 4021, 'msg'=>'交易密码错误');
//             $this->out_print($response);
//         }
//         $this->cd_logic->setUserCd($this->uid, $cd_data);
        
//         $this->load->model('logic/user_identity_logic', 'user_identity_logic');
//         $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all');
        
//         if(empty($identity_result) || $identity_result['ischeck'] != 1){
//             $response = array('error'=> 4000, 'msg'=>'请先绑定银行卡信息');
//             $this->out_print($response);
//         }
//         $this->load->model('base/balance_base', 'balance_base');
//         $balance = $this->balance_base->get_user_balance($this->uid);
//         if($balance < $money){
//             $response = array('error' => 12002, 'msg' => '用户余额不足');
//             $this->out_print($response);
//         }
//         $id_num = $identity_result['idCard'];
//         $id_name = $identity_result['realname'];
//         $bank_code = $identity_result['bankcode'];
//         $this->config->load('cfg/banklist', true, true);
//         $banklist = $this->config->item('cfg/banklist');
//         if(!isset($banklist[$bank_code])){
//             $response = array('error'=> 4001, 'msg'=>'占不支持此银行');
//             $this->out_print($response);
//         }
        
//         //$identity_result['cardno'] = '6226091210143311';      //尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
        
//         $bank_name = $banklist[$bank_code]['name'];
//         $account_no = $identity_result['cardno'];
        
//         $this->config->load('cfg/jytpay_config', true, true);
//         $jytpay_config = $this->config->item('cfg/jytpay_config');
        
//         $ret = $this->balance_base->cost_user_balance($this->uid, $money);
//         if(!$ret){
//             $err_data = array();
//             $err_data['uid'] = $this->uid;
//             $err_data['pid'] = 0;
//             $err_data['ptype'] = 'product';
//             $err_data['money'] = $money;
//             $err_data['balance'] = $balance;
//             $response = array('error'=> 3333, 'msg'=> '余额不足');
//             $this->out_print($response, 'json',  true,  true, $err_data);
//         }
//         //生成内部订单  早上跑脚本  返回内部订单号
//         $orderid = 'jyt' . date('YmdHis') . $this->uid . mt_rand(1000,9999);
        
//         //写用户日志
//         $user_log_data = array(
//             'uid' => $this->uid,
//             'pid' => 0,
//             'pname' => '取现处理中',
//             'orderid' => $orderid,
//             'money' => $money,
//             'balance' => $balance - $money,
//             'action' => USER_ACTION_PCASHOUT
//         );
//         $this->load->model('base/user_log_base', 'user_log_base');
//         $last_id = $this->user_log_base->addUserLog($this->uid, $user_log_data);
//         //保存内部订单
//         $log_data = array(
//             'uid' => $this->uid,
//             'nb_orderid' => $orderid,
//             'money' => $money,
//             'balance' => $balance - $money,
//             'logid' => $last_id
//         );
//         $this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
//         $this->weehours_withdraw_log->addLog($log_data);
        
//         $response = array('error' => 0, 'data' => array('balance' => $balance - $money));
//         $this->out_print($response);
//     }
    
    
    public function new_save_withdraw_order(){
        
        $amount = trim($this->input->post('amount'));
        
        if(!$amount || $amount <= 0){
            $response = array('error'=> 4017, 'msg'=>'请输入取现金额');
            $this->out_print($response);
        }
        if($amount < 1){
            $response = array('error'=> 4017, 'msg'=>'最低取现1元');
            $this->out_print($response);
        }
        //取现次数限制  测试先不用
        $this->load->model('logic/cd_logic', 'cd_logic');
        $cd_data = $this->cd_logic->getUserCd($this->uid);
    
        if($cd_data['withDraw'] <= 0){
            $response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
            $this->out_print($response);
        }
        
        //交易密码 测试先不用
        $tpwd = trim($this->input->post('tpwd'));
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $identity_result = $this->user_identity_base->getUserIdentity($this->uid);
        if(empty($identity_result)){
            $response = array('error' => 4019, 'msg' => '用户信息错误');
            $this->out_print($response);
        }
        if($identity_result['fengkong'] == 1){
        	$response = array('error'=> 4001, 'msg'=>'当前账户存在异常交易行为，已列为风险账户，如有疑问，请致电 400-871-9299');
        	$this->out_print($response);
        }
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $defulatWithdraw = $this->pay_redis_base->getDefaultWithdraw();
        $defulatWithdraw = empty($defulatWithdraw)?0:1;
        $tpwd_times = $this->pay_redis_base->getwithdrawtpwdtimes($identity_result['phone']);
        if((!empty($tpwd_times))&&$tpwd_times>=3){
        	$response = array('error'=> 4040, 'msg'=>'支付密码已尝试3次，请3小时后再试');
        	$this->out_print($response);
        }
        
        if($identity_result['tpwd'] != $tpwd){
        	$this->pay_redis_base->incrwithdrawtpwdtimes($identity_result['phone']);
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $this->pay_redis_base->delwithdrawtpwdtimes($identity_result['phone']);
        
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all');
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error'=> 4000, 'msg'=>'请先绑定银行卡信息');
            $this->out_print($response);
        }
        $money = $amount;
        $cost_money = $amount;
        
        $this->load->model('base/balance_base', 'balance_base');
        $balance = $this->balance_base->get_user_balance($this->uid);
        if($balance < $money){
            $response = array('error' => 12002, 'msg' => '用户余额不足');
            $this->out_print($response);
        }
        $ret = $this->check_withdraw_money($this->uid, $cost_money);
        if(!$ret){
            $response = array('error' => 12006, 'msg' => '账目金额不符,请联系客服!');
            $this->out_print($response);
        }
        $id_num = $identity_result['idCard'];
        $id_name = $identity_result['realname'];
        $bank_code = $identity_result['bankcode'];
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        if(!isset($banklist[$bank_code])){
            $response = array('error'=> 4001, 'msg'=>'占不支持此银行');
            $this->out_print($response);
        }
        $cd_data['withDraw']--;
        $orderid = date('YmdHis') . $this->uid . mt_rand(1000,9999);
        
        if($cd_data['free_withDraw'] > 0){      //如果有免费次数就减免费次数
            $cd_data['free_withDraw']--;
        }else{                                  //如果没有就扣手续费
            $money -= WITHDRAW_SXF;
            if($money <= 0){
                $response = array('error'=> 4118, 'msg'=>'您当前账户余额不足' . WITHDRAW_SXF . '元, 无法提现, 请明日重试');
                $this->out_print($response);
            }
            //记录手续费情况
            $this->load->model('base/withdraw_sxf_log_base', 'withdraw_sxf_log');
            $withdraw_sxf_log = array(
                'uid' => $this->uid,
                'orderid' => $orderid,
                'money' => $cost_money,
                'sxf' => WITHDRAW_SXF,
                'sd_money' => $money,
                'ctime' => time()
            );
            $this->withdraw_sxf_log->createLog($withdraw_sxf_log);
        }
        
        $this->cd_logic->setUserCd($this->uid, $cd_data);
        //$identity_result['cardno'] = '6226091210143311';      //尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
        $bank_name = $banklist[$bank_code]['name'];
        $account_no = $identity_result['cardno'];
    
        $this->config->load('cfg/jytpay_config', true, true);
        $jytpay_config = $this->config->item('cfg/jytpay_config');
    
        $ret = $this->balance_base->cost_user_balance($this->uid, $cost_money);
        if(!$ret){
            $err_data = array();
            $err_data['uid'] = $this->uid;
            $err_data['pid'] = 0;
            $err_data['ptype'] = 'product';
            $err_data['money'] = $cost_money;
            $err_data['balance'] = $balance;
            $response = array('error'=> 3333, 'msg'=> '余额不足');
            $this->out_print($response, 'json',  true,  true, $err_data);
        }
        //生成内部订单  早上跑脚本  返回内部订单号
        
    
        //写用户日志
        $user_log_data = array(
            'uid' => $this->uid,
            'pid' => 0,
            'pname' => '取现处理中',
            'orderid' => $orderid,
            'money' => $cost_money,
            'balance' => $balance - $cost_money,
            'action' => USER_ACTION_PCASHOUT
        );
        $this->load->model('base/user_log_base', 'user_log_base');
        $last_id = $this->user_log_base->addUserLog($this->uid, $user_log_data);
        $userlogid = trim($this->input->post('userlogid'));
        if (!empty($userlogid)){
        	$last_id = $last_id . ',' . $userlogid;
        }
            //保存内部订单
            $shenghe=1;
            if($money>=8000){
            	$shenghe=0;
            }
            $log_data = array(
                'uid' => $this->uid,
                'nb_orderid' => $orderid,
                'money' => $money,
                'balance' => $balance - $cost_money,
                'logid' => $last_id,
            	'shenghe' =>$shenghe,
            	'plat' =>$defulatWithdraw,
            	'ctime' =>NOW
            );
            $this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
            $this->weehours_withdraw_log->addLog($log_data);
        $response = array('error' => 0, 'data' => array('balance' => $balance - $cost_money,'amount'=>$cost_money));
        $this->out_print($response);
    }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */