<?php
/**
 * 活期权限管理
 * * */
class longproduct extends Controller{
    
    private $map;
    private $qx;
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '产品发布中心'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->map = array('0' => OP_DOMAIN . '/longproduct/Unpublished', 1 => OP_DOMAIN . '/longproduct');
        $this->qx =array('0' => '未发布活期产品','1' => '已发布活期产品');
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_longproductcontract_model','longproductcontract');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
    }
    
    function Unpublished(){
        $status=0;
        $func_name = '/longproduct/'.__FUNCTION__;
        $this->index($status, $func_name);
    }
    public function autotianchong(){
        $pname = $this->input->post('pname');
        $data = $this->longproduct->autotianchong($pname);
        echo json_encode($data[0]);
        exit;
    }
    public function index($status=1, $func_name = 'longproduct'){  
        $flag = $this->op->checkUserAuthority($this->qx[$status], $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        } else {
            $this->load->model('admin_ltype_model', 'ltype');
            $ltype = $this->ltype->getLtypeList();
            $ltype_list = array();
            foreach ($ltype as $_val){
               $ltype_list[$_val['ptid']] = $_val['name'];
            }
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            if($status=='0'){
                $data['list'] = $this->longproduct->getLongproductList(array('status'=>$status), 'ctime desc',array($psize, $offset));
                $count =count($this->longproduct->getLongproductList(array('status'=>$status), 'ctime desc',''));
                
            }else{
                //$where = array('uid' => array(11,22,33))  
                          //array('status'=>array(2,3,4))
                $data['list'] = $this->longproduct->getLongproductList(array('status'=>array(1,2,3,4,5,6,7)), 'ctime desc', array($psize, $offset));
                $count = $this->longproduct->getLongproductCount();
            }
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['ltype_list'] = $ltype_list;
            $data['func_name'] = $func_name;
            $data['status'] = $status;
            
            $function_name = "1024";
            if($func_name == 'longproduct'){
            	$function_name = "1030";
            }

            $edatable = $this->op->getEditable($this->getSession('uid'),$function_name);
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            
            $log = $this->op->actionData($this->getSession('name'), '活期产品末发布中心', '', '查看', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/longproduct/v_index', $data);
        }
    }
                        
    public function addLongproduct(){
        $flag = $this->op->checkUserAuthority('未发布活期产品', $this->getSession('uid'));   //检测用户操作权限
        if($flag == 0){
             echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '未发活期期产品');
        }else{
             if($this->input->request('op') == 'addlongproduct'){
                 
                $pid = trim($this->input->post('pid'));
                $ptid = trim($this->input->post('ptid'));
                $cid = trim($this->input->post('cid'));
                $pname = trim($this->input->post('pname'));
                $status =trim($this->input->post('status'));
                $income = trim($this->input->post('income'));
                $money = trim($this->input->post('money'));
                $sellmoney = trim($this->input->post('sellmoney'));
                $money_max = trim($this->input->post('money_max'));
                $uptime = trim($this->input->post('uptime'));
                $money_limit = trim($this->input->post('money_limit'));
                $startmoney = trim($this->input->post('startmoney'));
                $canbuyuser = trim($this->input->post('canbuyuser'));
                $online_time = trim($this->input->post('online_time'));
                $ctime = time();
                
                $cancm = trim($this->input->post('cancm'));
                $operation_tag = trim($this->input->post('operation_tag'));
                $standard_tag = trim($this->input->post('standard_tag'));
                $standard_text = trim($this->input->post('standard_text'));
                $standard_icon = trim($this->input->post('standard_icon'));
                $text_text = trim($this->input->post('text_text'));
                $text_url = trim($this->input->post('text_url'));
                
                if($money_max<=0){
                    $money_max = $money;
                }
                $data['pid']=$pid;
                $data['ptid']=$ptid;
                $data['cid']=$cid;
                $ret = $this->longproduct->getLongproductList(array('pname'=>$pname),'','');
                if(!empty($ret)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'产品名字已经存在了')));
                }
                $data['pname']=$pname;
                $data['status']=$status;
                $data['income']=$income;
                $data['money']=$money;
                $data['sellmoney']=$sellmoney;
                $data['money_max']=$money_max;
                $data['uptime']=$uptime;
                $data['money_limit']=$money_limit;
                $data['startmoney']=$startmoney;
                $data['canbuyuser']=$canbuyuser;
                $data['online_time']=$online_time;
                $data['ctime']=$ctime;
                $data['cancm']=$cancm;
                $data['operation_tag'] = $operation_tag;
                $data['standard_tag'] = $standard_tag;
                $data['standard_text'] = $standard_text;
                $data['text_text'] = $text_text;
                $data['text_url'] = $text_url;
                $data['standard_icon'] = $standard_icon;
                
                if(empty($ptid)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请选项目')));
                }
                if(empty($cid)){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'请选择活期合同')));
                }
                
                $ret = $this->longproduct->addLongporduct($data);
                
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'上传活期产品信息失败!')));
                }
                $log = $this->op->actionData($this->getSession('name'), '未发活期期产品', '', '上传活期产品信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '上传活期产品信息信息成功', array(), '上传产品信息 ', 'forward', OP_DOMAIN.'/longproduct/Unpublished'));
                
            }else{
                $this->load->model('admin_ltype_model','ltype');
                $ltype = $this->ltype->getLtypeList();
                $longproductcontract=$this->longproductcontract->getLongproductcontractList('','','');
                $ltype_list=array();
                $longproductcontract_list=array();
                foreach ($ltype as $_val){
                    $ltype_list[$_val['ptid']]=$_val['name'];
                }
                foreach ($longproductcontract as $_val){
                    $longproductcontract_list[$_val['cid']]=$_val['corname'];
                }
                $data['ltype_list']=$ltype_list;
                $data['longproductcontract_list']=$longproductcontract_list;
                $this->load->view('/longproduct/v_addLongProduct',$data);
            }
            
            
        }
    }
    
    public function uptoline(){
        $flag = $this->op->checkUserAuthority('未发布活期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '产品上架');
        } else {
            if($this->input->request('op') == 'online'){
                $ptid = $this->input->request('ptid');
                $pid = $this->input->request('pid');
                $odate = $this->input->request('odate');
                $time = time();
                $detail = $this->longproduct->getLongproductByPid($pid);
                if(empty($detail)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'产品不存在')));
                }
                $stype = 0;
                if($odate == 1){
                    $odate = date("Y-m-d");
                } else {
                    $odate = date('Y-m-d',strtotime("+1 day"));
                }
                //上线和预告时间if($detail['online_time'] && $detail['yugaotime']){
                if($detail['online_time']){
                    //添加到预告队列中
                    $stype = 1;
                    $maxRindex = 0;
                }else{
                    $maxRindex = $this->ltype_longproduct->getMaxRindxByPtid($ptid, $odate);
                    $maxRindex++;
                }
                $ltype_longproduct_info = array();
                $ltype_longproduct_info['ptid'] = $ptid;
                $ltype_longproduct_info['pid'] = $pid;
                $ltype_longproduct_info['rindex'] = $maxRindex;
                $ltype_longproduct_info['odate'] = $odate;
                $ltype_longproduct_info['stype'] = $stype;
                $ret = $this->ltype_longproduct->addLtypeLongproduct($ltype_longproduct_info);
                $update_data = array('status' => 1, 'uptime' => $time,'odate'=>$odate);
                $ret = $this->longproduct->updateLongproduct($pid, $update_data);
                $ret = $this->longproduct->_flushlongproductDetailRedisCache($pid);
                if($ret){
                    $log = $this->op->actionData($this->getSession('name'), '未发布活期产品', '', '产品上架', $this->getIP(), $this->getSession('uid'));
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '添加成功', array(), '产品上架 ', 'no', OP_DOMAIN.'/longproduct'));
                }
            }else{
                $pid = $this->uri->segment(3);
                $ptid = $this->uri->segment(4);
                $data['pid'] = $pid;
                $data['ptid'] = $ptid;
                $this->load->view('/longproduct/v_uptoline', $data);
            }
        }
    }
    
    public function editLongproduct(){
        $flag = $this->op->checkUserAuthority('未发布活期产品', $this->getSession('uid'));   //检测用户操作权限
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '产品上架');
        }else{
             if($this->input->request('op') == 'editLongproduct'){
                $pid = trim($this->input->post('pid'));
                if(!$pid){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $ptid = trim($this->input->post('ptid'));
                $cid = trim($this->input->post('cid'));
                $pname = trim($this->input->post('pname'));
                $income = trim($this->input->post('income'));
                $money = trim($this->input->post('money'));
                $sellmoney = trim($this->input->post('sellmoney'));
                $money_max = trim($this->input->post('money_max'));
                $uptime = trim($this->input->post('uptime'));
                $money_limit = trim($this->input->post('money_limit'));
                $startmoney = trim($this->input->post('startmoney'));
                $canbuyuser = trim($this->input->post('canbuyuser'));
                $online_time = trim($this->input->post('online_time'));
                $ctime = time();
                $cancm = trim($this->input->post('cancm'));
                
                $operation_tag = trim($this->input->post('operation_tag'));
                $standard_tag = trim($this->input->post('standard_tag'));
                $standard_text = trim($this->input->post('standard_text'));
                $standard_icon = trim($this->input->post('standard_icon'));
                $text_text = trim($this->input->post('text_text'));
                $text_url = trim($this->input->post('text_url'));
                
                $data['pid']=$pid;
                $data['ptid']=$ptid;
                $data['cid']=$cid;
                $data['pname']=$pname;
                $data['income']=$income;
                $data['money']=$money;
                $data['sellmoney']=$sellmoney;
                $data['money_max']=$money_max;
                $data['uptime']=$uptime;
                $data['money_limit']=$money_limit;
                $data['startmoney']=$startmoney;
                $data['canbuyuser']=$canbuyuser;
                $data['online_time']=$online_time;
                $data['ctime']=$ctime;
                $data['cancm']=$cancm;
                $data['operation_tag'] = $operation_tag;

                $data['standard_tag'] = $standard_tag;
                $data['standard_text'] = $standard_text;
                $data['text_text'] = $text_text;
                $data['text_url'] = $text_url;
                $data['standard_icon'] = $standard_icon;
                
                $ret = $this->longproduct->updateLongproduct($pid,$data);
                $ltype_longproduct = $this->ltype_longproduct->getLtypeLongproduct(array('pid'=>$pid));
                if(!empty($ltype_longproduct)){
                    $ret = $this->ltype_longproduct->rebuildLtypeLongproductListRedisCache($ptid,$ltype_longproduct['odate']);
                    if(!$ret){
                        exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                    }
                }
                
                $log = $this->op->actionData($this->getSession('name'), '未发活期期产品', '', '修改产品信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward', OP_DOMAIN.'/longproduct/Unpublished'));
                
            }else{
                $pid = $this->uri->segment(3);
                if($pid < 0 || !is_numeric($pid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                
                $this->load->model('admin_ltype_model','ltype'); 
                $ltype = $this->ltype->getLtypeList();
                $longproductcontract=$this->longproductcontract->getLongproductcontractList('','','');
                $ltype_list=array();
                $longproductcontract_list=array();
                foreach ($ltype as $_val){
                    $ltype_list[$_val['ptid']]=$_val['name'];
                }
                foreach ($longproductcontract as $_val){
                    $longproductcontract_list[$_val['cid']]=$_val['corname'];
                }
                $data['ltype_list']=$ltype_list;
                $data['longproductcontract_list']=$longproductcontract_list;
                $data['detail'] = $this->longproduct->getLongproductByPid($pid);
                $this->load->view('/longproduct/v_editLongProduct',$data);
            }
        }
    }
    public function detail(){                                                                                                                  
        $flag = $this->op->checkUserAuthority('未发布活期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '编辑产品');
        } else {
            
            $this->load->model('admin_ltype_model','ltype');
            $ltype = $this->ltype->getLtypeList();
            $longproductcontract=$this->longproductcontract->getLongproductcontractList('','','');
            $ltype_list=array();
            $longproductcontract_list=array();
            foreach ($ltype as $_val){
                $ltype_list[$_val['ptid']]=$_val['name'];
            }
            foreach ($longproductcontract as $_val){
                $longproductcontract_list[$_val['cid']]=$_val['corname'];
            }
            $data['ltype_list']=$ltype_list;
            $data['longproductcontract_list']=$longproductcontract_list;
            
            $pid = $this->input->get('pid');
            $data['detail'] = $this->longproduct->getLongproductByPid($pid);

        }
        $this->load->view('/longproduct/v_detail',$data);
    }
    public function delLongproduct($pid=''){
        $flag = $this->op->checkUserAuthority('未发布活期产品', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '删除未发布活期产品');
            exit;
        } else {
            $longproduct = $this->longproduct->getLongproductDetail($pid);
            //从缓存中去掉
            $this->longproduct->moveYugaoLongProduct($longproduct['ptid'], $pid);
            $ret = $this->longproduct->delLongproduct($pid);
            
            $typelongproductinfo = $this->ltype_longproduct->getTypeLongProductByPid($pid);
            if(!empty($typelongproductinfo)){
                $ret = $this->ltype_longproduct->delLtypeLongProduct($pid);
            }
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
            $log = $this->op->actionData($this->getSession('name'), '产品未发布中心', '', '删除未发活期期产品', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除未发布活期产品', 'forward', OP_DOMAIN.'/longproduct/Unpublished'));
        }
        
    }
    
    
}