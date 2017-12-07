<?php
//http://web.900112.com/main.html
class cpunc
{

    private $uid = "rexsong";   //用户账户
    private $pwd = "kikiku";    //用户密码
    
    //发送短信程序
    function send_msg($phone, $content){
        $otime = '';
        $client = new SoapClient("http://service2.winic.org:8003/Service.asmx?WSDL");
        $param = array('uid' => $this->uid,'pwd' => $this->pwd,'tos' => $phone,'msg' => $content,'otime'=>$otime);
        $result = $client->__soapCall('SendMessages',array('parameters' => $param));
        return $result;
    }
    
}

?>