<?php

/**
 * 易宝投资通接口
 */
class ebatong {
    
    public $connecttimeout = 30;
    public $timeout = 30;
    public $ssl_verifypeer = FALSE;
    
    private $partner;
    private $input_charset;
    private $sign_key;
    private $sign_type;
    
    
    private $public_parames;
    
    private $dny_url;
    
    
    
    public function __construct($config) {
        $this->partner = $config['partner'];
        $this->input_charset = $config['input_charset'];
        $this->sign_key =  $config['sign_key'];
        $this->sign_type = $config['sign_type'];
        
        $this->public_parames = array(
            'partner' => $this->partner,
            'input_charset' => $this->input_charset,
            'sign_type' => $this->sign_type,
            );
        
        $this->dny_url = 'https://www.ebatong.com/mobileFast/getDynNum.htm';      //验证码请求URL
    }
    
    public function getDnyURL() {
        return $this->dny_url;
    }
    
    //获取验证码
    public function getDynNum($customer_id, $card_no, $real_name, $cert_no, $cert_type, $out_trade_no, $amount, $bank_code, $card_bind_mobile_phone_no){
        $service = 'ebatong_mp_dyncode';
        $this->public_parames['service'] = $service;
        $encode = mb_detect_encoding( $real_name, array('ASCII','UTF-8','GB2312','GBK'));
        if ($encode != 'UTF-8' ){
            $real_name = iconv('UTF-8',$encode, $real_name);
        }
        $query = array(
            'customer_id' => $customer_id,
            'card_no' => $card_no,
            'real_name' => $real_name,
            'cert_no' => $cert_no,
            'cert_type' => $cert_type,
            'out_trade_no' => $out_trade_no,
            'amount' => $amount,
            'bank_code' => $bank_code,
            'card_bind_mobile_phone_no' => $card_bind_mobile_phone_no,
        );
        return $this->post($this->getDnyURL(), $query);
    }
    
    
    public function buildRequest($query) {
        $params = array_merge($this->public_parames, $query);
        $params['sign'] = $this->makeSign($params);
        return $params;
    }
    
    protected  function makeSign($params){
        $paramKey = array_keys($params);
        sort($paramKey);
        $md5src = "";
        $i = 0;
        $paramStr = "";
        foreach($paramKey as $arraykey){
            if($i==0){
                $paramStr .= $arraykey."=".$params[$arraykey];
            }
            else{
                $paramStr .= "&".$arraykey."=".$params[$arraykey];
            }
            $i++;
        }
//         print_r($paramStr);
        //加签
        $md5src .= $paramStr . $this->sign_key;
        $sign = md5($md5src);
         echo $md5src;
//         echo "<br />";
//         echo $sign;
        return $sign;
    }
    
    /**
     * 使用POST的方式发出API请求
     * @param type $url
     * @param type $query
     * @return type
     * @throws yeepayException
     */
    protected function post($url, $query) {
        // 		echo 'QUERY:';
        // 		dump($query);
        $request = $this->buildRequest($query);
        //print_r($request);
        $data = $this->http($url, json_encode($request));
        return $data;
    }
    
    /**
     * 使用GET的模式发出API请求
     * @param string $type
     * @param string $method
     * @param array $query
     * @return array
     */
    protected function get($url, $query) {
        // 		echo 'QUERY:';
        // 		dump($query);
        $request = $this->buildRequest($query);
        $url .= '?' . http_build_query($request);
        $data = $this->http($url, 'GET');
        return $data;
    }
    
    
    protected function http($url, $data = NULL) {
         print_r($data);

        $this->http_info = array();
        $ci = curl_init();
        //curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_POST, 1);
        curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, $data);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        curl_close($ci);
        return $response;
    }

}

