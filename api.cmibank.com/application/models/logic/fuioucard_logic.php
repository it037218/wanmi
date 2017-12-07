<?php

date_default_timezone_set('PRC');  // 设置时区

class fuioucard_logic extends CI_Model {

    function validate($orderid, $zj, $bankcard, $idcard, $realname, $mobile){
        $this->config->load('cfg/fuiou_config', true, true);
        $fuioupay_config = $this->config->item('cfg/fuiou_config');

        $mchntcd = $fuioupay_config['mchntcd'];
        $mchnt_key = $fuioupay_config['mchnt_key'];
        $url = $fuioupay_config['url'];
        $ver = $fuioupay_config['mver'];
        $params1 = array(
            'MchntCd' => $mchntcd,
            'Ver' => $ver,
            'OSsn' => $orderid,
            'Ono' => $bankcard,
            'OCerTp' => $zj,
            'OCerNo '=>$idcard,//身份证号码===类型:string,是否必须:是,
        );
        $signStr = $this->createSign ( $params1, $mchnt_key );
        $this->fuioucard_log($signStr);
        $params2 = array(
            'Onm' => $realname,
            'Mno' => $mobile,
            'Sign' => md5($signStr),
        );
        $params = array_merge($params1, $params2);
        $content = $this->fuioucurl($url, $params );
        $array_content = $this->XmltoArray($content);
        $this->fuioucard_log(json_encode($array_content));
        if ($array_content) {
            return $array_content;
        } else {
            return false;
        }
    }

    /**
     * 请求富有接口
     * @param $url
     * @param array $data
     * @param int $timeout
     * @param string $type
     * @param array $header
     * @return mixed|string
     */
    function fuioucurl($url,$data=array(),$timeout=10,$type="",$header=array()){
        $ch = curl_init();
        $querystring = http_build_query(array('FM' => $this->arrayToXml($data)));
        $url .= '?' . $querystring;
        //$url .= '?FM=' . $this->arrayToXml($data);
        curl_setopt($ch, CURLOPT_URL,$url);
        if($timeout>0) 	curl_setopt($ch, CURLOPT_TIMEOUT,$timeout);
        if(strstr($url,"https://")){
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        $user_agent = "Fuiou Curl/1.0";
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
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
     * 生成签名字符串
     * @param array $param
     * @param string $mkey
     * @return string
     */
    function createSign($param = array(), $mkey = "") {
        $string = "";
        foreach ( $param as $key => $val ) {
            $strType = mb_detect_encoding($val , array('UTF-8','GBK','LATIN1','BIG5' , 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'));
            if( $strType != 'UTF-8') {
                $val = mb_convert_encoding($val, 'utf-8', $strType);
            }
            $string .= $val . '|';

        }
        return $string.$mkey;
    }

    private function fuioucard_log($msg){
        if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
            $logFile = './fuioucard_log_xml_log.'.date("Y-m-d");
        }else{
            $logFile = '/tmp/fuioucard_log_xml_log.'.date("Y-m-d");
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

    /**
     * 数组转化为xml
     * @param $arr
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
        $result = "<FM>" . $xml . "</FM>";
        return $result;
    }

    /**
     * xml 转为数组
     * @param $xml
     * @return mixed
     */
    public function XmltoArray($xml){
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }
}



