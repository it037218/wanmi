<?php

class longproduct_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/longproduct_base' , 'longproduct_base');
    }


    public function getLongProductDetail($pid){
        return $this->longproduct_base->getLongProductDetail($pid);
    }
    
    public function setLongProductCache($longproduct){
        return $this->longproduct_base->setLongProductCache($longproduct);
    }
    
    public function getOnlineLongProductListFirstMem($ltid){
        return $this->longproduct_base->getOnlineLongProductListFirstMem($ltid);
    }
    
    public function rsyncLongProductSellMoney($pid){
        return $this->longproduct_base->rsyncLongProductSellMoney($pid);
    }
    
    public function getBuyUserByPid($pid){
        $this->load->model('base/longproduct_buy_info_base' , 'longproduct_buy_info_base');
        return $this->longproduct_buy_info_base->getBuyUserByPid($pid);
    }
    
    public function buy_longproduct($uid, $longproductInfo, $userIdentity, $money, $account, $paytype, $balance){
        $ptype = 'h';
        $pid = $longproductInfo['pid'];
        $ordid = date('YmdHis') . $uid . $pid . 'blp';
        $trxid = $ordid;
        
       
        
        //记录系统购买数
        $this->load->model('base/longproduct_buy_info_base' , 'longproduct_buy_info_base');
        $data = array(
            'uid' => $uid,
            'pid' => $pid,
            'account' => $account,
            'money' => $money,
            'trxid' => $trxid,
        );
        $this->longproduct_buy_info_base->addLongProductBuyInfo($pid, $data);
        
        //记录用户产品
        $this->load->model('base/userlongproduct_base', 'userlongproduct_base');
        $bankid = 0;
        if($paytype == 2){
            $bankid = $userIdentity['bankcode'];
        }
        $userproduct_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $longproductInfo['pname'],
            'money' => $money,
            'trxid' => $trxid,
            'paytype' => $paytype,
            'bankid' => $bankid,
            'from' => 'f'
        );
        $this->userlongproduct_base->addUserLongProductInfo($uid, $userproduct_data);
        //写用户日志
        $this->load->model('base/user_log_base', 'user_log_base');
        $user_log_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $longproductInfo['pname'],
            'money' => $money,
            'balance' => $balance - $money,
            'action' => USER_ACTION_LONGPRODUCT,                   //11购买活期成功
            'desc' => $paytype,
            'orderid' => $ordid
        );
        $this->user_log_base->addUserLog($uid, $user_log_data);
        
        $buy_log = array(
            'uid' => $uid,
            'ordid' => $ordid,
            'amt' => $money,
            'platform' => 'balance',
            'pid' => $pid,
            'pname' => $longproductInfo['pname'],
            'ctime' => time(),
            'ptype' => 'lp',
        );
        $this->load->model('base/buy_log_base' , 'buy_log');
        $this->buy_log->createBuyLog($buy_log);
        $this->load->model('base/longmoney_base', 'longmoney_base');
        $this->longmoney_base->add_longmoney($uid, $money);
        return $trxid;
    }
    
    
    /*
     * status 3售罄,
     */
    public function setLongProductSellOut($ltid, $pid){
        //从缓存中去掉
        $this->longproduct_base->moveOnlineLongProduct($ltid, $pid);
        //更改产品状态  3售罄
        $update_data = array('status' => 3, 'sellouttime' => NOW);
        $this->longproduct_base->updateLongProductStatus($pid, $update_data);
        $this->load->model('base/ltype_longproduct_base', 'ltype_longproduct_base');
        $data = array();
        $data['status'] = 1;
        $data['rindex'] = 0;
        $where = array();
        $where['ptid'] = $ltid;
        $where['pid'] = $pid;
        $where['odate'] = date('Y-m-d');
        //更改数据库备份数据
        $this->ltype_longproduct_base->updateltypeLongProduct($data, $where);
        if($ltid == LONGPRODUCT_PTID){
            $this->longproduct_base->addLongProductToSellOutList($pid);
        }
    }
    
    public function getLongmoney($uid){
        $this->load->model('base/longmoney_base', 'longmoney_base');
        return $this->longmoney_base->getUserLongMoney($uid);
    }
    
    public function cost_longmoney($uid, $money){
        $this->load->model('base/longmoney_base', 'longmoney_base');
        return $this->longmoney_base->cost($uid, $money);
    }
}


   
