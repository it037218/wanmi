<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
if(!isset($_GET['pwd']) || $_GET['pwd'] != 'lijiayi'){
    exit('滚粗！');
}
class duizhang extends Controller {

    public function __construct()
    {
        // exit;
        parent::__construct();
//         if($_SERVER['SERVER_PORT'] != 443){
//             die('not https request');
//         }
    }
    
    public function duizhang_daifu(){
    
        $file_name = APPPATH . 'third_party/'. date('Ymd', strtotime(' -1 day')) .'_daifu.csv';
        
        if(!file_exists($file_name)){
            die('file not exists');
        }
        $file = fopen($file_name,'r');
        
        $null_order = array();
        
        while ($data = fgetcsv($file)) { //每次读取CSV里面的一行内容
            $orderid = $data[2];
//             print_r($data);
//             exit;
            $this->load->model('base/withdraw_log_base', 'withdraw_log');
            $order_date = substr($orderid, 12,14);
            $year = date('Y', strtotime($order_date));
            $week = date('W', strtotime($order_date));
            $orderInfo = $this->withdraw_log->getLogByOrderId($orderid, $year, $week);
            if(empty($orderInfo)|| $orderInfo['money'] != $data[6]){
                $sub = array();
                $sub['file'] = $data;
                $sub['db'] = $orderInfo;
                $null_order[] = $sub;
            }
            echo $orderInfo['orderid'] . "|" . $orderInfo['money']. "|" . $orderInfo['money'] . "|" . $orderInfo['back_status'] . "<br />";
            echo "<br />";
            
        }
        print_r($null_order);
        fclose($file);
    
    }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */