<?php
//====================配置商户的宝付接口授权参数============================

$member_id = "810937";	//商户号
$terminal_id = "33053";	//终端号
$data_type="json";//加密报文的数据类型（xml/json）
$txn_type = "03311";
// $private_key_password = "jhpcpbtqyvhnn99r";	//商户私钥证书密码
$private_key_password = "040985";
$pfxfilename = APPPATH . "libraries/baofoo/cert/cmibank_withdraw_pri.pfx";  //商户私钥
$cerfilename = APPPATH . "libraries/baofoo/cert/cmibank_withdraw_pub.cer";//商户公钥

$jxcerfilename = APPPATH . "libraries/baofoo/cert/bfkey_810937@@33053.cer";//宝付公钥

if(!file_exists($pfxfilename)){
    die("私钥证书不存在！<br>");
}
if(!file_exists($cerfilename)){
    die("公钥证书不存在！<br>");
}
require_once(APPPATH."/libraries/baofoo/withdraw/TransContent.php");
require_once(APPPATH."/libraries/baofoo/withdraw/TransDataUtils.php");
require_once(APPPATH."/libraries/baofoo/withdraw/TransHead.php");
require_once(APPPATH."/libraries/baofoo/withdraw/TransReqData.php");
require_once(APPPATH."/libraries/baofoo/withdraw/withDrawBaofooSdk.php");
require_once(APPPATH."/libraries/baofoo/lib/Log.php");


