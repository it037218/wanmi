<?php

/**
 * 易宝投资通接口
 */
class yeepay_tg {
    
	// CURL 参数 
	private $toRegisterAction = 'toRegister';
	
	//回调接口
	private $zhilianUrl = 'http://220.181.25.233:8081/member/bhaexter/bhaController';
	
	private $complete_notify_url;
	
	public function __construct($config) {
	    $this->complete_notify_url = DOMAIN . 'tg_yee_notify/complete_notify';
	}

	/**
	 * 输入
	 * platformNo  平台号
	 * platformUserNo  用户UID
	 * 返回
        platformNo Y 商户编号
        code Y 【见返回码】
        description N 描述信息
        memberType Y 【见会员类型】
        activeStatus Y 【见会员激活状态】
        balance Y 账户余额
        availableAmount Y 可用余额
        freezeAmount Y 冻结金额
        cardNo N 绑定的卡号,没有则表示没有绑卡
        cardStatus N 【见绑卡状态】
        bank N 【见银行代码】
        autoTender Y 是否已授权自动投标,true 或则false
        paySwift N 表示用户是否已开通快捷支付。NORMAL 表示未升级，UPGRADE 表示已升级
        bindMobileNo Y 表示平台会员手机号
	 */
	function getAccountInfo($platformUserNo){
	    $service = 'ACCOUNT_INFO';
	    $req = '<request platformNo="' . YEE_TG_PLATFORMNO . '"><platformUserNo>' . $platformUserNo . '</platformUserNo></request>';
	    $result = $this->http_post($this->zhilianUrl, array(
                              'service' => $service,
                              'req' => $req,
                              'sign' => $this->getSign($platformUserNo),
                            ));
	    $result = simplexml_load_string($result);
	    return $result;
	}
	
	/**
	platformUserNo Y 平台会员编号
    requestNo Y 请求流水号
    amount Y 冻结金额
    expired Y 到期自动解冻
	 */
	function freeze($platformUserNo, $requestNo, $amount, $time = 86400){
	    $service = 'FREEZE';
        $req = '<request platformNo="' . YEE_TG_PLATFORMNO . '">';
        $req .= '<requestNo>' . $requestNo . '</requestNo>';
        $req .= '<platformUserNo>' . $platformUserNo . '</platformUserNo>';
        $req .= '<amount>' . $amount . '</amount>';
        $req .= '<expired>' . date('Y-m-d H:i:s', time() + $time) . '</expired>';        
        $req .= '</request>';
        $req = str_replace("\n", '', $req);
        $result = $this->http_post($this->zhilianUrl, array(
            'service' => $service,
            'req' => $req,
            'sign' => $this->getSign($platformUserNo),
        ));
        $result = simplexml_load_string($result);
        return $result;
	}
	
	/*
	 主动解除冻结资金
	 */
	function unfreeze($freezeRequestNo){
	    
	    $service = 'UNFREEZE';
	    $req = '<request platformNo="' . YEE_TG_PLATFORMNO . '">';
	    $req .= '<freezeRequestNo>' . $freezeRequestNo . '</freezeRequestNo>';
	    $req .= '</request>';
	    $req = str_replace("\n", '', $req);
	    
	    $result = $this->http_post($this->zhilianUrl, array(
	        'service' => $service,
	        'req' => $req,
	        'sign' => $this->getSign($platformUserNo),
	    ));
	    $result = simplexml_load_string($result);
	    return $result;
	    
	    
	}
	
	/*
	 查询 
	 */
	public function query($requestNo, $mode){
	    $modes = array( 
	        1 => 'WITHDRAW_RECORD',    //取现
	        2 => 'RECHARGE_RECORD',    //充值
	        3 => 'CP_TRANSACTION',     //转账
	        4 => 'FREEZERE_RECORD'     //冻结、解冻
	    );
	    if(!isset($modes[$mode])){
	        return false;
	    }
	    $mode = $modes[$mode];
	    $service = 'QUERY';
	    $req = '<request platformNo="' . YEE_TG_PLATFORMNO . '">';
	    $req .= '<requestNo>' . $requestNo . '</requestNo>';
	    $req .= '<mode>' . $mode . '</mode>';
	    $req .= '</request>';
	    $req = str_replace("\n", '', $req);
	    
	    $result = $this->http_post($this->zhilianUrl, array(
	        'service' => $service,
	        'req' => $req,
	        'sign' => $this->getSign($requestNo),
	    ));
	    $result = simplexml_load_string($result);
	    return $result;
	}
	
	//自动投标援权
	function auto_transaction($req){
	    $service = 'AUTO_TRANSACTION';
	   
	    $result = $this->http_post($this->zhilianUrl, array(
	        'service' => $service,
	        'req' => $req,
	        'sign' => $this->getSign(123),
	    ));
	    $result = simplexml_load_string($result);
	    return $result;
	    
	}
	
	//转账确认
	public function complete_transaction($requestNo, $mode = 1){
	    $modes = array(
	        1 => 'CONFIRM',    //表示解冻后完成资金划转
	        2 => 'CANCEL',    //表示解冻后取消转账
	    );
	    if(!isset($modes[$mode])){
	        return false;
	    }
	    $mode = $modes[$mode];
	    $service = 'COMPLETE_TRANSACTION';
	    $platformNo = YEE_TG_PLATFORMNO;
	    $req = '<request platformNo="' . YEE_TG_PLATFORMNO . '">';
	    $req .= '<requestNo>' . $requestNo . '</requestNo>';
	    $req .= '<mode>' . $mode . '</mode>';
	    $req .= '<notifyUrl>' . $this->complete_notify_url . '</notifyUrl>';
	    $req .= '</request>';
	    $req = str_replace("\n", '', $req);
	    $result = $this->http_post($this->zhilianUrl, array(
	        'service' => $service,
	        'req' => $req,
	        'sign' => $this->getSign(YEE_TG_PLATFORMNO),
	    ));
	    $result = simplexml_load_string($result);
	    return $result;
	}
	
	//取消自动投标授权
	function cancel_authorize_auto_transfer($platformUserNo){
	    $requestNo = 'cato'. date('YmdHis') . $platformUserNo . mt_rand(100,999);
	    $service = 'CANCEL_AUTHORIZE_AUTO_TRANSFER';
	    $req =  '<request platformNo="' . YEE_TG_PLATFORMNO . '">';
	    $req .= '<requestNo>' . $requestNo . '</requestNo>';
	    $req .= '<platformUserNo>' . $platformUserNo . '</platformUserNo>';
	    $req .= '</request>';
	    $req = str_replace("\n", '', $req);
	    $result = $this->http_post($this->zhilianUrl, array(
	        'service' => $service,
	        'req' => $req,
	        'sign' => $this->getSign(YEE_TG_PLATFORMNO),
	    ));
	    $result = simplexml_load_string($result);
	    return $result;
	}
	
	//项目(标的)查询
	function project_query($orderNo){
	    $service = 'PROJECT_QUERY';
	    $req =  '<request platformNo="' . YEE_TG_PLATFORMNO . '">';
	    $req .= '<orderNo>' . $orderNo . '</orderNo>';
	    $req .= '</request>';
	    $req = str_replace("\n", '', $req);
	    $result = $this->http_post($this->zhilianUrl, array(
	        'service' => $service,
	        'req' => $req,
	        'sign' => $this->getSign(YEE_TG_PLATFORMNO),
	    ));
	    $result = simplexml_load_string($result);
	    return $result;
	}
	
	
	function getSign($data){
	    return 'xxx';
	}
	
	function http_post($url, $data) {
	    $form_data = "";
	    foreach($data as $key => $value) {
	        if ($form_data == "") {
	            $form_data = $key . "=" . rawurlencode($value);
	        } else {
	            $form_data = $form_data . "&" . $key . "=" . rawurlencode($value);
	        }
	    }
	    $ch = curl_init($url);
	    curl_setopt_array($ch, array(
    	    CURLOPT_POST => TRUE,
    	    CURLOPT_RETURNTRANSFER => TRUE,
    	    CURLOPT_POSTFIELDS => $form_data
	    ));
	    $result = curl_exec($ch);
	    return $result;
	}

	
	
}

// class yeepayException extends Exception {
	
// }