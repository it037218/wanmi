<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class rate_coupon extends Controller {

    public function __construct()
    {
        parent::__construct();
//        $this->check_login();
    }

    public function lists(){
        //是否登录
        //uid 
        //0 0 0
        $uid = $this->input->request('uid');
        $loginUid = $this->uid;
        
        if($loginUid != $uid){
            $response = array('error'=> 1000, 'data'=> '非法UID');
            $this->out_print($response);
        }
        
        if(@$_SERVER['ENVIRONMENT'] == 'production'){
            $data = array();
        }elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
            if($loginUid){
                $ratecoupon = '123张（元）';
                $qiankun = '最高奖1千';
            }
            
            $data = array(
                'ratecoupon2' => array(
                    'name'=>'加息券',
                    'icon' =>'http://static.cmibank.vip/icon/menu/59954.gif',
                    'number'=> (isset($ratecoupon) ? $ratecoupon : '0张'),
                    'url' => 'http://api.cmibank.vip/rate_coupon/detail'
                ),
                'qiankun2' => array(
                    'name'=>'易传乾坤',
                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
                    'number'=> (isset($qiankun) ? $qiankun : '0'),
                    'url' => 'http://api.cmibank.vip/rate_coupon/detail3'
                ),
//                'coupon2' => array(
//                    'name'=>'抵用券',
//                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
//                    'number'=> '12张（元）',
//                    'url' => 'http://www.cmibank.com/'
//                ),
//                'redpacket' => array(
//                    'name'=>'红包',
//                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
//                    'number'=> '3个（元）',
//                    'url' => 'http://www.cmibank.com/'
//                ),
//                'choujiang' => array(
//                    'name'=>'抽奖'.$loginUid,
//                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
//                    'number'=> '12次',
//                    'url' => 'http://www.cmibank.com/'
//                ),
            );
        }else{
            $data = array(
                'ratecoupon' => array(
                    'name'=>'加息券',
                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
                    'number'=> '123张（元）',
                    'url' => 'http://api.cmibank.com/rate_coupon/detail'
                ),
                'coupon' => array(
                    'name'=>'抵用券',
                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
                    'number'=> '12张（元）',
                    'url' => 'http://www.cmibank.com/'
                ),
                'redpacket' => array(
                    'name'=>'红包',
                    'icon' =>'http://www.cmibank.com/images/ratecoupon.png',
                    'number'=> '3个（元）',
                    'url' => 'http://www.cmibank.com/'
                ),
            );
        }
        
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
        
        
    }
    public function detail() {
        $slag = 'QwErTCmIban';
        $rawUid = $this->input->request('uid');
        $base64uid = base64_decode($rawUid);
        $uid = str_replace($slag, '', $base64uid);
        if(!$uid){
            $response = array('error'=> 1000, 'data'=> '非法uid');
            $this->out_print($response);
        }
        $loginUid = $this->uid;
        echo "uid: $uid loginUid$loginUid";
    }
    
    public function detail3() {
        $slag = 'QwErTCmIban';
        $rawUid = $this->input->request('uid');
        $base64uid = base64_decode($rawUid);
        $uid = str_replace($slag, '', $base64uid);
        if(!$uid){
            $response = array('error'=> 1000, 'data'=> '非法uid');
            $this->out_print($response);
        }
        $loginUid = $this->uid;
        echo "uid: $uid loginUid$loginUid";
    }
    
    public function detail2() {
        $uid = $this->input->request('uid');
        if(!$uid){
            $response = array('error'=> 1000, 'data'=> '没有传递uid');
            $this->out_print($response);
        }
        $loginUid = $this->uid;
        echo "uid: $uid loginUid$loginUid";
    }
}