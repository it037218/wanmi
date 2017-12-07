<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
require (APPPATH . 'controllers/jytpay.php');
class jyt extends Controller {

    public function __construct($lock = true)
    {
//         $lock = false;
        parent::__construct();
        
    }
    
    
    
    public function getMsgCode(){
//         error_reporting(E_ALL);
        $jytpay = new jytpay(false);
        $jytpay->getMsgCode();
    }
    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */