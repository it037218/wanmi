<?php

class baofoopay_logic extends CI_Model {
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
    }   
    
    public function withDraw($orderid, $money, $realname,$mobile,$id_num, $cardno, $bankname){
//         header("Content-type: text/html; charset=utf-8");
    	require (APPPATH . 'config/cfg/baofoo_withdraw_ini.php');
    	require_once(APPPATH."libraries/baofoo/withdraw/TransReqDataBF0040001.php");
    	Log::withdrawLogWirte("1111：".$orderid);
    	$withDrawBaofooSdk = new withDrawBaofooSdk($member_id, $terminal_id, $data_type, $pfxfilename, $jxcerfilename, $private_key_password);
    	Log::withdrawLogWirte("2222：".$money);
    	$transReqDatas = new TransReqData();
    	Log::withdrawLogWirte("3333：".$realname);
    	$human = new TransReqDataBF0040001();
		$human -> _set("trans_no", $orderid);
		$human -> _set("trans_money", $money);
		$human -> _set("to_acc_name", $realname);
		$human -> _set("to_acc_no", $cardno);
		$human -> _set("to_bank_name", $bankname);
		$human -> _set("trans_card_id", $id_num);
		$human -> _set("trans_mobile", $mobile);
    	$human -> _set("to_pro_name", "");
    	$human -> _set("to_city_name", "");
    	$human -> _set("to_acc_dept", "");
    	Log::withdrawLogWirte("4444：".$mobile);
		$transReqDatas -> __array_json_push($human -> _getValues());
		$transReqDatas = $transReqDatas -> __getTransReqDatas();
		$tmp = array();
		array_push($tmp,array("trans_reqData"=>$transReqDatas));
		$trans_content1 = new TransContent();
		$trans_content1 -> __set("trans_reqDatas", $tmp);
		Log::withdrawLogWirte("5555：".$id_num);
		$trans_content = new TransContent();
		$trans_content -> __set("trans_content", $trans_content1 -> __getTransContent());
		$data_content = TransDataUtils :: __array2Json($trans_content -> __getTransContent());
		$data_content = str_replace("\\\"",'"',$data_content);
		Log::withdrawLogWirte("6666：".$cardno);
    	$request_url = "https://public.baofoo.com/baofoo-fopay/pay/BF0040001.do";
    	Log::withdrawLogWirte("请求的明文：".$data_content);
    	// 私钥加密
    	$encrypted = $withDrawBaofooSdk->encryptedByPrivateKey($data_content);

// 		Log::withdrawLogWirte("请求的密文：".$encrypted); 
    	$httpResult = $withDrawBaofooSdk->post($encrypted, $request_url);
    	$return_data = array();
    	if(count(explode("trans_content",$httpResult))>1){
    	    $return_data['data'] = json_decode($httpResult, true);
    		Log::withdrawLogWirte("返回：".$httpResult);
    	}else{
    		$temp_return = $withDrawBaofooSdk -> decryptByPublicKey($httpResult);
    	    Log::withdrawLogWirte("返回：".$temp_return);
    	    $return_data['data'] = json_decode($temp_return,true);
    	}
    	return $return_data; 
    }
    
    public function query_withDraw_status($orderid){
        require (APPPATH . 'config/cfg/baofoo_withdraw_ini.php');
        $withDrawBaofooSdk = new withDrawBaofooSdk($member_id, $terminal_id, $data_type, $pfxfilename, $jxcerfilename, $private_key_password);
        require_once(APPPATH."/libraries/baofoo/withdraw/TransReqDataBF0040002.php");
        
         
        $human = new TransReqDataBF0040002();
        $human -> _set("trans_no", $orderid);
        $human -> _set("trans_batchid", '');
        // 添加到trans_reqDatas
        $transReqDatas = new TransReqData();
        $transReqDatas -> __array_json_push($human -> _getValues());
		$transReqDatas = $transReqDatas -> __getTransReqDatas();
		$tmp = array();
		array_push($tmp,array("trans_reqData"=>$transReqDatas));
		$trans_content1 = new TransContent();
		$trans_content1 -> __set("trans_reqDatas", $tmp);
		
		$trans_content = new TransContent();
		$trans_content -> __set("trans_content", $trans_content1 -> __getTransContent());
		$data_content = TransDataUtils :: __array2Json($trans_content -> __getTransContent());
		$data_content = str_replace("\\\"",'"',$data_content);
		Log::queryLogWirte("请求的明文：".$data_content);
		$request_url = "https://public.baofoo.com/baofoo-fopay/pay/BF0040002.do";
         
        // 私钥加密
        $encrypted = $withDrawBaofooSdk->encryptedByPrivateKey($data_content);
//         Log::queryLogWirte("请求的密文：".$encrypted);
        $httpResult = $withDrawBaofooSdk->post($encrypted, $request_url);
        $return_data = array();
    	if(count(explode("trans_content",$httpResult))>1){
    	    $return_data['data'] = json_decode($httpResult, true);
    		Log::queryLogWirte("返回：".$httpResult);
    	}else{
    		$temp_return = $withDrawBaofooSdk -> decryptByPublicKey($httpResult);
    		Log::queryLogWirte("返回：".$temp_return);
    	    $return_data['data'] = json_decode($temp_return,true);
    	}
    	return $return_data; 
    }
}


   
