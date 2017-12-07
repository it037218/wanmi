<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class activity_luckmoney extends Controller {

    private $no_rob_txt;
    
    private $no_rob_num;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/activity_luckmoney_logic', 'activity_luckmoney_logic');
        $this->no_rob_txt = array(
                        '换个姿势再来一次~',
                        '你来晚了一步….',
                        '万水千山总有情，给个红包行不行',
                        '哎，又慢了'
        );
        $this->no_rob_num = count($this->no_rob_txt) - 1;
    }
    
    public function getLuckMoney(){

        $luckmoneyInfo = $this->activity_luckmoney_logic->getluckmoney();
        //判断时间点
        if($luckmoneyInfo){
            $status = 2;
            if(NOW <= $luckmoneyInfo['lstime']){        //小于开抢时间
                $status = 1;            //预告
            }
            if($luckmoneyInfo['etime'] > 0){
                $status = 3;            //结束
                //如果结束，加上结束延长时间
                if(NOW > ($luckmoneyInfo['etime'] + $luckmoneyInfo['delaytime'] * 60)){
                    $luckmoneyInfo = array();
                    $status = 0;
                }
            }
        }else{
            $status = 0;
        }
        if($luckmoneyInfo){
            unset($luckmoneyInfo['lmoney']);                    //获得金额红包权重
            unset($luckmoneyInfo['ltoweight']);                 //获得金额红包权重
            unset($luckmoneyInfo['ltoweightdown']);             //获得金额红包下降权重
            unset($luckmoneyInfo['delaytime']);                 //红包发完延续时间
            unset($luckmoneyInfo['nobless_text']);              //非获奖祝福语
            unset($luckmoneyInfo['bless_text']);                //获奖祝福语
            unset($luckmoneyInfo['status']);                    //0为未发布 1为发布
            unset($luckmoneyInfo['etime']);                     //红包抢完时间
            unset($luckmoneyInfo['lproportion1']);              //红包权重1金额
            unset($luckmoneyInfo['lproportion2']);              //红包权重2金额
            unset($luckmoneyInfo['lproportion3']);              //红包权重3金额
            unset($luckmoneyInfo['lweight1_money']);                //红包1占比
            unset($luckmoneyInfo['lweight2_money']);                //红包2占比
            unset($luckmoneyInfo['lweight3_money']);                //红包3占比
        }
        $response = array('error'=> 0, 'data' => array('luckmoneyDetail' => $luckmoneyInfo, 'status' => $status));
        $this->out_print($response);
    }
    
    //抢红包
    public function robluckmoney(){
        $this->check_login();
        $rob_end = 2;
        
        $this->load->model('logic/activity_luckmoney_logic', 'activity_luckmoney_logic');
        $luckmoneyInfo = $this->activity_luckmoney_logic->getluckmoney();
        
        if(empty($luckmoneyInfo)){
            $response = array('error' => 80000, 'msg' => '现在没有红包哟!');
            $this->out_print($response);
        }
        //判断时间点
        if(NOW < $luckmoneyInfo['yugaotime']){
            $response = array('error' => 80001, 'msg' => '还在预告阶段哟!');
            $this->out_print($response);
        }
        //判断时间点
        if(NOW < $luckmoneyInfo['lstime']){
            $response = array('error' => 80001, 'msg' => '你抢得有点早哟!');
            $this->out_print($response);
        }
        if($luckmoneyInfo['etime'] > 0){
            $response = array('error'=> 0, 'data' => array('money' => 0, 'msg' => '红包已经抢完,去查看手气吧!', 'status' => 3));
            $this->out_print($response);
        }
        //资格判断
        $no_rob_index = mt_rand(0, $this->no_rob_num);
        $lmid = $luckmoneyInfo['lmid'];
        $account = $this->account;
        $this->activity_luckmoney_logic->set_luckmoney_join_with_lmid($lmid, $account, 1);
        if($luckmoneyInfo['ltarget'] == 1){             //活期用户
            $this->load->model('base/userproduct_base', 'userproduct_base');
            $product_money = $this->userproduct_base->getUserSumProductMoney($this->uid);
            if($product_money <= 0){
                $this->load->model('logic/longproduct_logic', 'longproduct_logic');
                $longmoney = $this->longproduct_logic->getLongmoney($this->uid);
                if($longmoney <= 0){
                    $response = array('error' => 80012, 'msg' => '有一种红包叫只能看, 不能抢。您还不是投资用户,不能抢这个红包');
                    $this->out_print($response);
                }
            }
        }else if($luckmoneyInfo['ltarget'] == 2){       //定期用户
            $this->load->model('base/userproduct_base', 'userproduct_base');
            $product_money = $this->userproduct_base->getUserSumProductMoney($this->uid);
            if($product_money <= 0){
                $response = array('error' => 80013, 'msg' => '有一种红包叫只能看, 不能抢。您还不是定期用户,不能抢这个红包');
                $this->out_print($response);
            }
        }
        $user_cd = $this->activity_luckmoney_logic->getLuckMoneyUserCd($this->uid, $lmid, $luckmoneyInfo['ltoweight']);
        if($user_cd['lucktime'] + 5 > NOW){         //5秒CD不中
            $response = array('error'=> 0, 'data' => array('money' => 0, 'msg' => $this->no_rob_txt[$no_rob_index], 'status' => 2));
            $this->out_print($response);
        }
        $user_rate = mt_rand(0,100);
        if($user_rate >= $user_cd['u_rate']){       //没中
            $response = array('error'=> 0, 'data' => array('money' => 0, 'msg' => $this->no_rob_txt[$no_rob_index], 'status' => 2));
            $this->out_print($response);
        }else{
            //中奖
            $count_money = $this->activity_luckmoney_logic->get_luckmoney_money_incr($lmid);
            $count_money = $count_money ? $count_money : 0;
            if($count_money >= $luckmoneyInfo['lmoney']){
                $response = array('error'=> 0, 'data' => array('money' => 0, 'msg' => '红包已经抢完,去查看手气吧!', 'status' => 3));
                $this->out_print($response);
            }
            //根据权重得出用户获得金额
            $money_rate = mt_rand(0,100);
            $rate = array();
            $lporportion1 = $luckmoneyInfo['lproportion1'];
            $lporportion2 = $lporportion1 + $luckmoneyInfo['lproportion2'];
            $lporportion3 = $lporportion2 + $luckmoneyInfo['lproportion3'];
            $rate_key = '';
            $money_rate = mt_rand(0,$lporportion3);
//             $money_rate = 2;
            if($money_rate <= $lporportion1){
                $rate_key = 'lweight1_money';
            }else if($money_rate > $lporportion1 && $money_rate <= $lporportion2){
                $rate_key = 'lweight2_money';
            }else if($money_rate > $lporportion2 && $money_rate <= $lporportion3){
                $rate_key = 'lweight3_money';
            }else{
                $response = array('error'=> 0, 'data' => array('money' => 0, 'msg' => $this->no_rob_txt[$no_rob_index], 'status' => 2));
                $this->out_print($response);
            }
            $lweight = $luckmoneyInfo[$rate_key];
            list($_min, $_max) = explode('-', $lweight);
            //如果有小数
            $check_min = $this->_checkFloat($_min);
            $check_max = $this->_checkFloat($_max);
            if($check_min || $check_max){
                $_min *= 100;
                $_max *= 100;
                $rand_m = mt_rand($_min, $_max);
                $money = $rand_m / 100;
            }else{
                $money =  mt_rand($_min, $_max);
            }
            
            $user_cd['u_rate'] = $user_cd['u_rate'] * (1 - $luckmoneyInfo['ltoweightdown']/100);
            $user_cd['lucktime'] = NOW;
            $this->activity_luckmoney_logic->setLuckMoneyUserCd($this->uid, $lmid, $user_cd);
            if($count_money + $money > $luckmoneyInfo['lmoney']){
                $money = $luckmoneyInfo['lmoney'] - $count_money;
            }
            if($money == 0){
                $response = array('error' => 80004, 'msg' => '红包已经抢完,去查看手气吧!', 'status' => 3);
                $this->out_print($response);
            }
            $this->activity_luckmoney_logic->set_luckmoney_money_incr($lmid, $money);
            $count_money = $this->activity_luckmoney_logic->get_luckmoney_money_incr($lmid);

            if($count_money >= $luckmoneyInfo['lmoney']){
                $rob_end = 3;
                $update_data = array('etime' => NOW);
                $this->activity_luckmoney_logic->update_luckmoney_db_info($update_data, $lmid);
            }
            //插入用户红包日志
            $ordid = 'luck'.date('YmdHis').$this->uid.mt_rand(1000,9999);
            $luckmoney_log = array(
                'orderid' => $ordid,
                'uid' => $this->uid,
                'account' => $account,
                'money' => $money,
                'lmid' => $lmid,
                'ltype' => $luckmoneyInfo['ltarget'],
                'lmoney' => $count_money,
                'ctime' => NOW,
            );
            $this->load->model('base/activity_luckmoney_log_base', 'activity_luckmoney_log_base');
            $this->activity_luckmoney_log_base->createOrder($luckmoney_log);
            
            //放入用户余额
            $this->load->model('base/balance_base', 'balance_base');
            $balance = $this->balance_base->get_user_balance($this->uid);
            $balance += $money;
            
            //写用户日志
            $user_log_data = array(
                'uid' => $this->uid,
                'pid' => 0,
                'pname' => '抢红包',
                'paytime' => NOW,
                'money' => $money,
                'balance' => $balance,
                'orderid' => $ordid,
                'action' => USER_ACTION_ACTIVITY
            );
            $this->load->model('base/user_log_base', 'user_log_base');
            $this->user_log_base->addUserLog($this->uid, $user_log_data);
            //加用户余额
            $this->balance_base->add_user_balance($this->uid, $money);
            
            //放入红包排行
            $this->activity_luckmoney_logic->set_luckmoney_rank_with_lmid($lmid, $account, $money);
            $response = array('error'=> 0, 'data' => array('money' => $money, 'msg' => $luckmoneyInfo['bless_text'], 'status' => $rob_end));
            $this->out_print($response);
        }
    }
    
    public function userRankList(){
        $lmid = $this->input->post('lmid');
        $data = $this->activity_luckmoney_logic->get_luckmoney_rank_with_lmid($lmid);
        $luckmoneyInfo = $this->activity_luckmoney_logic->getluckmoney();
        $overtime = $luckmoneyInfo['etime'] - $luckmoneyInfo['lstime'];
        $return_data = array();
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $this->load->model('logic/login_logic', 'login_logic');
        foreach ($data as $phone => $_value){
            $new_array = array();
            $uid = $this->login_logic->getUidByAccount($phone);
            $userIdentity = $this->user_identity_base->getUserIdentity($uid);
            if($userIdentity){
                $new_array['name'] = '*' . mb_substr($userIdentity['realname'], 1);
            }else{
                $new_array['name'] = '*某某';
            }
            
            $new_array['score'] = sprintf("%.2f",substr(sprintf("%.3f", $_value), 0, -1));
            $phone = substr($phone, 0, 3) . '****' . substr($phone, -4);
            $return_data[$phone] = $new_array;
        }
        if($overtime <= 0){
            $overtime = 0;
        }
        $response = array('error'=> 0, 'data' => array('rank' => $return_data, 'overtime' => $overtime));
        $this->out_print($response);
    }
    
    private function _checkFloat($num) {
        $count = 0;
        $temp = explode ( '.', $num );
        if (count($temp ) > 1) {
            return true;
        }
        return false;
    }
}
