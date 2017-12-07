<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends Controller {

    private $site_info      = array(
        'site_name' => 'cmibank',
        'copyright' => 'Copyright &copy; 2015 cmibank',
    );
    
    public function __construct(){
        parent::__construct();
        $this->load->model('admin_base_model', 'op');
    }
    
	public function index()
	{
		
	    $data['static_path'] = STATIC_DOMAIN . 'v3/admin/dwz/';
        $this->load->view('public/login_tpl', array('site_info' => $this->site_info));
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
    
    public function logout(){
        $data = array('name', 'status', 'lastLoginTime', 'createTime', 'loginTimes', 'realname', 'uid', 'is_logged_in', 'group', 'group_id');
        $this->unsetCookie($data);
        $this->unsetSession('uid');
        header("Location: ".OP_DOMAIN . 'login', TRUE, 302);
    }
	
}


/* End of file test.php */
/* Location: ./application/controllers/test.php */