<?php

class fuiou {

    private $ver;
    private $merchant_id;
    private $merchant_key;
    private $withdraw_url;
    private $useragent = 'www.cmibank.com Client';
    public $connecttimeout = 30;
    public $timeout = 30;
    public $ssl_verifypeer = FALSE;
    private $http_info;
    public $http_header = array();
    public $http_code;

    function __construct($config) {
        $this->ver = $config['ver'];
        $this->merchant_id = $config['withdraw_merchant_id'];
        $this->merchant_key = $config['withdraw_merchant_key'];
        $this->withdraw_url = $config['withdraw_url'];
    }

    /**
     * 提现
     * @param type $requestid
     * @param type $identityid
     * @param type $identitytype
     * @param type $card_top
     * @param type $card_last
     * @param type $amount
     * @param type $imei
     * @param type $userip
     * @param type $ua
     * @return type
     */
    public function withdraw($orderno, $bankno, $cityno, $accntno, $accntnm, $amount,$reqname = 'payforreq') {
        $param = array($reqname => array(
                'ver' => $this->ver,
                'merdt' => date("Ymd"),
                'orderno' => $orderno,
                'bankno' => $bankno,
                'cityno' => $cityno,
                'branchnm' => '',
                'accntno' => $accntno,
                'accntnm' => $accntnm,
                'amt' => $amount,
                'entseq' => '',
                'memo' => 156,
                'mobile' => '',
        ));
        return $this->post($this->withdraw_url, $param,'payforreq');
    }
    
    /**
     * 查询订单状态
     * @param type $orderno
     * @param type $startdt
     * @param type $enddt
     * @param type $transst
     * @return type
     */
    public function queryWithDrawOrder($orderno,$startdt,$enddt,$transst='') {
        return $this->queryWithDraw($orderno,$startdt,$enddt,$transst,'AP01');
    }
    
    public function queryWithDraw($orderno,$startdt,$enddt,$transst, $busicd, $reqname = 'qrytransreq') {
        $param = array($reqname => array(
                'ver' => $this->ver,
                'busicd' => $busicd,
                'orderno' => $orderno,
                'startdt' => $startdt,
                'enddt' => $enddt,
                'transst' => $transst
        ));
        return $this->post($this->withdraw_url, $param,$reqname);
    }
    
    /**
     *  查询退票状态
     * @param type $orderno
     * @param type $startdt
     * @param type $enddt
     * @param type $transst
     * @return type
     */
    public function queryWithDrawStatus($orderno,$startdt,$enddt,$transst) {
         return $this->queryWithDraw($orderno,$startdt,$enddt,$transst,'TP01');
    }
    
    /**
     * 使用POST的方式发出API请求
     * @param type $url
     * @param type $param
     * @return type
     * @throws yeepayException
     */
    protected function post($url, $param,$reqtype = 'payforreq') {
        $request = $this->buildRequest($param,$reqtype);
//        print_r($request);
        $data = $this->http($url, 'POST', http_build_query($request));
        return $this->parseReturnData($data);
    }

    public function buildRequest($param,$reqtype) {
        $xml = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' . $this->arrayToXml($param);

        $data = array(
            'merid' => $this->merchant_id,
            'reqtype' => $reqtype,
            'xml' => $xml
        );
        $sign_str = $data['merid'] . '|' . $this->merchant_key . '|' . $data['reqtype'] . '|' . $data['xml'];

        $data['mac'] = md5($sign_str);
        return $data;
    }

    /**
     * 数组转xml文本
     * @param type $arr
     * @return string
     */
    public function arrayToXml($arr) {
        $xml = "";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . $this->arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        return $xml;
    }

    /**
     * 模拟HTTP协议
     * @param string $url
     * @param string $method
     * @param string $postfields
     * @return mixed
     */
    protected function http($url, $method, $postfields = NULL) {
        $this->http_info = array();
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        $method = strtoupper($method);
        switch ($method) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
            case 'GET':
                if (!empty($postfields)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        $response = curl_exec($ci);
        /**
         * <?xml version="1.0" encoding="UTF-8" standalone="yes"?><payforrsp><ret>000000</ret><memo>成功</memo></payforrsp>
         */
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci));
        $this->url = $url;
        curl_close($ci);
        return $response;
    }

    /**
     * Get the header info to store.
     * @param type $ch
     * @param type $header
     * @return type
     */
    public function getHeader($ch, $header) {
        $i = strpos($header, ':');
        if (!empty($i)) {
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
            $value = trim(substr($header, $i + 2));
            $this->http_header[$key] = $value;
        }
        return strlen($header);
    }
    
    public function parseReturnData($responde) {
        $obj = simplexml_load_string($responde);
        $json= json_encode($obj);
        $data = json_decode($json, true);
        return $data;
    }
    
    public function VerifySign($data,$sign) {
        $sing_str = $this->merchant_id.'|'.$this->merchant_key.'|'.$data['orderno'].'|'.$data['merdt'].'|'.$data['accntno'].'|'.$data['amt'];
        if(md5($sing_str) == $sign){
            return true;
        } else {
            return false;
        }
    }
}
?>