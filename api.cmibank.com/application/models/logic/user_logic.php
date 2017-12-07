<?php
class user_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();        
    }

    
    public function getUserProductProfitDetail($uid, $type, $star, $end, $withScore = false){
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $data = $this->userproduct_base->getProfitDetailCache($uid, $type, $star, $end, $withScore);
        if(empty($data)){
            $this->initUserProductProfitDetailCache($uid, $type);
            $data = $this->userproduct_base->getProfitDetailCache($uid, $type, $star, $end, $withScore);
        }
        $rtn = array();
        if(!empty($data)){
            foreach ($data as $_val){
                $_val = json_decode($_val, true);
                $k = key($_val);
                $v = current($_val);
                $rtn[$k] = $v;
            }
        }
        return $rtn;
    }
    
    public function initUserProductProfitDetailCache($uid, $type = 1){
        $this->load->model('base/product_base', 'product_base');
        $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
        $productProfitList = $this->up_profit_log_base->get_up_profit_buy_uid($uid);
        $productDetailList = array();
        $rtn_productList = array();
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $data = $this->userproduct_base->_get_db_userProduct($uid, $type, false);
        $product_ids = array();
        foreach ($data as $_d){
            $product_ids[] = $_d['pid'];
        }
        foreach ($productProfitList as $_u_profit){
            if(!in_array($_u_profit['pid'], $product_ids)){
                continue;
            }
            if(!isset($productDetailList[$_u_profit['pid']])){
                $productDetailList[$_u_profit['pid']] = $this->product_base->getProductDetail($_u_profit['pid']);
            }
            $productDetail = $productDetailList[$_u_profit['pid']];
            if(!isset($rtn_productList[$_u_profit['odate']][$_u_profit['pid']])){
                $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['profit'] = 0;
                $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['pname'] = $productDetail['pname'];
                $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['income'] = $productDetail['income'];
                $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['money'] = 0;
                $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['date'] = $_u_profit['odate'];
            }
            $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['money'] += $_u_profit['money'];
            $rtn_productList[$_u_profit['odate']][$_u_profit['pid']]['profit'] += $_u_profit['profit'];
        }
        $this->userproduct_base->setProfitDetailCache($uid, $type, $rtn_productList);
        return true;
    }
    
    
    public function get_not_finished_product($uid){
        $this->load->model('base/userproduct_base', 'userproduct_base');
        return $this->userproduct_base->getUserSumProductMoney($uid);
        
    }
    
    public function get_finished_product_profit($uid){
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $pid_arr = $this->userproduct_base->getPidsByStatus($uid, 1);
        if(empty($pid_arr)){
            return 0;
        }
        $pids = array();
        foreach ($pid_arr as $_pid){
            $pids[] = $_pid['pid'];
        }
        $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
        $sum_profit = $this->up_profit_log_base->get_sum_profit_by_pids($uid, $pids);
        return $sum_profit;
    }
    
    public function get_profit_buy_uid_and_pid($uid, $pids){
        $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
        $productProfitList = $this->up_profit_log_base->get_up_profit_buy_uid_and_pids($uid, $pids);
        return $productProfitList;
    }
    
    public function getUserKlProductProfitDetail($uid, $startime, $endtime){
        $this->load->model('base/uklp_profit_log_base', 'uklp_profit_log_base');
        $data = $this->uklp_profit_log_base->_get_user_klproduct_profit($uid, $startime, $endtime);
        return $data;
    }
    
    
    public function getUserLongProductProfitDetail($uid, $startime, $endtime){
        $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
        $data = $this->ulp_profit_log_base->_get_user_longproduct_profit($uid, $startime, $endtime);
        return $data;
    }
    
    public function getUserExpProductProfitDetail($uid, $startime, $endtime, $ue_ids = array()){
        $this->load->model('base/exp_profit_log_base', 'exp_profit_log_base');
        $data = $this->exp_profit_log_base->_get_user_expproduct_profit($uid, $startime, $endtime, $ue_ids);
        return $data;
    }
    
    public function getUserExpMoneyProfitDetail($uid, $startime, $endtime, $ue_ids = array()){
    	$this->load->model('base/expmoney_profit_base', 'expmoney_profit_base');
    	$data = $this->expmoney_profit_base->_get_user_expmoney_profit($uid, $startime, $endtime, $ue_ids);
    	return $data;
    }
    
    public function get_expproduct_yesterday_profit($uid){
        $this->load->model('base/exp_profit_log_base', 'exp_profit_log_base');
        return $this->exp_profit_log_base->get_yesterday_profit($uid);
    }
    
    public function get_expmoney_yesterday_profit($uid){
    	$this->load->model('base/expmoney_profit_base', 'expmoney_profit_base');
    	return $this->expmoney_profit_base->get_yesterday_profit($uid);
    }
    
    public function countUserLongProduct($uid){
        $this->load->model('base/ulp_profit_log_base', 'ulp_profit_log_base');
        $data = $this->ulp_profit_log_base->_count_user_longproduct_profit($uid);
        return $data;
    }

    public function countUserKlProduct($uid){
        $this->load->model('base/uklp_profit_log_base', 'uklp_profit_log_base');
        $data = $this->uklp_profit_log_base->_count_user_klproduct_profit($uid);
        return $data;
    }
    
    public function countUserExpProductProfit($uid, $ue_ids){
        $this->load->model('base/exp_profit_log_base', 'exp_profit_log_base');
        $data = $this->exp_profit_log_base->_count_user_expproduct_profit($uid, $ue_ids);
        return $data;
    }
    
    public function countUserExpmoneyProfit($uid, $eids){
    	$this->load->model('base/expmoney_profit_base', 'expmoney_profit_base');
    	$data = $this->expmoney_profit_base->get_product_count_profit($uid, $eids);
    	return $data;
    }
    
    public function userProfitInfo($uid){
        $this->load->model('base/userproduct_base', 'userproduct_base');
        return $this->userproduct_base->getAllProductInfo($uid);
    }
    
    public function get_product_yesterday_profit($uid){
        $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
        return $this->up_profit_log_base->get_yesterday_profit($uid);
    }
        
    public function get_product_count_profit($uid){
        $this->load->model('base/up_profit_log_base', 'up_profit_log_base');
        return $this->up_profit_log_base->get_product_count_profit($uid);
    }
    
    public function getUserProductInfo($uid, $status = 0){
        $this->load->model('base/userproduct_base', 'userproduct_base');
        $data = $this->userproduct_base->getUserProductInfo($uid, $status);
        return $data;
    }
    
    public function getUserLongProductInfo($uid, $status = 0){
        $this->load->model('base/userlongproduct_base', 'userlongproduct_base');
        $data = $this->userlongproduct_base->getUserLongProductInfo($uid, $status);
        return $data;
    }
    
    
    public function getUserKlProductInfo($uid, $status = 0){
        $this->load->model('base/userklproduct_base', 'userklproduct_base');
        $data = $this->userklproduct_base->getUserKlProductInfo($uid, $status);
        return $data;
    }
    
    public function getUserIdentity($uid){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->getUserIdentity($uid);
    }
    
    public function getUserIdentityByIdcard($idcard){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->getUserIdentityByIdcard($idcard);
    }
    
    public function countByPhone($phone){
    	$this->load->model('base/user_identity_base', 'user_identity_base');
    	return $this->user_identity_base->countByPhone($phone);
    }
    
    public function getUserIdentityByRequestId($requestid){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->getUserIdentityByRequestId($requestid);
    }
    
    public function initUserIdentity($data){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->initUserIdentity($data);
    }
    
    public function updateUserIdentity($data, $where){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->updateUserIdentity($data, $where);
    }
    
    public function setUserBankInfo($uid, $acctid, $bankid, $provid, $areaid, $phone){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->setUserBankInfo($uid, $acctid, $bankid, $provid, $areaid, $phone);
    }
    
    public function setUserTpwd($uid, $tpwd){
        $this->load->model('base/user_identity_base', 'user_identity_base');
        return $this->user_identity_base->setUserTpwd($uid, $tpwd);
    }
    
    public function getUserLog($uid, $type, $start, $end){
        $this->load->model('base/user_log_base', 'user_log_base');
        return $this->user_log_base->getUserLog($uid, $type, $start, $end);
    }
    
    //新用户日志 分段取数据
    public function getNewUserLog($uid, $type, $start, $end){
        $this->load->model('base/user_log_base', 'user_log_base');
        return $this->user_log_base->getUserLog($uid, $type, $start, $end);
    }
    
    public function getUserLogDetail($uid, $orderid){
    	$this->load->model('base/user_log_base', 'user_log_base');
    	return $this->user_log_base->getLogByOrderid($uid, $orderid);
    }
    
    public function makeSMSCode($uid){
        //后面改成短信
        $code = mt_rand(1000, 9999);
        $this->load->model('base/user_base', 'user_base');
        $ret = $this->user_base->setSMSCode($uid, $code);
        if($ret){
            return $code;
        }
        return false;
    }
    
    public function getSMSCode($uid){
        $this->load->model('base/user_base', 'user_base');
        $ret = $this->user_base->getSMSCode($uid);
        if($ret){
            return $ret;
        }
        return false;
    }
    
    //第三步验证码
    public function makeModifyTpwdCode($uid){
        $code = mt_rand(7000, 9999);
        $this->load->model('base/user_base', 'user_base');
        $ret = $this->user_base->setModifyTpwdCode($uid, $code);
        if($ret){
            return $code;
        }
        return false;
    }
    
    public function getBindBankCode($uid){
        $this->load->model('base/user_base', 'user_base');
        $code = $this->user_base->getBindBankCode($uid);
        if($code){
            return $code;
        }
        return false;
    }
    
    public function setBindBankCode($uid, $code){
        $this->load->model('base/user_base', 'user_base');
        $this->user_base->setBindBankCode($uid, $code);
        return true;
    }
    
    public function getBindBankPhone($uid){
        $this->load->model('base/user_base', 'user_base');
        $code = $this->user_base->getBindBankPhone($uid);
        if($code){
            return $code;
        }
        return false;
    }
    
    public function setBindBankPhone($uid, $phone){
        $this->load->model('base/user_base', 'user_base');
        $this->user_base->setBindBankPhone($uid, $phone);
        return true;
    }
    
    public function getModifyTpwdCode($uid){
        $this->load->model('base/user_base', 'user_base');
        $ret = $this->user_base->getModifyTpwdCode($uid);
        if($ret){
            return $ret;
        }
        return false;
    }
    
    public function moveModifyTpwdCode($uid){
        $this->load->model('base/user_base', 'user_base');
        $ret = $this->user_base->moveModifyTpwdCode($uid);
        if($ret){
            return $ret;
        }
        return false;
    }

    public function createMsgCode(){
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= mt_rand(0,9);
        }
        return $code;
    }
    
    public function send_pay_msg($phone, $money){
        $this->load->model('logic/msm_logic', 'msm_logic');
        return $this->msm_logic->send_pay_msg($phone, $money);        
    }
    
    public function incrModifyTpwdCode($uid){
    	$this->load->model('base/user_base', 'user_base');
    	return $this->user_base->incrModifyTpwdCode($uid);
    }
    
    public function check_pay_code($phone, $input_code){
    	$this->load->model('base/user_base', 'user_base');
    	$vc_code = $this->user_base->getPayCode($phone);
    	if($vc_code != $input_code){
    		return false;
    	}
    	return true;
    }
}


   
