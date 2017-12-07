<?php

/* *
 * 类名：LLpayNotify
 * 功能：连连支付通知处理类
 * 详细：处理连连支付各接口通知返回
 * 版本：1.1
 * 日期：2014-04-16
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************注意*************************
 * 调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
 */

require_once ("llpay_core.function.php");
require_once ("llpay_md5.function.php");
require_once ("llpay_rsa.function.php");

class LLpayNotify {
	var $llpay_config;
    var $notifyResp = array();
    var $result = false;
	function __construct($llpay_config) {
		$this->llpay_config = $llpay_config;
	}
	function LLpayNotify($llpay_config) {
		$this->__construct($llpay_config);
	}
	

	/**
	 * 针对notify_url验证消息是否是连连支付发出的合法消息
	 * @return 验证结果
	 */
	function verifyNotify() {
		//生成签名结果
			$is_notify = true;
			include_once ('llpay_cls_json.php');
			$json = new JSON;
			$str = file_get_contents("php://input");
			//$str = '{"acct_name":"王相尧","bank_code":"03080000","dt_order":"20151222151940","id_no":"430525198703302314","id_type":"0","info_order":"充值","money_order":"0.01","no_agree":"2015122137120509","no_order":"201512221519401208311794","oid_partner":"201408071000001543","oid_paybill":"2015122241340471","pay_type":"D","result_pay":"SUCCESS","settle_date":"20151222","sign":"fc1ea75d9aebff9c848c9db4742d239e","sign_type":"MD5"}';
			$val = $json->decode($str);
			$oid_partner = getJsonVal($val,'oid_partner' );
			$sign_type = getJsonVal($val,'sign_type' );
			$sign = getJsonVal($val,'sign' );
			$dt_order = getJsonVal($val,'dt_order' );
			$no_order = getJsonVal($val,'no_order' );
			$oid_paybill = getJsonVal($val,'oid_paybill' );
			$money_order = getJsonVal($val,'money_order' );
			$result_pay = getJsonVal($val,'result_pay' );
			$settle_date = getJsonVal($val,'settle_date' );
			$info_order = getJsonVal($val,'info_order');
			$pay_type = getJsonVal($val,'pay_type' );
			$bank_code = getJsonVal($val,'bank_code' );
			$no_agree = getJsonVal($val,'no_agree' );
			$id_type = getJsonVal($val,'id_type' );
			$id_no = getJsonVal($val,'id_no' );
			$acct_name = getJsonVal($val,'acct_name' );
		
		//首先对获得的商户号进行比对
		if ($oid_partner != $this->llpay_config['oid_partner']) {
			//商户号错误
			return;
		}
		$parameter = array (
			'oid_partner' => $oid_partner,
			'sign_type' => $sign_type,
			'dt_order' => $dt_order,
			'no_order' => $no_order,
			'oid_paybill' => $oid_paybill,
			'money_order' => $money_order,
			'result_pay' => $result_pay,
			'settle_date' => $settle_date,
			'info_order' => $info_order,
			'pay_type' => $pay_type,
			'bank_code' => $bank_code,
			'no_agree' => $no_agree,
			'id_type' => $id_type,
			'id_no' => $id_no,
			'acct_name' => $acct_name
		);
		
		if (!$this->getSignVeryfy($parameter, $sign)) {
			return;
		}
		$this->notifyResp = $parameter;
		$this->result = true;
		return true;
	}

	//和取现分开，但内容一样，方便分开调试
	function withdrawVerifyNotify() {
	    //生成签名结果
	    $is_notify = true;
	    include_once ('llpay_cls_json.php');
	    $json = new JSON;
	    $str = file_get_contents("php://input");
// 	    $str = '{"dt_order":"20151228132715","info_order":"用户取现","money_order":"5.0","no_order":"201512281327141208371524","oid_partner":"201510201000546504","oid_paybill":"2015122855457973","result_pay":"SUCCESS","settle_date":"20151228","sign":"c2bb973f8aa469928e697f739a4cb325","sign_type":"MD5"}';
	    $val = $json->decode($str);
	    $oid_partner = getJsonVal($val,'oid_partner');
	    $sign_type = getJsonVal($val,'sign_type');
	    $sign = getJsonVal($val,'sign');
	    $dt_order = getJsonVal($val,'dt_order');
	    $no_order = getJsonVal($val,'no_order');
	    $oid_paybill = getJsonVal($val,'oid_paybill');
	    $money_order = getJsonVal($val,'money_order');
	    $result_pay = getJsonVal($val,'result_pay');
	    $settle_date = getJsonVal($val,'settle_date');
	    $info_order = getJsonVal($val,'info_order');
	    $pay_type = getJsonVal($val,'pay_type');
	    $bank_code = getJsonVal($val,'bank_code');
	    $no_agree = getJsonVal($val,'no_agree');
	    $id_type = getJsonVal($val,'id_type');
	    $id_no = getJsonVal($val,'id_no');
	    $acct_name = getJsonVal($val,'acct_name');
	
	    //首先对获得的商户号进行比对
	    if ($oid_partner != $this->llpay_config['oid_partner']) {
	        //商户号错误
	        return;
	    }
	    $parameter = array (
	        'oid_partner' => $oid_partner,
	        'sign_type' => $sign_type,
	        'dt_order' => $dt_order,
	        'no_order' => $no_order,
	        'oid_paybill' => $oid_paybill,
	        'money_order' => $money_order,
	        'result_pay' => $result_pay,
	        'settle_date' => $settle_date,
	        'info_order' => $info_order,
	        'pay_type' => $pay_type,
	        'bank_code' => $bank_code,
	        'no_agree' => $no_agree,
	        'id_type' => $id_type,
	        'id_no' => $id_no,
	        'acct_name' => $acct_name
	    );
	    if (!$this->getSignVeryfy($parameter, $sign)) {
	        return;
	    }
	    $this->notifyResp = $parameter;
	    $this->result = true;
	    return true;
	}
	
	/**
	 * 针对return_url验证消息是否是连连支付发出的合法消息
	 * @return 验证结果
	 */
	function verifyReturn() {
		if (empty ($_POST)) { //判断POST来的数组是否为空
			return false;
		} else {
			//首先对获得的商户号进行比对
			if (trim($_POST['oid_partner' ]) != $this->llpay_config['oid_partner']) {
				//商户号错误
				return false;
			}

			//生成签名结果
			$parameter = array (
				'oid_partner' => $_POST['oid_partner' ],
				'sign_type' => $_POST['sign_type'],
				'dt_order' => $_POST['dt_order' ],
				'no_order' =>  $_POST['no_order' ],
				'oid_paybill' => $_POST['oid_paybill' ],
				'money_order' => $_POST['money_order' ],
				'result_pay' =>  $_POST['result_pay'],
				'settle_date' => $_POST['settle_date'],
				'info_order' =>$_POST['info_order'],
				'pay_type'=>$_POST['pay_type'],
				'bank_code'=>$_POST['bank_code'],
			);

			if (!$this->getSignVeryfy($parameter, trim($_POST['sign' ]))) {
				return false;
			}
			return true;

		}
	}

	/**
	 * 获取返回时的签名验证结果
	 * @param $para_temp 通知返回来的参数数组
	 * @param $sign 返回的签名结果
	 * @return 签名验证结果
	 */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = argSort($para_filter);

		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);

		//file_put_contents("log.txt", "原串:" . $prestr . "\n", FILE_APPEND);
		//file_put_contents("log.txt", "sign:" . $sign . "\n", FILE_APPEND);
		$isSgin = false;
		switch (strtoupper(trim($this->llpay_config['sign_type']))) {
		    case "MD5" :
		        $isSgin = md5Verify($prestr, $sign, $this->llpay_config['key']);
		        break;
		    case "RSA" :
		        $isSgin = Rsaverify($prestr, $sign);
		        break;
		    default :
		        $isSgin = false;
		}

		return $isSgin;
	}

}
?>
