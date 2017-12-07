<?php
class equalproduct_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/equalproduct_base' , 'equalproduct_base');
    }

    
    public function getAllOnlineEqualProduct($page){
        $page_info = $this->equalproduct_base->getAllOnlineProduct($page);
        $product_list = array();
        if(is_array($page_info['products']) && count($page_info['products']) > 0){
            foreach ($page_info['products'] as $pid => $score){
                $detail = $this->equalproduct_base->getProductDetail($pid);
                $product_list[] = $this->equalproduct_base->getProductDetail($pid);
            }
        }
        $page_info['product_list'] = $product_list;
        unset($page_info['products']);
        return $page_info;
    }
    
    public function rsyncEqualProductSellMoney($pid){
        return $this->equalproduct_base->rsyncEqualProductSellMoney($pid);
    }
    
    public function getEqualProductDetail($pid){
        return $this->equalproduct_base->getEqualProductDetail($pid);
    }
    

    public function CountEqualProductBuyMoney($pid){
        $this->load->model('base/equalproduct_buy_info_base' , 'equalproduct_buy_info_base');
        return $this->equalproduct_buy_info_base->CountEqualProductBuyMoney($pid);
    }
    
    
    public function getBuyUserByPid($pid){
        $this->load->model('base/equalproduct_buy_info_base' , 'equalproduct_buy_info_base');
        return $this->equalproduct_buy_info_base->getBuyUserByPid($pid);
    }
    
    public function setProductCache($product){
        return $this->equalproduct_base->setProductCache($product);
    }
    
    public function buy_equalproduct($uid, $productInfo, $userIdentity, $money, $account, $paytype, $balance){
        $pid = $productInfo['pid'];
        $ordid = date('Ymds'). $uid . $pid . mt_rand(100, 999) . 'bp';
        $trxid = $ordid;
        //记录系统购买数
        $this->load->model('base/equalproduct_buy_info_base' , 'equalproduct_buy_info_base');
        $data = array(
            'uid' => $uid,
            'pid' => $pid,
            'account' => $account,
            'money' => $money,
            'trxid' => $trxid,
        );
        $this->equalproduct_buy_info_base->addEqualProductBuyInfo($pid, $data);
        //记录用户产品
        $bankid = 0;
        if($paytype == 2){
            $bankid = $userIdentity['bankcode'];
        }
        $this->load->model('base/userequalproduct_base', 'userequalproduct_base');
        $userproduct_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $productInfo['pname'],
            'income' => $productInfo['income'],
            'money' => $money,
            'uietime' => $productInfo['uietime'],
            'paytype' => $paytype,
            'bankid' => $bankid,
            'trxid' => $trxid,
            'from' => 'f'
        );
        //分段生成还款产品
//     $money = 100000;
//     //还款日
//     $productInfo['uistime'] = '';
//     $productInfo['uietime'] = 10;
//     $productInfo['income'] = 12;
//     $productInfo['instalment'] = 5;
        $month_income = $productInfo['income'] / 12 / 100;
        
        //每月应还金额
        $i_money = ($money*$month_income*pow(1+$month_income, $productInfo['instalment']))/(pow(1+$month_income, $productInfo['instalment']) - 1);
        $i_money = round($i_money, 2);
        for($i = 1; $i <= $productInfo['instalment']; $i++){
            //每月利息
            $month_profit = $money * $month_income;
            $month_profit = round($month_profit, 2);
            //每月本金
            $benjing = $i_money - $month_profit;
            $benjing = round($benjing, 2);
            
            
            echo "第". $i . "个月:本金:" . $money . ",还款本息" . $i_money . ";其中本金：" . $benjing . "利息:" .  $month_profit ."<br /><br />";
            //加入到用户等额本息产品中
            $money -= $benjing;
        }


        //
        $this->userequalproduct_base->addUserEqualProductInfo($uid, $userproduct_data);
        $buy_log = array(
            'uid' => $uid,
            'ordid' => $ordid,
            'amt' => $money,
            'platform' => 'balance',
            'pid' => $pid,
            'pname' => $productInfo['pname'],
            'ctime' => time(),
            'ptype' => 'ep',
        );
        $this->load->model('base/buy_log_base' , 'buy_log');
        $this->buy_log->createBuyLog($buy_log);
        //写用户日志
        $this->load->model('base/user_log_base', 'user_log_base');
        $user_log_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $productInfo['pname'],
            'money' => $money,
            'balance' => $balance - $money,
            'action' => USER_ACTION_PRODUCT,
            'orderid' => $ordid,
            'desc' => $paytype
        );
        $this->user_log_base->addUserLog($uid, $user_log_data);
        return $trxid;
    }
    
    /*
     * status 3售罄,
     */
    public function setEqualProductSellOut($ptid, $pid){
        //从缓存中去掉
        $this->equalproduct_base->moveOnlineEqualProduct($ptid, $pid);
        //更改产品状态  3售罄
        $update_data = array('status' => 3, 'sellouttime' => NOW);
        $ret = $this->equalproduct_base->updateEqualProductStatus($pid, $update_data);
        if(!$ret){
            return false;
        }
        $this->load->model('base/equalptype_equalproduct_base', 'equalptype_equalproduct_base');
        $data = array();
        $data['status'] = 1;
        $data['rindex'] = 0;
        $where = array();
        $where['ptid'] = $ptid;
        $where['pid'] = $pid;
        $where['odate'] = date('Y-m-d');
        //更改数据库备份数据
        $this->equalptype_equalproduct_base->updatePtypeProduct($data, $where);
        $this->equalproduct_base->addEqualProductToSellOutList($pid);
    }
    
    
    public function setProductDownline($ptid, $pid){
        //从缓存中去掉
        $this->equalproduct_base->moveOnlineEqualProduct($ptid, $pid);
        //更改产品状态   2下架
        $update_data = array('status' => 2, 'downtime' => NOW);
        $this->equalproduct_base->updateEqualProductStatus($pid, $update_data);
        
        $product = $this->equalproduct_base->getEqualProductDetail($pid);
        $back_money = $product['money'] - $product['sellmoney'];
        
        $this->load->model('base/equalamountcontract_base', 'equalcontract_base');
        $crontract_sellmoney = $this->equalproduct_base->countSellMoneyByCid($product['cid']);               //已卖出金额
        $crontract_onlineMoney = $this->equalproduct_base->countOnlineEqualProductMoneyByCid($product['cid']);    //在架上的金额
        $countMoney = $crontract_sellmoney + $crontract_onlineMoney;
        //检查合同是否有采购
        //echo 'countMoney:'.$countMoney .'<br />';
        $contract = $this->equalcontract_base->getContractByCid($product['cid']);
        //print_r($contract);
        if($contract['is_stock']){
           $this->load->model('base/stock_equalproduct_base', 'stock_equalproduct_base');
           $stock_money = $this->stock_equalproduct_base->sum_stock_money($product['cid']);
           echo 'stock_money:'.$stock_money .'<br />';
           $countMoney += $stock_money;
        }
        //余额回合同
        $this->equalcontract_base->backMoneytoContract($product['cid'], $countMoney);
        //记个文本日志
        $log = array();
        $log['pid'] = $pid;
        $log['cid'] = $product['cid'];
        $log['ptid'] = $ptid;
        $log['back_money'] = $back_money;
        $log['cron_sell_money'] = $crontract_sellmoney;
        $this->load->model('base/log_base', 'log_base');
        $this->log_base->back_contract_log($log);
        return true;
    }
    
    public function buy($ordid){
        $this->load->model('base/buy_log_base' , 'buy_log');
        $orderInfo = $this->buy_log->getLogByOrdid($ordid);
        if($orderInfo['status'] == 1){
            show_app_error('此订单已交易完成,重复的购买请求');
        }
        //没完成进入购买逻辑
        $uid = $orderInfo['uid'];
        $pid = $orderInfo['pid'];
        $money = $orderInfo['amt'];
        $paytype = 1;
        $money = floor($money);
        $money = strval($money);
        
        if($money <= 0){
            show_app_error('金额错误');
        }
        $this->load->model('logic/yeepay_tg_logic', 'yeepay_tg_logic');
        
        $this->load->model('logic/product_logic', 'product_logic');
        $productInfo = $this->product_logic->getProductDetail($pid);
        if(!$productInfo){
            $this->yeepay_tg_logic->complete_transaction($ordid, 2);
            show_app_error('产品不存在');
        }
        if($productInfo['status'] != 1 || $productInfo['uptime'] > NOW){
            $this->yeepay_tg_logic->complete_transaction($ordid, 2);
            show_app_error('产品还末上线或已下标!');
        }
        $this->load->model('base/equalproduct_base', 'equalproduct_base');
        $ptid = $productInfo['ptid'];
        $productid = $this->equalproduct_base->getOnlineProductListFirstMem($ptid);
        if($productid != $pid){
            $this->yeepay_tg_logic->complete_transaction($ordid, 2);
            show_app_error('产品还末上线或已下标!!');
        }
        //print_r($productInfo);
        if($money < $productInfo['startmoney'] ||
            ($money - $productInfo['startmoney']) % $productInfo['money_limit'] != 0 || //累进金额不能小
            $money > $productInfo['money_max']){
            $this->yeepay_tg_logic->complete_transaction($ordid, 2);
            show_app_error('购买金额错误');
        
        }
        $sellMoney = $this->product_logic->rsyncProductSellMoney($pid);
        if($sellMoney >= $productInfo['money']){
            $this->yeepay_tg_logic->complete_transaction($ordid, 2);
            show_app_error('产品已卖完');
        }
        if($productInfo['money'] - $sellMoney < $money){
            $this->yeepay_tg_logic->complete_transaction($ordid, 2);
            show_app_error('产品剩余金额不足');
        }
        
        $data = array();
        //同步第三方余额
        $this->load->model('logic/yeepay_tg_logic', 'yeepay_tg_logic');
        $balance = $this->yeepay_tg_logic->get_tg_balance($uid);
        
        $this->load->model('logic/tg_user_identity_logic', 'identity_logic');
        $userIdentity = $this->identity_logic->getUserIdentity($uid);
        $this->load->model('base/user_base', 'user_base');
        //结算订单
        $account = $this->user_base->getAccountInfo($uid);
        $this->yeepay_tg_logic->complete_transaction($ordid);
        
        $this->buy_log->updateLog(array('isAutoBack' => 1), array('ordid' => $ordid));
        $trxid = $this->product_logic->tg_buy_product($uid, $productInfo, $userIdentity, $money, $account['account'], $ordid, $balance);
        if(!$trxid){
            show_app_error('购买失败，请重试');
        }
        //再次同步
        $sellMoney = $this->product_logic->rsyncProductSellMoney($pid);
        if($productInfo['money'] == $sellMoney){
            $this->product_logic->setProductSellOut($productInfo['ptid'], $pid);
        }
        $this->product_logic->rsyncProductSellMoney($pid);
        //送体验金
        $add_exp = false;
        $send_expmoney = 0;
        $exp_balance = 0;
        
        if($productInfo['exp_buy'] > 0 && $productInfo['exp_send'] && $money >= $productInfo['exp_buy']){
            $this->load->model('base/exp_cd_base', 'exp_cd_base');
            $cd_info = $this->exp_cd_base->get($uid, $pid);
            //             $cd_info = false;
            if(!$cd_info){
                $this->load->model('logic/expmoney_logic','expmoney_logic');
                if($productInfo['exp_send'] == '+'){
                    $send_expmoney = $money;
                }else{
                    $send_expmoney = $productInfo['exp_send'];
                }
                $add_exp = true;
                //添加体验金
                $this->expmoney_logic->add_expmoney($uid, $send_expmoney);
                $exp_balance = $this->expmoney_logic->get_expmoney($uid);
                //添加体验金日志
                $exp_log_data = array(
                    'uid' => $uid,
                    'ctime' => NOW,
                    'log_desc' => '购买'. $productInfo['pname'].'赠送',
                    'money' => $send_expmoney,
                    'action' => EXPMONEY_LOG_ADD,
                    'balance'  => $exp_balance
                );
                $log_data = $this->expmoney_logic->addLog($uid, $exp_log_data);
                $cd_info = $this->exp_cd_base->set($uid, $pid);
            }
        }
        $this->load->model('logic/activity_logic', 'activity_logic');
        $ret = array();
        $this->buy_log->updateLog(array('status' => 1), array('ordid' => $ordid));
        
//         if( $productInfo['ptid'] != NEW_USER_PTID){
//             if($userIdentity['isnew'] == 1 ){
//                 //新手购买送现金活动
//                 $add_activity_money = $money * 0.01;
//                 $add_activity_money = max(2, $add_activity_money);
//                 $add_activity_money = min(300, $add_activity_money);
//                 $ret = $this->activity_logic->checkAndSendUserGiveMoneyActivity($uid, $userIdentity['realname'], $account, $pid, $add_activity_money);
//                 if($ret){
//                     $balance += $add_activity_money;
//                 }
//                 $this->identity_logic->set_isnew($uid);
//             }
//             //好友邀请奖励
//             if(defined('INVITE') && INVITE == true){
//                 $this->activity_logic->invite_activity($uid, $account, $money, $productInfo);
//             }
            //--------818积分活动  start ----------------------
            //  $this->activity_logic->checkAndAddUserIntegral($this->account, $money, $ptid);
            //-------818积分活动   end  ------------------------
            //---运营数据-----  老新用户第一次购买
//             $this->load->model('base/user_base', 'user_base');
//             $account = $this->user_base->getAccountInfo($uid);
//             if(date('Y-m-d', $account['ctime']) != date('Y-m-d') && $userIdentity['isnew'] == 1){
//                 $this->load->model('base/olduser_base', 'olduser_base');
//                 $olduser_data = array();
//                 $olduser_data['uid'] = $uid;
//                 $olduser_data['ctime'] = mktime(0,0,0);
//                 $olduser_data['plat'] = $account['plat'];
//                 $this->olduser_base->add($olduser_data);
//             }
//        }
        //更新定单状态
        
        //---------
        $data['balance'] = $balance;
        $data['cost'] = $money;
        $data['trxid'] = $trxid;
        $data['add_exp'] = $add_exp;
        if($add_exp){
            $data['exp_add'] = $send_expmoney;
            $data['exp_balance'] =  $exp_balance;
        }
        $response = array('data'=> $data, 'activity' => $ret);
        return $response;
    }
    
    public function tg_buy_product($uid, $productInfo, $userIdentity, $money, $account, $ordid, $balance){
        $pid = $productInfo['pid'];
        $trxid = $ordid;
        //记录系统购买数
        $this->load->model('base/product_buy_info_base' , 'product_buy_info_base');
        $data = array(
            'uid' => $uid,
            'pid' => $pid,
            'account' => $account,
            'money' => $money,
            'trxid' => $trxid,
        );
        $this->product_buy_info_base->addProductBuyInfo($pid, $data);
        //记录用户产品
        $bankid = 0;

        $this->load->model('base/userequalproduct_base', 'userequalproduct_base');
        $userproduct_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $productInfo['pname'],
            'income' => $productInfo['income'],
            'money' => $money,
            'uietime' => $productInfo['uietime'],
            'paytype' => 1,
            'bankid' => $bankid,
            'trxid' => $trxid,
            'from' => 'f'
        );
        $this->userequalproduct_base->addUserProductInfo($uid, $userproduct_data);
        //写用户日志
        $this->load->model('base/user_log_base', 'user_log_base');
        $user_log_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $productInfo['pname'],
            'money' => $money,
            'balance' => $balance,
            'action' => USER_ACTION_PRODUCT,
            'orderid' => $ordid,
            'desc' => 1
        );
        $this->user_log_base->addUserLog($uid, $user_log_data);
        return $trxid;
    }
    
}


   
