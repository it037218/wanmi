<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_remindermail_model extends Basemodel {
     
     public function __construct() {
         parent::__construct();
     }

//    public function send_email($email,$cc,$subject, $vars, $email_tpl_num){
//       $this->load->library('email');
//        $config['protocol'] = 'smtp';  
//        $config['smtp_host'] = 'cloud.mysubmail.com';  
//        $config['smtp_user'] = '12646';
//        $config['smtp_pass'] = '06d3cbd872589aae0af6950e2680d896';
//        $config['smtp_port'] = '25';  
//        $config['charset'] = 'utf-8';  
//        $config['wordwrap'] = TRUE;  
//        $config['mailtype'] = 'html';  
//        $this->email->initialize($config);              
//        $this->email->from('service@cmibank.com', '万米财富管理有限公司催款邮件');  
//        $this->email->to($email);  
//        $this->email->cc($cc);
//        $this->email->subject($subject); 
//        $this->email->message($vars['content']);
////        $this->email->attach('application\controllers\1.jpeg');           //相对于index.php的路径  
//       $ret = $this->email->send();
//       if($ret){
//           return json_encode(array('status' => 'success'));
//       }
//       return json_encode(array('status' => 'fail'));
//    }
     
     public function send_email($email,$cc,$subject, $vars, $email_tpl_num){
         include(APPPATH . 'libraries/submail.lib.php');
         $submail = new submail();
         $rtn = $submail->send_email($email,$cc,$subject, $vars, $email_tpl_num);
         return $rtn;
     }
     
}