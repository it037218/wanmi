<?php

include_once (APPPATH . 'libraries/XingeApp.php');
class push_logic extends CI_Model {
    
    private $access_id = '2100139661';
    private $access_key = 'ACF9926AK2KG';
    private $secret_key = '191ef585676f28bc5a2ee03a8a0929af';
    
    private $ios_access_id = '2200141314';
    private $ios_access_key = 'IKWT6P75X56I';
    private $ios_secret_key = 'a6e85aa2f215d664dbceab1ec29acf2e';
    
    function __construct() {
        # 继承CI 父类
        parent::__construct();
    }
    
    public function PushAccountAndroid($title, $content, $account){
        return XingeApp::PushAccountAndroid($this->access_id, $this->secret_key, $title, $content, $account);
    }
    
    public function PushAccountIos($content, $account, $environment){
        echo $account;
        return XingeApp::PushTokenIos($this->ios_access_id, $this->ios_secret_key, $content, $account, $environment);
    }
    
    public function PushAllAndroid($title, $content){
        return XingeApp::PushAllAndroid($this->access_id, $this->secret_key,$title, $content);
    }
    
    public function PushAllIos($accessId, $secretKey, $content, $environment){
        return XingeApp::PushAllIos($this->access_id, $this->secret_key, $content, XingeApp::IOSENV_DEV);
    }
    
    
    
}