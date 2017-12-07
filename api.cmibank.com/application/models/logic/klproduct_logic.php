<?php

class klproduct_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/klproduct_base' , 'klproduct_base');
    }


    public function getklproductDetail($pid){
        return $this->klproduct_base->getklproductDetail($pid);
    }
    
    public function setklproductCache($klproduct){
        return $this->klproduct_base->setklproductCache($klproduct);
    }
    
    public function getOnlineklproductListFirstMem($ltid){
        return $this->klproduct_base->getOnlineklproductListFirstMem($ltid);
    }
    
    public function rsyncklproductSellMoney($pid){
        return $this->klproduct_base->rsyncklproductSellMoney($pid);
    }
    
    public function getBuyUserByPid($pid){
        $this->load->model('base/klproduct_buy_info_base' , 'klproduct_buy_info_base');
        return $this->klproduct_buy_info_base->getBuyUserByPid($pid);
    }
    
    public function buy_klproduct($uid, $klproductInfo, $userIdentity, $money, $account, $paytype, $balance){
        $ptype = 'kl';
        $pid = $klproductInfo['pid'];
        $ordid = date('YmdHis') . $uid . $pid . 'bkl';
        $trxid = $ordid;
        //记录系统购买数
        $this->load->model('base/klproduct_buy_info_base' , 'klproduct_buy_info_base');
        $data = array(
            'uid' => $uid,
            'pid' => $pid,
            'account' => $account,
            'money' => $money,
            'trxid' => $trxid,
        );
        $this->klproduct_buy_info_base->addKlProductBuyInfo($pid, $data);
        
        //记录用户产品
        $this->load->model('base/userklproduct_base', 'userklproduct_base');
        $bankid = 0;
        if($paytype == 2){
            $bankid = $userIdentity['bankcode'];
        }
        $userproduct_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $klproductInfo['pname'],
            'money' => $money,
            'trxid' => $trxid,
            'paytype' => $paytype,
            'bankid' => $bankid,
            'from' => $ptype
        );
        $this->userklproduct_base->addUserKlProductInfo($uid, $userproduct_data);
        //写用户日志
        $this->load->model('base/user_log_base', 'user_log_base');
        $user_log_data = array(
            'uid' => $uid,
            'pid' => $pid,
            'pname' => $klproductInfo['pname'],
            'money' => $money,
            'balance' => $balance - $money,
            'action' => USER_ACTION_KLPRODUCT,                   //31购买快活宝
            'desc' => $ptype,
            'orderid' => $ordid
        );
        $this->user_log_base->addUserLog($uid, $user_log_data);
        
        $buy_log = array(
            'uid' => $uid,
            'ordid' => $ordid,
            'amt' => $money,
            'platform' => 'balance',
            'pid' => $pid,
            'pname' => $klproductInfo['pname'],
            'ctime' => time(),
            'ptype' => $ptype,
        );
        $this->load->model('base/buy_log_base' , 'buy_log');
        $this->buy_log->createBuyLog($buy_log);
        $this->load->model('base/klmoney_base', 'klmoney_base');
        $this->klmoney_base->add_klmoney($uid, $money);
        return $trxid;
    }
    
    
    /*
     * status 3售罄,
     */
    public function setklproductSellOut($ltid, $pid){
        //从缓存中去掉
        $this->klproduct_base->moveOnlineklproduct($ltid, $pid);
        //更改产品状态  3售罄
        $update_data = array('status' => 3, 'sellouttime' => NOW);
        $this->klproduct_base->updateklproductStatus($pid, $update_data);
        $this->load->model('base/kltype_klproduct_base', 'kltype_klproduct_base');
        $data = array();
        $data['status'] = 1;
        $data['rindex'] = 0;
        $where = array();
        $where['ptid'] = $ltid;
        $where['pid'] = $pid;
        $where['odate'] = date('Y-m-d');
        //更改数据库备份数据
        $this->kltype_klproduct_base->updateKltypeKlProduct($data, $where);
        $this->klproduct_base->addKlProductToSellOutList($pid);
    }
    
    public function getklmoney($uid){
        $this->load->model('base/klmoney_base', 'klmoney_base');
        return $this->klmoney_base->getUserKlMoney($uid);
    }
    
    public function cost_klmoney($uid, $money){
        $this->load->model('base/klmoney_base', 'klmoney_base');
        return $this->klmoney_base->cost($uid, $money);
    }
    
}


   
