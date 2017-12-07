<?php

class tongdun_logic extends CI_Model {

    private $register_plat_params = array(
                                        'web' => array(
                                                        'secret_key' => '9486f707654147afacee82f47c856f38',
                                                        'register' => 'register_credit_web',
                                                        'login' => 'login_credit_web',
                                        ),
                                        'android' => array(
                                                        'secret_key' => '5bec3663a2cc4f7fbdb6fd380768f83a',
                                                        'register' => 'register_professional_android',
                                                        'login' => 'login_professional_android',
                                        ),
                                        'ios' => array(
                                                        'secret_key' => 'c1fc804546ce48eca568c27a7dc822f0',
                                                        'register' => 'register_professional_ios',
                                                        'login' => 'login_professional_ios',
                                        ),
                                    );
    private $devices = array('web', 'android', 'ios');
    function __construct() {
        # 继承CI 父类
        parent::__construct();
    }

    function login_check_phone($phone, $userip, $token_id, $state, $device){
        if(!in_array($device, $this->devices)){
            $device = 'web';
        }
        // 准备接口参数
        $data = array(
            "partner_code" => "cmibank",
            "secret_key" => $this->register_plat_params[$device]['secret_key'],
            "event_id" => $this->register_plat_params[$device]['login'],
            "account_login" => $phone,
            "state" => $state,
            "ip_address" => $userip,
        );
        if($device == 'web'){
            $data["token_id"] = $token_id;       //此处填写设备指纹服务的会话标识，和部署设备脚本的token一致
        }else{
            $data["black_box"] = $token_id;
        }
//         print_r($data);
        // 调用接口
        $result = $this->invoke_fraud_api($data);
    
        // 换行符， 如果是通过http访问本页面，则换行为<br/>,
        //         如果是通过命令行执行此脚本，换行符为\n
        $seperator = PHP_SAPI == "cli" ? "\n" : "<br/>";
    
        //         echo "接口调用结果: ";
//                  var_dump($result);
        //         echo "调用成功: ".($result["success"] ? "true" : "false").$seperator;
        //         if($result["success"]) {
        //             echo "决策结果: ".$result["final_decision"].$seperator;
        //         } else {
        //             echo "失败原因: ".$result["reason_code"].$seperator;
        //         }
        if($result["success"]) {
            if(isset($result["final_score"]) && $result["final_score"] < 60){
                return true;
            }else{
                return false;
            }
        } else {
             return array('msg' => $result["reason_code"]);
        }
        /* ----------- demo output --------------
         接口调用结果:
         Array
         (
         [final_decision] => Accept  // 最终的风险决策结果
         [final_score] => 0          // 风险分数
         [policy_name] => 登录策略                // 策略名称
         [seq_id] => xxx             // 请求序列号，全局唯一
         [spend_time] => 9           // 花费的时间，单位ms
         [success] => 1              // 执行是否成功，不成功时对应reason_code
         [hit_rules] => ["正则匹配"]   // 命中规则列表
         )
         调用成功: true
         决策结果: Accept
         -------------- demo output ----------- */
    }
    
    
    function register_check_phone($phone, $userip, $token_id, $device){
        if(!in_array($device, $this->devices)){
            $device = 'web';
        }
        // 准备接口参数
        $data = array(
            "partner_code" => "cmibank",
            "secret_key" => $this->register_plat_params[$device]['secret_key'],
            "event_id" => $this->register_plat_params[$device]['register'],
            "account_login" => $phone,
            "account_mobile" => $phone,
            "ip_address" => $userip,
        );
        if($device == 'web'){
            $data["token_id"] = $token_id;       //此处填写设备指纹服务的会话标识，和部署设备脚本的token一致
        }else{
            $data["black_box"] = $token_id;
        }
 //       print_r($data);
        // 调用接口
        $result = $this->invoke_fraud_api($data);
        
        // 换行符， 如果是通过http访问本页面，则换行为<br/>,
        //         如果是通过命令行执行此脚本，换行符为\n
        $seperator = PHP_SAPI == "cli" ? "\n" : "<br/>";
        
//         echo "接口调用结果: ";
//         var_dump($result);
        
//         echo "调用成功: ".($result["success"] ? "true" : "false").$seperator;
//         if($result["success"]) {
//             echo "决策结果: ".$result["final_decision"].$seperator;
//         } else {
//             echo "失败原因: ".$result["reason_code"].$seperator;
//         }

        if($result["success"]) {
            if(isset($result["final_score"]) && $result["final_score"] < 60){
                return true;
            }else{
                return false;
            }
        } else {
            return array('msg' => $result["reason_code"]);
        }
        /* ----------- demo output --------------
        接口调用结果:
        Array
        (
            [final_decision] => Accept  // 最终的风险决策结果
            [final_score] => 0          // 风险分数
            [policy_name] => 登录策略                // 策略名称
            [seq_id] => xxx             // 请求序列号，全局唯一
            [spend_time] => 9           // 花费的时间，单位ms
            [success] => 1              // 执行是否成功，不成功时对应reason_code
        	[hit_rules] => ["正则匹配"]   // 命中规则列表
        )
        调用成功: true
        决策结果: Accept
        -------------- demo output ----------- */
    }
    
    
    private function invoke_fraud_api(array $params, $timeout = 500, $connection_timeout = 500) {
//         $api_url = "https://apitest.fraudmetrix.cn/riskService";         //测试
        $api_url = "https://api.tongdun.cn/riskService";                //生产
        $options = array(
            CURLOPT_POST => 1,            // 请求方式为POST
            CURLOPT_URL => $api_url,      // 请求URL
            CURLOPT_RETURNTRANSFER => 1,  // 获取请求结果
            // -----------请确保启用以下两行配置------------
            CURLOPT_SSL_VERIFYPEER => 1,  // 验证证书
            CURLOPT_SSL_VERIFYHOST => 2,  // 验证主机名
            // -----------否则会存在被窃听的风险------------
            CURLOPT_POSTFIELDS => http_build_query($params) // 注入接口参数
        );
        if (defined("CURLOPT_TIMEOUT_MS")) {
            $options[CURLOPT_NOSIGNAL] = 1;
            $options[CURLOPT_TIMEOUT_MS] = 30000;
        } else {
            $options[CURLOPT_TIMEOUT] = 30;
        }
        if (defined("CURLOPT_CONNECTTIMEOUT_MS")) {
            $options[CURLOPT_CONNECTTIMEOUT_MS] = 30000;
        } else {
            $options[CURLOPT_CONNECTTIMEOUT] = 30;
        }
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        if(!($response = curl_exec($ch))) {
            // 错误处理，按照同盾接口格式fake调用结果
            return array(
                "success" => false,
                "reason_code" => "000:调用API时发生错误[".curl_error($ch)."]",
                
            );
        }
        curl_close($ch);
        $this->tongdun_log($params['account_login'].':'.$response);
        return json_decode($response, true);
    }
    
    private function tongdun_log($msg){
    	if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
    		$logFile = './tongdun_log_log.'.date("Y-m-d");
    	}else{
    		$logFile = '/usr/logs/tongdun_log.'.date("Y-m-d");
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


   
