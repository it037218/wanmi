<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 用户购买产品（资产）信息
 */
class invite_page extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('base/invite_limit_base', 'invite_limit_base');
        $this->load->model('logic/invite_logic', 'invite_logic');
    }
     
    public function activity1111(){
        $code = $this->input->request('code');
        
        if(strpos($code,'null') !== false){
            $this->invalidHtml();
        }
        
        if(!$code){
            $response = array('error'=> 2222, 'msg'=>'好友验证码错误');
            $this->out_print($response);
        }
        
        $invite_uid = $this->decode_invite($code);
        $this->load->model('logic/login_logic', 'login_logic');
        $account = $this->login_logic->getAccountInfo($invite_uid);
        $phone = $account['account'];
        $data['phone'] = substr($phone, 0, 3) . '****' . substr($phone, -4);
        $data['code'] = $code;

        if(isset($_SERVER['HTTP_X_CLIENT_PROTO']) && $_SERVER['HTTP_X_CLIENT_PROTO'] == 'https'){
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("Location:$url");
        }
        
        $isActive = true;
        if($isActive){
            $data['be_invite_num'] =@ $this->invite_limit_base->get() ? $this->invite_limit_base->get() : 0;
            $data['invite_num'] = @$this->invite_limit_base->get2() ? $this->invite_limit_base->get() : 0;
        }

        $this->load->view('invite', $data);
    }
    
    /**
     * 邀请界面
     * @return [type] [description]
     */
    public function index(){
        $code = $this->input->request('code');
        if(strpos($code,'null') !== false){
            $this->invalidHtml();
        }
        if(!$code){
            $response = array('error'=> 2222, 'msg'=>'好友验证码错误');
            $this->out_print($response);
        }

//        $top_rank = $this->invite_logic->top_rank(4);
//        $firstInvenst = $this->invite_logic->getFirstInvenst($top_rank);
        $firstInvenst = array();
//        print_r($firstInvenst);
        
        $invite_uid = $this->decode_invite($code);
        $this->load->model('logic/login_logic', 'login_logic');
        $account = $this->login_logic->getAccountInfo($invite_uid);
        $phone = $account['account'];
        $data['phone'] = substr($phone, 0, 3) . '****' . substr($phone, -4);
        $data['code'] = $code;
        $data['toprank'] = $firstInvenst;

        if(isset($_SERVER['HTTP_X_CLIENT_PROTO']) && $_SERVER['HTTP_X_CLIENT_PROTO'] == 'https'){
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            header("Location:$url");
        }
        
        $isActive = true;
        if($isActive){
            $data['be_invite_num'] =@ $this->invite_limit_base->get(1) ? $this->invite_limit_base->get(1) : 0;
            $data['invite_num'] = @$this->invite_limit_base->get2(2) ? $this->invite_limit_base->get(2) : 0;
        }

        $this->load->view('activity1111', $data);
    }
    /**
     * 邀请排行榜
     * @return [type] [description]
     */
    public function toprank() {
        $top_rank = $this->invite_logic->top_rank2();
        $firstInvenst = $this->invite_logic->getFirstInvenst2($top_rank);
//        $firstInvenst = array();
        $data = array();
        $data['toprank'] = $firstInvenst;
        $this->load->view('activity1111toprank', $data);
    }
    /**
     * 广告
     * @return [type] [description]
     */
    public function activity_ad(){
        $code = $this->input->request('code');
        
        $from = $this->input->request('from');
        
        $this->config->load('cfg/festivity_cfg', true, true);
        $festivity = $this->config->item('cfg/festivity_cfg');
        
        if(!in_array($from, $festivity['remove'])){
            $response = array('error'=> 3333, 'msg'=>'channel error');
            $this->out_print($response);
        }
        
        if(strpos($code,'null') !== false){
            $this->invalidHtml();
        }
       
        $invite_uid = $this->decode_invite($code);
        $this->load->model('logic/login_logic', 'login_logic');
        $account = $this->login_logic->getAccountInfo($invite_uid);
        $phone = $account['account'];
        $data['phone'] = substr($phone, 0, 3) . '****' . substr($phone, -4);
        $data['code'] = $code;
        $data['from'] = $from;
        
        $this->load->view('invitead', $data);
    }
    /**
     * 广告
     * @return [type] [description]
     */
    public function activity_ad2(){
        $code = $this->input->request('code');
        
        $from = $this->input->request('from');
        echo 4;
        $this->config->load('cfg/festivity_cfg', true, true);
        $festivity = $this->config->item('cfg/festivity_cfg');
        exit('here');
        if(!in_array($from, $festivity['remove'])){
            $response = array('error'=> 3333, 'msg'=>'channel error');
            $this->out_print($response);
        }
        
        if(strpos($code,'null') !== false){
            $this->invalidHtml();
        }
       
        $invite_uid = $this->decode_invite($code);
        $this->load->model('logic/login_logic', 'login_logic');
        $account = $this->login_logic->getAccountInfo($invite_uid);
        $phone = $account['account'];
        $data['phone'] = substr($phone, 0, 3) . '****' . substr($phone, -4);
        $data['code'] = $code;
        $data['from'] = $from;
        
        $this->load->view('invitead', $data);
    }
    
    /**
     * 广告
     * @return [type] [description]
     */
    public function activity_daad(){
        $code = $this->input->request('code');
        
        $from = $this->input->request('from');
        
        $this->config->load('cfg/festivity_cfg', true, true);
        $festivity = $this->config->item('cfg/festivity_cfg');
        
        if(!in_array($from, $festivity['remove'])){
            $response = array('error'=> 3333, 'msg'=>'channel error');
            $this->out_print($response);
        }
        
        if(strpos($code,'null') !== false){
            $this->invalidHtml();
        }
       
        $invite_uid = $this->decode_invite($code);
        $this->load->model('logic/login_logic', 'login_logic');
        $account = $this->login_logic->getAccountInfo($invite_uid);
        $phone = $account['account'];
        $data['phone'] = substr($phone, 0, 3) . '****' . substr($phone, -4);
        $data['code'] = $code;
        $data['from'] = $from;
        
        $this->load->view('inviteaddaad', $data);
    }
    
    /**
     * 活动说明
     * @return [type] [description]
     */
    public function activity_note(){
        $top_rank = $this->invite_logic->top_rank2();
        $firstInvenst = $this->invite_logic->getFirstInvenst2($top_rank);
//        $firstInvenst =array();
//        print_r($firstInvenst);
        $data['list'] = $firstInvenst;
        $this->load->view('activity_note', $data);
    }
    
    /**
     * 活动说明
     * @return [type] [description]
     */
    public function activity_note2(){
        $top_rank = $this->invite_logic->top_rank2();
//        echo count($top_rank);exit;
        $firstInvenst = $this->invite_logic->getFirstInvenst2($top_rank);
//        print_r($firstInvenst);
        $data['list'] = $firstInvenst;
        $this->load->view('activity_note', $data);
    }
    
    /**
     * banner
     * @return [type] [description]
     */
    public function activity_banner(){
        $top_rank = $this->invite_logic->top_rank2();
        $firstInvenst = $this->invite_logic->getFirstInvenst2($top_rank);
//        $firstInvenst = array();
        $data['list'] = $firstInvenst;
        $this->load->view('activity_banner', $data);
    }
    
    /**
     * 查询我邀请的人的交易
     */
    public function queryBeInviteDetail() {
        $account = $this->input->request('account');
        $this->load->model('logic/invite_logic', 'invite_logic');
        $BeInvite = $this->invite_logic->queryBeInvite($account);
        print_r($BeInvite);
    }
    
    private function invalidHtml() {
        echo "<html>";
        echo "<head>";
        echo "<meta charset=\"UTF-8\">";
        echo "<meta name=\"viewport\" content=\"width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;\">";
        echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">";
        echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">";
        echo "<meta name=\"format-detection\" content=\"telephone=no\">";
        echo "</head>";
        echo "<title>邀请链接无效</title>";
        echo "<body>";
        echo "<h3 style=\"text-align:center\">邀请链接无效,请退出重新登陆即可生效</h3>";
        echo "</body>";
        echo "<html>";
        exit;
    }
    
    private function stopHtml() {
        //        echo '<script type="text/javascript"> console.log('. json_encode($_SERVER).');</script>';
        echo "<html>";
        echo "<head>";
        echo "<meta charset=\"UTF-8\">";
        echo "<meta name=\"viewport\" content=\"width=device-width,height=device-height,inital-scale=1.0,maximum-scale=1.0,user-scalable=no;\">";
        echo "<meta name=\"apple-mobile-web-app-capable\" content=\"yes\">";
        echo "<meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black\">";
        echo "<meta name=\"format-detection\" content=\"telephone=no\">";
        echo "</head>";
        echo "<title>暂不能邀请他人注册，谢谢！</title>";
        echo "<body>";
        echo "<h3>暂不能邀请他人注册，谢谢！</h3>";
        echo "</body>";
        echo "<html>";
        exit;
    }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */