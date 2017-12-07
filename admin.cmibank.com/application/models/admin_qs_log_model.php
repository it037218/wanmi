<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_qs_log_model extends Basemodel {
    
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
     
     public function getSumWithOdate($odate){
         $sql = "SELECT sum(`pay`) as sum_pay, sum(`withdraw`) as sum_withdraw, sum(p_profit) as sum_p_profit, sum(l_profit) as sum_l_profit,  sum(`invite_reward`) as sum_invite_reward, sum(`invite_user_reward`) as sum_invite_user_reward, sum(`activity_reward`) as sum_activity_reward,sum(`i_first_buy`) as sum_i_first_buy,sum(`sxf`) as sum_sxf,sum(`hongbao`) as sum_hongbao,sum(`coupon`) as sum_coupon,sum(`repayment_profit`) as sum_repayment_profit,sum(`buchang`) as sum_buchang,sum(`exp_profit`) as sum_exp_profit,sum(`luckybag`) as sum_luckybag,sum(`jifeng`) as sum_jifeng FROM " . $this->_table . " WHERE `odate` <= '$odate'";
         $data = $this->executeSql($sql);
         return $data;
     }
     
     public function getListBetweenTime($stime,$etime){
     	$sql = "select odate, pay,withdraw,p_userbuy,lp_buy from $this->_table where UNIX_TIMESTAMP(odate)>=$stime and UNIX_TIMESTAMP(odate)<=$etime order by UNIX_TIMESTAMP(odate) asc";
     	$data = $this->executeSql($sql);
     	return $data;
     }
     
     public function getListforSevendays($stime){
     	$sql = "select withdraw from $this->_table where UNIX_TIMESTAMP(odate)<$stime order by UNIX_TIMESTAMP(odate) desc limit 7";
     	$data = $this->executeSql($sql);
     	return $data;
     }
     
     public function getListforForteendays($stime){
     	$sql = "select withdraw from $this->_table where UNIX_TIMESTAMP(odate)<$stime order by UNIX_TIMESTAMP(odate) desc limit 15";
     	$data = $this->executeSql($sql);
     	return $data;
     }
}