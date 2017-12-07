<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_invite_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_invite';
     
     public function __construct() {
         parent::__construct();
     }
     

     public function getInviteList($where,$order,$limit){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     public  function getInviteCount(){
         return $this->selectDataCountSql($this->_table);
     }
     public function getInviteByUid($uid){
         return $this->selectDataSql($this->_table, array('uid' => $uid));
     }


     
}