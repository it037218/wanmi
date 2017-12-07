<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 凌晨取现
 */

class weehours_withdraw_crontab extends Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            die('only cli model can run!');
        }
    }

    public function run_weehours_withdraw_order() {
        error_reporting(E_ALL);
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $flag = $this->pay_redis_base->getWithdraw();
        
        if ($flag) {
            $response = array('error' => 111111, 'msg' => '取现已关闭！');
            $this->out_print($response, 'json', false);
        }
        
        $lock_ret = $this->pay_redis_base->addredislock('run_weehours_withdraw_order', 3600);
        
        if (!$lock_ret){
            $response = array('error' => 111111, 'msg' => '脚本正在运行！');
            $this->out_print($response, 'json', false);
        }
        
        $this->load->model('base/weehours_withdraw_log_base', 'weehours_withdraw_log');
        $order_list = $this->weehours_withdraw_log->getDrawLogTableList(array('status' => 0, 'shenghe' => 1));
        $this->load->model('logic/fuioupay_logic', 'fuioupay_logic');
        $this->load->model('base/user_log_base', 'user_log_base');
        $this->load->model('base/balance_base', 'balance_base');
        $this->load->model('base/pay_log_base', 'pay_log');
        $this->load->model('base/withdraw_log_base', 'withdraw_log');
        $this->config->load('cfg/banklist', true, true);
        $banklist = $this->config->item('cfg/banklist');
        foreach ($order_list as $_order) {
            $times = $this->pay_redis_base->incrweehourstimes($_order['nb_orderid']);
            if ($times > 1) {
                echo '||' . $_order['nb_orderid'] . '.repeat!||';
                continue;
            }
            $uid = $_order['uid'];
            $this->load->model('logic/user_identity_logic', 'user_identity_logic');
            $identity_result = $this->user_identity_logic->getPublicUserIdentity($uid, 'all');
            $id_num = $identity_result['idCard'];
            $id_name = $identity_result['realname'];
            $bank_code = $identity_result['bankcode'];

            if (!isset($banklist[$bank_code])) {
                $response = array('error' => 4001, 'msg' => '暂不支持此银行');
                $lock_ret = $this->pay_redis_base->delredislock('run_weehours_withdraw_order');
                $this->out_print($response);
            }
            $bank_name = $banklist[$bank_code]['name'];
            $account_no = $identity_result['cardno'];
            $this->config->load('cfg/fuiou_config', true, true);
            $fuiou_config = $this->config->item('cfg/fuiou_config');
            $money = $_order['money'];
            $balance = $this->balance_base->get_user_balance($uid);
            $last_id = $_order['logid'];
            $isfind = strpos($_order['logid'], ',');
            if ($isfind) {
                $update_logid = explode(',', $_order['logid']);
            } else {
                $update_logid = $_order['logid'];
            }
            
            $orderid = $fuiou_config['withdraw_merchant_id'] . date('YmdHis') . rand(100000, 999999);    //请根据商户系统自行定义订单号
            $return_data = $this->fuioupay_logic->withdraw($uid, $orderid, $money, $id_num, $id_name, $bank_name, $account_no);
            if (!$return_data) {
                $response = array('error' => 4002, 'msg' => '验签失败');
                $lock_ret = $this->pay_redis_base->delredislock('run_weehours_withdraw_order');
                $this->out_print($response);
            }
            
            //服务器记录
            $this->wee_withdraw_log(json_encode($return_data));
            if($return_data['ret'] == '000000') {
                $withdraw_log = array();
                $update_data = array('orderid' => $orderid, 'pname' => '取现处理中', 'paytime' => time());
                //系统取现日志
                $withdraw_log['uid'] = $uid;
                $withdraw_log['orderid'] = $orderid;
                $withdraw_log['ybdrawflowid'] = $orderid;
                $withdraw_log['status_code'] = '';
                $withdraw_log['money'] = $money;
                $withdraw_log['logid'] = $last_id;
                $withdraw_log['plat'] = 'fuiou';
                $this->withdraw_log->addLog($withdraw_log);
                $update_weehours_data = array(
                    'status' => 1,
                    'orderid' => $orderid,
                    'utime' => NOW
                );
                $this->weehours_withdraw_log->updateDrawLog($update_weehours_data, array('id' => $_order['id']));
            } else {   //失败
                $withdraw_log['uid'] = $uid;
                $withdraw_log['orderid'] = $orderid;
                $withdraw_log['ybdrawflowid'] = $orderid;
                $withdraw_log['status_code'] = '1';
                $withdraw_log['money'] = $money;
                $withdraw_log['logid'] = $last_id;
                $withdraw_log['plat'] = 'fuiou';
                $insert_log_id = $this->withdraw_log->addLog($withdraw_log);
                //更改用户日志
                $update_data = array('orderid' => $orderid, 'pname' => '取现失败(' . $return_data['memo']. ',17点前退回账户)', 'paytime' => time(), 'action' => USER_ACTION_WITHDRAWFAILED);
                if (is_array($update_logid)) {
                    foreach ($update_logid as $userlogid) {
                        $this->user_log_base->updateUserLogByIdForWithdrawNotify($uid, $userlogid, $update_data, false);
                    }
                } else {
                    $update_where = array('id' => $update_logid);
                    $ret = $this->user_log_base->updateUserLogOnlyWithDraw($uid, array('all', 'out'), $update_data, $update_where);
                }

                $drawlog_data = array('back_status' => 'FAILED', 'status' => 1, 'succtime' => time());
                $where = array('id' => $insert_log_id);
                $ret = $this->withdraw_log->updateDrawLog($drawlog_data, $where);

                $update_weehours_data = array(
                    'status' => 2,
                    'orderid' => $orderid
                );
                $this->weehours_withdraw_log->updateDrawLog($update_weehours_data, array('id' => $_order['id']));
                //                 $this->balance_base->add_user_balance($uid, $money, true);
                $err_data = array();
                $err_data['uid'] = $uid;
                $err_data['money'] = $money;
                $err_data['ordid'] = $orderid;
                $err_data['balance'] = $balance - $money;
                $err_data['msg'] =  $return_data['memo'];
                $err_data['method'] = __METHOD__;
                $response = array('error' => 6666, 'msg' => $return_data['memo']);
                $this->load->model('base/error_log_base', 'error_log_base');
                $this->error_log_base->addLog($response, $err_data);

                $faild_log_data = array(
                    'uid' => $uid,
                    'orderid' => $orderid,
                    'money' => $money,
                    'realname' => $id_name,
                    'bankname' => $bank_name,
                    'bankcode' => $bank_code,
                    'cardNo' => $account_no,
                    'back_code' => $return_data['ret'],
                    'back_msg' => $return_data['memo'],
                    'logid' => $last_id,
                    'plat' => 'fuiou',
                    'ctime' => NOW
                );
                $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
                $this->withdraw_failed_log_base->addFailedLog($faild_log_data);
                echo $orderid . '_failed \n';
            }
        }

        //生成内部订单  早上跑脚本  返回内部订单号
        echo 'OK';
        $lock_ret = $this->pay_redis_base->delredislock('run_weehours_withdraw_order');
    }

//     public function run_weehours_withdraw_order(){
//     }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */