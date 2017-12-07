<?php

date_default_timezone_set('PRC');  // 设置时区

class huoduoshuju_logic extends CI_Model {
	
	function validate($bankcard, $idcard, $realname, $mobile){
		// 配置您申请的mkey
		$mkey = "3524AB776BF34641";
		
		// ************银行卡四元素校验************
		$url = "https://api.haoduoshuju.com/credit/cert/five";
		$params = array(
                                'type' => '4',
				'realname'=>$realname,//真实姓名===类型:string,是否必须:是,
				'idcard'=>$idcard,//身份证号码===类型:string,是否必须:是,
				'bankcard'=>$bankcard,//银行卡卡号===类型:string,是否必须:是,
				'mobile'=>$mobile,//手机号码===类型:string,是否必须:是,
		);
		$params["uid"]="101627";
		$signStr = $this->createSign ( $params, $mkey );
		$this->haoduoshuju_log($signStr);
		$params["sign"] = strtoupper ( md5 ( $signStr ) );
		$paramstring = http_build_query ( $params );
		$content = $this->haocurl ( $url, $paramstring );
		$this->haoduoshuju_log($content);
		$result = json_decode ( $content, true );
		if ($result) {
                    return $result;
		} else {
                    return false;
		}
	}
	/** 
	 * curl请求
	 * @param string $url 请求地址
	 * @param array|string $data 请求参数
	 * @param number $timeout 超时设置
	 * @param string $type 类型
	 * @param array $header 头
	 */
	function haocurl($url,$data=array(),$timeout=10,$type="",$header=array()){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		if($timeout>0) 	curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
		if(strstr($url,"https://")){
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		}
		$user_agent = "Haoduo Curl/1.0";
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		if(!empty($data)) {
			$querystring = "";
			if (is_array($data)){
				$querystring = http_build_query($data);
			} else {
				$querystring = $data;
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $querystring);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$header=$header?$header:array();
		if($type=="json"){
			$header[]="Content-Type: application/json; charset=utf-8";
			$header[]="Cache-Control: no-cache";
		}
		if($type=="json"){
			$header[]="Content-Type: application/json; charset=utf-8";
			$header[]="Cache-Control: no-cache";
		}elseif($type=="xml"){
			$header[]="Content-Type: text/xml; charset=utf-8";
		}
	
		if(!empty($header)){
			curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		}
	
		$result=curl_exec($ch);
		$error = curl_error($ch);
		curl_close ($ch);
		return $error ? $error : $result;
	}
	/**
	 * 生成待签名参数串
	 *
	 * @access private
	 * @param array $param
	 *        	业务参数
	 * @param string $mkey
	 *        	商户md5密钥
	 *        	return string
	 */
	function createSign($param = array(), $mkey = "") {
		ksort ( $param );
		$string = "";
		foreach ( $param as $key => $val ) {
			if ($key != "" && $val != "") {
				$string .= $key . "=" . $val . "&";
			}
		}
		$string .= "key=" . $mkey;
		return $string;
	}
	
	private function haoduoshuju_log($msg){
		if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
			$logFile = './haoduoshuju_xml_log.'.date("Y-m-d");
		}else{
			$logFile = '/tmp/haoduoshuju_xml_log.'.date("Y-m-d");
		}
		$fp = fopen($logFile, 'a');
		$isNewFile = !file_exists($logFile);
		if (flock($fp, LOCK_EX)) {
			if ($isNewFile) {
				chmod($logFile, 0666);
			}
			fwrite($fp, $msg . "\n");
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
}


   
