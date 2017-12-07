<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class recommend extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/recommend_logic', 'recommend_logic');
        
    }

    
    public function index(){
//         $Banner_list = $this->banner_base->getBannerList();
//         $data['banner'] = $Banner_list;
        //精品推荐
        $stoptime = mktime(23,40,0);
        $odate = date('Y-m-d');
        if(NOW > $stoptime){
            $odate = date('Y-m-d', strtotime("+1 day"));
        }
        $uid = $this->uid ? $this->uid : false;
        $competitiveProductInfo = $this->recommend_logic->getrecommend($odate, $uid);
//         print_r($competitiveProductInfo);
//         exit;
        if(!$competitiveProductInfo){
            $odate = date('Y-m-d', strtotime("+1 day"));
            $competitiveProductInfo = $this->recommend_logic->getrecommend($odate);
        }
        $data['recommend'] = $competitiveProductInfo;
        $response = array('error'=> 0, 'data' => $data);
        $this->out_print($response);
    }
    
    
    public function banner(){
        $this->load->model('base/banner_base', 'banner_base');
        $Banner_list = $this->banner_base->getBannerList();
        foreach ($Banner_list as $key => &$_banner){
            if($key == 4 || $key == 5){
                $_banner['isshenghe'] = 1;
            }
        }
        $data['banner'] = $Banner_list;
        $response = array('error'=> 0, 'data' => $data);
        $this->out_print($response);
    }
    
//     推荐列表
//     public function index(){
//         die('接口已禁用');
//         $recommend_list = $this->recommend_logic->getrecommend();
//         if(is_array($recommend_list['recommend_list']) && count($recommend_list['recommend_list']) > 0) {
//             $response = array('error'=> 0, 'data'=> $recommend_list);
//             $this->out_print($response);
//         }else{
//             $response = array('error'=> 3001, 'msg' => '系统错误');
//             $this->out_print($response);
//         }
//     }
    

    
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */