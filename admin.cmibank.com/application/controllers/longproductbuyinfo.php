<?php
/**
 * 活期用户购买记录
 * * */
class longproductbuyinfo extends Controller{
    
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
        $this->load->model('admin_userlongproduct_model', 'userlongproduct');
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_longproduct_buy_info_model', 'longproduct_buy_info');
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
                    $rtnlongmoney = 0 ;
                    $longproduct = $this->longproduct->getLongproductlikepname($searchpname,'');
                    foreach ($longproduct as $key=>$val){
                        $longproduct[$key]['count_people'] = count($this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$val['pid']),'ctime desc'));
                        $rtnlongmoney += $val['sellmoney'];
                    }
                    $count = count($this->longproduct->getLongproductlikepname($searchpname,''));
                    $data['list'] = $longproduct;
                    $data['searchpname'] = $searchpname;
                    $data['rtnlongmoney'] = $rtnlongmoney;
                    $data['none'] = 'none';
   
                }else if($searchstart && $searchstart != '请输入开始日期' && $this->input->request('op') == "search"){
                    $longproduct = $this->longproduct->getLongproduct($searchstart,$searchend,'');
                    $rtnlongmoney = 0;
                    foreach ($longproduct as $key=>$val){
                        $longproduct[$key]['count_people'] = count($this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$val['pid']),'ctime desc'));
                        $rtnlongmoney += $val['sellmoney'];
                    }
                    $count = count($this->longproduct->getLongproduct($searchstart, $searchend, ''));
                    $data['list'] = $longproduct;
                    $data['searchstart'] = $searchstart;
                    $data['searchend'] = $searchend;
                    $data['rtnlongmoney'] = $rtnlongmoney;
                    $data['none'] = 'none';
                }else{
                    $longproduct = $this->longproduct->getLongproductList('', 'pid desc',array($psize, $offset));
                    foreach ($longproduct as $key=>$val){
                        $longproduct[$key]['count_people'] = count($this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$val['pid']),'ctime desc'));
                    }
                    $count = count($this->longproduct->getLongproductList('', '', ''));
                    $data['list'] = $longproduct;
                }
                if($count>0){
                
                    $data['pageNum']    = $page;
                    $data['numPerPage'] = $psize;
                    $data['count'] = $count;
                    $data['rel'] = OP_DOMAIN . 'longproductbuyinfo/index?page=' . $page;
                    if(!empty($searchpname)){
                        $data['rel'] .= '&title=' . $searchpname;
                    }
                }else{
                    $data['list'] = $data['page'] = '';
                }
            } 
        $this->load->view('/longproductbuyinfo/v_index',$data);
    }
    
    public function getlongproductbuyinfoBypid(){
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
                $longproduct = $this->longproduct->getLongproductList(array('pname'=>$searchpname), '', '');
                if(empty($longproduct)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                $longprodcutbuyinfo = $this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$longproduct[0]['pid']),'ctime desc');
                foreach ($longprodcutbuyinfo as $key=>$val){
                    $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$val['uid']),'','');
                    $longprodcutbuyinfo[$key]['realname'] = $useridentity[0]['realname'];
                    $longprodcutbuyinfo[$key]['idCard'] = $useridentity[0]['idCard'];
                    $longprodcutbuyinfo[$key]['pname'] = $searchpname;
                }
                $count = count($this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$longproduct[0]['pid']),'ctime desc'));
                $data['list'] = $longprodcutbuyinfo;
                $data['searchpname'] = $searchpname; 
                if(empty($longprodcutbuyinfo)){
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
                   $userlongproduct = $this->userlongproduct->getUserLongProductlistByUid($val['uid'],array('uid'=>$val['uid']),'ctime desc','');
                   $count = count($userlongproduct);
                   foreach ($userlongproduct as $key=>$_val){
                       $userlongproduct[$key]['realname'] = $val['realname'];
                       $userlongproduct[$key]['idCard'] = $val['idCard'];
                       $userlongproduct[$key]['ctime'] = $_val['buytime'];
                       $userlongproduct[$key]['account'] = $val['phone'];
                   }   
                }
                $data['list'] = $userlongproduct;
                $data['searchtitle'] = $searchtitle;
                if(empty($data['list'])){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
                 
            }else{
                $longprodcutbuyinfo = $this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$pid),'ctime desc',array($psize, $offset));
                foreach ($longprodcutbuyinfo as $key=>$val){
                    $useridentity = $this->useridentity->getUseridentityList(array('uid'=>$val['uid']),'','');
                    $longproduct = $this->longproduct->getLongproductList(array('pid' => $pid), '','');
                    $longprodcutbuyinfo[$key]['realname'] = $useridentity[0]['realname'];
                    $longprodcutbuyinfo[$key]['idCard'] = $useridentity[0]['idCard'];
                    $longprodcutbuyinfo[$key]['pname'] = $longproduct[0]['pname'];
                }
                $data['list'] = $longprodcutbuyinfo;
                $data['pid'] = $pid;
                $count = count($this->longproduct_buy_info->getLongProductBuyInfoByPid(array('pid'=>$pid),'ctime desc'));
                
            }
            if($count>0){
                
                    $data['pageNum']    = $page;
                    $data['numPerPage'] = $psize;
                    $data['count'] = $count;
                    $data['rel'] = OP_DOMAIN . 'longproductbuyinfo/index?page=' . $page;
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
            $this->load->view('/longproductbuyinfo/v_buylongproductdetails',$data);
        
        }
    
}