<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class activity_luckmoney extends Controller {

    private $no_rob_txt;
    
    private $no_rob_num;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/activity_luckmoney_logic', 'activity_luckmoney_logic');
    }
    
    public function qiaodao(){

        
    }
    
}
