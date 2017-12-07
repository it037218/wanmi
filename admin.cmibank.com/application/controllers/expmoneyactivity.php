<?php
class expmoneyactivity extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '体验金活动') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_expmoneyactivity_model', 'expmoneyactivity');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('体验金活动', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '体验金活动');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            
            $expmoneyActivityList = $this->expmoneyactivity->getExpmoneyActivityList(null,'stime desc',array($psize, $offset));
            if(!empty($expmoneyActivityList)){
            foreach ($expmoneyActivityList as $index=>$_val){
               if($_val['etime']<NOW){
               		if($_val['status'] != 3){
	               		$_data['status'] = 3;
	               		$ret = $this->expmoneyactivity->updateExpmoneyActivityById($_val['id'],$_data);
	               		$expmoneyActivityList[$index]['status'] = 3;
               		}
               }
            }
            }
            $count = $this->expmoneyactivity->getExpmoneyActivityCount();
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $expmoneyActivityList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1410');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '体验金活动', '', '体验金活动', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/expmoneyactivity/v_index', $data);
        }
    }
    
    public function addExpmoneyActivity(){
        $flag = $this->op->checkUserAuthority('体验金活动', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '体验金活动');
        } else {
            if($this->input->request('op') == 'addexpmoneyactivity'){
               	$name = trim($this->input->post('name'));
               	$expname = trim($this->input->post('expname'));
                $type = trim($this->input->post('type'));
                $money = trim($this->input->post('money'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $data = array();
                $data['name'] = $name;
                $data['expname'] = $expname;
                $data['money'] = $money;
                $data['stime'] = strtotime($stime);
                $data['etime'] = strtotime($etime);
                $data['ctime'] = NOW;
                $data['type'] = $type;
                $data['days'] = 3;
                $data['income'] = 8;
                $ret = $this->expmoneyactivity->addExpmoneyActivity($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加抵用券活动失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '抵用券活动列表', '', '抵用券活动列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加抵用券活动成功', array(), '抵用券活动列表 ', 'forward', OP_DOMAIN.'/expmoneyactivity'));
            }else{
                $this->load->view('/expmoneyactivity/v_addExpmoneyActivity');
            }
        }
    }
    public function editExpmoneyActivity(){
        $flag=$this->op->checkUserAuthority('体验金活动',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '体验金活动');
        }else{
            
            if($this->input->request('op') == 'editexpmoneyactivity'){
               $id = $this->input->post('id');    
               if(!$id){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               } 
               	$id = trim($this->input->post('id'));
               	$name = trim($this->input->post('name'));
               	$expname = trim($this->input->post('expname'));
                $type = trim($this->input->post('type'));
                $money = trim($this->input->post('money'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $data = array();
                $data['name'] = $name;
                $data['expname'] = $expname;
                $data['money'] = $money;
                $data['stime'] = strtotime($stime);
                $data['etime'] = strtotime($etime);
               $ret = $this->expmoneyactivity->updateExpmoneyActivityById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '体验金活动', '', '修改体验金活动', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改体验金活动 ', 'forward', OP_DOMAIN.'/expmoneyactivity'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $activityList = $this->expmoneyactivity->getExpmoneyActivityById($id);
                $data['detail'] =$activityList[0];
                $this->load->view('/expmoneyactivity/v_editExpmoneyActivity', $data);
            }  
        }
    }
    public function delExpmoneyActivity(){
        $flag=$this->op->checkUserAuthority('体验金活动',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '体验金活动');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->expmoneyactivity->delExpmoneyActivityById($id);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '体验金活动', '', '删除抵用券', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除抵用券', 'forward', OP_DOMAIN.'/expmoneyactivity'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('体验金活动',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '体验金活动');
    	}else{
	    		$id = $this->uri->segment(3);
	    		$activityList = $this->expmoneyactivity->getExpmoneyActivityById($id);
	    		if($activityList[0]['etime']<NOW){
	    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'活动已过期')));
	    		}
	    		$expmoneyActivityList = $this->expmoneyactivity->getOnlineExpmoneyActivity($activityList[0]['type']);
	    		if(!empty($expmoneyActivityList)){
	    			foreach ($expmoneyActivityList as $expmoneyActivity){
	    				if($expmoneyActivity['etime']>NOW){
	    					if(!($activityList[0]['etime']<$expmoneyActivity['stime']||$activityList[0]['stime']>$expmoneyActivity['etime'])){
		    					exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'与已有活动时间冲突')));
	    					}
	    				}
	    			}
	    		}
	    		$data['status'] = 2;
	    		$ret = $this->expmoneyactivity->updateExpmoneyActivityById($id,$data);
	    		if(!$ret){
	    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发布失败')));
	    		}
    		}
    	$log = $this->op->actionData($this->getSession('name'), '体验金活动', '', '发布抵用券', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布抵用券', 'forward', OP_DOMAIN.'/expmoneyactivity'));
    }
    public function downLine(){
    	$flag=$this->op->checkUserAuthority('体验金活动',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '体验金活动');
    	}else{
    		$id = $this->uri->segment(3);
    		$data['status'] = 3;
    		$ret = $this->expmoneyactivity->updateExpmoneyActivityById($id,$data);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'下架失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '体验金活动', '', '下架抵用券', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '下架成功', array(), '下架抵用券', 'forward', OP_DOMAIN.'/expmoneyactivity'));
    }
}