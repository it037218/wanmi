<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class system extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('base/feedback_base', 'feedback_base');
    }
    
    //银行限制
    /*
                     农业银行	2万	2万    60万	     1万	2万	2万
                    浦发银行	5万	5万	150万	 1万	2万	5万
                    工商银行	5万	5万	150万	 1万	2万	2万
                    民生银行	50万	100万   3000万       1万	1万	5万
                    平安银行	50万	100万       无限额      1万	2万	5万
                    中国银行	1万	1万	无限额              1万	1万	5万
                    建设银行	50万	无限额        无限额	1万	1万	5万
                    光大银行	50万	100万	无限额	1万	2万	5万
                    兴业银行	5万	5万	150万	1万	2万	2万
                    华夏银行	50万	100万	无限额	1万	1万	5万

     */
//     public function bankLimit(){

//             $jyt_bankList = array(
//                     'ABC' => array('single' => 20000, 'singleDay' => 20000, 'singleMonth' => 600000),           //农行
//                     'SPDB' => array('single' => 50000, 'singleDay' => 50000, 'singleMonth' => 1500000),         //浦发
//                     'ICBC' => array('single' => 50000, 'singleDay' => 50000, 'singleMonth' => 1500000),         //工商
//                     'CMBC' => array('single' => 500000, 'singleDay' => 1000000, 'singleMonth' => 30000000),     //民生
//                     'PINGAN' => array('single' => 500000, 'singleDay' => 1000000, 'singleMonth' => 30000000),   //平安
//                     'BOC' => array('single' => 10000, 'singleDay' => 10000, 'singleMonth' => 300000),           //中国
//                     'CCB' => array('single' => 500000, 'singleDay' => 1000000, 'singleMonth' => 30000000),      //建设
//                     'CEB' => array('single' => 500000, 'singleDay' => 1000000, 'singleMonth' => 30000000),      //光大
//                     'CIB' => array('single' => 50000, 'singleDay' => 50000, 'singleMonth' => 1500000),          //兴业
//                     'HXB' => array('single' => 500000, 'singleDay' => 1000000, 'singleMonth' => 3000000),       //华厦
//                     'ECITIC' => array('single' => 500000, 'singleDay' => 1000000, 'singleMonth' => 3000000),    //中信
//                     'CMBCHINA' => array('single' => 5000, 'singleDay' => 5000, 'singleMonth' => 50000),         //招商
//                     'GDB' => array('single' => 500000, 'singleDay' => 5000000, 'singleMonth' => 15000000),      //广发 
//                     'POST' => array('single' => 5000, 'singleDay' => 5000, 'singleMonth' => 150000),             //储蓄
//                     'BOCO' =>  array('single' => 50000, 'singleDay' => 200000, 'singleMonth' => 6000000)         //交通
//             );
//     }
    

    public function sendfeedback(){
        $phone = $this->input->post('phone');
        $content = $this->input->post('content');
        if(strlen($content) >= 200){
            $response = array('error'=> 11020, 'msg'=>'内容太长了');
            $this->out_print($response);
        }
        if(empty($phone)){
            $response = array('error'=> 11021, 'msg'=>'请输入联系方式');
            $this->out_print($response);
        }
        if(empty($content)){
            $response = array('error'=> 11021, 'msg'=>'请输入意见内容');
            $this->out_print($response);
        }
        $feedback_data = array();
        $feedback_data['phone'] = $phone;
        $feedback_data['content'] = $content;
        $feedback_data['ctime'] = NOW;
        $ret = $this->feedback_base->add($feedback_data);
        if($ret){
            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }else{
            $response = array('error'=> 11023, 'msg'=>'发送失败！请联系客服');
            $this->out_print($response);
        }
    }
    
    public function getAndroidVersion(){
    	$uid = $this->getCookie('uid');
    	$this->load->model('base/pay_redis_base', 'pay_redis_base');
    	$this->pay_redis_base->expAndoridVersion($uid);
    	$this->config->load('cfg/upgroup_notic_android', true, true);
    	$upgroup_notic = $this->config->item('cfg/upgroup_notic_android');
    	$response = array('error'=> 0, 'data'=>array(
    			'version' => $upgroup_notic['new_version'],
    			'paytype' => PAY_TYPE,
    			'pay_qudao' => PAY_QUDAO,
    			'withdraw_txt' => 'day',
    			'withdraw_sxf' => WITHDRAW_SXF,
    			'pay_list' => array('baofoo','llpay'),
    			'withdraw_tips' => false,
    			'yee_amount_limit' => YEE_AMOUNT_LIMIT
    	));
    	$week_days = array(6, 0);
    	if(in_array(date('w'), $week_days)){
    		$response['withdraw_tips'] = true;
    	}
    	$this->out_print($response);
    }
    
    public function getVersion(){
        $this->config->load('cfg/upgroup_notic', true, true);
        $upgroup_notic = $this->config->item('cfg/upgroup_notic');
        $response = array('error'=> 0, 'data'=>array(
            'version' => $upgroup_notic['new_version'],
            'paytype' => PAY_TYPE,
            'pay_qudao' => PAY_QUDAO,
            'withdraw_txt' => 'day',
            'withdraw_sxf' => WITHDRAW_SXF,
            'pay_list' => array('baofoo','llpay'),
            'withdraw_tips' => false,
            'yee_amount_limit' => YEE_AMOUNT_LIMIT
        ));
        $week_days = array(6, 0);
        if(in_array(date('w'), $week_days)){
            $response['withdraw_tips'] = true;
        }
        $this->out_print($response);
    }
    
    public function checkSystemInfo(){
        $current_version = $this->input->post('version');
    	
    	$updateParams = array();
    	$updateParams['uid'] = $this->getCookie('uid');
    	$updateParams['device'] =  trim($this->input->post('plat'));
    	$updateParams['version'] =  trim($current_version);
    	$this->load->model('logic/login_logic', 'login_logic');
    	$this->login_logic->updateAccountInfo($updateParams);
    	
        if(!$current_version){
            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }
        list($num_1, $num_2, $num_3) = explode('.',$current_version);
        $this->config->load('cfg/upgroup_notic', true, true);
        $upgroup_notic = $this->config->item('cfg/upgroup_notic');
        $upgroup_notic['force_use_time'] = strtotime($upgroup_notic['force_use_time']);
        if(!isset($upgroup_notic['qj_version'])){
            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }
        $qj_version = $upgroup_notic['qj_version'];
        list($v_1, $v_2, $v_3) = explode('.',$qj_version);
        $num_1 = str_pad($num_1,3,0,STR_PAD_RIGHT);
        $num_2 = str_pad($num_2,3,0,STR_PAD_RIGHT);
        $num_3 = str_pad($num_3,3,0,STR_PAD_RIGHT);
        $v_1 = str_pad($v_1,3,0,STR_PAD_RIGHT);
        $v_2 = str_pad($v_2,3,0,STR_PAD_RIGHT);
        $v_3 = str_pad($v_3,3,0,STR_PAD_RIGHT);
        $current_version = intval($num_1 . $num_2 . $num_3);
        $qj_version = intval($v_1 . $v_2 . $v_3);
//         echo $current_version;
//         echo '|';
//         echo $qj_version;
        if($qj_version <= $current_version){
            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }
        $response = array('error'=> 0, 'data'=> $upgroup_notic);
        $this->out_print($response);
    }
    
    public function checkSystemInfo_android(){
    	$uid = $this->getCookie('uid');
    	usleep(900000);
    	$this->load->model('base/pay_redis_base', 'pay_redis_base');
    	$this->pay_redis_base->expAndoridVersion($uid);

    	$current_version = $this->input->post('version');

    	$updateParams = array();
    	$updateParams['uid'] = $uid;
    	$updateParams['version'] =  trim($current_version);
    	$this->load->model('logic/login_logic', 'login_logic');
    	$this->login_logic->updateAccountInfo($updateParams);


        if(!$current_version){
            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }
        $type = $this->input->post('type');
        if($type == 1){
            $type = 'qj_version';
        }else{
            $type = 'new_version';
        }
        list($num_1, $num_2, $num_3) = explode('.',$current_version);
        $this->config->load('cfg/upgroup_notic_android', true, true);
        $upgroup_notic = $this->config->item('cfg/upgroup_notic_android');
        $upgroup_notic['force_use_time'] = strtotime($upgroup_notic['force_use_time']).'000';
        if(!isset($upgroup_notic[$type])){

            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }
        $qj_version = $upgroup_notic[$type];
        list($v_1, $v_2, $v_3) = explode('.',$qj_version);
        $num_1 = str_pad($num_1,3,0,STR_PAD_RIGHT);
        $num_2 = str_pad($num_2,3,0,STR_PAD_RIGHT);
        $num_3 = str_pad($num_3,3,0,STR_PAD_RIGHT);
        $v_1 = str_pad($v_1,3,0,STR_PAD_RIGHT);
        $v_2 = str_pad($v_2,3,0,STR_PAD_RIGHT);
        $v_3 = str_pad($v_3,3,0,STR_PAD_RIGHT);
        $current_version = intval($num_1 . $num_2 . $num_3);
        $qj_version = intval($v_1 . $v_2 . $v_3);
        //         echo $current_version;
        //         echo '|';
        //         echo $qj_version;
        if($qj_version <= $current_version){
            $response = array('error'=> 0, 'data'=>array());
            $this->out_print($response);
        }
        $response = array('error'=> 0, 'data'=> $upgroup_notic);
        $this->out_print($response);
    }
    
    public function get_activity_time(){
        //注册红包和新手红包
        $this->config->load('cfg/activity_time', true, true);
        $activity_time = $this->config->item('cfg/activity_time');
        $activity_id = array(3, 4);     //3注册红包   4红包雨
        $return_data = array();
        foreach ($activity_time as $_id => $v){
            if(in_array($_id, $activity_id)){
                $return_data[$_id] = $v;
            }
        }
        $response = array('error'=> 0, 'data'=> $return_data);
        $this->out_print($response);
    }
    
    public function get_expmoney_amount(){
    	$this->load->model('base/expmoney_activity_base' , 'expmoney_activity_base');
    	$activityDetailList = $this->expmoney_activity_base ->getExpmoneyActivityDetail(1);
    	$amount = '0';
    	if($activityDetailList){
    		$activityInfo=$activityDetailList[0];
    		$amount=$activityInfo['money'];
    	}
    	$return_data['amount'] = $amount;
    	$response = array('error'=> 0, 'data'=> $return_data);
    	$this->out_print($response);
    }
    

    public function nofityLuckybag(){
    	$uid = $this->getCookie('uid');
    	$account = $this->getCookie('account');
    	$lid = trim($this->input->post('lid'));
    	$this->load->model('logic/luckybag_logic', 'luckybag_logic');
    	$luckybagDetail = $this->luckybag_logic->getLuckybagDetailByid($uid,$lid);
    	if(!empty($luckybagDetail)){
    		if($luckybagDetail['status']!=1){
    			$response = array('error'=> 0, 'msg'=> '提示成功');
    			$this->out_print($response);
    		}else if($luckybagDetail['noticed']==1){
    			$response = array('error'=> 0, 'msg'=> '提示成功');
    			$this->out_print($response);
    		}else{
    			$this->load->model('logic/msm_logic', 'msm_logic');
    			$result = $this->msm_logic->send_notify_luckybag_msg($luckybagDetail['uuaccount'], substr($account,-4),$luckybagDetail['money']);
    			if($result){
    				$noticedresult = $this->luckybag_logic->setNoticed($uid,$lid);
    				if($noticedresult){
    					$response = array('error'=> 0, 'msg'=> '提示成功');
    					$this->out_print($response);
    				}else{
    					$response = array('error'=> 1011, 'msg'=> '提示失败');
    					$this->out_print($response);
    				}
    			}
    		}
    	}else{
    		$response = array('error'=> 1011, 'msg'=> '提示失败');
    		$this->out_print($response);
    	}
    }
    
    public function getNews(){
    	$this->load->model('base/news_base', 'news_base');
    	$page = max(1, intval($this->input->post('page')));
    	$psize = 10;
    	$start = ($page - 1) * $psize;
    	$end = $start + $psize - 1;
    	$rtn = array();
    	$newslist = $this->news_base->getNewslist($start, $end);
    	$response = array('error'=> 0, 'data'=> $newslist);
    	$this->out_print($response);
    }

    public function GetDownLoad(){
        if(@$_SERVER['ENVIRONMENT'] == 'production') {
            header("Location: http://static.cmibank.com/apk/cmibank.apk");
        }else{
            header("Location: http://static.cmibank.vip/apk/cmibank.apk");
        }
    }
}



/* End of file test.php */
/* Location: ./application/controllers/test.php */