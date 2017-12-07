<?php

class onlineklproduct extends Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '产品发布中心') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        
    }
    
    public function tomorrow(){
    
        $odate = date('Y-m-d',strtotime("+1 day"));
        $this->index($odate);
    }
    
    public function index($odate = ''){
        $flag = $this->op->checkUserAuthority('快乐宝产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        } 
        $this->load->model('admin_kltype_model', 'kltype');
        $this->load->model('admin_klproduct_model', 'klproduct');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        //$odate = $this->uri->segment(3);
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $kltypeList = $this->kltype->getKltypeList();
        $rtn = array();
        $yugao = array();
        foreach ($kltypeList as $_kltypeinfo){
            $kltypeklproduct = $this->kltype_klproduct->getKltypeKlproductList($_kltypeinfo['ptid'], $odate);
            $_kltypeinfo['type'] = 'changping';
            $rtn[$_kltypeinfo['ptid']] = $_kltypeinfo;
            if($kltypeklproduct){
                $count = 0;
                foreach ($kltypeklproduct as $_tp){
                    if($count >= 2){     //就取2个
                        break;
                    }
                    $rtn[$_kltypeinfo['ptid']]['lplist'][$count] = $this->klproduct->getKlproductByPid($_tp['pid']);
                    $count++;
                }
            }
            $rtn[$_kltypeinfo['ptid'] . '_yugao'] = array('name' => $_kltypeinfo['name'] . '_预告', 'type' => 'yugao', 'ptid' => $_kltypeinfo['ptid']);
            $kltypeklproduct = $this->kltype_klproduct->getKltypeKlproductList($_kltypeinfo['ptid'], $odate, true);
            if($kltypeklproduct){
                $count = 0;
                foreach ($kltypeklproduct as $_tp){
                    if($count >= 2){     //就取2个
                        break;
                    }
                    $rtn[$_kltypeinfo['ptid'] . '_yugao']['lplist'][$count] = $this->klproduct->getKlproductByPid($_tp['pid']);
                    $count++;
                }
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '快乐宝产品发布', $this->getIP(), $this->getSession('uid'));
        $rtn_data['list'] = $rtn;   
        $rtn_data['odate'] = $odate;
        $this->load->view('/onlineklproduct/v_index', $rtn_data);
    }
    
    public function changeindex(){
        $flag = $this->op->checkUserAuthority('快乐宝产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $this->load->model('admin_ltype_model', 'ltype');
        $this->load->model('admin_klproduct_model', 'klproduct');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $ltype = $this->ltype->getLtypeList();
        $ltype_list = array();
        foreach ($ltype as $_val){
            $ltype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($ltype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        $kltypeklproduct = $this->kltype_klproduct->getKltypeKlproductList($ptid, $odate);
        
        foreach ($kltypeklproduct as $_index => $_tp){
            $kltypeklproduct[$_index]['detail'] = $this->klproduct->getKlproductBypid($_tp['pid']);
        }
        if(empty($kltypeklproduct)){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'项目中没有产品')));
        }
        $rtn_data['list'] = $kltypeklproduct;
        $rtn_data['odate'] = $odate;
        $rtn_data['ltname'] = $ltype_list[$ptid];
        $this->load->view('/onlineklproduct/v_changeindex', $rtn_data);
    }
    
    public function tiaoxu(){
        $flag = $this->op->checkUserAuthority('快乐宝产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $pid = $this->input->get('pid');
        $action = $this->input->get('action');
        $this->load->model('admin_kltype_model', 'kltype');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $kltype = $this->kltype->getKltypeList();
        $kltype_list = array();
        foreach ($kltype as $_val){
            $kltype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($kltype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        $where = array('ptid' => $ptid , 'pid' => $pid, 'odate' => $odate);
        $c_kltype_klproduct_info = $this->kltype_klproduct->getKltypeKlproduct($where);
        $minrindex = $this->kltype_klproduct->getminrindex($ptid, $odate);
    
        if($c_kltype_klproduct_info['rindex'] == $minrindex){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'第一名不能调序!!')));
        }
        if($action == 'up'){
            $n_kltype_klproduct_info = $this->kltype_klproduct->getupKlproduct($c_kltype_klproduct_info['rindex'], $ptid, $odate, $minrindex);
    
        }else if($action == 'down'){
            $n_kltype_klproduct_info = $this->kltype_klproduct->getdownKlproduct($c_kltype_klproduct_info['rindex'], $ptid, $odate);
        }else {
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'错误的请求!!')));
        }
        if(!$n_kltype_klproduct_info){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'前面已没有产品或第1名产品不能替换')));
        }
        $c_rindex = $c_kltype_klproduct_info['rindex'];
        $n_rindex = $n_kltype_klproduct_info['rindex'];
        $c_kltype_klproduct_info['rindex'] = $n_rindex;
        $n_kltype_klproduct_info['rindex'] = $c_rindex;
        $ret1 = $this->kltype_klproduct->updateKltypeKlporduct($c_kltype_klproduct_info);
        if(!$ret1){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!')));
        }
        $ret2 = $this->kltype_klproduct->updateKltypeKlporduct($n_kltype_klproduct_info);
        if(!$ret2){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!!')));
        }
        $ret2 = $this->kltype_klproduct->rebuildKltypeKlproductListRedisCache($ptid, $odate);
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '调整' . $kltype_list[$_val['ptid']] . '顺序', $this->getIP(), $this->getSession('uid'));
        //         echo OP_DOMAIN.'/onlineproduct/changeindex?ptid='.$ptid.'&odate="'.$odate.'"';exit;
        exit($this->ajaxDataReturnParams(self::AJ_RET_SUCC,  '调序成功', array(), '调整' . $kltype_list[$_val['ptid']] . '顺序', 'forward' , OP_DOMAIN.'/onlineklproduct/changeindex?odate='.$odate.'&ptid='.$ptid ));
    }
    
    public function totop(){
        $flag = $this->op->checkUserAuthority('快乐宝产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $pid = $this->input->get('pid');
        $action = $this->input->get('action');
        $this->load->model('admin_kltype_model', 'kltype');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $kltype = $this->kltype->getKltypeList();
        $kltype_list = array();
        foreach ($kltype as $_val){
            $kltype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($kltype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
        $minrindex = $this->kltype_klproduct->getminrindex($ptid, $odate);
        $the2_min = $this->kltype_klproduct->get_the2_min_rindex($ptid, $odate, $minrindex);
    
        $where = array('ptid' => $ptid , 'pid' => $pid, 'odate' => $odate, 'status' => 0);
        $c_kltype_klproduct_info = $this->kltype_klproduct->getKltypeKlproduct($where);
    
        $c_kltype_klproduct_info['rindex'] = $the2_min;
    
        $ret1 = $this->kltype_klproduct->updateAllKltypeKlproductRindex($odate, $ptid, $the2_min);
        if(!$ret1){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!!')));
        }
        $ret2 = $this->kltype_klproduct->updateKltypeKlporduct($c_kltype_klproduct_info);
    
        if(!$ret2){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!')));
        }
        $ret2 = $this->kltype_klproduct->rebuildKltypeKlproductListRedisCache($ptid, $odate);
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '调整' . $kltype_list[$_val['ptid']] . '顺序', $this->getIP(), $this->getSession('uid'));
        //      echo OP_DOMAIN.'/onlineproduct/changeindex?ptid='.$ptid.'&odate="'.$odate.'"';exit;
        exit($this->ajaxDataReturnParams(self::AJ_RET_SUCC,  '调序成功', array(), '调整' . $kltype_list[$_val['ptid']] . '顺序', 'forward' , OP_DOMAIN.'/onlineklproduct/changeindex?odate='.$odate.'&ptid='.$ptid ));
    }
    
    public function yugao(){
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        
        $this->load->model('admin_kltype_model', 'kltype');
        $this->load->model('admin_klproduct_model', 'klproduct');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        
        if(empty($odate)){
            $odate = date("Y-m-d");
        }
        $kltype = $this->ltype->getKltypeList();
        $kltype_list = array();
        foreach ($kltype as $_val){
            $kltype_list[$_val['ptid']] = $_val['name'];
        }
        if(!isset($kltype_list[$ptid])){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在项目')));
        }
    
        $kltypeklproduct = $this->kltype_klproduct->getKltypeKlproductList($ptid, $odate,true);
        foreach ($kltypeklproduct as $_index => $_tp){
            $kltypeklproduct[$_index]['detail'] = $this->klproduct->getKlproductByPid($_tp['pid']);
        }
        if(empty($kltypeklproduct)){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'项目中没有产品')));
        }
        $rtn_data['list'] = $kltypeklproduct;
        $rtn_data['odate'] = $odate;
        $rtn_data['ltname'] = $kltype_list[$ptid];
        //         echo json_encode($rtn_data, true);exit;
        $this->load->view('/onlineklproduct/v_yugao', $rtn_data);
    }
    
    public function Soldout($ptid, $pid, $odate){
        $this->load->model('admin_klproduct_model', 'klproduct');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        //从缓存中去掉
        $ret = $this->klproduct->moveOnlineKlproduct($ptid, $pid, $odate);
        if(!$ret){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'删除REDIS失败')));
        }
        //更改产品状态   2下架
        $update_data = array('status' => 2, 'downtime' => time());
//         //更改产品状态   3售罄
//         $update_data = array('status' => 3, 'downtime' => time());
        $this->klproduct->updateKlproductStatus($pid, $update_data);
        $this->kltype_klproduct->updateKlorductByPid($pid,array('status' => 1));

        if($odate == date('Y-m-d')){
        //加入到售罄队列缓存
            $this->klproduct->addklProductToSellOutList($pid);
//             $this->kltype_klproduct->updateLongorductByPid($pid,array('status' =>1));
        }
        //记个文本日志
        $log = array();
        $log['status'] = '4';
        $log['pid'] = $pid;
        $log['ptid'] = $ptid;
        $this->load->model('base/log_base', 'log_base');
        $this->log_base->back_contract_log($log);
         
        $log = $this->op->actionData($this->getSession('name'), '快乐宝产品发布', '', '停售', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '售罄成功', array(), '售罄产品信息 ', 'forward',OP_DOMAIN.'/onlineklproduct'));
    
    }
    public function downtoline($ptid, $pid, $odate = ''){
        $this->load->model('admin_klproduct_model', 'klproduct');
        $this->load->model('admin_kltype_klproduct_model', 'kltype_klproduct');
        
        
        //从缓存中去掉
        $ret = $this->klproduct->moveOnlineKlproduct($ptid, $pid, $odate);
        if(!$ret){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'删除REDIS失败')));
        }
        //更改产品状态   2下架
        $update_data = array('status' => 2, 'downtime' => time());
        $this->klproduct->updateKlproductStatus($pid, $update_data);
        $this->kltype_klproduct->updateKlorductByPid($pid,array('status' =>1));
        
        $klproduct = $this->klproduct->getKlproductByPid($pid);
        if($klproduct['sellmoney'] == 0){
            //删除这两张表
            $ret = $this->klproduct->delKlproduct($pid);
            $ret = $this->kltype_klproduct->delKltypeKlProduct($pid);
        }else{
            //加入到售罄队列缓存
            $this->klproduct->addklProductToSellOutList($pid);
        }
        //记个文本日志
        $log = array();
        $log['status'] = '4';
        $log['pid'] = $pid;
        $log['ptid'] = $ptid;
        $this->load->model('base/log_base', 'log_base');
        $this->log_base->back_contract_log($log);
         
        $log = $this->op->actionData($this->getSession('name'), '快乐宝产品发布', '', '停售', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '下架成功', array(), '修改产品信息 ', 'forward',OP_DOMAIN.'/onlineklproduct'));
    }
}