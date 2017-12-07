<?php

class user_identity_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/user_identity_base' , 'user_identity_base');
    }

    
    public function getPublicUserIdentity($uid, $type = 'yee'){
        $result = $this->user_identity_base->getUserIdentity($uid);
        $identity = !empty($result) ? $result : false;
        if($type != 'yee'){
            return $identity;
        }
        $rtn = array();
        if($identity && $identity['ischeck'] == 1){
            $idcard = $identity['idCard'];
            $cardno = $identity['cardno'];
            $rtn['idCard'] = substr($idcard, 0, 6) . '********' . substr($idcard, -4);
            if($cardno){
                $rtn['cardno_top'] = substr($cardno, 0, 4);
                $rtn['cardno'] = substr($cardno, -4);
            }else{
                $rtn['cardno_top'] = '';
                $rtn['cardno'] = '';
            }
            $rtn['realname'] = '*' . mb_substr($identity['realname'], 1);
            $rtn['bankname'] = '';
            $this->config->load('cfg/banklist', true, true);
            $bankCfg = $this->config->item('cfg/banklist');
            $OpenBankId = $identity['bankcode'];
            if(isset($bankCfg[$OpenBankId])){
                $rtn['bankname'] = $bankCfg[$OpenBankId]['name'];
            }
            $rtn['bankid'] = $OpenBankId;
            $rtn['tpwd'] = $identity['tpwd'] ? true : false;
            $rtn['isnew'] = $identity['isnew'];
        }
        return $rtn;
    }
    
    
}


   
