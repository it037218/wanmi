<?php

class submail
{
    private $email_from = 'kefu@cmibank.com';
    private $email_from_name = '万米财富管理有限公司';
    
    private $msg_url = 'http://api.submail.cn/message/xsend';
    private $msg_appid = '15325';
    private $msg_signature = 'fe17e9ff0991f2216d6df3bf7dab04d9';
    
    private $email_url = 'https://api.submail.cn/mail/xsend.json';
    private $email_appid = '12646';
    private $email_signature = '06d3cbd872589aae0af6950e2680d896';
    
//     private $msg_url = 'http://api.submail.cn/message/xsend';
//     private $msg_appid = '10209';
//     private $msg_signature = '61ab11ab42be633d8b272ad3dd7be5d2';
    
//     private $email_url = 'https://api.submail.cn/mail/xsend.json';
//     private $email_appid = '10404';
//     private $email_signature = 'bf2b4c63ae5751bdf67a361bec0b75e6';
    
    //发送短信程序
    function send_msg($phone, $code, $msg_tpl_num = 'LdU4Z1'){
        if(is_array($code)){
            $var = $code;
        }else{
            $var['code'] = $code;
        }
        $data = array('appid' => $this->msg_appid, 'to' => $phone, 'vars' => json_encode($var), 'project' => $msg_tpl_num, 'signature' => $this->msg_signature);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->msg_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;
    }
    
    //发送邮件程序
    function send_email($email,$cc,$subject, $vars, $email_tpl_num){
        $data = array(  'appid' => $this->email_appid,
                        'from' => $this->email_from,
                        'from_name' => $this->email_from_name,
                        'to' => $email,
                        'subject' => $subject,
                        'cc' => $cc,
                        'vars' => json_encode($vars),
                        'project' => $email_tpl_num,
                        'signature' => $this->email_signature
                );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->email_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    
}

?>