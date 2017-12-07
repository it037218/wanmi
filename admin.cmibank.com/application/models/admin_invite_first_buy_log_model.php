<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_invite_first_buy_log_model extends Basemodel {
    
     private $_table = 'cmibank_log.cmibank_invite_first_buy_log_2015_';
     
     public function __construct() {
         parent::__construct();
     }

     
     
    public function get_invite_first_buy_list($index,$where,$order,$limit=''){
         $tableName ="cmibank_log.cmibank_invite_first_buy_log_2015_$index";
         $sql = "SELECT SUM(money) FROM $tableName where ctime BETWEEN '1446307200' and '1448899199';";
         $aa = $this->executeSql($sql);
         return $aa[0]['SUM(money)'];
     }


     
     
     
}