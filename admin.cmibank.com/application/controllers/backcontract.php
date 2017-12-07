<?php
/**
 * 用户账户信息管理
 * * */
class backcontract extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '回库'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_backcontract_model', 'backcontract');
        $this->load->model('admin_contract_model','contract');
    }
    
    
    public function index(){
        $flag = $this->op->checkUserAuthority('回库',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $aboutustitle = trim($this->input->post('aboutustitle'));
            if($aboutustitle && $aboutustitle != '请输入搜索内容' && $this->input->request('op') == "search_aboutustitle"){
                
            }else{
                $data['list'] = $this->backcontract->getbackcontractList('', 'bid desc', array($psize, $offset));
                $count = $this->backcontract->getbackcontractCount();
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            
        }
        $this->load->view('/backcontract/v_index',$data);
    }
    
    public function addbackcontract(){
        $flag = $this->op->checkUserAuthority('回库',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            if($this->input->request('op') == 'addbackcontract'){

                $pname = trim($this->input->post('pname'));
                $product = $this->product->getProductList(array('pname'=>$pname),'');
                if(empty($product)){
                   exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'产品名错误')));
                }
                $pid = $product[0]['pid'];
                $number = trim($this->input->post('number'));
                $ctime = time();
                
                $data['pname'] = $pname;
                $data['pid'] = $pid;
                $data['number'] = $number;
                $data['ctime'] = $ctime;
                
                $ret = $this->backcontract->addbackcontract($data);
                
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加回库单失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '回库', '', '回库', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加回库单成功', array(), '回库','forward', OP_DOMAIN.'/backcontract'));
            }else{
                $this->load->view('/backcontract/v_addbackcontract',$data);
            }
            
        }
        
    }
    
    public function editbackcontract(){
        $flag = $this->op->checkUserAuthority('回库',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            if($this->input->request('op') == 'saveedit'){
                $bid = $this->input->post('bid');
                
                $pname = trim($this->input->post('pname'));
                $product = $this->product->getProductList(array('pname'=>$pname),'');
                if(empty($product)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'产品名错误')));
                }
                $pid = $product[0]['pid'];
                $number = trim($this->input->post('number'));
                $ctime = time();
                
                $data['pname'] = $pname;
                $data['pid'] = $pid;
                $data['number'] = $number;
                $data['ctime'] = $ctime;
                
                $ret = $this->backcontract->editbackcontract($bid, $data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '回库', '', '修改回库', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改回库', 'forward', OP_DOMAIN.'/backcontract'));
                
            }else{
                $bid = $this->uri->segment(3);
                $ret = $this->backcontract->getbackcontractByBid($bid);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加回库单失败')));
                }
                $data['detail'] = $this->backcontract->getbackcontractByBid($bid);
                $this->load->view('/backcontract/v_editbackcontract',$data);
            }

        }
        
    }
    public function delbackcontract(){
        $flag = $this->op->checkUserAuthority('回库', $this->getSession('uid'));   //检测用户操作权限
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $bid = $this->uri->segment(3);
            $ret = $this->backcontract->delbackcontract($bid);
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '回库', '', '删除回库', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除回库', 'forward', OP_DOMAIN.'/backcontract'));
    }
    public function confirmback(){
        $flag = $this->op->checkUserAuthority('回库', $this->getSession('uid'));   //检测用户操作权限
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'回库');
        }else{
            $pid = $this->uri->segment(3);
            $bid = $this->uri->segment(4);
            
            $product = $this->product->getProductList(array('pid'=>$pid),'');
            $product[0]['cid'];
            $sellmoney = $product[0]['sellmoney'];
            
            $nowtime = date('Y-m-d',time());
            $contract = $this->contract->getContractByCid($product[0]['cid']);
            $repaymenttime = $contract['repaymenttime'];
            $cid = $contract['cid'];
            
            $con_money = $contract['con_money'];
            
            $money = $sellmoney + $con_money;

            $day = floor((strtotime($repaymenttime)-strtotime($nowtime))/86400+1);
            
            if($day>7){
                
               $data1['con_money'] = $money;
               $ret = $this->contract->updatebackcontract($cid, $data1);
               $ret = $this->contract->deContractRedisCache($cid);

               $data2['status'] = 1;
               $ret = $this->backcontract->editbackcontract($bid, $data2);
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '回库成功', array(), '回库成功', 'forward', OP_DOMAIN.'/backcontract'));
            }else{
                $data['status'] = 2;
                $ret = $this->backcontract->editbackcontract($bid, $data);
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '时间不够了哦', array(), '失败了', 'forward', OP_DOMAIN.'/backcontract'));
            }
           



            
        }
    }
}