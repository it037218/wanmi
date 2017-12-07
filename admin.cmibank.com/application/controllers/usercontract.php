<?php
/**
 * 用户电子合同模板
 * * */
class usercontract extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '用户电子合同模板') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_usercontract_model', 'usercontract');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('用户电子合同模板', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '用户电子合同模板');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $this->load->model('admin_proftype_model', 'proftype');
            $proftype = $this->proftype->getproftypeList();
            $rtn_proftype = array();
            foreach ($proftype as $key => $val){
                $rtn_proftype[$val['profid']] = $val;
            }
            $data['proftype'] = $rtn_proftype;
            $data['list'] = $this->usercontract->getUsercontractList('', 'ctime desc', array($psize, $offset));
            $count = $this->usercontract->getUsercontractCount();
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['rel'] = OP_DOMAIN . 'usercontract/index?page=' . $page;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1033');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $this->load->model('admin_proftype_model', 'proftype');
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '用户电子合同模板', '', '用户电子合同模板', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/usercontract/v_index', $data);
        }
    }
    
    public function addusercontract(){
        $flag = $this->op->checkUserAuthority('用户电子合同模板', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加合同');
        } else {
            if($this->input->request('op') == 'addusercontract'){
                $tplname = trim($this->input->post('tplname'));
                $tplnumber = trim($this->input->post('tplnumber'));
                $profid = trim($this->input->post('profid'));
				
				$ret = $this->usercontract->getUsercontractList(array('profid'=>$profid),'','');
				if(!empty($ret)){
					exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'业务类型已经添加过了')));
				}
				
                $tpl_pagename = trim($this->input->post('tpl_pagename'));
                
                $data['tplname'] = $tplname;
                $data['tplnumber'] = $tplnumber;
                $data['profid'] = $profid;
                $data['tpl_pagename'] = $tpl_pagename;
                $data['ctime'] = time();        //创建时间
                $ret = $this->usercontract->addusercontract($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加合同失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '用户电子合同模板', '', '添加用户电子合同模板', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加成功', array(), '添加用户电子合同模板 ', 'forward', OP_DOMAIN.'/usercontract'));
            }else{
                $this->load->model('admin_proftype_model', 'proftype');
                $data['proftypelist'] = $this->proftype->getProftypeGroup();
                $this->load->view('/usercontract/v_addusercontract', $data);
            }
        }
    }
    
    
    public function editusercontract() {
        $flag = $this->op->checkUserAuthority('用户电子合同模板', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            
            if($this->input->request('op') == 'editusercontract'){
                $ucid = $this->input->post('ucid');
                if(!$ucid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $tplname = trim($this->input->post('tplname'));
                $tpllink = trim($this->input->post('tpllink'));
                $tplnumber = trim($this->input->post('tplnumber'));
                $profid = trim($this->input->post('profid'));
                $tpl_pagename = trim($this->input->post('tpl_pagename'));
                
                $data['tplname'] = $tplname;
                $data['tpllink'] = $tpllink;
                $data['tplnumber'] = $tplnumber;
                $data['profid'] = $profid;
                $data['tpl_pagename'] = $tpl_pagename;
                $ret = $this->usercontract->updateusercontract($ucid, $data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '修改用户电子合同模板', '', '修改用户电子合同模板', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改用户电子合同模板成功', array(), '修改用户电子合同模板 ', 'forward', OP_DOMAIN.'/usercontract'));
            }else{
                $ucid = $this->uri->segment(3);
                if($ucid < 0 || !is_numeric($ucid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $usercontraceInfo = $this->usercontract->getusercontractByUcid($ucid);
                $data['detail'] = $usercontraceInfo;
                $this->load->model('admin_proftype_model', 'proftype');
                $currect_proftype = $this->proftype->getproftypeByProfid($usercontraceInfo['profid']);
                $data['proftypelist'] = $this->proftype->getProftypeGroup();
                $data['profnamelist'] = $this->proftype->getprofnamebyproftype(array('proftype' => $currect_proftype['proftype']));
                
                $data['currect_proftype'] = $currect_proftype;
                $this->load->view('/usercontract/v_editusercontract', $data);
            }
        }
    }
    
    
    public function delusercontract($ucid){
        $flag = $this->op->checkUserAuthority('用户电子合同模板', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '删除合同');
        } else {
            $data = $this->usercontract->getUsercontractByUcid($ucid);
            if(empty($data)){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'不存在的合同ID')));
            }
            if($data['money'] != 0 ){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已开售合同不能删除')));
            }
            $this->usercontract->delusercontract($ucid);
            $log = $this->op->actionData($this->getSession('name'), '删除用户电子合同模板', '', '删除用户电子合同模板', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除用户电子合同模板成功', array(), '删除用户电子合同模板 ', 'forward', OP_DOMAIN.'/usercontract'));
            
        }
    }
    
    private function diff_days($start, $now){
        $a_dt=getdate($start);
        $b_dt=getdate($now);
        $a_new=mktime(12,0,0,$a_dt['mon'],$a_dt['mday'],$a_dt['year']);
        $b_new=mktime(12,0,0,$b_dt['mon'],$b_dt['mday'],$b_dt['year']);
        return abs(($a_new-$b_new)/86400);
    }
    
    public function showtpl($tpl_name){
        $data = array();
        $data['croname'] = '上海XXX股份有限公司';
        $data['con_number'] = '第(2015)年(XX债转XX)号';
        $data['pname'] = '天天盈A1';
        $data['money'] = 10000000;
        $data['uname'] = '王小明';
        $data['ubuytime'] = '2015-05-22';
        $data['buytime'] = '2015-05-22';
        $data['buymoney'] = '3000';
        $data['con_income'] = '11%';
        $data['con_money'] = 1000000;
        //预期利息收益人民币
        $data['incomemoney'] = '1000';
        //合计为人民币
        $data['countmoney'] = '1000';
        //服务费
        $data['servermoney'] = '1000';
        //甲方户名
        $data['jcardname'] = '张晓梅';
        //甲方开户银行
        $data['jbankname'] = '建设银行';
        //甲方指定账户
        $data['jbankid'] = '4392 25000 4431 6369';
        //甲方地址
        $data['jaddress'] = '上海市闵行区陈行路2388号浦江科技广场9号6A';
        //丙方户名
        $data['bcardname'] = '周森锋';
        //丙方开户银行
        $data['bbankname'] = '招商银行';
        //丙方指定账户
        $data['bbankid'] = '4392 25000 4431 6369';
        
        //每月归还日期
        $data['eday'] = '07-19';
        //每月归还日期
        $data['edaymoney'] = '500';
        
        //合同期限
        $data['diff_day'] = '33';
        $data['idcard'] = 330830198807072316;
        //丙方公司
        $data['ourcompany'] = '万米财富管理有限公司';
        $data['address'] = '上海徐汇区虹桥路3号港汇恒隆广场2座3806室';
        $data['faren'] = '包青天';
        $data['tel'] = '13600132139';
        $data['repaymenttime'] = '2015-06-25';
        $rtn_data['info'] = $data;
        $this->load->view('/usercontract/tpl/'.$tpl_name, $rtn_data);
        
    }
}