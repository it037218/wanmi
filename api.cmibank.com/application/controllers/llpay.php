<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require (APPPATH . 'libraries/top-sdk/TopSdk.php');
/*
 * 购买产品
 */
class llpay extends Controller {

    private $notify_url;
    private $withdraw_notify_url;
    
    private $diff_bankcode_map = array(
    		'PAYH' => 'PINGAN',
    		'CITIC' => 'ECITIC',
    		'CGB' => 'GDB',
    		'PSBC' => 'POST',
    		'BOCM' => 'BOCO',
    		'SHBANK' => 'SHB',
    		'CMB' => 'CMBCHINA',
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/user_logic', 'user_logic');
        $this->load->model('logic/llpay_logic', 'llpay_logic');
        $this->check_link();
        $this->check_login();
        $this->notify_url = DOMAIN . 'llpay_notify/pay_notify';
        $this->withdraw_notify_url = DOMAIN . 'llpay_notify/withdraw_notify';
        $this->load->model('base/user_notice_base', 'user_notice_base');
        $this->load->model('logic/activity_logic', 'activity_logic');
    }

    public function bulidPaySign(){
        $this->load->model('logic/user_identity_logic', 'user_identity_logic');
        $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all'); 

        $this->load->model('base/user_base', 'user_base');
        $user_account_info = $this->user_base->getAccountInfo($this->uid);
        if(empty($identity_result) || $identity_result['ischeck'] != 1){
            $response = array('error' => 11999, 'msg' => '请先进行绑卡操作!');
            $this->out_print($response);
        }
        $tpwd = trim($this->input->post('pwd'));
        if(!$identity_result || !$identity_result['tpwd']){
            $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
            $this->out_print($response);
        }
        if($identity_result['tpwd'] != $tpwd){
            $response = array('error'=> 4021, 'msg'=>'交易密码错误');
            $this->out_print($response);
        }
        $acct_name = $identity_result['realname'];                  //银行卡用户名
        $id_no = $identity_result['idCard'];                        //身份证号
        $card_no = $identity_result['cardno'];                      //银行卡号

        $money = $this->input->request('money_order');                 //充值金额
        $ordid = date('YmdHis') . $this->uid . mt_rand(1000,9999);
        $id_no = (string)$id_no;

        $risk_item = array(
            'frms_ware_category' => 2009,
            'user_info_mercht_userno' => $this->uid,
            'user_info_bind_phone' => $user_account_info['account'],
            'user_info_dt_register' => date("YmdHis",$user_account_info['ctime']),
            'user_info_full_name' => $acct_name,
            'user_info_id_type' => 0,
            'user_info_id_no' => $id_no,
            'user_info_identify_state' => 0,
            'user_info_identify_type' => 4,
        );
        $risk_item_json = json_encode($risk_item);
        
        $parameter = array (
        	"acct_name" => $acct_name,                      //银行卡用户名
            "busi_partner" => '101001',                     //产品ID  因是充值 固定1001
            "dt_order" => date('YmdHis'),                   //14位数字，精确到秒
            "id_no" => $id_no,                              //身份证号
            "info_order" => '充值',                          //订单备注信息
            "money_order" => $money,                        //充值金额
            "name_goods" => "投资币",                         //商品名称
            "no_order" => "$ordid",                         
            "notify_url" => $this->notify_url,              
            "card_no" => $card_no,                          //银行卡号
           // "oid_partner" => 201510201000546504,            //logic里组合上去
            "risk_item" => $risk_item_json,                 //风控参数      "{\"user_info_dt_register\":\"20131030122130\"}";
            "sign_type" => 'MD5',
            "user_id" => $this->uid,
            "valid_order" => '30',                          //订单有效时间
        );
        $return_data = $this->llpay_logic->bulidPaySign($parameter);

        //创建购买订单
        $pay_log = array(
            'uid' => $this->uid,
            'ordid' => $ordid,
            'amt' => $money,
            'platform' => 'llpay',
            'curcode' => 'RMB',
            'ctime' => NOW,
            'status' => 0,
        );
        $this->load->model('base/pay_log_base' , 'pay_log');
        $this->pay_log->createOrder($pay_log);
        $response = array('error'=> 0, 'data'=> $return_data);
        $this->out_print($response);
    }
    
    public function queryOrder(){
        $orderid = $this->input->request('orderid');
        $dt_order = $this->input->request('dt_order');
        $parameter = array(
            'sign_type' => 'MD5',
            'no_order' => $orderid,
            'dt_order' => $dt_order
        );
        
        $data  = $this->llpay_logic->queryOrder($parameter);
        $data = json_decode($data, true);
        /*
         {
              "bank_code": "01020000",
              "card_no": "622202*********9617",
              "dt_order": "20151223112929",
              "info_order": "充值",
              "money_order": "1.00",
              "no_order": "201512231129291208312164",
              "oid_partner": "201510201000546504",
              "oid_paybill": "2015122343274946",
              "pay_type": "D",
              "result_pay": "SUCCESS",
              "ret_code": "0000",
              "ret_msg": "交易成功",
              "settle_date": "20151223",
              "sign": "7de3cad77826ac01cce8a2bcea526d0c",
              "sign_type": "MD5"
            }
         */
        $ischeck = 0;
        $status = 2;
        if($data['result_pay'] == 'SUCCESS'){
            //查询后台钱到了没
            $this->load->model('base/pay_log_base', 'pay_log_base');
            $orderInfo = $this->pay_log_base->getLogByOrdid($orderid);
            if($orderInfo['status'] == 0){
                $status = 3;
            }else{
                $status = 1;
                $this->load->model('logic/cd_logic', 'cd_logic');
                $cd_data = $this->cd_logic->getUserCd($this->uid);
                $cd_data['pay']--;
                $this->cd_logic->setUserCd($this->uid, $cd_data);
            }
            $check = 1;
            $response = array('error' => 0, 'data' => array('status' => $status, 'errormsg' => $data['ret_msg'], 'check' => 1));
            $this->out_print($response);
        }else if($data['result_pay'] == 'WAITING' || $data['result_pay'] == 'PROCESSING'){      //等待  处理中
            $response = array('error' => 0, 'data' => array('status' => 2, 'errormsg' => $data['ret_msg'], 'check' => 0));
            $this->out_print($response);
        }else{
            $response = array('error' => 0, 'data' => array('status' => 0, 'errormsg' => $data['ret_msg'], 'check' => 0));
            $this->out_print($response);
        }
        
    }
    
    public function llpay_regist(){
        $cardno = trim($this->input->post('cardno'));          //银行卡号
        $idcardno = trim($this->input->post('idcardno'));      //身份证号
        $username = trim($this->input->post('name'));          //用户名
        $phone = trim($this->input->post('phone'));            //银行预留电话
        $bankCode = trim($this->input->post('bankid'));
        $cityId = trim($this->input->post('cityid'));
        $zj = trim($this->input->post('zj'));
        
//        $response = array('error' => 11999, 'msg' => '由于刷数量严重，暂停活动1天！');
//        $this->out_print($response);
        
        if(empty($zj) || !in_array($zj, array('01','04','05'))){
            $zj = '01';
        }
        $phone_count = $this->user_logic->countByPhone($phone);
        if(!empty($phone_count)){
        	$response = array('error' => 11200, 'msg' => '当前手机号码在平台已绑定相关银行卡，请选择其他号码进行绑定');
        	$this->out_print($response);
        }
        if(strlen($cardno) < 10){
            $response = array('error' => 11998, 'msg' => '错误的卡号');
            $this->out_print($response);
        }
        $this->config->load('cfg/banklist', true, true);
        $bankCfg = $this->config->item('cfg/banklist');
        if(!isset($bankCfg[$bankCode])){
            $response = array('error' => 11999, 'msg' => '暂不支持此银行!');
            $this->out_print($response);
        }
//        if(empty($cityId)){
//            $response = array('error' => 11997, 'msg' => '请选择银行开户地!');
//            $this->out_print($response);
//        }
        
        if($this->uid < 42){
            $response = array('error' => 11200, 'msg' => '请重新注册！');
            $this->out_print($response);
        }
        
        $jyt_bank_code = $bankCfg[$bankCode]['jyt_bank_code'];
        
        $identityid = $this->uid;
        $requestid = $this->uid.date('Y-m-d').mt_rand(1000,9999);
        $userip = $this->getIP();                               //用户支付时使用的网络终端 IP
        $identity_card = $this->user_logic->getUserIdentityByIdcard($idcardno);
        if($identity_card['ischeck'] == 1 && $identity_card['isvalidate'] == 1 ){
            $response = array('error' => 11999, 'msg' => '用户信息已在其它账号绑定!');
            $this->out_print($response);
        }
        $identity_result = $this->user_logic->getUserIdentity($this->uid);
        if(!empty($identity_result['cardno']) && $identity_result['cardno'] != $cardno){
            $response = array('error' => 12000, 'msg' => '只能重新绑定原卡');
            $this->out_print($response);
        }
        if(($identity_result && $identity_result['idCard'] != $idcardno) || ($identity_result && $identity_result['realname'] != $username)){
            $response = array('error' => 12000, 'msg' => '身份证与原预留信息不符');
            $this->out_print($response);
        }
        $ischeck = false;
        $orderid = '290060100031' . date('YmdHis') . rand(100000,999999);    //请根据商户系统自行定义订单号;
        $this->load->model('base/bind_card_cd_base', 'bind_card_cd_base');
        $num = $this->bind_card_cd_base->get($this->uid);
        if($num > 2){
            $response = array('error' => 12001, 'msg' => '您操作过于频繁，请半小时后再试！');
            $this->out_print($response);
        }
        $validatecode = trim($this->input->post('validatecode'));
        $phone = trim($this->input->post('phone'));
        
        //验证手机是否为刚才收短信的手机
        $msg_phone = $this->user_logic->getBindBankPhone($this->uid);
        if($phone != $msg_phone){
            $response = array('error' => 12003, 'msg' => '验证手机号不正确!!');
            $this->out_print($response);
        }
        
        //验证短信
        $code = $this->user_logic->getBindBankCode($this->uid);
        //             echo $code .'|'. $validatecode;
        if($validatecode != $code){
            $response = array('error' => 12001, 'msg' => '短信验证码不正确!!');
            $this->out_print($response);
        }
        $codesender='';
        $validated=false;
        if(HDSJ == 'true' && TEST_IS_IDENTITY == true){
            $this->load->model('logic/huoduoshuju_logic', 'huoduoshuju_logic');
            //$result = @$this->huoduoshuju_logic->validate($cardno, $idcardno, $username, $phone);
            $result = false;
            if ($result) {
                $this->bind_card_cd_base->incr_hdsj_validate();
                if ($result && $result["err"] == "0") {
                    if ($result["output"]['res'] == 1) {
                        if ($result["output"]['bankinfo']['cardtype'] == '借记卡') {
                            $codesender = 'HDSJ';
                            $validated = true;
//			        			$hdsj_bank_code=$result["output"]['bank_code'];
//			        			if(isset($this->diff_bankcode_map[$hdsj_bank_code])){
//			        				$bankCode = $this->diff_bankcode_map[$hdsj_bank_code];
//			        			}else{
//			        				$bankCode=$hdsj_bank_code;
//			        			}
                        } else {
                            $this->bind_card_cd_base->incr($this->uid);
                            $response = array('error' => 12005, 'msg' => '暂不支持信用卡绑卡!');
                            $this->out_print($response);
                        }
                    } else {
                        $this->bind_card_cd_base->incr($this->uid);
                        $response = array('error' => 12002, 'msg' => $result["output"]['msg'].'绑卡失败');
                        $this->out_print($response);
                    }
                }
            }else{
//                $this->bind_card_cd_base->incr($this->uid);
//                $response = array('error' => 12000, 'msg' => '认证服务发生错误!请联系客服');
//                $this->out_print($response);

                $this->load->model('logic/fuioucard_logic', 'fuioucard_logic');
                $fuiouzj = $zj == '01' ? 0 : '';
                $orderid = rand(10000,99999) . date('YmdHis') . rand(100000,999999);
                $result = @$this->fuioucard_logic->validate($orderid, $fuiouzj, $cardno, $idcardno, $username, $phone);
                $this->bind_card_cd_base->incr_hdsj_validate();
                if ($result['Rcd'] === '0000') {
                    if ($result['InsCd'] == $bankCfg[$bankCode]['fuiou_bank_card']){
                        $codesender = 'FUIOU';
                        $validated = true;
                    }else{
                        $validated = false;
                    }
                }else{
                    $this->bind_card_cd_base->incr($this->uid);
                    $response = array('error' => 12002, 'msg' => $result['RDesc']);
                    $this->out_print($response);
                }
            }
        }

//        if(!$validated){
// 	        $this->load->model('logic/jytpay_logic', 'jytpay_logic');
// 	        $data = $this->jytpay_logic->validate($orderid, $cardno, $idcardno, $username, $phone, $jyt_bank_code);
// 	        $this->bind_card_cd_base->incr_jyt_validate();
// 	        $this->validate_log($data);
// 	        $validate = simplexml_load_string($data);
// 	        //print_r($validate);
// 	        if(empty($validate)){
// 	            $response = array('error' => 12001, 'msg' => '验证信息不正确!');
// 	            $this->out_print($response);
// 	        }
// 	        if((string)$validate->head->resp_code == 'S0000000'){
//	        	$codesender='JYT';
//	        	$validated=true;
// 	        }
//        }

        if(!TEST_IS_IDENTITY){
            $validated = true;
        }
        
        $this->load->model('base/user_base', 'user_base');
        $this->load->model('base/balance_base' , 'balance_base');//后面用到
        $this->load->model('base/user_log_base', 'user_log_base');//后面用到
        $this->load->model('logic/msm_logic', 'msm_logic');//后面用到
        
        if($validated){
            //绑定卡
            $ischeck = true;
            $identity_data = array();
            $identity_data['realname'] = trim($username);
            $identity_data['idCard'] = $idcardno;
            $identity_data['cardno'] = $cardno;
            $identity_data['phone'] = $phone;
            $identity_data['bankcode'] = $bankCode;
            $identity_data['cityid'] = $cityId;
            $identity_data['requestid'] = NOW.$this->uid;
            $identity_data['codesender'] = $codesender;
            $identity_data['zj'] = $zj;
            $identity_data['ischeck'] = 1;
            $identity_data['isvalidate'] = 1;
            $identity_data['ctime'] = NOW;
            $identity_result = $this->user_logic->getUserIdentity($this->uid);
            if($identity_result){
                $this->load->model('base/balance_base', 'balance_base');
                $balance = $this->balance_base->get_user_balance($this->uid);
                
                //活期
                $this->load->model('logic/longproduct_logic', 'longproduct_logic');
                $longmoney = $this->longproduct_logic->getLongmoney($this->uid);
//                  if($balance + $longmoney > 0){
//                      $response = array('error' => 12001, 'msg' => '危险的操作');
//                      $this->out_print($response);
//                  }
                $where = array('uid' => $this->uid);
                $rtn = $this->user_logic->updateUserIdentity($identity_data, $where);
                if(!$rtn){
                    $response = array('error' => 12001, 'msg' => '用户信息更新失败');
                    $this->out_print($response);
                }
            }else{
                $identity_data['uid'] = $this->uid;
                $rtn = $this->user_logic->initUserIdentity($identity_data);
                if(!$rtn){
                    $response = array('error' => 12001, 'msg' => '用户信息保存失败');
                    $this->out_print($response);
                }
                
                //被邀请的新用户首次绑卡邀请送5元
//                $accountInfo = $this->user_base->getAccountInfo($this->uid);
//                if($accountInfo && $accountInfo['plat'] == 'invite'){
//                    $this->load->model('base/invite_base', 'invite_base');
//                    $invite_my = $this->invite_base->get_invite_my($this->uid);
//                    $binaCardSendInviteMoney = 5;
//                    $user_balance = $this->balance_base->get_user_balance($invite_my['invite_uid']);
//                    $ret = $this->balance_base->add_user_balance($invite_my['invite_uid'], $binaCardSendInviteMoney);
//                    if($ret){
//                        $log_data = array(
//                            'uid' => $invite_my['invite_uid'],
//                            'pid' => 0,
//                            'paytime' => NOW,
//                            'pname' => '好友注册绑卡奖励',
//                            'money' => $binaCardSendInviteMoney,
//                            'balance' => $user_balance+$binaCardSendInviteMoney,
//                            'action' => USER_ACTION_INVITE,
//                            'orderid' => 'bk'.$this->uid.date('YmdHis').mt_rand(100,999)
//                        );
//                        $this->user_log_base->addUserLog($invite_my['invite_uid'], $log_data);
//                    }
//                    
//                    $this->msm_logic->send_bina_card_reward_msg($invite_my['invite_uid'], $binaCardSendInviteMoney, $invite_my['invite_account']);
//                    
//                    $notice_data = array(
//                                    'uid' => $invite_my['invite_uid'],
//                                    'title' => '好友注册绑卡奖励提醒',
//                                    'content' => "恭喜您获得".$binaCardSendInviteMoney."元的好友注册绑卡奖励，可在资产余额里面查看。赶紧把这个好消息告诉你的小伙伴吧！",
//                                    'ctime' => NOW
//                    );
//                    $this->user_notice_base->addNotice($invite_my['invite_uid'],$notice_data);
//                    
//                }
                //邀请送5元end
            }
            
            //添加微信红包到余额
            $this->load->model('base/redbag_base' , 'redbag_base');
            $res = $this->redbag_base->get_user_redbag_money($this->account);
            if($res){
            	$balance = $this->balance_base->get_user_balance($this->uid);
            	$balance += $res['money'];
            	 
            	//写用户日志
            	$user_log_data = array(
            			'uid' => $this->uid,
            			'pid' => 0,
            			'pname' => '新手红包',
            			'money' => $res['money'],
            			'balance' => $balance,
            			'orderid' => $orderid,
            			'action' => USER_ACTION_ACTIVITY
            	);
            	
            	$this->user_log_base->addUserLog($this->uid, $user_log_data);
            	$ret = $this->balance_base->add_user_balance($this->uid, $res['money']);
            	if($ret){
            		$this->redbag_base->delete_user_redbag_money($this->account);
            		$this->load->model('base/redbag_log_base' , 'redbag_log_base');
            		$red_log['utime']=NOW;
            		$this->redbag_log_base->update_redbag_log($red_log,$res['id']);
            	}
            }

            $this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
            $count = $this->coupon_activity_logic->sendCoupon(COUPON_ACTIVITY_VALIDATE,0,$this->uid,$this->account);
            if($count['singlcount']>0){
            	$this->msm_logic->send_coupon_user_msg($this->account,$username, $count['singlcount'],$count['moneycount']);
            	$notice_data = array(
            			'uid' => $this->uid,
            			'title' => '抵用券获得提醒',
            			'content' => "恭喜您获得了".$count['singlcount']."张共价值".$count['moneycount']."元的现金抵用券，可在购买产品是直接抵扣现金使用，赶快去【我的资产-抵用券】看看吧！",
            			'ctime' => NOW
            	);
            	$this->user_notice_base->addNotice($this->uid,$notice_data);
            }
            
//             $c = new TopClient;
//             $c->appkey = '23565889';
//             $c->secretKey = '230e6b7925b47913972a7530fde7d779';
//             $t_uid = 'cmibank'.$this->account;
//             $req = new OpenimUsersUpdateRequest;
//             $req->setUserinfos("{'userid':'".$t_uid."','nick':'".$identity_data['realname']."','mobile':'".$this->account."'}");
//             $resp = $c->execute($req);
            $this->activity_logic->addUserIntegral($this->account,30,$this->uid,JIFENG_BANGKA);
           	$response = array('error' => 0, 'data' => array('status' => 1,'couponcounts'=>$count['singlcount']));
            $this->out_print($response);
        }else{
            //次数+1 单用户不能超过5次   CD 24小时
            $this->bind_card_cd_base->incr($this->uid);
            $response = array('error' => 12002, 'msg' => '银行预留信息与输入信息不符');
            $this->out_print($response);
        }
    }
    
    
    public function bindcard(){
        $uid = $this->input->post('uid');
        $parameter = array(
            'user_id' => $uid,
            'pay_type' => 'D',
            'sign_type' => 'MD5',
            'offset' => '0'
        );
        $data  = $this->llpay_logic->querybindcard($parameter);
        $data = json_decode($data, true);
        print_r($data);
    }
    
    
//     public function unbindcard(){
//         //2015122341718672
//         $uid = $this->input->post('uid');
//         $no_agree = $this->input->post('no_agree');
//         $parameter = array(
//             'user_id' => $uid,
//             'pay_type' => 'D',
//             'sign_type' => 'MD5',
//             'no_agree' => $no_agree
//         );
        
//         $data  = $this->llpay_logic->unbindCard($parameter);
//         $data = json_decode($data, true);
//         print_r($data);
//     }
    
    private function validate_log($msg){
        $logFile = '/tmp/validate_back.log' . date('Y-m-d');
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
    
//  取现
//     public function withDraw(){
//         if($this->uid != 120837){
//             die('禁用接口');
//         }
//         if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
//             die('内网不能取现');
//         }else if((isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '117.50.2.20')
//             || (isset($_SERVER['HOSTNAME']) && $_SERVER['HOSTNAME'] == '120-132-48-121')){
//             die('测试环境不能取现');
//         }
        
//         $money = $this->input->request('money_order');                 //取现金额
//         if(!$money|| $money <= 0){
//             $response = array('error'=> 4017, 'msg'=>'请输入取现金额');
//             $this->out_print($response);
//         }
//         //取现次数限制  
//         $this->load->model('logic/cd_logic', 'cd_logic');
//         $cd_data = $this->cd_logic->getUserCd($this->uid);
//         if($cd_data['withDraw'] <= 0){
//             $response = array('error'=> 4018, 'msg'=>'今日取现次数已达上限');
//             $this->out_print($response);
//         }
//         $cd_data['withDraw']--;
//         $this->cd_logic->setUserCd($this->uid, $cd_data);
        
//         $this->load->model('logic/user_identity_logic', 'user_identity_logic');
//         $identity_result = $this->user_identity_logic->getPublicUserIdentity($this->uid, 'all');
        
//         $this->load->model('base/user_base', 'user_base');
//         $user_account_info = $this->user_base->getAccountInfo($this->uid);
//         if(empty($identity_result) || $identity_result['ischeck'] != 1){
//             $response = array('error' => 11999, 'msg' => '请先进行绑卡操作!');
//             $this->out_print($response);
//         }
//         $tpwd = trim($this->input->post('pwd'));
//         if(!$identity_result || !$identity_result['tpwd']){
//             $response = array('error'=> 4020, 'msg'=>'请先设置交易密码');
//             $this->out_print($response);
//         }
//         if($identity_result['tpwd'] != $tpwd){
//             $response = array('error'=> 4021, 'msg'=>'交易密码错误');
//             $this->out_print($response);
//         }
//         $acct_name = $identity_result['realname'];                  //银行卡用户名
//         $id_no = $identity_result['idCard'];                        //身份证号
//         $card_no = $identity_result['cardno'];                      //银行卡号
        
//         //扣除余额
//         $this->load->model('base/balance_base', 'balance_base');
//         $balance = $this->balance_base->get_user_balance($this->uid);
//         if($balance < $money){
//             $response = array('error' => 12002, 'msg' => '用户余额不足');
//             $this->out_print($response);
//         }
//         $ret = $this->balance_base->cost_user_balance($this->uid, $money);
//         if(!$ret){
//             $err_data = array();
//             $err_data['uid'] = $this->uid;
//             $err_data['pid'] = 0;
//             $err_data['ptype'] = 'product';
//             $err_data['money'] = $money;
//             $err_data['balance'] = $balance;
//             $response = array('error'=> 3333, 'msg'=> '余额不足');
//             $this->out_print($response, 'json',  true,  true, $err_data);
//         }
        
//         $orderid = date('YmdHis') . $this->uid . mt_rand(1000,9999);
//         $dt_order = date('YmdHis');
        
//         $user_log_data = array(
//             'uid' => $this->uid,
//             'pid' => 0,
//             'pname' => '取现处理中',
//             'orderid' => $orderid,
//             'money' => $money,
//             'balance' => $balance - $money,
//             'action' => USER_ACTION_PCASHOUT
//         );
//         $this->load->model('base/user_log_base', 'user_log_base');
//         $last_id = $this->user_log_base->addUserLog($this->uid, $user_log_data);
        
//         //系统取现日志
//         $this->load->model('base/withdraw_log_base', 'withdraw_log');
//         $withdraw_log['uid'] = $this->uid;
//         $withdraw_log['orderid'] = $orderid;
//         $withdraw_log['dt_order'] = $dt_order;
//         $withdraw_log['ybdrawflowid'] = $orderid;
//         $withdraw_log['status_code'] = '';
//         $withdraw_log['money'] = $money;
//         $withdraw_log['logid'] = $last_id;
//         $withdraw_log['plat'] = 'llpay';
//         $withdraw_id = $this->withdraw_log->addLog($withdraw_log);
        
//         /************************************************************/
        
//         //构造要请求的参数数组，无需改动
//         $parameter = array (
// //             "oid_partner" => trim($llpay_config['oid_partner']),
//             "sign_type" => 'RSA',
//             "no_order" => $orderid,
//             "dt_order" => $dt_order,                   //14位数字，精确到秒,
//             "money_order" => $money,
//             "flag_card" => "0",
//             "card_no" => $card_no,
//             "acct_name" => $acct_name,
// //             "bank_code" => $bank_code,
// //             "city_code" => $city_code,
// //             "brabank_name" => $brabank_name,
//             "info_order" => '用户取现',
//             "notify_url" => $this->withdraw_notify_url,
//             "api_version" => '1.2',
// //            "prcptcd" => $prcptcd
//         );
//         //建立请求
//         $return_data = $this->llpay_logic->withDraw($parameter);
//         $response = array('error'=> 0, 'data'=> $return_data);
//         $this->out_print($response);
//     }

    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */