<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

//require_once ROOTPATH . DS . APPPATH. 'libraries/curl.lib.php';
require_once APPPATH. 'libraries/curl.lib.php';

class admin_pnr_model extends Basemodel {

    private $host = "127.0.0.1";
    private $port = 8733;
    private $OperId = '510806';
    private $merId = 510806;
    private $userpwd = '95f446df4adc1548073326ecfb97947f';
    private $user_role = 50;
    private $url = 'http://test.chinapnr.com';
    private $action = '/gar/entry.do';
    private $AutoPaySign_action = '/gau/UnifiedServlet';
    private $ret_url = "http://120.132.69.210/pnr_pay_ret.php";
    private $repayment_url = "http://120.132.69.210/pnr_repayment_ret.php";
    function __construct() {
        # 继承CI 父类
        parent::__construct();
        if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
            $this->host = '120.132.69.210';
        }
    }
    
    //取现绑卡
    public function SDPBindCard($OperId, $openAcctid, $name, $OpenBankCode, $OpenProvId, $OpenAreaId){
        $name = $this->charsetToGBK($name);
        $params = array(
            'Version' => 10,
            'CmdId' => 'SDPBindCard',
            'MerId' => $this->merId,
            'OperId' => $OperId,            //开户时传的MerUsrId
            'Password' => $this->userpwd,   //开户时传的UsrPwd
            'OpenAcctId' => $openAcctid,    //银行卡号
            'OpenAcctName' => $name,        //银行卡用户名
            'OpenBankCode' => $OpenBankCode,    //银行编号
            'OpenProvId' => $OpenProvId,    //省份地区编码
            'OpenAreaId' => $OpenAreaId,    //省份地区编码
            'AutoCashFlag' => 'Y'           
        );
        return $this->send_params($params);
    }
    
    //充值绑卡  WHBindCard
    public function WHBindCard($operid, $cardno,  $name, $bankcode, $CertId, $usermp){
        $params = array(
            'Version' => 10,
            'CmdId' => 'WHBindCard',
            'MerId' => $this->merId,
            'OperId' => $operid,            //用户操作员号
            'LoginPwd' => $this->userpwd,   //登录密码  对密码明文的md5
            'CardNo' => $cardno,            //银行卡号码
            'OpenAcctName' => $name,        //用户在银行的开户名
            'BankCode' => $bankcode,        //银行编码
            'CertType' => '00',             //证件类型     身份证
            'CertId' => $CertId,            //证件号码
            'UsrMp' => $usermp,             //手机号
            'CardType' => 'D',              //银行卡类型：D->借记卡
            );
        $params = $this->charsetToGBK($params);
        return $this->send_params($params);
    }
    
    //解绑充值银行卡
    public function WHCancelBindCard($operid, $cardno){
        $params = array(
            'Version' => 10,
            'CmdId' => 'WHCancelBindCard',
            'MerId' => $this->merId,
            'OperId' => $operid,            //用户操作员号
            'CardNo' => $cardno,            //银行卡号码
        );
        //print_r($params);
        $params = $this->charsetToGBK($params);
        return $this->send_params($params);
    }
    
    
    //查询用户余额
    public function QueryBalance($operid){
        $params = array(
            'Version' => 10,
            'CmdId' => 'QueryBalance',
            'MerId' => $this->merId,
            'UsrId' => $operid,             //用户操作员号
        );
        
        $params = $this->charsetToGBK($params);
        return $this->send_params($params);
        
    }
    
    //分账扣款
    public function BuyPayOut($ordid, $amt, $pid, $divDetails){
        $params = array(
            'Version' => 10,
            'CmdId' => 'BuyPayOut',
            'MerId' => $this->merId,
            'OrdId' => $ordid,
            'OrdAmt' => $amt,
            'Pid' => $pid,
            'MerPriv' => 'repayment',
            'GateId' => 61,
            'DivDetails' => $divDetails,
            'PayUsrId' => '510806',
            'BgRetUrl' => $this->repayment_url,
            'IsBalance' => 'N',     //是否自动结算
        );
        //print_r($params);
        $params = $this->charsetToGBK($params);
        return $this->send_params($params);
    }
    
    //订单结算
    public function PaymentConfirm($ordid){
        $params = array(
            'Version' => 10,
            'CmdId' => 'PaymentConfirm',
            'MerId' => $this->merId,
            'OrdId' => $ordid,
        );
        //print_r($params);
        $params = $this->charsetToGBK($params);
        return $this->send_params($params);
    }
    
    //普通退款
    public function Refund(){
        
    }
    
    
    
    public function PCashOut($OperId, $amt, $CardNo, $Remark){
        $params = array(
            'Version' => 10,
            'CmdId' => 'PCashOut',
            'MerId' => $this->merId,
            'OperId' => $OperId,
            'TransPwd' => $this->userpwd,
            'TransAmt' => $amt,
            'CardNo' => $CardNo,
            'Remark' => $Remark
        );
        $params = $this->charsetToGBK($params);
        return $this->send_params($params);
    }
    
    //自动扣款签约  现在没用  在绑卡里面
    public function AutoPaySign(){
        $params = array(
            'Version' => 10,
            'CmdId' => 'AutoPaySign',
            'MerId' => $this->merId,
            'MerDate' => date('Ymd'),
            'MerTime' => date('His'),
            'BgRetUrl' => $this->ret_url
        );
        $check_val = '';
        foreach ($params as $_val){
            $check_val .= $_val;
        }
        $params['ChkValue'] = $this->createSign($check_val);
        $params = $this->charsetToGBK($params);
        return $params;
//         return $this->send_params($params, $this->AutoPaySign_action);
    }
    
    private function send_params($params, $action = '/gar/entry.do'){
        $check_val = '';
        foreach ($params as $_val){
            $check_val .= $_val;
        }
        $params['ChkValue'] = $this->createSign($check_val);
        
        $curlobj = new Curl();
        //echo $this->url . $action;
        $r = $curlobj->post($this->url . $action, $params);
        if($params['CmdId'] == 'AutoPaySign'){
            return $r;
        }
        $rtn = $this->formatPnrStringReturn($r);
        return $rtn;
    }
    
    private function formatPnrStringReturn($str){
        $str = mb_convert_encoding($str, "UTF-8", "GBK");
        $str = str_replace(PHP_EOL, ';', $str);
        $str = explode(';', $str);
        $rtn = array();
        foreach($str as $_val){
            if(empty($_val)){
                continue;
            }
            $arr = explode('=', $_val);
            if(!empty($arr[0])){
                $rtn[$arr[0]] = trim($arr[1]);
            }
        }
        return $rtn;
    }
    
    private function charsetToGBK($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $k => $v) {
                if (is_array($v)) {
                    $mixed[$k] = charsetToGBK($v);
                } else {
                    $encode = mb_detect_encoding($v, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
                    //if ($encode == 'UTF-8') {
                    $mixed[$k] = iconv($encode, 'GBK', $v);
                    //}
                }
            }
        } else {
            $encode = mb_detect_encoding($mixed, array('ASCII', 'UTF-8', 'GB2312', 'GBK', 'BIG5'));
            //var_dump($encode);
            //if ($encode == 'UTF-8') {
            $mixed = iconv($encode, 'GBK', $mixed);
            //}
        }
        return $mixed;
    }
    
    //head_fix S为生成  
    private function createSign($MsgData){
        $errno = 0;
        $errstr = '';
        $head_fix = 'S';
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, 10);//请按照npc_server 安装在一台公网服务器上，并把您当前调试使用环境的IP地址加入 Trust_ip_list 生成环境需要把要集成的所有服务器IP地址加入列表
        /*************************************************** 加签 *****************************************************/
        if (!$fp) {
            die("汇付天下验证服务器连接失败". $errstr);
        } else {
            $MsgData_len =strlen($MsgData);
            if($MsgData_len < 100 ){
                $MsgData_len = '00'.$MsgData_len;
            }
            elseif($MsgData_len < 1000 ){
                $MsgData_len = '0'.$MsgData_len;
            }
            $out = $head_fix . $this->merId . $MsgData_len . $MsgData;// S-> sign   V->verify
            $out_len = strlen($out);
            if($out_len < 100 ){
                $out_len = '00'.$out_len;
            }
            elseif($out_len < 1000 ){
                $out_len = '0'.$out_len;
            }
            $out =$out_len.$out;
//             echo $out;
            fputs($fp, $out);
            $ChkValue ='';
            while (!feof($fp)) {
                $ChkValue .= fgets($fp, 128);
            }
            $ChkValue = substr($ChkValue,15,256);
            fclose($fp);
            return $ChkValue;
        }
    }
    
    public function checkSign($MsgData, $ChkValue){
        $errno = 0;
        $errstr = '';
        $fp = fsockopen($this->host, $this->port, $errno, $errstr, 10);//请按照npc_server 安装在一台公网服务器上，并把您当前调试使用环境的IP地址加入 Trust_ip_list    生成环境需要把要集成的所有服务器IP地址加入列表
        if (!$fp) {
            die("签名验证失败");//todo:: 通知运营，技术
        } else {
            $MsgData_len =strlen($MsgData);
            if($MsgData_len < 100 ){
                $MsgData_len = '00'.$MsgData_len;
            }elseif($MsgData_len < 1000 ){
                $MsgData_len = '0'.$MsgData_len;
            }
            $out = 'V'.$this->merId.$MsgData_len.$MsgData.$ChkValue;// S-> sign   V->verify
            $out_len = strlen($out);
            if($out_len < 100 ){
                $out_len = '00'.$out_len;
            }elseif($out_len < 1000 ){
                $out_len = '0'.$out_len;
            }
            $out =$out_len.$out;
            fputs($fp, $out);
            $ChkValue ='';
            while (!feof($fp)) {
                $ChkValue .= fgets($fp, 128);
            }
            $ChkValue = substr($ChkValue, -260);
            $ChkValue = substr($ChkValue, 0, 256);
            fclose($fp);
            return $ChkValue;
        }
    }
    
}


   
