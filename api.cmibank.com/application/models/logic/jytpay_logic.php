<?php
require_once (APPPATH . 'libraries/jytpay/lib/Snoopy.class.php');
require_once (APPPATH . 'libraries/jytpay/lib/ENC.class.php');
require_once (APPPATH . 'libraries/jytpay/lib/ArrayToXML.class.php');


date_default_timezone_set('PRC');  // 设置时区

class jytpay_logic extends CI_Model {

    private $jytpay_config;
    
    private $pay_url;
    private $validate_url;
    private $mer_pub_file;                        // 商户RSA公钥
    private $mer_pri_file;                        // 商户RSA私钥
    private $pay_pub_file;                        // 平台RSA公钥
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->config->load('cfg/jytpay_config', true, true);
        $this->jytpay_config = $this->config->item('cfg/jytpay_config');
        
        $this->pay_url = 'https://www.jytpay.com:9010/JytCPService/tranCenter/encXmlReq.do';
        $this->validate_url = 'https://www.jytpay.com:9210/JytAuth/tranCenter/authReq.do';
//         $this->pay_url = 'http://test1.jytpay.com:8080/JytCPService/tranCenter/encXmlReq.do';
//         $this->validate_url = 'http://test1.jytpay.com:20080/JytAuth/tranCenter/authReq.do';
        $this->mer_pub_file = APPPATH . 'libraries/jytpay/cert/mer_public_key_2048.pem';                         // 商户RSA公钥
        $this->mer_pri_file = APPPATH . 'libraries/jytpay/cert/mer_private_key_2048.pem';                        // 商户RSA私钥
        $this->pay_pub_file = APPPATH . 'libraries/jytpay/cert/pay_public_key_2048.pem';                         //平台RSA公钥
    }

    function validate($ordid, $account_no, $id_num, $id_name, $mobile, $bank_code = ''){                             // 平台RSA公钥
        $mer_pub_file = $this->mer_pub_file;                         // 商户RSA公钥
        $mer_pri_file = $this->mer_pri_file;                         // 商户RSA私钥
        $pay_pub_file = $this->pay_pub_file;                         // 平台RSA公钥
        $m = new ENC($pay_pub_file, $mer_pri_file);
        //代收
        $request_code = 'TR4003';
        
        $req_param = $this->buildHead($ordid, $request_code);
        
        $req_param[ 'tran_date' ] = date( 'Ymd' );
        $req_param[ 'tran_time' ] = date( 'His' );
        $req_param[ 'tran_code' ] =  $request_code;
        /* 2. --- 请根据接口报文组织请求报文体 ，下面例子为身份认证交易请求报文体，请按照实际交易接口填充内容  */
        
        //         $req_body['mer_viral_acct'] = $this->jytpay_config['pay_account'];      //代收虚拟账号
        
        $req_body['bank_card_no'] = $account_no;        //银行卡号
        if($bank_code){
            $req_body['bank_code'] = $bank_code;            //银行编号
        }
        
        $req_body['id_num'] = $id_num;                  //开户身份证号
        $req_body['id_name'] = $id_name;                //开户名字
        $req_body['terminal_type'] = '01';              //请求终端类型   01 APP，02 WAP，03 WEB，04 SIM卡，05 VI-POS，06 SD卡
        $req_body['bank_card_type'] = 'D';              //D 借记卡  C 贷记卡   A 全部（如果商户平台借记卡贷记卡都支持的话传A）
        $req_body['phone_no'] = $mobile;                //银行卡开户预留的手机号码
        
//         print_r($req_body);
        /* 3. 转换请求数组为xml格式  */
        return $this->jiexi($req_param, $req_body, $m, true);
    }
    
    function pay($uid, $ordid, $tran_amt, $id_num, $id_name, $bank_name, $account_no, $mobile, $zj){
        /* 0. 请根据对接产品类型和实际商户号修改如下信息  */
                                                     
        $mer_pub_file = $this->mer_pub_file;                         // 商户RSA公钥
        $mer_pri_file = $this->mer_pri_file;                         // 商户RSA私钥
        $pay_pub_file = $this->pay_pub_file;                         // 平台RSA公钥
        
        $m = new ENC($pay_pub_file, $mer_pri_file);
        //代收
        $request_code = 'TC1001';
        
        $req_param = $this->buildHead($ordid, $request_code);
        
        /* 2. --- 请根据接口报文组织请求报文体 ，下面例子为身份认证交易请求报文体，请按照实际交易接口填充内容  */
        
//         $req_body['mer_viral_acct'] = $this->jytpay_config['pay_account'];      //代收虚拟账号
        
        $req_body['bank_name'] = $bank_name;            //银行名称
        $req_body['account_no'] = $account_no;          //银行卡号
        $req_body['account_name'] = $id_name;           //银行用户名称
        $req_body['tran_amt'] = $tran_amt;              //交易金额
        $req_body['mobile'] = $mobile;                  //手机号码
        
        $req_body['account_type'] = '00';               //账户类型
        $req_body['currency'] = 'CNY';                  //交易金额
        $req_body['bsn_code'] = '11201';                //业务类型代码
        $req_body['cert_type'] = $zj;                  //开户证件类型      01：身份证
        $req_body['cert_no'] = $id_num;                 //开户证件号
        
        
        /* 3. 转换请求数组为xml格式  */
        return $this->jiexi($req_param, $req_body, $m);
    }
    
    public function queryPayOrdid($query_ordid, $type = 'pay'){
        $mer_pub_file = $this->mer_pub_file;                         // 商户RSA公钥
        $mer_pri_file = $this->mer_pri_file;                         // 商户RSA私钥
        $pay_pub_file = $this->pay_pub_file;                         // 平台RSA公钥
        
        $m = new ENC($pay_pub_file, $mer_pri_file);
        $ordid = $this->jytpay_config['merchant_id'].date('YmdHis').rand(100000,999999);
        
        $request_code = 'TC2001';
        if($type == 'withDraw'){
            $request_code = 'TC2002';
        }

        $req_param = $this->buildHead($ordid, $request_code);
        $req_body['ori_tran_flowid'] = $query_ordid;            //银行名称
        
        $data = $this->jiexi($req_param, $req_body, $m);
        return  simplexml_load_string($data);
    }
    
    function withDraw($uid, $ordid, $tran_amt, $id_num, $id_name, $bank_name, $account_no, $zj = '01'){
        /* 0. 请根据对接产品类型和实际商户号修改如下信息  */
        $mer_pub_file = $this->mer_pub_file;                         // 商户RSA公钥
        $mer_pri_file = $this->mer_pri_file;                         // 商户RSA私钥
        $pay_pub_file = $this->pay_pub_file;                         // 平台RSA公钥
    
        $m = new ENC($pay_pub_file, $mer_pri_file);
        
        //代付
        $request_code = 'TC1002';
    
        $req_param = $this->buildHead($ordid, $request_code);
    
        /* 2. --- 请根据接口报文组织请求报文体 ，下面例子为身份认证交易请求报文体，请按照实际交易接口填充内容  */
        //         $req_body['mer_viral_acct'] = $this->jytpay_config['pay_account'];      //代收虚拟账号
        $req_body['bank_name'] = $bank_name;            //银行名称
        $req_body['account_no'] = $account_no;          //银行卡号
        $req_body['account_name'] = $id_name;           //银行用户名称
        $req_body['tran_amt'] = $tran_amt;              //交易金额
        
        $req_body['account_type'] = '00';               //账户类型
        $req_body['currency'] = 'CNY';                  //交易金额
        $req_body['bsn_code'] = '09400';                //业务类型代码
        $req_body['cert_type'] = $zj;                  //开户证件类型      01：身份证
        $req_body['cert_no'] = $id_num;                 //开户证件号
    
        /* 3. 转换请求数组为xml格式  */
        return $this->jiexi($req_param, $req_body, $m);
    }
    
    /**
     * 
     * @param unknown $mer_viral_acct  代付账号
     */
    function queryWithDrawBalance($ordid){
        /* 0. 请根据对接产品类型和实际商户号修改如下信息  */
        $mer_pub_file = $this->mer_pub_file;                         // 商户RSA公钥
        $mer_pri_file = $this->mer_pri_file;                         // 商户RSA私钥
        $pay_pub_file = $this->pay_pub_file;                         // 平台RSA公钥
    
        $m = new ENC($pay_pub_file, $mer_pri_file);
    
        //代付
        $request_code = 'TC2020';
    
        $req_param = $this->buildHead($ordid, $request_code);
    
        /* 2. --- 请根据接口报文组织请求报文体 ，下面例子为身份认证交易请求报文体，请按照实际交易接口填充内容  */
    
        $req_body['mer_viral_acct'] = $this->jytpay_config['draw_account'];      //代收虚拟账号
       
        /* 3. 转换请求数组为xml格式  */
        $data = $this->jiexi($req_param, $req_body, $m);
        return simplexml_load_string($data);
    }
    
    
    private function buildHead($ordid, $request_code){
        /* 1. 组织报文头  */
        $req_param[ 'merchant_id' ] = $this->jytpay_config['merchant_id'];
        $req_param[ 'tran_type' ] =  '01';
        $req_param[ 'version' ] = '1.0.0';
        $req_param[ 'tran_flowid' ] =  $ordid;
        
        $req_param[ 'tran_date' ] = date( 'Ymd' );
        $req_param[ 'tran_time' ] = date( 'His' );
        $req_param[ 'tran_code' ] =  $request_code;
        return $req_param;
    }
    
    
    private function jiexi($req_param, $req_body, $m, $validate = false){
        $data=array("head" => $req_param, "body" => $req_body);
        $xml_ori = ArrayToXML::toXml($data);
        //print_r($xml_ori);
        /* 4. 组织POST字段  */
        $req['merchant_id'] = $req_param['merchant_id'];
        $req['sign' ]  = $m->sign($xml_ori,'hex');
        $key = rand(pow(10, (8-1)), pow(10,8)-1);
        $req['key_enc'] = $m->encrypt($key, 'hex');
        $req['xml_enc'] = $m->desEncrypt($xml_ori, $key);
        /* 5. post提交到支付平台 */
//         print_r($xml_ori);
        
        $this->jytpay_xml_log($xml_ori);
        $url = $this->pay_url;
        if($validate === true){
            $url = $this->validate_url;
        }
        $snoopy = new Snoopy;
        $snoopy->submit($url, $req);
        
        /* 6. 正则表达式分解返回报文 */
        preg_match('/^merchant_id=(.*)&xml_enc=(.*)&key_enc=(.*)&sign=(.*)$/', $snoopy->results, $matches);
        
//         if($validate){
//             var_dump($matches);
//         }
        $xml_enc = $matches[2];
        $key_enc = $matches[3];
        $sign = $matches[4];
        
        /* 7. 解密并验签返回报文  */
        $key = $m->decrypt($key_enc,'hex');
        $xml = $m->desDecrypt($xml_enc,$key);
//         print_r($xml);
 		$this->jytpay_xml_log($xml);
        if(!$m->verify($xml,$sign,'hex')){
            return false;
        }else{
            return $xml;
        }
    }
    
    private function jytpay_xml_log($msg){
        if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
            $logFile = './jytpay_xml_log.'.date("Y-m-d");
        }else{
            $logFile = '/tmp/jytpay_xml_log.'.date("Y-m-d");
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


   
