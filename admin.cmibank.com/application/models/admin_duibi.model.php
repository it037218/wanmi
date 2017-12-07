<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_duibi_model extends Basemodel {

    private $_table = 'cmibank_yunying.cmibank_qs_log';
 
 
  
    public function __construct() {
        parent::__construct();
    }
 
     public function getList($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     
     public function getCount(){
         return $this->selectDataCountSql($this->_table);
     }
     
     public function getSumWithOdate(){  //余额                                                                                                                                                                                                                                                                                                                                               //用户奖励总和                                         邀请奖励用户总和                                          活期奖励总和         
          $sql = "SELECT sum(`pay`) as sum_pay, sum(`withdraw`) as sum_withdraw, sum(p_profit) as sum_p_profit, sum(l_profit) as sum_l_profit,  sum(`invite_reward`) as sum_invite_reward, sum(`invite_user_reward`) as sum_invite_user_reward, sum(`activity_reward`) as sum_activity_reward,sum(`i_first_buy`) as sum_i_first_buy,sum(`sxf`) as sum_sxf,sum(`hongbao`) as sum_hongbao,sum(`coupon`) as sum_coupon,sum(`repayment_profit`) as sum_repayment_profit FROM " . $this->_table;
         $data = $this->executeSql($sql);
         return $data;
     }
}