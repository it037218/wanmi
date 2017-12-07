<?php
/**
 * report管理
 * * */
class bireport extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '运营统计报表'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_bireport_model', 'bireport');
        $this->load->model('admin_bireport_week_model', 'bireport_week');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('日报汇总统计',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'日报汇总统计');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            if($this->input->request('op') == "search"){
	            $stime =trim($this->input->request('stime'));
	            $etime =trim($this->input->request('etime'));
                $type = trim($this->input->post('type'));
                $data['type'] = $type;
                $data['stime'] = $stime;
                $data['etime'] = $etime;
                $data['list'] = $this->bireport->getBireportByCondition($type,$stime,$etime, array($psize,$offset));
                $count = count($this->bireport->getBireportByCondition($type,$stime,$etime));
            }else{
                $data['list'] = $this->bireport->getBireport('','cdate desc,daymoney desc',array($psize, $offset));
                $count = count($this->bireport->getBireport('','',''));
                $data['type'] = '';
                $data['stime'] =  '';
                $data['etime'] =  '';
            }
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            if($count>0){
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $log = $this->op->actionData($this->getSession('name'), '运营统计报表', '', '日报汇总统计', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/bireport/v_index', $data);
        }
    }
    public function dayallplat(){
        $flag = $this->op->checkUserAuthority('日报汇总统计',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'日报汇总统计');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $stime =trim($this->input->request('stime'));
            $etime =trim($this->input->request('etime'));
            if($this->input->request('op') == "search_bannertime"){
                $data['stime'] = $stime;
                $data['etime'] = $etime;
                $data['list'] = $this->bireport->getBireportbetweenTime($stime,$etime, array($psize,$offset));
                $count = count($this->bireport->getBireportbetweenTime($stime,$etime));
            }else if($this->input->request('op') == "searchqd"){
                echo "sdfsdf";
            }else{
                error_reporting(0);
                $rtnbireport = array();
                $bireport = $this->bireport->getBireport('','cdate desc','');
                foreach ($bireport as $key=>$val){
                    if(!isset($rtnbireport[$val['cdate']]['register'])){
                        $rtnbireport[$val['cdate']]['register'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['daydeal'])){
                        $rtnbireport[$val['cdate']]['daydeal'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['olddeal'])){
                        $rtnbireport[$val['cdate']]['olddeal'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['countdeal'])){
                        $rtnbireport[$val['cdate']]['countdeal'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['dealmoney'])){
                        $rtnbireport[$val['cdate']]['dealmoney'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['daybuyuser'])){
                        $rtnbireport[$val['cdate']]['daybuyuser'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['daymoney'])){
                        $rtnbireport[$val['cdate']]['daymoney'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['daymoney_d'])){
                        $rtnbireport[$val['cdate']]['daymoney_d'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['daymoney_h'])){
                        $rtnbireport[$val['cdate']]['daymoney_h'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['fugou'])){
                        $rtnbireport[$val['cdate']]['fugou'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['qt'])){
                        $rtnbireport[$val['cdate']]['qt'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['activate'])){
                        $rtnbireport[$val['cdate']]['activate'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['daynumber'])){
                        $rtnbireport[$val['cdate']]['daynumber'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['bangkashu'])){
                        $rtnbireport[$val['cdate']]['bangkashu'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['oldmoney'])){
                        $rtnbireport[$val['cdate']]['oldmoney'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['oldnum'])){
                        $rtnbireport[$val['cdate']]['oldnum'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['newuid'])){
                        $rtnbireport[$val['cdate']]['newuid'] = 0;
                    }
                    if(!isset($rtnbireport[$val['cdate']]['cdate'])){
                        $rtnbireport[$val['cdate']]['cdate'] = 0;
                    }
                    $rtnbireport[$val['cdate']]['activate'] += $val['activate'];
                    $rtnbireport[$val['cdate']]['register'] += $val['register'];
                    $rtnbireport[$val['cdate']]['daydeal'] += $val['daydeal'];
                    $rtnbireport[$val['cdate']]['olddeal'] += $val['olddeal'];
                    $rtnbireport[$val['cdate']]['countdeal'] += $val['countdeal'];
                    $rtnbireport[$val['cdate']]['dealmoney'] += $val['dealmoney'];
                    $rtnbireport[$val['cdate']]['daybuyuser'] += $val['daybuyuser'];
                    $rtnbireport[$val['cdate']]['daymoney'] += $val['daymoney'];
                    $rtnbireport[$val['cdate']]['daymoney_h'] += $val['daymoney_h'];
                    $rtnbireport[$val['cdate']]['daymoney_d'] += $val['daymoney_d'];
                    $rtnbireport[$val['cdate']]['daynumber'] += $val['daynumber'];
                    $rtnbireport[$val['cdate']]['qt'] += $val['qt'];
                    $rtnbireport[$val['cdate']]['fugou'] += $val['fugou'];
                    $rtnbireport[$val['cdate']]['bangkashu'] += $val['bangkashu'];
                    $rtnbireport[$val['cdate']]['newuid'] += $val['newuid'];
                    $rtnbireport[$val['cdate']]['oldnum'] += $val['oldnum'];
                    $rtnbireport[$val['cdate']]['oldmoney'] += $val['oldmoney'];
                    $rtnbireport[$val['cdate']]['cdate'] = $val['cdate'];
                    $rtnbireport[$val['cdate']]['qt'] += $val['qt'];
                    $rtnbireport[$val['cdate']]['fugou'] += $val['fugou'];
                    if($rtnbireport[$val['cdate']]['register'] == 0){
                        $rtnbireport[$val['cdate']]['register'] = 1;
                    }
                    if($rtnbireport[$val['cdate']]['bangkashu'] == 0){
                        $rtnbireport[$val['cdate']]['bangkashu'] = 1;
                    }
                    if($rtnbireport[$val['cdate']]['qt'] == 0){
                        $rtnbireport[$val['cdate']]['qt'] = 1;
                    }
                    if($rtnbireport[$val['cdate']]['daynumber'] == 0){
                        $rtnbireport[$val['cdate']]['daynumber'] = 1;
                    }
                    $rtnbireport[$val['cdate']]['deal_reg'] = ($rtnbireport[$val['cdate']]['daydeal']/$rtnbireport[$val['cdate']]['register'])*100;
                    $rtnbireport[$val['cdate']]['daydeal_bangka'] = ($rtnbireport[$val['cdate']]['daydeal']/$rtnbireport[$val['cdate']]['bangkashu'])*100;
                    $rtnbireport[$val['cdate']]['bangka_reg'] = ($rtnbireport[$val['cdate']]['bangkashu']/$rtnbireport[$val['cdate']]['register'])*100;
                    
                    $rtnbireport[$val['cdate']]['arpu'] = ($rtnbireport[$val['cdate']]['daymoney']/$rtnbireport[$val['cdate']]['daybuyuser']);
                    $rtnbireport[$val['cdate']]['fugoulv'] = ($rtnbireport[$val['cdate']]['fugou']/$rtnbireport[$val['cdate']]['qt'])*100;
                }
                $data['list'] = $rtnbireport;
                $count = count($this->bireport->getBireport('','',''));
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
            $log = $this->op->actionData($this->getSession('name'), '运营统计报表', '', '日报汇总统计', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/bireport/v_dayallplat', $data);
        }
    }
    public function week(){
        $flag = $this->op->checkUserAuthority('日报汇总统计',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'日报汇总统计');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $stime =trim($this->input->request('stime'));
            $etime =trim($this->input->request('etime'));
            if($this->input->request('op') == "search_bannertime"){
                $data['stime'] = $stime;
                $data['etime'] = $etime;
                $data['list'] = $this->bireport->getBireportbetweenTime($stime,$etime, array($psize,$offset));
                $count = count($this->bireport->getBireportbetweenTime($stime,$etime));
            }else{
                $data['list'] = $this->bireport_week->getBireportWeek('','cdate desc,daymoney desc',array($psize, $offset));
                $count = count($this->bireport_week->getBireportWeek('','',''));
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
            $log = $this->op->actionData($this->getSession('name'), '运营统计报表', '', '日报汇总统计', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/bireport/v_week', $data);
        }
    }

}