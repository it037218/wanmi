<?php
class product_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/product_base' , 'product_base');
        $this->load->model('base/user_notice_base', 'user_notice_base');
    }

    
    public function getAllOnlineProduct($page){
        $page_info = $this->product_base->getAllOnlineProduct($page);
        $product_list = array();
        if(is_array($page_info['products']) && count($page_info['products']) > 0){
            foreach ($page_info['products'] as $pid => $score){
                $detail = $this->product_base->getProductDetail($pid);
                $product_list[] = $this->product_base->getProductDetail($pid);
            }
        }
        $page_info['product_list'] = $product_list;
        unset($page_info['products']);
        return $page_info;
    }
    
    public function rsyncProductSellMoney($pid){
        return $this->product_base->rsyncProductSellMoney($pid);
    }
    
    public function getProductDetail($pid){
        return $this->product_base->getProductDetail($pid);
    }
    

    public function CountProductBuyMoney($pid){
        $this->load->model('base/product_buy_info_base' , 'product_buy_info_base');
        return $this->product_buy_info_base->CountProductBuyMoney($pid);
    }
    
    
    public function getBuyUserByPid($pid){
        $this->load->model('base/product_buy_info_base' , 'product_buy_info_base');
        return $this->product_buy_info_base->getBuyUserByPid($pid);
    }
    
    public function setProductCache($product){
        return $this->product_base->setProductCache($product);
    }
    
    public function getTotalStockMoneyByRepaymentDate($date){
    	$this->load->model('base/contract_base' , 'contract_base');
    	$contractList = $this->contract_base->getContractByRepaymentDate($date);
    	$totalStockMoney = 0;
    	$totalStockProfit = 0;
    	foreach ($contractList as $contract){
    		if(!empty($contract['stockmoney'])){
    			$totalStockMoney = $totalStockMoney+$contract['stockmoney'];
    			$stockProfit = $this->countProfit($this->diff_days(date('Y-m-d',$contract['remittime']),$contract['repaymenttime']),$contract['stockmoney'],9);
    			$totalStockProfit = $totalStockProfit+$stockProfit;
    		}
    	}
    	$data['totalStockMoney'] = $totalStockMoney;
    	$data['totalStockProfit'] = round($totalStockProfit,2);
    	return $data;
    }
    
    private function countProfit($days, $money, $income){
    	$profit = $income/100/365 * $money * $days;
    	return $profit;
    }
    
    public function diff_days($start, $end){
    	list($a_year, $a_month, $a_day) = explode('-', $start);
    	list($b_year, $b_month, $b_day) = explode('-', $end);
    	$a_new=mktime(0, 0, 0, $a_month, $a_day, $a_year);
    	$b_new=mktime(0, 0, 0, $b_month, $b_day, $b_year);
    	return abs(($a_new-$b_new)/86400) + 1;
    }
    public function buy_product($uid, $productInfo, $userIdentity, $money, $account, $paytype, $balance){
        $pid = $productInfo['pid'];
        $ordid = date('Ymds'). $uid . $pid . mt_rand(100, 999) . 'bp';
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
        if($paytype == 2){
            $bankid = $userIdentity['bankcode'];
        }
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $userproduct_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'ptid' => $productInfo['ptid'],
            'pname' => $productInfo['pname'],
            'income' => $productInfo['income'],
            'money' => $money,
            'uietime' => $productInfo['uietime'],
            'paytype' => $paytype,
            'bankid' => $bankid,
            'trxid' => $trxid,
            'from' => 'f'
        );
        $this->userproduct_base->addUserProductInfo($uid, $userproduct_data);
        $buy_log = array(
            'uid' => $uid,
            'ordid' => $ordid,
            'amt' => $money,
            'platform' => 'balance',
            'pid' => $pid,
            'pname' => $productInfo['pname'],
            'ctime' => time(),
        );
        $this->load->model('base/buy_log_base' , 'buy_log');
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
        $this->buy_log->createBuyLog($buy_log);
        
        $notice_data = array(
        		'uid' => $uid,
        		'title' => '投资成功提醒',
        		'content' => "恭喜您已成功购买【".$productInfo['pname']."】，还款时间为".date('Y-m-d',strtotime($productInfo['uietime'])+86400)."，还款当日14:30-16:30将本金和收益一同还款至您的余额，坐等收益吧！",
        		'ctime' => NOW
        );
        $this->load->model('base/user_notice_base', 'user_notice_base');
        $this->user_notice_base->addNotice($uid,$notice_data);
        return $trxid;
    }
    
    /*
     * status 3售罄,
     */
    public function setProductSellOut($ptid, $pid){
        //从缓存中去掉
        $this->product_base->moveOnlineProduct($ptid, $pid);
        //更改产品状态  3售罄
        $update_data = array('status' => 3, 'sellouttime' => NOW);
        $ret = $this->product_base->updateProductStatus($pid, $update_data);
        if(!$ret){
            return false;
        }
        $this->load->model('base/ptype_product_base', 'ptype_product_base');
        $data = array();
        $data['status'] = 1;
        $data['rindex'] = 0;
        $where = array();
        $where['ptid'] = $ptid;
        $where['pid'] = $pid;
        $where['odate'] = date('Y-m-d');
        //更改数据库备份数据
        $this->ptype_product_base->updatePtypeProduct($data, $where);
        $this->product_base->addProductToSellOutList($pid);
    }
    
    
    public function setProductDownline($ptid, $pid){
        //从缓存中去掉
        $this->product_base->moveOnlineProduct($ptid, $pid);
        
        //更改产品状态   2下架
        $product = $this->product_base->getProductDetail($pid);
        $update_data = array('status' => 2, 'downtime' => NOW);
        if($product['sellmoney'] == 0){
            $update_data['is_upload'] = 1;
        }
        $this->product_base->updateProductStatus($pid, $update_data);
        $back_money = $product['money'] - $product['sellmoney'];
        
        $this->load->model('base/contract_base', 'contract_base');
        $crontract_sellmoney = $this->product_base->countSellMoneyByCid($product['cid']);               //已卖出金额
        $crontract_onlineMoney = $this->product_base->countOnlineProductMoneyByCid($product['cid']);    //在架上的金额
        $countMoney = $crontract_sellmoney + $crontract_onlineMoney;
        //检查合同是否有采购
        //echo 'countMoney:'.$countMoney .'<br />';
        $contract = $this->contract_base->getContractByCid($product['cid']);
        //print_r($contract);
        if($contract['is_stock']){
           $this->load->model('base/stock_product_base', 'stock_product_base');
           $stock_money = $this->stock_product_base->sum_stock_money($product['cid']);
           echo 'stock_money:'.$stock_money .'<br />';
           $countMoney += $stock_money;
        }
        //余额回合同
        $this->contract_base->backMoneytoContract($product['cid'], $countMoney);
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
        $this->load->model('base/product_base', 'product_base');
        $ptid = $productInfo['ptid'];
        $productid = $this->product_base->getOnlineProductListFirstMem($ptid);
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

        $this->load->model('base/userproduct_base', 'userproduct_base');
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
        $this->userproduct_base->addUserProductInfo($uid, $userproduct_data);
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


   
