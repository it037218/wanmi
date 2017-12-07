<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 用户购买产品（资产）信息
 */
class invite extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        $this->load->model('logic/invite_logic', 'invite_logic');
    }
    
    //我邀请的人
    public function my_invite(){
        $data = $this->invite_logic->get_my_invite($this->uid);
        $response = array('error'=> 0, 'data' => $data);
        $this->out_print($response);
    }
    
    //我邀请的交易过的人
    public function my_invite_buy(){
        $data = $this->invite_logic->get_my_invite($this->uid, true);
        $response = array('error'=> 0, 'data' => $data);
        $this->out_print($response);
    }
    
    public function get_user_inviterward(){
        $page = max(1, intval($this->input->post('page')));
        $psize = 20;
        $start = ($page - 1) * $psize;
        $end = $start + $psize - 1;
        $this->load->model('logic/invite_logic', 'invite_logic');
        $data = $this->invite_logic->get_user_inviterward($this->uid, $start, $end);
        $response = array('error'=> 0, 'data' => $data);
        $this->out_print($response);
    }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */