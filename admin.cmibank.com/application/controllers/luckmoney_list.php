<?php
/**
 * aboutus管理
 * * */
class luckmoney_list extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '节假日红包'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_luckmoney_list_model', 'luckmoney_list');
        $this->load->model('admin_luckmoney_log_model', 'luckmoney_log');
        $this->load->model('admin_useridentity_model','useridentity');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加红包');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            
            if($this->input->request('op') == "search"){
                
            }else{
                $luckmoney_list = $this->luckmoney_list->getLuckMoneyList(array('etime =' => 0,'lstime >' => time())," yugaotime asc",array($psize, $offset));
                
                $data['list'] = $luckmoney_list;
                $count = count($this->luckmoney_list->getLuckMoneyList(array('etime =' => 0,'lstime >' => time())," yugaotime asc",''));
                if($count>0){
                    $data['pageNum']    = $page;
                    $data['numPerPage'] = $psize;
                    $data['count'] = $count;
                    $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                    
                }else{
                    $data['list'] = '';
                    $data['pageNum'] = $data['page'] = $data['numPerPage'] = $data['count'] = 0;
                }
                $edatable = $this->op->getEditable($this->getSession('uid'),'1130');
                if(!empty($edatable)){
                	$data['editable'] = $edatable[0]['editable'];
                }else{
                	$data['editable']=0;
                }
                $this->load->view('/luckmoney/v_index',$data);
            }
        }
        
     
    }
    public function addluckmoney(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if ($flag == 0) {
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加红包');
        }else{
            $data = array();
            if($this->input->request('op') == 'addluckmoney'){
                $lname = trim(($this->input->post("lname")));
                $lmoney = trim($this->input->post("lmoney"));
                $ltarget = trim($this->input->post("ltarget"));
                $lweight1_money = trim($this->input->post("lweight1_money_1"))."-".trim($this->input->post("lweight1_money_2"));
                $lweight2_money = trim($this->input->post("lweight2_money_1"))."-".trim($this->input->post("lweight2_money_2"));
                $lweight3_money = trim($this->input->post("lweight3_money_1"))."-".trim($this->input->post("lweight3_money_2"));
                $lproportion1 = trim($this->input->post("lproportion1"));
                $lproportion2 = trim($this->input->post("lproportion2"));
                $lproportion3 = trim($this->input->post("lproportion3"));
                if($lmoney > 5000){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'超过最大限额')));
                }
                if($lproportion1+$lproportion2+$lproportion3 != 100){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'三个红包占比相加不等于100%')));
                }
                
                $yugaotime = trim($this->input->post("yugaotime"));
                $lstime = trim($this->input->post("lstime"));
                
                if($yugaotime>$lstime){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'预告时间不能大于上线时间')));
                }
                
                $yg_m = substr($yugaotime,5,2);
                $ls_m = substr($lstime,5,2);
                $yg_d = substr($yugaotime,8,2);
                $ls_d = substr($lstime,8,2);
                
                if($yg_m != $ls_m or $yg_d != $ls_d){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'预告时间和上线时间不能跨天')));
                }
                
                $delaytime = trim($this->input->post("delaytime"));
                $ltoweight = trim($this->input->post("ltoweight"));
                $ltoweightdown = trim($this->input->post("ltoweightdown"));
                $ltext = trim($this->input->post("ltext"));
                $bless_text = trim($this->input->post("bless_text"));
                $nobless_text = trim($this->input->post("nobless_text"));
                
                $data['lname'] = $lname;
                $data['lmoney'] = $lmoney;
                $data['ltarget'] = $ltarget;
                $data['lweight1_money'] = $lweight1_money;
                $data['lweight2_money'] = $lweight2_money;
                $data['lweight3_money'] = $lweight3_money;
                $data['lproportion1'] = $lproportion1;
                $data['lproportion2'] = $lproportion2;
                $data['lproportion3'] = $lproportion3;
                $data['yugaotime'] = strtotime($yugaotime);
                $data['lstime'] = strtotime($lstime);
                $data['delaytime'] = $delaytime;
                $data['ltoweight'] = $ltoweight;
                $data['ltoweightdown'] = $ltoweightdown;
                $data['ltext'] = $ltext;
                $data['bless_text'] = $bless_text;
                $data['nobless_text'] = $nobless_text;
  
                $this->luckmoney_list->addluckmoney($data);
                $log = $this->op->actionData($this->getSession('name'), '节假日红包', '', '添加红包信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加红包成功', array(), '添加红包', 'forward', OP_DOMAIN . '/luckmoney_list'));
                
            }else{
                
                $this->load->view('/luckmoney/v_addluckmoney');
            }
        }
        
        
    }
    public function editLuckmoney(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if ($flag == 0) {
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑红包');
        }else{
            $data = array();
            if($this->input->request('op') == 'edit'){
                
                $lmid = trim($this->input->post("lmid"));
                $lname = trim(($this->input->post("lname")));
                $lmoney = trim($this->input->post("lmoney"));
                $ltarget = trim($this->input->post("ltarget"));
                $lweight1_money = trim($this->input->post("lweight1_money_1"))."-".trim($this->input->post("lweight1_money_2"));
                $lweight2_money = trim($this->input->post("lweight2_money_1"))."-".trim($this->input->post("lweight2_money_2"));
                $lweight3_money = trim($this->input->post("lweight3_money_1"))."-".trim($this->input->post("lweight3_money_2"));
                $lproportion1 = trim($this->input->post("lproportion1"));
                $lproportion2 = trim($this->input->post("lproportion2"));
                $lproportion3 = trim($this->input->post("lproportion3"));
                
                if($lproportion1+$lproportion2+$lproportion3 != 100){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'三个红包占比相加不等于100%')));
                }
                
                $yugaotime = trim($this->input->post("yugaotime"));
                $lstime = trim($this->input->post("lstime"));
                
                if($yugaotime>$lstime){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'预告时间不能大于上线时间')));
                }
                
                $yg_m = substr($yugaotime,5,2);
                $ls_m = substr($lstime,5,2);
                $yg_d = substr($yugaotime,8,2);
                $ls_d = substr($lstime,8,2);
                
                if($yg_m != $ls_m or $yg_d != $ls_d){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'预告时间和上线时间不能跨天')));
                }
                
                $delaytime = trim($this->input->post("delaytime"));
                $ltoweight = trim($this->input->post("ltoweight"));
                $ltoweightdown = trim($this->input->post("ltoweightdown"));
                $ltext = trim($this->input->post("ltext"));
                $bless_text = trim($this->input->post("bless_text"));
                $nobless_text = trim($this->input->post("nobless_text"));
                
                $data['status'] = 0;
                $data['lname'] = $lname;
                $data['lmoney'] = $lmoney;
                $data['ltarget'] = $ltarget;
                $data['lweight1_money'] = $lweight1_money;
                $data['lweight2_money'] = $lweight2_money;
                $data['lweight3_money'] = $lweight3_money;
                $data['lproportion1'] = $lproportion1;
                $data['lproportion2'] = $lproportion2;
                $data['lproportion3'] = $lproportion3;
                $data['yugaotime'] = strtotime($yugaotime);
                $data['lstime'] = strtotime($lstime);
                $data['delaytime'] = $delaytime;
                $data['ltoweight'] = $ltoweight;
                $data['ltoweightdown'] = $ltoweightdown;
                $data['ltext'] = $ltext;
                $data['bless_text'] = $bless_text;
                $data['nobless_text'] = $nobless_text;
                
                $this->luckmoney_list->updateLuckMoneyList($lmid, $data);
                $this->luckmoney_list->delRedis($lmid,strtotime($yugaotime));
                $log = $this->op->actionData($this->getSession('name'), '节假日红包', '', '修改红包信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward',OP_DOMAIN.'/luckmoney_list'));
                
            }else{
                $lmid = $this->uri->segment(3);
                $data['detail'] = $this->luckmoney_list->getLuckMoneyByLmid($lmid);
                $this->load->view('/luckmoney/v_editluckmoney',$data);
            }
        }
    }
    public function uptoline(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加红包');
        }else{
            $data = array();
            $lmid = $this->uri->segment(3);
            $data = array('status'=>'1');
            $luckmoney_data = $this->luckmoney_list->getLuckMoneyByLmid($lmid);
            $yugaotime = $luckmoney_data['yugaotime'];
            
            $this->luckmoney_list->addLuckMoneyRedisList($lmid, $yugaotime);
            $this->luckmoney_list->updateLuckMoneyList($lmid, $data);
            
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布红包', 'forward', OP_DOMAIN.'/luckmoney_list'));
        }
    }
    public function seehandle(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加红包');
        }else{
            $lmid = $this->uri->segment(3);
            $data['etime'] = time();
            $this->luckmoney_list->updateLuckMoneyList($lmid,$data);
            $this->luckmoney_list->downtolineRedis($lmid);
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '红包下线', array(), '红包中心', 'forward', OP_DOMAIN.'/luckmoney_list/detailLuckMoney'));
        }
    }
    public function downtoline(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加红包');
        }else{
            $lmid = $this->uri->segment(3);
            $data['etime'] = time();
            $data['delaytime'] = 0;
            $this->luckmoney_list->updateLuckMoneyList($lmid,$data);
            $this->luckmoney_list->downtolineRedis($lmid);
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '红包下线', array(), '红包中心', 'forward', OP_DOMAIN.'/luckmoney_list/detailLuckMoney'));
        }
    }
    public function detailLuckMoney(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if($flag == 0){
             echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户红包明细');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $data = array();
            $lname = trim($this->input->post('searchlname'));
            if($this->input->request('op') == 'search'){
                $luckmoneyEnd = $this->luckmoney_list->getLuckMoneyLikeLname($lname,array($psize, $offset));
                $data['list'] = $luckmoneyEnd;
                $count = count($this->luckmoney_list->getLuckMoneyLikeLname($lname,''));
            }else{
                $rtnLuckMoneyEnd = array();
                $luckmoneyEnd = $this->luckmoney_list->getLuckMoneyEnd(array($psize, $offset));               
                $data['list'] = $luckmoneyEnd;
                $count = count($this->luckmoney_list->getLuckMoneyEnd());
               
            }
            $money = array();
            $peoples = array();
            $dlmid = $this->luckmoney_list->getLuckMoneyRedisList(NOW);//当前能下线的红包id
            $JoinPeoples = array();
            foreach ($luckmoneyEnd as $key=>$val){
                $JoinPeoples[$val['lmid']] = $this->luckmoney_list->countJoinPeople($val['lmid']);
                $peoples[$val['lmid']] = $this->luckmoney_list->countPeople($val['lmid']);
                $money[$val['lmid']] = ($this->luckmoney_list->getIncrRedis($val['lmid']));
            }
            $data['money'] = $money;
            $data['dlmid'] = $dlmid;
            $data['JoinPeoples'] = $JoinPeoples;
            $data['peoples'] = $peoples;
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
            
            }else{
                $data['list'] = '';
                $data['pageNum'] = $data['page'] = $data['numPerPage'] = $data['count'] = 0;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1131');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '节假日红包', '', '查看用户红包明细', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/luckmoney/v_detailluckmoney',$data);
        }
         
    }
    
    public function getAlreadyLuckMoney(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户红包明细');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $data = array();
            $account  = trim($this->input->post('searchphone'));
            $lmid = $this->uri->segment(3);
            if($this->input->request('op') == 'search'){
                $luckmoney_log = $this->luckmoney_log->getLuckmoneyLogList(array('account'=>$account,'lmid'=>$lmid),'',array($psize, $offset));
                $count = count($this->luckmoney_log->getLuckmoneyLogList(array('account'=>$account,'lmid'=>$lmid),'',''));
            }else{
                $luckmoney_log = $this->luckmoney_log->getLuckmoneyLogList(array('lmid'=>$lmid),'',array($psize, $offset));
                $count = count($this->luckmoney_log->getLuckmoneyLogList(array('lmid'=>$lmid),'',''));
            }
            $data['list'] = $luckmoney_log;
            $data['lmid'] = $lmid;
            $lname = $this->luckmoney_list->getLuckMoneyByLmid($lmid);
            $data['lname'] = $lname['lname'];
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
            
            }else{
                $data['list'] = '';
                $data['pageNum'] = $data['page'] = $data['numPerPage'] = $data['count'] = 0;
            }
            $this->load->view('/luckmoney/v_alreadyluckmoney',$data);
            
        }
        
    }
    public function autotianchong(){
        $lname = $this->input->post('lname');
        $data = $this->luckmoney_list->autotianchong($lname);
        echo json_encode($data[0]);
        exit;
    }
    public function deleteluckmoney(){
        $flag = $this->op->checkUserAuthority('红包中心', $this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户红包明细');
        }else{
            $lmid = $this->uri->segment(3);
            $yugaotime = $this->uri->segment(4);
            $ret = $this->luckmoney_list->deleteluckmoney($lmid);
            $this->luckmoney_list->delRedis($lmid,$yugaotime);
            $log = $this->op->actionData($this->getSession('name'), '删除成功', '', '删除红包信息', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '红包信息 ', 'forward',OP_DOMAIN.'/luckmoney_list'));
        }
    }
    
    
}