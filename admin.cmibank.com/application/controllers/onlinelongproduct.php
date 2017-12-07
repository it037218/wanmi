<?php

class onlinelongproduct extends Controller{
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
        $flag = $this->op->checkUserAuthority('活期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        } 
        $this->load->model('admin_ltype_model', 'ltype');
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
        $function_name = "1026";
        //$odate = $this->uri->segment(3);
        if(empty($odate)){
            $odate = date("Y-m-d");
            $function_name = "1028";
        }
        $ltypeList = $this->ltype->getLtypeList();
        $rtn = array();
        $yugao = array();
        foreach ($ltypeList as $_ltypeinfo){
            $ltypelongproduct = $this->ltype_longproduct->getLtypeLongproductList($_ltypeinfo['ptid'], $odate);
            $_ltypeinfo['type'] = 'changping';
            $rtn[$_ltypeinfo['ptid']] = $_ltypeinfo;
            if($ltypelongproduct){
                $count = 0;
                foreach ($ltypelongproduct as $_tp){
                    if($count >= 2){     //就取2个
                        break;
                    }
                    $rtn[$_ltypeinfo['ptid']]['lplist'][$count] = $this->longproduct->getLongproductByPid($_tp['pid']);
                    $count++;
                }
            }
            $rtn[$_ltypeinfo['ptid'] . '_yugao'] = array('name' => $_ltypeinfo['name'] . '_预告', 'type' => 'yugao', 'ptid' => $_ltypeinfo['ptid']);
            $ltypelongproduct = $this->ltype_longproduct->getLtypeLongproductList($_ltypeinfo['ptid'], $odate, true);
            if($ltypelongproduct){
                $count = 0;
                foreach ($ltypelongproduct as $_tp){
                    if($count >= 2){     //就取2个
                        break;
                    }
                    $rtn[$_ltypeinfo['ptid'] . '_yugao']['lplist'][$count] = $this->longproduct->getLongproductByPid($_tp['pid']);
                    $count++;
                }
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '活期期产品发布', $this->getIP(), $this->getSession('uid'));
        $rtn_data['list'] = $rtn;   
        $rtn_data['odate'] = $odate;
        $edatable = $this->op->getEditable($this->getSession('uid'),'1026');
        if(!empty($edatable)){
        	$rtn_data['editable'] = $edatable[0]['editable'];
        }else{
        	$rtn_data['editable']=0;
        }
        $this->load->view('/onlinelongproduct/v_index', $rtn_data);
    }
    
    public function changeindex(){
        $flag = $this->op->checkUserAuthority('活期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $this->load->model('admin_ltype_model', 'ltype');
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
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
        $ltypelongproduct = $this->ltype_longproduct->getLtypeLongproductList($ptid, $odate);
        
        foreach ($ltypelongproduct as $_index => $_tp){
            $ltypelongproduct[$_index]['detail'] = $this->longproduct->getLongproductBypid($_tp['pid']);
        }
        if(empty($ltypelongproduct)){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'项目中没有产品')));
        }
        $rtn_data['list'] = $ltypelongproduct;
        $rtn_data['odate'] = $odate;
        $rtn_data['ltname'] = $ltype_list[$ptid];
        $this->load->view('/onlinelongproduct/v_changeindex', $rtn_data);
    }
    
    public function tiaoxu(){
        $flag = $this->op->checkUserAuthority('活期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $pid = $this->input->get('pid');
        $action = $this->input->get('action');
        $this->load->model('admin_ltype_model', 'ltype');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
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
        $where = array('ptid' => $ptid , 'pid' => $pid, 'odate' => $odate);
        $c_ltype_longproduct_info = $this->ltype_longproduct->getLtypeLongproduct($where);
        $minrindex = $this->ltype_longproduct->getminrindex($ptid, $odate);
    
        if($c_ltype_longproduct_info['rindex'] == $minrindex){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'第一名不能调序!!')));
        }
        if($action == 'up'){
            $n_ltype_longproduct_info = $this->ltype_longproduct->getupLongproduct($c_ltype_longproduct_info['rindex'], $ptid, $odate, $minrindex);
    
        }else if($action == 'down'){
            $n_ltype_longproduct_info = $this->ltype_longproduct->getdownLongproduct($c_ltype_longproduct_info['rindex'], $ptid, $odate);
        }else {
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'错误的请求!!')));
        }
        if(!$n_ltype_longproduct_info){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'前面已没有产品或第1名产品不能替换')));
        }
        $c_rindex = $c_ltype_longproduct_info['rindex'];
        $n_rindex = $n_ltype_longproduct_info['rindex'];
        $c_ltype_longproduct_info['rindex'] = $n_rindex;
        $n_ltype_longproduct_info['rindex'] = $c_rindex;
        $ret1 = $this->ltype_longproduct->updateLtypeLongporduct($c_ltype_longproduct_info);
        if(!$ret1){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!')));
        }
        $ret2 = $this->ltype_longproduct->updateLtypeLongporduct($n_ltype_longproduct_info);
        if(!$ret2){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!!')));
        }
        $ret2 = $this->ltype_longproduct->rebuildLtypeLongproductListRedisCache($ptid, $odate);
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '调整' . $ltype_list[$_val['ptid']] . '顺序', $this->getIP(), $this->getSession('uid'));
        //         echo OP_DOMAIN.'/onlineproduct/changeindex?ptid='.$ptid.'&odate="'.$odate.'"';exit;
        exit($this->ajaxDataReturnParams(self::AJ_RET_SUCC,  '调序成功', array(), '调整' . $ltype_list[$_val['ptid']] . '顺序', 'forward' , OP_DOMAIN.'/onlinelongproduct/changeindex?odate='.$odate.'&ptid='.$ptid ));
    }
    
    public function totop(){
        $flag = $this->op->checkUserAuthority('活期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        $pid = $this->input->get('pid');
        $action = $this->input->get('action');
        $this->load->model('admin_ltype_model', 'ltype');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
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
        $minrindex = $this->ltype_longproduct->getminrindex($ptid, $odate);
        $the2_min = $this->ltype_longproduct->get_the2_min_rindex($ptid, $odate, $minrindex);
    
        $where = array('ptid' => $ptid , 'pid' => $pid, 'odate' => $odate, 'status' => 0);
        $c_ltype_longproduct_info = $this->ltype_longproduct->getLtypeLongproduct($where);
    
        $c_ltype_longproduct_info['rindex'] = $the2_min;
    
        $ret1 = $this->ltype_longproduct->updateAllLtypeLongproductRindex($odate, $ptid, $the2_min);
        if(!$ret1){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!!')));
        }
        $ret2 = $this->ltype_longproduct->updateLtypeLongporduct($c_ltype_longproduct_info);
    
        if(!$ret2){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新失败!')));
        }
        $ret2 = $this->ltype_longproduct->rebuildLtypeLongproductListRedisCache($ptid, $odate);
        $log = $this->op->actionData($this->getSession('name'), '产品发布中心', '', '调整' . $ltype_list[$_val['ptid']] . '顺序', $this->getIP(), $this->getSession('uid'));
        //      echo OP_DOMAIN.'/onlineproduct/changeindex?ptid='.$ptid.'&odate="'.$odate.'"';exit;
        exit($this->ajaxDataReturnParams(self::AJ_RET_SUCC,  '调序成功', array(), '调整' . $ltype_list[$_val['ptid']] . '顺序', 'forward' , OP_DOMAIN.'/onlinelongproduct/changeindex?odate='.$odate.'&ptid='.$ptid ));
    }
    
    public function yugao(){
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理');
        }
        $odate = $this->input->get('odate');
        $ptid = $this->input->get('ptid');
        
        $this->load->model('admin_ltype_model', 'ltype');
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
        
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
    
        $ltypelongproduct = $this->ltype_longproduct->getLtypeLongproductList($ptid, $odate,true);
        foreach ($ltypelongproduct as $_index => $_tp){
            $ltypelongproduct[$_index]['detail'] = $this->longproduct->getLongproductByPid($_tp['pid']);
        }
        if(empty($ltypelongproduct)){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'项目中没有产品')));
        }
        $rtn_data['list'] = $ltypelongproduct;
        $rtn_data['odate'] = $odate;
        $rtn_data['ltname'] = $ltype_list[$ptid];
        //         echo json_encode($rtn_data, true);exit;
        $this->load->view('/onlinelongproduct/v_yugao', $rtn_data);
    }
    
    public function Soldout($ptid, $pid, $odate){
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
        //从缓存中去掉
        $ret = $this->longproduct->moveOnlineLongproduct($ptid, $pid, $odate);
        if(!$ret){
           // exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'删除REDIS失败')));
        }
        //更改产品状态   2下架
        $update_data = array('status' => 2, 'downtime' => time());
//         //更改产品状态   3售罄
//         $update_data = array('status' => 3, 'downtime' => time());
        $this->longproduct->updateLongproductStatus($pid, $update_data);
        $this->ltype_longproduct->updateLongorductByPid($pid,array('status' => 1));

        if($odate == date('Y-m-d') && $ptid == LONGPRODUCT_PTID){
        //加入到售罄队列缓存
            $this->longproduct->addlongProductToSellOutList($pid);
//             $this->ltype_longproduct->updateLongorductByPid($pid,array('status' =>1));
        }
        //记个文本日志
        $log = array();
        $log['status'] = '4';
        $log['pid'] = $pid;
        $log['ptid'] = $ptid;
        $this->load->model('base/log_base', 'log_base');
        $this->log_base->back_contract_log($log);
         
        $log = $this->op->actionData($this->getSession('name'), '活期期产品发布', '', '停售', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward',OP_DOMAIN.'/onlinelongproduct'));
    
    }
    public function downtoline($ptid, $pid, $odate = ''){
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_ltype_longproduct_model', 'ltype_longproduct');
        
        
        //从缓存中去掉
        $ret = $this->longproduct->moveOnlineLongproduct($ptid, $pid, $odate);
        if(!$ret){
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'删除REDIS失败')));
        }
        //更改产品状态   2下架
        $update_data = array('status' => 2, 'downtime' => time());
        $this->longproduct->updateLongproductStatus($pid, $update_data);
        $this->ltype_longproduct->updateLongorductByPid($pid,array('status' =>1));
        
        $longproduct = $this->longproduct->getLongproductByPid($pid);
        if($longproduct['sellmoney'] == 0){
            //删除这两张表
            $ret = $this->longproduct->delLongproduct($pid);
            $ret = $this->ltype_longproduct->delLtypeLongProduct($pid);
        }else{
            //加入到售罄队列缓存
            $this->longproduct->addlongProductToSellOutList($pid);
        }
        //记个文本日志
        $log = array();
        $log['status'] = '4';
        $log['pid'] = $pid;
        $log['ptid'] = $ptid;
        $this->load->model('base/log_base', 'log_base');
        $this->log_base->back_contract_log($log);
         
        $log = $this->op->actionData($this->getSession('name'), '活期期产品发布', '', '停售', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改产品信息 ', 'forward',OP_DOMAIN.'/onlinelongproduct'));
    }
}