<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class cd extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/cd_logic', 'cd_logic');
        $this->check_login();
    }

    public function getUserCd(){
        $data = $this->cd_logic->getUserCd($this->uid);
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    

    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */