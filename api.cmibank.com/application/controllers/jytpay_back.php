<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class jytpay_back extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/jytpay_logic', 'jytpay_logic');
        
    }
    
    public function jytpay_withdraw_return(){
        $mer_pub_file = APPPATH . 'libraries/jytpay/cert/mer_public_key_2048.pem';                         // 商户RSA公钥
        $mer_pri_file = APPPATH . 'libraries/jytpay/cert/mer_private_key_2048.pem';                        // 商户RSA私钥
        $pay_pub_file = APPPATH . 'libraries/jytpay/cert/pay_public_key_2048.pem';                         // 平台RSA公钥
        
        $m = new ENC($pay_pub_file, $mer_pri_file);
        $result = $_REQUEST;
//         $result = '{"sign":"4240a154339b9ecfdef1d9058f228ac52647c0d73704d17e91e176360b3d85a0d04718b9542decdfd0b7d6b2f05f6c65b8f695a4376c953c108c0d435deb9269a0d07e47dc2eb0005446d76cebda0e3c6264545404c0cad121e03ee92f73016e26c84f86f6d0f393cd62ef1cbf80d803bcfc8558f433173989b0996ae9215f3dc011ff0a387118bdca170bfd2072db8ffd7f4c1a26bc63c7005f2e19cece8cb50cd8aa1ec48dc50dfc1517eebca93da5a5f579bd2892271694ce171e48b58b4e131c5200cca50e39974cfd25810c87ca297b572e7e203e38323dc0e759bc8ae5a525e04f3071a78891edb8f0a1ba9adf8e0225c8997cb895be803bb9ef656e00","xml_enc":"0adf93b498de5a11e917616dafe58edf680fe28b4c1dc840c459a694d38a287a1d5f526f0700c5419c644685351e09073d14da5d71838f6d9cfe49315664e15a6ce6ef8d0e87448c387aa2d3066bc3924c8e597a496543823e122a708b47f73236f1544ae3b9a13c4f427fd13d8244b51cf648e9602db017eb108dd4a48cb2b116a24b72751951f62fdcafe070d9702f3035cb10703ce751445e6afb54eeec2dee3d578fefc03e790ea3aeec71ffd51718345091ee2406dfd09d26a95e6606517631c54cb46d523caca9e87095919bf9a9cae16dc5ad571dd015dcd26de4722afdaa8c89485b53f99457225ce42caa90bcc9e09d93646955b70eb9f804912fff05e564a202af7b6f8cc7e3800c1c033dfb8cdc96cfa284bec3b958a5e2c6e85ab8a1ff7c21cbcdef5a0e09779cc780b5ee444d7156467f4edfd92ee3845d19f80a285d0ffc288ae154cb9e69b8ebc173b65e7aa3fdc6e42724c48c568683e3203a3de8bdcda4772c9b49a018c6b6feedfc81990db3fbda1eef6d8ffa7b845aefcd116c5d4367cd78cc2487a94feb97aa2c29a047302661ff8e987f1c9a0884b81aa45448899d0cafc26bddb810884cfcc6f4f84dd1bc673ea63c5ffc72cf3ba4e1ac903eceef93e3df3c035846fff86eee93da231674fff92a8a50f030104a431b3f9c7a503832fbb00cc59dfb7a13839b32f0a32e21b2ff69ee4d60a823a6f1be66e6a0dd92a50ef6d33bcf6f468d019e77ba6a16b4083ba22a605185bedb36d0cc8b5a6c4e0c89e0f580e8b202426325b1710dcbad28fc55de75912df1e8f7aab4d13b1ad463be078b2052edd58ee4aee09d9657adfebb715b18e0bac189455401b437dc661b4097fbafaa5c00f533757d6a6d99019febba02031e709548735113cb4f1058c8bfa6e498884ca6d8124cbb2129e80d78fa","merchant_id":"290060120008","key_enc":"9f54bed10fdd32413a0fe700280195372dbcc1baff6a67fc16a27bf8be496f9e1a949d6eeba5f7c34869ac7dc8a00a90dfd822afd5fcba8b59a14d4fdf7ed111751479ed45d064cef38832b2a92b4920a1057739c2622603b18c209b13a9c01470f6f23961898c957ffa0f77b050918ddb942b57c0a9f647c61fe378bb68232d9a1bb9ec88982a17d22287f061bbb773a89f06bbb0b9445b34ba822cdd90c89bbb024e18aeda92f32b752df27f44481f22be804894fb65923df197f3580e71ff74c22c19264eca2a498abe24621563720bbb0e582609b44b15b51842e8d4763740a99d991ffd18830395a8c46925417f1b9da29374396848ece6140df604cf82"}';
//         $result = json_decode($result, true);
        
        $xml_enc = $result['xml_enc'];
        $key_enc = $result['key_enc'];
        $sign = $result['sign'];
        if(!$sign || !$key_enc || !$xml_enc){
            echo 'failed';
            exit;
        }
        /* 7. 解密并验签返回报文  */
        $key = $m->decrypt($key_enc,'hex');
        $xml = $m->desDecrypt($xml_enc,$key);
        if(empty($xml)){
            echo 'data error!';
            exit;
        }
        $this->back_withdraw_log($xml);
        
        $xml = simplexml_load_string($xml);
        
        if(empty($xml)){            //如果解析不了
            echo 'xml error!';
            exit;
        }
        
        if((string)$xml->head->tran_code != 'TC3002'){
            echo 'tran_code error';
            exit;
        }
        $this->config->load('cfg/jytpay_config', true, true);
        $jytpay_config = $this->config->item('cfg/jytpay_config');
        if((string)$xml->head->merchant_id != $jytpay_config['merchant_id']){
            echo 'merchant_id error';
            exit;
        }
//         if((string)$xml->body->tran_resp_code != 'S0000000'){
//             echo 'tran_resp_code error';
//             exit;
//         }
        if((string)$xml->body->tran_state == '00'){
            echo 'tran_state not complete or failed!';
            exit;
        }
        $orderid = (string)$xml->body->ori_tran_flowid;
        $this->load->model('base/withdraw_log_base', 'withdraw_log');
        $date = substr($orderid, 12,14);
        $year = date('Y', strtotime($date));
        $week = date('W', strtotime($date));
        $orderInfo = $this->withdraw_log->getLogByOrderId($orderid, $year, $week);
        if(!$orderInfo){
            echo 'order not found!';
            exit;
        }
        if($orderInfo['status'] != 0){
            echo 'S0000000';
            exit;
        }
        
        /*
         SimpleXMLElement Object
        (
            [head] => SimpleXMLElement Object
                (
                    [version] => 1.0.0
                    [tran_type] => 01
                    [merchant_id] => 290060120008
                    [tran_date] => 20151023
                    [tran_time] => 140540
                    [tran_flowid] => 29006012000820151023140539291381
                    [tran_code] => TC3002
                    [resp_code] => SimpleXMLElement Object
                        (
                        )
        
                    [resp_desc] => SimpleXMLElement Object
                        (
                        )
        
                )
            [body] => SimpleXMLElement Object
                (
                    [ori_tran_flowid] => 29006012000820151023140539291381
                    [account_no] => 6226091210148312
                    [account_name] => 李佳毅
                    [tran_amt] => 1
                    [tran_resp_code] => S0000000
                    [tran_resp_desc] => 交易成功
                    [tran_state] => 01
                )
        
        )
         */
        $status = 1;
        $action_type = USER_ACTION_PCASHOUT;
        
        $pname = '提现退回';
        if((string)$xml->body->tran_resp_code == 'S0000000' && (string)$xml->body->tran_state == '01'){
            $pname = '提现成功';
            $status = 2;
            $back_status = 'SUCCESS';
        }else if(($xml && $xml->head->resp_code != 'S0000000') || ($xml && $xml->body->tran_state == '03')){
            $pname = '提现失败(' . (string)$xml->body->tran_resp_desc . ',将于次日17点之回到账户)';
            $back_status = 'FAILED';
            $this->config->load('cfg/banklist', true, true);
            $banklist = $this->config->item('cfg/banklist');
            $action_type = USER_ACTION_WITHDRAWFAILED;
            //$identity_result['cardno'] = '6226091210143311';      //尾号为1的话会返回失败，尾号为2的会返回处理中，其他的尾号会返回成功。
            $this->load->model('logic/user_identity_logic', 'user_identity_logic');
            $identity_result = $this->user_identity_logic->getPublicUserIdentity($orderInfo['uid'], 'all');
            $id_num = strtoupper($identity_result['idCard']);
            
            $id_name = $identity_result['realname'];
            $bank_code = $identity_result['bankcode'];
            
            $bank_name = $banklist[$bank_code]['name'];
            $account_no = $identity_result['cardno'];
            $faild_log_data = array(
                'uid' => $orderInfo['uid'],
                'orderid' => $orderid,
                'money' => $orderInfo['money'],
                'realname' => $id_name,
                'bankname' => $bank_name,
                'bankcode' => $bank_code,
                'cardNo' => $account_no,
                'back_code' => (string)$xml->body->tran_resp_code,
                'back_msg' =>  (string)$xml->body->tran_resp_desc,
                'logid' => $orderInfo['logid'],
                'plat' => 'jyt',
            	'ctime' =>NOW
            );
            $this->load->model('base/withdraw_failed_log_base', 'withdraw_failed_log_base');
            $failedInfo = $this->withdraw_failed_log_base->getFailedLogByOrderId($orderid);
            if(empty($failedInfo)){
                $this->withdraw_failed_log_base->addFailedLog($faild_log_data);
            }
        }else{
            echo 'FAILED !!!';
            exit;
        }
        $this->load->model('base/withdraw_log_base', 'withdraw_log_base');
        $data = array('back_status' => $back_status, 'status' => $status, 'succtime' => time(), 'status_code' => (string)$xml->body->tran_resp_code);
        $where = array('id' => $orderInfo['id']);
        $ret = $this->withdraw_log_base->updateDrawLog($data, $where, $year, $week);
        if($ret){
            $this->load->model('base/user_log_base', 'user_log_base');
            
            $update_data = array('orderid' => $orderInfo['orderid'], 'pname' => $pname, 'paytime' => time(), 'action' => $action_type);
            
            $isfind = strpos($orderInfo['logid'], ',');
            if($isfind){
                $update_logid = explode(',', $orderInfo['logid']);
            }else{
                $update_logid = $orderInfo['logid'];
            }
            
            if(is_array($update_logid)){
            	foreach ($update_logid as $userlogid){
            		$this->user_log_base->updateUserLogByIdForWithdrawNotify($orderInfo['uid'],$userlogid, $update_data,true);
            	}
            }else{
            	$update_where = array('id' => $update_logid);
            	$ret = $this->user_log_base->updateUserLogByIdForWithdrawNotify($orderInfo['uid'],$update_logid, $update_data,true);
            }
        }
        echo 'S0000000';
        exit;
    }
    
    public function jytpay_return(){
        
        $mer_pub_file = APPPATH . 'libraries/jytpay/cert/mer_public_key_2048.pem';                         // 商户RSA公钥
        $mer_pri_file = APPPATH . 'libraries/jytpay/cert/mer_private_key_2048.pem';                        // 商户RSA私钥
        $pay_pub_file = APPPATH . 'libraries/jytpay/cert/pay_public_key_2048.pem';                         // 平台RSA公钥
        
        $m = new ENC($pay_pub_file, $mer_pri_file);
        
//         $result = '{"sign":"8298059fd61a04c0cb3200be320125667e39d293ea502e2f11dbacd9de9dc3feb53a421027355574c09e75780ed31cf0e18970a806b8d229c277933d5d96e9b0d6ae3ed8d458474216b97a3e0ef4787cfc16f7bc3515226c1ddc1253dd689dd0e4cb27dc91950a637ecc6b3580ecf1832fdb32000cd33bcdc62ab7eda82c6f1ff570fcb9658266e43f79d6fe8a8f71eae639a98d983ab4fe6418cb225d38d20ab6467c05259e028549799f0b0d59bddbda7a6d99110237d408dd0b08eea3c7bef42f527920b033570f8eccd4249a7b54d5f9ce7bbad3550bf1d3f62058f8071bafc2044167dece18b62cb9da4d560b1ceff59dc3dd5031b60e5b88ce3b6b4983","xml_enc":"fd1e3d0e897a1197aa31202e23f59ac673f44914b0a18db9dfcb127c4134750b5c72c531700a048944171ea9677bfcd2337e98cfac2e84f8b8b5532e242fdd7cbd416ea6b6857f724ce65a83e06cd05f159655c2ba810431f503888fc0a0933a6a0bce39124908d8a24c98b37d1a070cab36b33c896f4efc97decb2ee7c42cdd1d333d504c426f190e12daa68ce84c6028f4d958c8e948538ca81a84c5d97cae49ed08b36985a41cfa132aa59c0d0eafbd9592667930332fb9b8120bbd6e8ad569b6b14aa4e8a7a70f256490907922ab001bf3c8b9d2146ea4879b6af48015a12ce325b6f24cc58d9907b027da5c36f813ac2ce6dadf40337b6973211a0484fefcbc38d6c8e825a9af06d5852362f00ff79c6f7db4ff253936af1651011aa872cbb9fe626c5176600256c9bf948a55201586299842b6844fc6c18dc08a84add13206213aa4c9088d270cf019127b86b4719f577f2f5bfc568aebad0e8798d92ee7a1c9ae8a7de4c4b906329af442808df2f379da845dae5e66bf832b006b3b0c7631b5d8df6eaa1c686c22d86b73008720b34cea7d6df140484273ce8d9025d5fbcc20e1a9f2509fa925e0455a5726809f3ae4bf5e23ebe86abb00f9a9d3ef0d6f7794810d66896d98e6f136e4f31d55eb4fdbb03570347b04e6a83682123f753c7378179a1c52f7cf3bbd99b0080b212e0be10b216acf1a3f5472db11e89a7f93db2b335a1c05df733c2c06f8af946e0110b16e2c240a1f00fcec329fe2fb726191cab77c75bdcad238b28eb52d76fe8d0be86e26a77ed55ff8e7883bfd10e6fa124d4fbc51d1f807b702585cb414353d58031fbd37b9e910666fb6c7a7ed7e4f24adaaabad1c004dfbc4d31efa10180717aaec32cd246cbce2253544485e24b29bbc2a655e8849cbd1e533726d55741e2bfa0adecbf3c8","merchant_id":"290060120008","key_enc":"81a889ec912d7a3d38550e223cf218495c87f765cf50a9065b09ee5b84075eba817e72561a67c8da46a18c6f94face87b91f12b800ccaa3f54f9ae803930e53fa2ad2fa5da8f1351f6a4dfa12be8217c3704702b1772c4a6da66aa538ccdc171764e191d6bb8b736b575084eb728dac6ca73107d10bb19792e50f079daf64dae2f0b43ddfcd8078ebffe82520012a75b72125707eec4ed40f71865ef25f92072f768ce23065cb579d78973d398843b9771f79c4cb0c8a46512fa4ba98e83f95cb14046d692fc499731414f6df15e48d719f82808a4b64139c95726b048cd2bf1d231da626ec6522c848f955664a8d9cff7e2a68e6fcb425efe23a8d3b7c1fe80"}';
//         $result = json_decode($result, true);
        
        $result = $_REQUEST;
        
        $xml_enc = $result['xml_enc'];
        $key_enc = $result['key_enc'];
        $sign = $result['sign'];
        if(!$sign || !$key_enc || !$xml_enc){
            echo 'failed';
            exit;
        }
        /* 7. 解密并验签返回报文  */
        $key = $m->decrypt($key_enc,'hex');
        $xml = $m->desDecrypt($xml_enc,$key);
        if(empty($xml)){
            echo 'data error!';
            exit;
        }
        $xml = simplexml_load_string($xml);
        if(empty($xml)){            //如果解析不了
            echo 'xml error!';
            exit;
        }
        
        if((string)$xml->head->tran_code != 'TC3001'){
            echo 'tran_code error';
            exit;
        }
        $this->config->load('cfg/jytpay_config', true, true);
        $jytpay_config = $this->config->item('cfg/jytpay_config');
        if((string)$xml->head->merchant_id != $jytpay_config['merchant_id']){
            echo 'merchant_id error';
            exit;
        }
        $ordid = (string)$xml->body->ori_tran_flowid;
        //防并发  redis锁
        $this->load->model('base/pay_redis_base', 'pay_redis_base');
        $incr = $this->pay_redis_base->incr($ordid);
        if($incr != 1){
            echo 'repeat order request(redis)!';
            exit;
        }
        $this->load->model('base/pay_log_base', 'pay_log');
        $order_info = $this->pay_log->getLogByOrdid($ordid);
        //查看订单是否已完结
        if($order_info['isback'] == 1 || $order_info['status'] == 1){
            echo 'repeat order request!';
            exit;
        }
        $uid = $order_info['uid'];
        $createOrderTime = $order_info['ctime'];
        
        $money = (string)$xml->body->tran_amt;
        if(!$money || $money <= 0){
            echo 'amount error';
            exit;
        }
        $this->load->model('base/user_log_base', 'user_log_base');
        //成功的订单
        if((string)$xml->body->tran_resp_code == 'S0000000' && (string)$xml->body->tran_state == '01'){
            //查询订单结果
            $queryInfo = $this->jytpay_logic->queryPayOrdid($ordid);
            /*
            SimpleXMLElement Object
            (
                [head] => SimpleXMLElement Object
                    (
                        [version] => 1.0.0
                        [tran_type] => 02
                        [merchant_id] => 290060120008
                        [tran_date] => 20151022
                        [tran_time] => 215536
                        [tran_flowid] => 29006012000820151022215341215988
                        [tran_code] => TC2001
                        [resp_code] => S0000000
                        [resp_desc] => 交易成功
                    )
            
                [body] => SimpleXMLElement Object
                    (
                        [tran_resp_code] => EX000003
                        [tran_resp_desc] => 查开户方原因
                        [tran_state] => 03
                    )
            
            )
             */
            if((string)$queryInfo->body->tran_state != '01' && (string)$queryInfo->head->resp_code != 'S0000000'){
                echo 'error';
                exit;
            }
            //
            
            $log_data = array();
            $log_data['isback'] = 1;
            $log_data['status'] = 1;
            $log_data['errormsg'] = '';
            $log_data['errorcode'] = '';
            $log_data['trxid'] = $ordid;
            $this->pay_log->updateOrder($ordid, $log_data);
            //加钱
            $this->load->model('base/balance_base', 'balance_base');
            $balance = $this->balance_base->get_user_balance($uid);
            $balance += $money;
            
            //写用户日志
            $user_log_data = array(
                'uid' => $uid,
                'pid' => 0,
                'pname' => '充值',
                'paytime' => $createOrderTime,
                'money' => $money,
                'balance' => $balance,
                'orderid' => $ordid,
                'action' => USER_ACTION_PAY
            );
            $this->user_log_base->addUserLog($uid, $user_log_data);
            
            $ret = $this->balance_base->add_user_balance($uid, $money);
            if($ret){
                //绑定用户信息
                $this->load->model('logic/user_identity_logic', 'user_identity_logic');
                $identity_result = $this->user_identity_logic->getPublicUserIdentity($uid, 'all');
                if($identity_result && $identity_result['ischeck'] == 0){
                    $identity_data = array('ischeck' => 1);
                    $where = array('uid' => $uid);
                    $this->load->model('logic/user_logic', 'user_logic');
                    $this->user_logic->updateUserIdentity($identity_data, $where);
                }
            }
            echo 'S0000000';        //商户在报文头中的响应码返回S0000000，表示正常接收处理报文。否则平台认为商户没有收到通知，继续发送通知
            exit;
            
        }else{      //失败的订单
            //取用户订单
            $log_data = array();
            $log_data['isback'] = 1;
            $log_data['errormsg'] = (string)$xml->body->tran_resp_desc;
            $log_data['errorcode'] = (string)$xml->body->tran_resp_code;
            $log_data['trxid'] = $ordid;
            $this->pay_log->updateOrder($ordid, $log_data);
            //写用户日志
            $uid = $order_info['uid'];
            
            $this->load->model('base/balance_base', 'balance_base');
            $balance = $this->balance_base->get_user_balance($uid);
            $user_log_data = array(
                'uid' => $uid,
                'pid' => 0,
                'paytime' => $createOrderTime,
                'pname' => '充值（' . (string)$xml->body->tran_resp_desc . ')',
                'money' => $money,
                'balance' => $balance,
                'action' => USER_ACTION_PAY_FAIL
            );
            $this->user_log_base->addUserLog($uid, $user_log_data);
            echo 'S0000000';        //商户在报文头中的响应码返回S0000000，表示正常接收处理报文。否则平台认为商户没有收到通知，继续发送通知
            exit;
        }
        echo 'error error error';
        exit;
    }

    public function back_withdraw_log($msg){
        $logFile = '/tmp/jytpay_withdraw_back_xml.log' . date('Y-m-d');
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

/* End of file test.php */
/* Location: ./application/controllers/test.php */