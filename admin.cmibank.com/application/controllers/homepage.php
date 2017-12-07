<?php

define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);

include APPPATH . 'libraries/class-excel-xml.inc.php';

/**
 * 后台管理入口
 * Class Enter
 */
class Homepage extends Controller {
    //返回值状态
    const AJ_RET_SUCC       = 200;
    const AJ_RET_FAIL       = 300;
    const AJ_RET_FORB       = 300;
    const AJ_RET_NOLOGIN    = 301;


    // 返回值类型
    const JSON              = 'application/json';
    const HTML              = 'text/html';
    const JAVASCRIPT        = 'text/javascript';
    const JS                = 'text/javascript';
    const TEXT              = 'text/plain';
    const XML               = 'text/xml';

    //cookie 前缀
    const COOKIE_PREFIX     = 'admin_enter_';



    //站内推广管理权限验证用
    private $menu_name = '站内推广管理';
    private $submenu_extensioninfo_name = '推广信息管理';
    private $submenu_extensiontype_name = '推广类型管理';
    private $submenu_extensiondate_name = '推广排期管理';

    //后台网站信息基本配置
    private $site_info      = array(
        'site_name' => 'cmibank',
        'copyright' => 'Copyright &copy; 2016 cmibank',
    );

    //可在新版后台使用的模块
    private $new_admin_arr  = array(
        '资讯管理',
        '站内推广管理',
        'SEO'
    );

    /**
     * 初始化
     */
    public function __construct() {
        
        parent::__construct();
        $this->load->helper('url');
        //当前路由控制器
        $_controller    = $this->router->fetch_class();
        //当前路由方法
        $_function      = $this->router->fetch_method();
        if($_function == 'login' || $_function == 'ajaxlogin'){

        }else{
            if (false == $this->menu) {
                redirect(OP_DOMAIN . '/login', 'location');
            }
        }
        $this->load->model('admin_extension_model', 'Extension');

        $this->load->model('admin_base_model', 'op');
        if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
            $this->site_info['site_name'] = 'cmibank（本地）';
        }else if($_SERVER['SERVER_ADDR'] == '10.9.160.199'){
            $this->site_info['site_name'] = 'cmibank(UC测试)';
        }else{
            $this->site_info['site_name'] = 'cmibank(正式)';
        }
        $config = include APPPATH.'/config/ios3.php';
        $this->ios3list = $config;
    }

    public function xml() {
        $type     = $this->input->get_post('t', true);
        $type     = $type ? (int)$type : 1;
        
        if($type == 1){
            $this->cross_xml();
        }else{
            $this->dwz_xml();
        }
    }

    public function dwz_xml() {
        $filepath = dirname(dirname(dirname(__FILE__))) . '/dwz.frag.xml';
        if (file_exists($filepath)) {
            header('Content-Type: text/xml');
            $s = file_get_contents($filepath);
            echo($s);
            exit;
        }
    }

    public function cross_xml() {
        $filepath = dirname(dirname(dirname(dirname(__FILE__)))) . '/crossdomain.xml';
        
        if (file_exists($filepath)) {
            header('Content-Type: text/xml');
            $s = file_get_contents($filepath);
            echo($s);
            exit;
        }
    }

    /**
     * 管理后台首页
     */
    public function index(){
        $data = array();
        $flag = $this->op->checkUserAuthority($this->submenu_extensioninfo_name, $this->getSession('uid'));
        $data = $this->getDefaultData($flag, array($this->menu_name, $this->submenu_extensioninfo_name));
        
        $data['menu'] = $this->menu;
        $data['IOS3'] = $this->ios3list;
        $data['site_info']        = $this->site_info;
        $data['static_path']      = STATIC_DOMAIN . '/admin/dwz/';
        $data['static_dwz_path']  =  'http://j-ui.com/';
        $data['new_admin_arr']  =  $this->new_admin_arr;
        $this->load->view('index/index', $data);
    }

    public function login(){
        $data['static_path'] = STATIC_DOMAIN . 'v3/admin/dwz/';
        $this->load->view($this->temp_base_path . 'public/login', array('site_info' => $this->site_info));
    }

    public function logout(){
        $data = array('name', 'status', 'lastLoginTime', 'createTime', 'loginTimes', 'realname', 'uid', 'is_logged_in', 'group', 'group_id');
        $this->unsetCookie($data);
        $this->unsetSession('uid');
        redirect(OP_DOMAIN.'v2/enter/login','location');
    }

    public function ajaxlogin(){
        $data = array();
        $name = $this->input->post('name');
        $password = $this->input->post('password');

        $name = strip_tags($name);
        $name = htmlspecialchars($name);
        $name = addslashes($name);

        $password = strip_tags($password);
        $password = htmlspecialchars($password);
        $password = addslashes($password);

        if(false==$name){
            $data['flag'] = '-4';
        }else if(false==$password){
            $data['flag'] = '-5';
        }else{
            $data['flag'] = $this->op->login($name, $password);
        }

        switch($data['flag']){
            case "-1":
                $data['msg'] = '用户名或者密码错误';
                break;
            case "-2":
                $data['msg'] = '用户名或者密码错误';
                break;
            case "-3":
                $data['msg'] = "您输入的账号异常";
                break;
            case "-4":
                $data['msg'] = "请输入您的账号";
                break;
            case "-5":
                $data['msg'] = "请输入您的密码";
                break;
            default:
                if($data['flag']>0){
                    $data['msg'] = "登录成功";
                }else{
                    $data['flag'] = 0;
                    $data['msg'] = "登录失败";
                }

                break;
        }

        echo json_encode($data);
    }
}