<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class product extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/product_logic', 'product_logic');
        $this->check_link();
    }

    
    public function getProductDetail(){
        $pid = trim($this->input->post('pid'));
        $product = $this->product_logic->getProductDetail($pid);
        if(!$product){
            $response = array('error'=> 2002, 'msg' => '产品不存在');
            $this->out_print($response);
        }
        $users = $this->product_logic->getBuyUserByPid($product['pid']);
        $product['buyUserNumer'] = count($users);
        if(!isset($product['capital_overview'])){
            $this->load->model('base/contract_base', 'contract_base');
            $contract = $this->contract_base->getContractByCid($product['cid']);
            $product['capital_overview'] = $contract['capital_overview'];
            $product['object_overview'] = $contract['object_overview'];
            $this->product_logic->setProductCache($product);
        }
        if($product['ptid'] == NEW_USER_PTID){
            $product['activity_url'] = STATIC_DOMAIN.'banner/banner7tian.html';
        }else{
            $product['activity_url'] = STATIC_DOMAIN.'banner/bannerzytztyj.html';
        }
        
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        $uid = $this->uid ? $this->uid : 0;
        if($uid && strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW && $product['ptid'] != NEW_USER_PTID){
            $this->load->model('base/user_identity_base', 'user_identity_base');
            $userIdentity = $this->user_identity_base->getUserIdentity($uid);
            $this->load->model('base/user_base' , 'user_base');
            $account = $this->user_base->getAccountInfo($uid);
            if($userIdentity['isnew'] == 1 && $account['plat'] == 'invite'){
                $product['text_url'] = STATIC_DOMAIN.'banner/banneryqhy_abcdefghgodefg.html';
            }
        }
        
        if($product['online_time'] == ''){
            $odate = date('Y-m-d', strtotime("+1 day"));
            $this->load->model('base/product_base', 'product_base');
            $r_pid = $this->product_base->getOnlineProductListFirstMem($product['ptid'], $odate);
            if($pid == $r_pid){
                $product['online_time'] = $odate . ' 01:00';
            }
        }
        if(strtotime($product['online_time']) < mktime(1,0,0)){
            $product['online_time'] = date('Y-m-d') . ' 01:00';
        }
        
        $data['detail'] = $product;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function getContractObject(){
        $cid = trim($this->input->request('cid'));
        $type = trim($this->input->request('type'));
        $this->load->model('base/contract_base', 'contract_base');
        $contract = $this->contract_base->getContractByCid($cid);
        if(empty($contract)){
            $response = array('error'=> 2003, 'msg' => '不存在的合同信息');
            $this->out_print($response);
        }
        $data = array();
        $data['type']= $type;
        if($type == 'obj'){
            $data['overview'] = $contract['object_overview'];
            $data['desc'] = $contract['object_desc'];
            $data['img'] = $contract['object_img'];
        }else if($type == 'cap'){
            $data['overview'] = $contract['capital_overview'];
            $data['desc'] = $contract['capital_desc'];
            $data['img'] = $contract['capital_img'];
        }else{
            $response = array('error'=> 2004, 'msg' => '错误的合同类型');
            $this->out_print($response);
        }
        $this->load->view('contractInfo', $data);
    }

    public function getContractObject1(){
        $cid = trim($this->input->request('cid'));
        $type = trim($this->input->request('type'));
        $this->load->model('base/contract_base', 'contract_base');
        $contract = $this->contract_base->getContractByCid($cid);
        if(empty($contract)){
            $response = array('error'=> 2003, 'msg' => '不存在的合同信息');
            $this->out_print($response);
        }
        $data = array();
        $data['object_overview'] = $contract['object_overview'];
        $data['object_desc'] = $contract['object_desc'];
        $data['object_img'] = $contract['object_img'];
        $data['capital_overview'] = $contract['capital_overview'];
        $data['capital_desc'] = $contract['capital_desc'];
        $data['capital_img'] = $contract['capital_img'];
        $response = array('error'=> 2003, 'data' => $data);
        $this->out_print($response);
    }
    
    public function getProductBuyInfo(){
        $pid = trim($this->input->request('pid'));
        $buyUserInfo = $this->product_logic->getBuyUserByPid($pid);
        $return_info = array();
        foreach ($buyUserInfo as $_info){
            $new_info = array();
            $new_info['account'] = substr($_info['account'], 0, 3) . '****' . substr($_info['account'], -4);
            $new_info['money'] = $_info['money'];
            $new_info['ctime'] = $_info['ctime'];
            $return_info[] = $new_info;
        }
        $data['list'] = $return_info;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */