<?php
/**
 * 活期用户购买记录
 * * */
class klproductbuyinfo extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '活期产品购买取现'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_userklproduct_model', 'userklproduct');
        $this->load->model('admin_klproduct_model', 'klproduct');
        $this->load->model('admin_klproduct_buy_info_model', 'klproduct_buy_info');
        $this->load->model('admin_useridentity_model','useridentity');
        $this->load->model('admin_pay_log_model','pay_log');
        $this->load->model('admin_buy_log_model','buy_log');
    }
   
    public function index(){
        $flag = $this->op->checkUserAuthority('活期产品购买记录', $this->getSession('uid'));   //检测用户操作权限
            if ($flag == 0) {
                echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
            }else{
                $page = max(1, intval($this->input->request('pageNum')));
                $psize = max(20, intval($this->input->request('numPerPage')));
                $data = array();
                $offset = ($page - 1) * $psize;
                $asc = htmlspecialchars($this->input->request('asc'));
                $orderby = htmlspecialchars($this->input->request("orderby"));
                $searchpname = trim($this->input->post('searchpname'));
                $searchstart = trim($this->input->post('searchstart'));
                $searchend = trim($this->input->post('searchend'));
                if($searchpname && $searchpname != '请输入搜索内容' && $this->input->request('op') == "search"){
                    $rtnklmoney = 0 ;
                    $klproduct = $this->klproduct->getKlproductlikepname($searchpname,'');
                    foreach ($klproduct as $key=>$val){
                        $klproduct[$key]['count_people'] = count($this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$val['pid']),''));
                        $rtnklmoney += $val['sellmoney'];
                    }
                    $count = count($this->klproduct->getKlproductlikepname($searchpname,''));
                    $data['list'] = $klproduct;
                    $data['searchpname'] = $searchpname;
                    $data['rtnklmoney'] = $rtnklmoney;
                    $data['none'] = 'none';
   
                }else if($searchstart && $searchstart != '请输入开始日期' && $this->input->request('op') == "search"){
                    $klproduct = $this->klproduct->getKlproduct($searchstart,$searchend,'');
                    $rtnklmoney = 0;
                    foreach ($klproduct as $key=>$val){
                        $klproduct[$key]['count_people'] = count($this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$val['pid']),''));
                        $rtnklmoney += $val['sellmoney'];
                    }
                    $count = count($this->klproduct->getKlproduct($searchstart, $searchend, ''));
                    $data['list'] = $klproduct;
                    $data['searchstart'] = $searchstart;
                    $data['searchend'] = $searchend;
                    $data['rtnklmoney'] = $rtnklmoney;
                    $data['none'] = 'none';
                }else{
                    $klproduct = $this->klproduct->getKlproductList('', 'pid desc',array($psize, $offset));
                    foreach ($klproduct as $key=>$val){
                        $klproduct[$key]['count_people'] = count($this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$val['pid']),''));
                    }
                    $count = count($this->klproduct->getKlproductList('', '', ''));
                    $data['list'] = $klproduct;
                }
                if($count>0){
                
                    $data['pageNum']    = $page;
                    $data['numPerPage'] = $psize;
                    $data['count'] = $count;
                    $data['rel'] = OP_DOMAIN . 'klproductbuyinfo/index?page=' . $page;
                    if(!empty($searchpname)){
                        $data['rel'] .= '&title=' . $searchpname;
                    }
                }else{
                    $data['list'] = $data['page'] = '';
                }
            } 
        $this->load->view('/klproductbuyinfo/v_index',$data);
    }
    
    public function getklproductbuyinfoBypid(){
        $flag = $this->op->checkUserAuthority('活期产品购买记录', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '权限管理');
        }else{
            $pid = $this->uri->segment(3);
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $searchpname = trim($this->input->post('searchpname'));
            $searchtype = trim($this->input->post('searchtype'));
            $searchtitle = trim($this->input->post('searchtitle'));
            $searchtrxId = trim($this->input->post('searchtrxId'));
            if($searchpname && $searchpname != '请输入产品名称' && $this->input->request('op') == "search"){
                $klproduct = $this->klproduct->getKlproductList(array('pname'=>$searchpname), '', '');
                if(empty($klproduct)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                $klprodcutbuyinfo = $this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$klproduct[0]['pid']),'');
                foreach ($klprodcutbuyinfo as $key=>$val){
                    $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$val['uid']),'','');
                    $klprodcutbuyinfo[$key]['realname'] = $useridentity[0]['realname'];
                    $klprodcutbuyinfo[$key]['idCard'] = $useridentity[0]['idCard'];
                    $klprodcutbuyinfo[$key]['pname'] = $searchpname;
                }
                $count = count($this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$klproduct[0]['pid']),''));
                $data['list'] = $klprodcutbuyinfo;
                $data['searchpname'] = $searchpname; 
                if(empty($klprodcutbuyinfo)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
            }else if($searchtrxId && $searchtrxId != '请输入订单号' && $this->input->request('op') == "search"){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'本功能暂无')));
//                 $buy_log = $this->buy_log->getBuyLog($searchtrxId);
//                 if(empty($buy_log)){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'产品不存在')));
//                 }
//                 $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$buy_log[0]['uid']),'','');
//                 $buy_log[0]['realname'] = $useridentity[0]['realname'];
//                 $buy_log[0]['idCard'] = $useridentity[0]['idCard'];
//                 $buy_log[0]['money'] = $buy_log[0]['amt'];
//                 $buy_log[0]['account'] = $useridentity[0]['phone'];
//                 $buy_log[0]['trxId'] = $buy_log[0]['ordid'];
//                 $data['list'] = $buy_log;
//                 $data['searchtrxId'] = $searchtrxId;
//                 $count = count($this->buy_log->getBuyLog($searchtrxId));
                    
            }else if($searchtitle && $searchtitle != '请输入搜索内容' && $this->input->request('op') == "search"){
                $useridentity = $this->useridentity->getUseridentityListByLike($searchtitle,$searchtype, array($psize, $offset));
                if(empty($useridentity)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                $rtn = array();   
                foreach ($useridentity as $key=>$val){
                   $userklproduct = $this->userklproduct->getUserKlProductlistByUid($val['uid'],array('uid'=>$val['uid']),'','');
                   $count = count($userklproduct);
                   foreach ($userklproduct as $key=>$_val){
                       $userklproduct[$key]['realname'] = $val['realname'];
                       $userklproduct[$key]['idCard'] = $val['idCard'];
                       $userklproduct[$key]['ctime'] = $_val['buytime'];
                       $userklproduct[$key]['account'] = $val['phone'];
                   }   
                }
                $data['list'] = $userklproduct;
                $data['searchtitle'] = $searchtitle;
                if(empty($data['list'])){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                 
            }else{
                $klprodcutbuyinfo = $this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$pid),'',array($psize, $offset));
                foreach ($klprodcutbuyinfo as $key=>$val){
                    $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$val['uid']),'','');
                    $klproduct = $this->klproduct->getKlproductList(array('pid' => $pid), '','');
                    $klprodcutbuyinfo[$key]['realname'] = $useridentity[0]['realname'];
                    $klprodcutbuyinfo[$key]['idCard'] = $useridentity[0]['idCard'];
                    $klprodcutbuyinfo[$key]['pname'] = $klproduct[0]['pname'];
                }
                $data['list'] = $klprodcutbuyinfo;
                $data['pid'] = $pid;
                $count = count($this->klproduct_buy_info->getKlProductBuyInfoByPid(array('pid'=>$pid),''));
                
            }
            if($count>0){
                
                    $data['pageNum']    = $page;
                    $data['numPerPage'] = $psize;
                    $data['count'] = $count;
                    $data['rel'] = OP_DOMAIN . 'klproductbuyinfo/index?page=' . $page;
                    if(!empty($searchpname)){
                        $data['rel'] .= '&title=' . $searchpname;
                    }
                }else{
                    $data['pageNum']    = 0;
                    $data['numPerPage'] = 0;
                    $data['count'] = 0;
                    $data['list'] = $data['page'] = '';
                }
           
            }  
            $this->load->view('/klproductbuyinfo/v_buyklproductdetails',$data);
        
        }
    
}