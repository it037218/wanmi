<?php
/**
 * 快乐宝合同模板
 * * */
class klproductcontract extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '电子合同系统') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_klproductcontract_model', 'klproductcontract');
    }
    
    public function index(){
        $flag = $this->op->checkUserAuthority('快乐宝合同模板',$this->getSession('uid'));
        if($flag==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'快乐宝合同模板');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $data['list'] = $this->klproductcontract->getklproductcontractList('', 'ctime desc', array($psize, $offset));
            $count = $this->klproductcontract->getklproductcontractCount();
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['rel'] = OP_DOMAIN . 'klproductcontract/index?page=' . $page;
            $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '合同管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/klproductcontract/v_index', $data);
        }
    }
    public  function editklproductcontract($cid=''){
        $flag = $this->op->checkUserAuthority('快乐宝合同模板', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑快乐宝合同模板');
        }else{
            if($this->input->request('op') =='editKlproductcontract'){
                $cid = trim($this->input->post('cid'));
                if(!$cid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $corname = trim($this->input->post('corname'));
                $object_overview = trim($this->input->post('object_overview'));
                $object_img = trim($this->input->post('object_img'));
                $capital_overview = trim($this->input->post('capital_overview'));
                $capital_img = trim($this->input->post('capital_img'));
                $income = trim($this->input->post('income'));
                $capital_desc = trim($this->input->post('capital_desc'));
                $object_desc = trim($this->input->post('object_desc'));
                
                $data['capital_desc'] = $capital_desc;
                $data['object_desc'] = $object_desc;
                $data['income'] = $income;
                $data['corname'] = $corname;
                $data['object_overview'] = $object_overview;
                $data['object_img'] = $object_img;
                $data['capital_overview'] = $capital_overview;
                $data['capital_img'] = $capital_img;
                
                $ret=$this->klproductcontract->updateklproductcontract($cid,$data);
                //var_dump($ret);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '修改快乐宝合同信息', '', '修改合同信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改合同信息成功', array(), '修改合同信息 ', 'forward', OP_DOMAIN.'/klproductcontract'));
            }else{
                if($cid < 0 || !is_numeric($cid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail']=$this->klproductcontract->getklproductcontractByCid($cid);
                $this->load->view('/klproductcontract/v_editklproductcontract', $data);
            }
        }
    }
    
    public function getKlproductcontractByCid($cid){
        $data = $this->klproductcontract->getklproductcontractByCid($cid);
        echo json_encode($data);
        exit;
    }
    
    public function rebuildBannerListRedisCache(){

        $ret = $this->klproductcontract->rebuildBannerListRedisCache();
        echo "---";
    }
    
    

   
    
}