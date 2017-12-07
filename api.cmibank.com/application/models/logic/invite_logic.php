<?php
class invite_logic extends CI_Model {

    private $_cfg ;
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/invite_base' , 'invite_base');
    }

    public function add_invite($uid, $u_account, $invite_uid, $invite_account){
        return $this->invite_base->add_invite($uid, $u_account, $invite_uid, $invite_account);
    }
    
    
    //用户UID  我邀请的人
    public function get_my_invite($uid, $isbuy = false){
        $data = $this->invite_base->get_my_invite($uid);
        $rtn = array();
        foreach ($data as $_d){
            $_r = array();
            if($isbuy && !$_d['buytime']){
                continue;
            }
            if($isbuy){
                $_r['rewardmoney'] = $_d['rewardmoney'];
                $_r['buytime'] = $_d['buytime'];
            }
            $_r['account'] = substr($_d['u_account'], 0, 3) . '****' . substr($_d['u_account'], -4);
            $_r['itime'] = $_d['itime'];
            $rtn[] = $_r;
        }
        return $rtn;
    }
    
    //邀请我的人
    public function get_invite_my($uid){
        $data = $this->invite_base->get_invite_my($uid);
        return $data;
    }
    
    /**
     * @param int $uid 用户uid
     * @param int $invite_uid 用户邀请人uid
     * @param int $rewardmoney
     * @param int $first_buy_money
     * @param int $money
     * @return
     */
    public function update_my_buytime($uid, $invite_uid, $rewardmoney,$first_buy_money,$money){
        return $this->invite_base->_db_update_my_buytime(array('buytime' => NOW, 'rewardmoney' => $rewardmoney,'first_buy_reward' =>$first_buy_money,'buymoney' =>$money), array('uid' => $uid, 'invite_uid' => $invite_uid));

    }
    
    public function getCfg(){
        if($this->_cfg){
            return $this->_cfg;
        }
        $this->config->load('cfg/invite_cfg', true, true);
        $this->_cfg = $this->config->item('cfg/invite_cfg');
        return $this->_cfg;
    }

    public function get_user_inviterward($uid, $start, $end){
        $this->load->model('base/user_invitereward_base', 'user_invitereward');
        $data = $this->user_invitereward->get_user_inviterward($uid, $start, $end);
        if(empty($data)){
            $this->user_invitereward->init_user_invitereward($uid);
            $data = $this->user_invitereward->get_user_inviterward($uid, $start, $end);
        }
        foreach ($data as &$_d){
            $_d = json_decode($_d, true);
        	$_d['account'] = substr($_d['account'], 0, 3) . '****' . substr($_d['account'], -4);
        }
        return $data;
    }
    
    public function getinvitemoney($uid){
        $this->load->model('base/invite_base', 'invite_base');
        $data = $this->invite_base->count_user_invite_money($uid);
        return $data ? $data : 0;
    }
    
    public function getinvitereward($uid){
        $this->load->model('base/user_invitereward_base', 'user_invitereward_base');
        $data = $this->user_invitereward_base->count_user_inviterward($uid);
        return $data ? $data : 0;
    }
    public function top_rank($type = false){
        return $this->invite_base->top_rank($type);
    }
    public function top_rank2($type = false){
        return $this->invite_base->top_rank2($type);
    }
    public function getFirstInvenst($top_rank){
        return $this->invite_base->getFirstInvenst($top_rank);
    }
    public function getFirstInvenst2($top_rank){
        return $this->invite_base->getFirstInvenst2($top_rank);
    }
    
    public function queryBeInvite($account) {
        return $this->invite_base->queryBeInvite($account);
    }
	public function getUserInfo($uid){
        if ($uid){
            $this->load->model('base/invite_base', 'invite_base');
            $result = $this->invite_base->getUserInfo($uid);
            return $result;
        }
    }
}

   
