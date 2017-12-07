<?php
class festivity_logic extends CI_Model
{
    function __construct()
    {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/user_identity_base', 'user_identity_base');
        $this->load->model('base/user_notice_base', 'user_notice_base');
        $this->load->model('base/activity_base', 'activity_base');
    }

    /**
     * 双十一活动
     * @param $uid
     * @param $money
     * @param $userIdentity
     * @param $config
     * @return bool
     */
    public function double_activity($uid, $money, $userIdentity, $config){
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $this->load->model('base/user_base', 'user_base');
        $remove_qudao = $this->user_base->_db_get_account_info_by_uid($uid, 0);

        if (in_array($remove_qudao['plat'], $config['remove'])){
            return false;
        }
        if ($userIdentity['isnew'] == 1) {
            if(isset($config['fest_rule'])){
                foreach ($config['fest_rule'] as $_stage => $cfg_arr){
                    list($_min, $_max) = explode('-', $_stage);
                    if($money >= $_min && $money <= $_max){
                        $first_buy_money = isset($cfg_arr['first_buy_reward']) ? $cfg_arr['first_buy_reward'] : 0;
                        break;
                    }
                }
            }
            $this->load->model('base/balance_base' , 'balance_base');
            $uid_balance = $this->balance_base->get_user_balance($uid);
            $balance = $this->balance_base->add_user_balance($uid, $first_buy_money);
            if ($balance) {
                $uuiduser_log_data = array(
                    'uid' => $uid,
                    'pid' => 0,
                    'pname' => '首次投资红包',
                    'paytime' => NOW,
                    'money' => $first_buy_money,
                    'balance' => $uid_balance+$first_buy_money,
                    'orderid' => 'f'.$uid.date('YmdHis').mt_rand(100,999),
                    'action' => USER_ACTION_ACTIVITY
                );

                $this->load->model('base/user_log_base', 'user_log_base');
                $this->user_log_base->addUserLog($uid, $uuiduser_log_data);
                $this->load->model('logic/msm_logic', 'msm_logic');
                $this->msm_logic->send_double_eleven_msg($userIdentity['realname'], $userIdentity['phone'], $first_buy_money);
                $notice_data = array(
                    'uid' => $uid,
                    'title' => '首次投资红包提醒',
                    'content' => "恭喜您获得“双11迎新 易触即发”活动奖励：现金".$first_buy_money."元，已充入您的余额账户。请查收！",
                    'ctime' => NOW
                );
                $this->user_notice_base->addNotice($uid, $notice_data);
            }
        }
    }
    
    /**
     * 复投活动
     * @param int   $uid
     * @param int   $account
     * @param int   $money          用户投资的金额
     * @param array $productInfo    产品信息
     * @param int   $isnew          是否新人
     * @param array $config         复投活动配置
     * @return null
     */
    public function futou_activity($uid,$account,$money,$productInfo,$isnew,$config) {
        if($this->futou_condition($uid,$isnew,$productInfo,$config)){
            //开始发奖励
            $acvitity_key = _KEY_REDIS_USER_FUTOU_ACTIVITY_MARK_INFO_.$uid;
            $acvitity_cache = $this->get_put_redis_cache($acvitity_key);
            if (! $acvitity_cache || $acvitity_cache < 1){
                $this->award($uid,$money,$productInfo,$config,$acvitity_cache);
            }
        }
    }

    /**
     * 给用户发送奖励
     * @param string $uid
     * @param string $money
     * @param string $productInfo
     * @param array $config
     */
    public function award($uid = '', $money = '',$productInfo = '',$config = array(),$acvitity_cache = ''){
        $activety_fu_rule = $config['activety_fu_rule'];
        foreach ($activety_fu_rule as $ptid_str => $value) {
            $ptid_strs = explode("-", $ptid_str);
            if($ptid_strs[1] == $productInfo['ptid']){
                if (isset($value['rule']) && is_array($value['rule'])){
                    foreach ($value['rule'] as $key => $rule){
                        list($min, $max) = explode("-", $key);
                        if ($money >= $min && $money <= $max){
                            $award_moeny = isset($rule['buy_reward']) ? $rule['buy_reward'] : 0;
                            $result = $this->add_user_blance_log($uid,$award_moeny,'易转乾坤');
                            if ($result) {
                                $cache_num = $acvitity_cache ? $acvitity_cache+1 : 1;
                                $this->get_put_redis_cache(_KEY_REDIS_USER_FUTOU_ACTIVITY_MARK_INFO_.$uid,$cache_num);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 添加日志余额信息
     * @param string $uid
     * @param string $money
     * @param string $pname
     * @return bool
     */
    public function add_user_blance_log($uid = '', $money = '', $pname = ''){
        $this->load->model('base/balance_base' , 'balance_base');
        $user_balance = $this->balance_base->get_user_balance($uid);
        $balance = $this->balance_base->add_user_balance($uid, $money);
        if ($balance) {
            $uuiduser_log_data = array(
                'uid' => $uid,
                'pid' => 0,
                'pname' => $pname,
                'paytime' => NOW,
                'money' => $money,
                'balance' => $user_balance+$money,
                'orderid' => 'f'.$uid.date('YmdHis').mt_rand(100,999),
                'action' => USER_ACTION_ACTIVITY
            );

            $this->load->model('base/user_log_base', 'user_log_base');
            $this->user_log_base->addUserLog($uid, $uuiduser_log_data);
            $notice_data = array(
                'uid' => $uid,
                'title' => $pname,
                'content' => '恭喜您获得“'.$pname.'”活动奖励：现金'.$money.'元，已充入您的余额账户。请查收！',
                'ctime' => NOW
            );
            $this->user_notice_base->addNotice($uid, $notice_data);
            return true;
        }
    }

    /**
     * 复投条件
     */
    private function futou_condition($uid,$isnew,$productInfo,$config) {
        if($isnew == 0){
            $activety_fu_rule = $config['activety_fu_rule'];
            foreach ($activety_fu_rule as $ptid_str => $value) {
                $ptid_strs = explode("-", $ptid_str);
                if($ptid_strs[1] == $productInfo['ptid']){
                    $condition = $value['condition'];
                    $this->load->model('base/userproduct_base', 'userproduct_base');
                    $userproducts = $this->userproduct_base->getUserProductInfo($uid);
                    $userproduct_stat = array();
                    $this->load->model('base/product_base', 'product_base');
                    foreach ($userproducts as $key => $userproduct) {
                        if((NOW - $userproduct['buytime']) > 10){
                            if($userproduct['ptid'] == 0){
                                $productdetail = $this->product_base->getProductDetail($userproduct['pid']);
                                if(!isset($userproduct_stat[$productdetail['ptid']]['totalmoney'])){
                                    $userproduct_stat[$productdetail['ptid']]['totalmoney'] = 0;
                                }
                                $userproduct_stat[$productdetail['ptid']]['totalmoney'] += $userproduct['money'];
                            }else{
                                if(!isset($userproduct_stat[$userproduct['ptid']]['totalmoney'])){
                                    $userproduct_stat[$userproduct['ptid']]['totalmoney'] = 0;
                                }
                                $userproduct_stat[$userproduct['ptid']]['totalmoney'] += $userproduct['money'];
                            }
                        }
                    }
                    foreach ($condition as $ptids_str => $compare) {
                        $ptid_strs = explode("-", $ptids_str);
                        $compare_str = explode(" ", $compare);
                        if(count($ptid_strs) > 1){
                            $totalmoneys = 0;
                            foreach ($ptid_strs as $k => $ptid) {
                                if(isset($userproduct_stat[$ptid])){
                                    $totalmoneys += $userproduct_stat[$ptid]['totalmoney'];
                                }
                            }
                            if($compare_str[0] == '>='){
                                if($totalmoneys >= $compare_str[1]){
                                    return true;
                                }
                            } elseif($compare_str[0] == '<='){
                                if($totalmoneys <= $compare_str[1]){
                                    return true;
                                }
                            } elseif($compare_str[0] == '='){
                                if($totalmoneys == $compare_str[1]){
                                    return true;
                                }
                            }
                        }else{
                            foreach ($ptid_strs as $k => $ptid) {
                                if(isset($userproduct_stat[$ptid])){
                                    $ptid_totalmoney = $userproduct_stat[$ptid]['totalmoney'];
                                    if($compare_str[0] == '>='){
                                        if($ptid_totalmoney >= $compare_str[1]){
                                            return true;
                                        }
                                    } elseif($compare_str[0] == '<='){
                                        if($ptid_totalmoney <= $compare_str[1]){
                                            return true;
                                        }
                                    } elseif($compare_str[0] == '='){
                                        if($ptid_totalmoney == $compare_str[1]){
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
//                        print_r($ptid_strs);
//                        print_r($compare_str);
                    }
                    

                }
            }
        }
        return false;
    }

    /**
     * 双十二活动二
     * @param string $uid
     * @param string $account
     * @param array $product
     * @param string $money
     */
    public function double_twelve_activity_one($uid='',$account = '', $product = array(),$money=''){
        $this->config->load('cfg/festivity_cfg', true, true);
        $config = $this->config->item('cfg/festivity_cfg');

        if (NOW >= strtotime($config['twelve_start_time']) && NOW <= strtotime($config['twelve_end_time'])) {
            $double_twelve_key = _KEY_REDIS_USER_DOUBLE_TWELVE_ACTIVITY_ONE_INFO_ . $uid;
            $double_twelve_one_cache = $this->get_put_redis_cache($double_twelve_key);
            $result = $this->doubel_twelve_condition($product, $money, $config['twelve_single_condition']);
            if ($result > 0 && $double_twelve_one_cache < 3) {
                $this->add_user_blance_log($uid, $result, '单笔投资');
                $this->add_activity_luck_result('单笔投资奖励',$product['ptid'],$uid,$account,$money,$result);
                $cache_num = $double_twelve_one_cache ? $double_twelve_one_cache + 1 : 1;
                $this->get_put_redis_cache(_KEY_REDIS_USER_DOUBLE_TWELVE_ACTIVITY_ONE_INFO_ . $uid, $cache_num);
            }
        }
    }

    /**
     * 双十二活动三
     * @param string $uid
     * @param string $account
     * @param array $product
     */
    public function double_twelve_activity_two($uid='',$account = '', $product = array()){
        $this->config->load('cfg/festivity_cfg', true, true);
        $config = $this->config->item('cfg/festivity_cfg');
        if (NOW >= strtotime($config['twelve_start_time']) && NOW <= strtotime($config['twelve_end_time'])) {
            $this->load->model('base/userproduct_base', 'userproduct_base');
            $money = $this->userproduct_base->_db_userproduct_money($uid);
            $result = $this->doubel_twelve_condition($product, $money, $config['twelve_accumulative_condition']);
            $double_twelve_two_key = _KEY_REDIS_USER_DOUBLE_TWELVE_ACTIVITY_TWO_INFO_.$uid.':'.$result;
            $double_twelve_two_cache = $this->get_put_redis_cache($double_twelve_two_key);
            if ($result > 0 && $double_twelve_two_cache < 1 ) {
                $this->add_user_blance_log($uid, $result, '累积投资');
                $this->add_activity_luck_result('累计投资奖励',$product['ptid'],$uid,$account,$money,$result);
                $cache_num = $double_twelve_two_cache ? $double_twelve_two_cache + 1 : 1;
                $this->get_put_redis_cache(_KEY_REDIS_USER_DOUBLE_TWELVE_ACTIVITY_TWO_INFO_ . $uid.':'.$result, $cache_num);
            }
        }
    }

    /**
     * 双十二活动四
     * @return array|bool
     */
    public function double_twelve_activity_three(){
        $sort = array();
        $this->config->load('cfg/festivity_cfg', true, true);
        $config = $this->config->item('cfg/festivity_cfg');
        $start_time = strtotime($config['twelve_start_time']);
        $end_time = strtotime($config['twelve_end_time']);
        if (NOW >= $start_time && NOW <= $end_time) {
            if(!empty($config['rank_product_ptid']) && is_array($config['rank_product_ptid'])){
                foreach ($config['rank_product_ptid'] as $product_key => $ptid){
                    $result_array = $this->activity_base->get_product_info($product_key,$ptid,$start_time,$end_time);
                    $res[$product_key] = $this->activity_base->get_array($result_array,$product_key,$ptid);
                    $sort[$product_key] = $this->activity_base->array_sort($res[$product_key], 'money', 1);
                    $award_user[$product_key] = array_slice($sort[$product_key],0,3);
                    //发送奖励
                    foreach ($award_user[$product_key] as $rank => $value){
                        $this->add_user_blance_log($value['uid'],$config['double_twelve_rank_award_money'],'夺标之王');
                        $this->add_activity_luck_result('夺标之王奖励',$value['product_tpid'],$value['uid'],$value['account'],$rank+1,$value['money'],$config['double_twelve_rank_award_money']);
                    }
                }
            }
        }

        return $sort ? $sort : false;
    }

    /**
     * @param $product
     * @param $money
     * @param $double_twelve_config
     * @return int
     */
    public function doubel_twelve_condition($product,$money,$double_twelve_config){
        foreach ($double_twelve_config as $product_key => $value) {
            $product_str = explode('-', $product_key);
            if($product_str[1] == $product['ptid']){
                foreach ($value as $moeny_key => $reward){
                    list($min, $max) = explode('-', $moeny_key);
                    if ($min <= $money && $money <= $max){
                        return $reward['buy_reward'] ? $reward['buy_reward'] : 0;
                    }
                }
            }
        }
    }

    /**
     * 添加活动中奖结果
     * @param $act_name
     * @param $ptid
     * @param $uid
     * @param $account
     * @param $rank
     * @param $money
     * @param $award_money
     * @return mixed
     */
    public function add_activity_luck_result($act_name,$ptid,$uid,$account,$rank,$money,$award_money){
        $data = array(
            'act_name' => $act_name,
            'ptid' => $ptid,
            'uid' => $uid,
            'account' => $account,
            'rank' => $rank,
            'award_money' => $award_money,
            'money' => $money,
            'luck_time' => NOW,
            'ctime' => NOW,

        );
        if (!empty($rank)) $data['status'] = 0;
        return $this->activity_base->add_activity_luck_result($data);
    }

    /**
     * @param string $key
     * @param string $data
     * @return mixed
     */
    public function get_put_redis_cache($key = '', $data = ''){
        $acvitity_cache = $this->activity_base->redis_cache($key,$data,'',0);

        return $acvitity_cache;
    }

}
