<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class klproduct extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('logic/klproduct_logic', 'klproduct_logic');
    }

    public function index(){
        
    }
    
    public function getKlproductDetail(){
        $pid = trim($this->input->post('pid'));
        $klproduct = $this->klproduct_logic->getKlProductDetail($pid);
        if(!$klproduct){
            $response = array('error'=> 2002, 'msg' => '产品不存在');
            $this->out_print($response);
        }
        $users = $this->klproduct_logic->getBuyUserByPid($klproduct['pid']);
        $klproduct['buyUserNumer'] = count($users);
        if(!isset($klproduct['capital_overview'])){
            $this->load->model('base/klproductcontract_base', 'klproductcontract_base');
            $klproductcontract= $this->klproductcontract_base->getContractByCid($klproduct['cid']);
            $klproduct['capital_overview'] = $klproductcontract['capital_overview'];
            $klproduct['object_overview'] = $klproductcontract['object_overview'];
            $this->klproduct_logic->setklproductCache($klproduct);
        }
        $odate = date('Y-m-d');
        if($odate != $klproduct['odate'] && $klproduct['online_time'] == ''){
            $klproduct['online_time'] = $klproduct['odate'] . ' 01:00';
        }
        if(NOW < mktime(1,0,0) && $klproduct['online_time'] == ''){
            $klproduct['online_time'] = date('Y-m-d') . ' 01:00';
        }
        $data['detail'] = $klproduct;
        $response = array('error'=> 0, 'data'=> $data);
        $this->out_print($response);
    }
    
    public function getKlproductBuyInfo(){
        $pid = trim($this->input->request('pid'));
        $buyUserInfo = $this->klproduct_logic->getBuyUserByPid($pid);
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
   
    public function getKlProductContractObject(){
        $type = trim($this->input->request('type'));
        $this->load->model('base/klproductcontract_base', 'klproductcontract_base');
        $cid = KLPRODUCT_CID;
        $contract = $this->klproductcontract_base->getContractByCid($cid);
        if(empty($contract)){
            $response = array('error'=> 2003, 'msg' => '不存在的合同信息');
            $this->out_print($response);
        }
        $data = array();
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
    
    
	
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */