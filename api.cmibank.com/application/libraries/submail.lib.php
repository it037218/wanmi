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
    
    //发送短信程序
    function send_msg($phone, $value, $msg_tpl_num = 'htWo6'){
        if(is_array($value)){
            $var = $value;
        }else{
            $var['code'] = $value;
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
    function send_email($email, $vars, $email_tpl_num = 'xxx'){
        $data = array(  'appid' => $this->email_appid,
                        'from' => $this->email_from,
                        'from_name' => $this->email_from_name,
                        'to' => $email,
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