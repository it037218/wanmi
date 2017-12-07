<?php
//====================配置商户的宝付接口授权参数============================

$member_id = "810937";	//商户号
$terminal_id = "29394";	//终端号
$data_type="json";//加密报文的数据类型（xml/json）
$txn_type = "03311";
// $private_key_password = "jhpcpbtqyvhnn99r";	//商户私钥证书密码
$private_key_password = "umt4wls7hkfewu5x";	

$pfxfilename = APPPATH . "libraries/baofoo/cert/wancaidog_pri.pfx"; 
$cerfilename = APPPATH . "libraries/baofoo/cert/wancaidog_pub.cer";

$jxcerfilename = APPPATH . "libraries/baofoo/cert/bfkey_810937@@29394.cer";


if(!file_exists($pfxfilename)){
    die("私钥证书不存在！<br>");
}
if(!file_exists($cerfilename)){
    die("公钥证书不存在！<br>");
}
require_once(APPPATH."/libraries/baofoo/lib/BaofooSdk.php");
require_once(APPPATH."/libraries/baofoo/lib/SdkXML.php");
require_once(APPPATH."/libraries/baofoo/lib/Log.php");
require_once(APPPATH."/libraries/baofoo/lib/HttpClient.php");

$ApiPostData = array("version" => "4.0.0.0",
                    "input_charset" => "1",	
                    "language" => "1",		 
                    "terminal_id" => $terminal_id,
                    "txn_type" => $txn_type,
                    "member_id" =>$member_id,
                    "data_type" => $data_type);

