<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class longproduct extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/longproduct_logic', 'longproduct_logic');
    }

    public function index(){
        
    }
    
    public function getLongproductDetail(){
        $pid = trim($this->input->post('pid'));
        $longproduct = $this->longproduct_logic->getLongProductDetail($pid);
        if(!$longproduct){
            $response = array('error'=> 2002, 'msg' => '产品不存在');
            $this->out_print($response);
        }
        $users = $this->longproduct_logic->getBuyUserByPid($longproduct['pid']);
        $longproduct['buyUserNumer'] = count($users);
        if(!isset($longproduct['capital_overview'])){
            $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
            $longproductcontract= $this->longproductcontract_base->getContractByCid($longproduct['cid']);
            $longproduct['capital_overview'] = $longproductcontract['capital_overview'];
            $longproduct['object_overview'] = $longproductcontract['object_overview'];
            $this->longproduct_logic->setLongproductCache($longproduct);
        }
        $odate = date('Y-m-d');
        if($odate != $longproduct['odate'] && $longproduct['online_time'] == ''){
            $longproduct['online_time'] = $longproduct['odate'] . ' 01:00';
        }
        if(NOW < mktime(1,0,0) && $longproduct['online_time'] == ''){
            $longproduct['online_time'] = date('Y-m-d') . ' 01:00';
        }
        if($this->uid){
            $this->load->model('base/userlongproduct_base', 'userlongproduct_base');
            $user_longproduct_max = $this->userlongproduct_base->get_user_longproduct_max($this->uid, $pid);
            $longproduct['user_buyed'] = $user_longproduct_max;
        }
        $data['detail'] = $longproduct;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function getLongproductBuyInfo(){
        $pid = trim($this->input->request('pid'));
        $buyUserInfo = $this->longproduct_logic->getBuyUserByPid($pid);
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
   
    public function getLongProductContractObject(){
        $type = trim($this->input->request('type'));
        $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
        $cid = LONGPRODUCT_CID;
        $contract = $this->longproductcontract_base->getContractByCid($cid);
        
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
        $this->load->view('/contractInfo', $data);
    }

    public function getLongProductContractObject1(){
        $type = trim($this->input->request('type'));
        $this->load->model('base/longproductcontract_base', 'longproductcontract_base');
        $cid = LONGPRODUCT_CID;
        $contract = $this->longproductcontract_base->getContractByCid($cid);
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
    
    
	
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */