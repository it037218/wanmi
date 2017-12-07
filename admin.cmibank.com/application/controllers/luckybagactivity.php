<?php
/**
 * Luckybag活动管理
 * * */
class luckybagactivity extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '邀请红包活动') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_luckybagactivity_model', 'luckybagactivity');
        $this->load->model('admin_luckybag_model', 'luckybag');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('邀请红包活动', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '邀请红包活动');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            
            $luckybagActivityList = $this->luckybagactivity->getLuckybagActivityList(null,'stime desc',array($psize, $offset));
            if(!empty($luckybagActivityList)){
            foreach ($luckybagActivityList as $index=>$_val){
               $cids = explode(",",$_val['lid']);
               $cnames = "";
               foreach ($cids as $cid){
	               $luckybag = $this->luckybag->getLuckybagById($cid);
	               if(!empty($luckybag)){
		               $cnames = $cnames.$luckybag[0]['name'].',';
	               }
               }
               $cnames = substr($cnames,0,-1);
               $luckybagActivityList[$index]['cnames'] = $cnames;
               if($_val['etime']<NOW){
               		if($_val['status'] != 3){
	               		$_data['status'] = 3;
	               		$ret = $this->luckybagactivity->updateLuckybagActivityById($_val['id'],$_data);
	               		$luckybagActivityList[$index]['status'] = 3;
               		}
               }
            }
            }
            $count = $this->luckybagactivity->getLuckybagActivityCount();
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $luckybagActivityList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'10300');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '邀请红包活动', '', '邀请红包活动', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/luckybagactivity/v_index', $data);
        }
    }
    
    public function add(){
        $flag = $this->op->checkUserAuthority('邀请红包活动', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '邀请红包活动');
        } else {
            if($this->input->request('op') == 'add'){
                $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $buymoney = trim($this->input->post('buymoney'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $lid = trim($this->input->post('lid'));
                $data = array();
                $data['name'] = $name;
                $data['buymoney'] = $buymoney;
                $data['stime'] = strtotime($stime);
                $data['etime'] = strtotime($etime);
                $data['lid'] = $lid;
                $data['type'] = $type;
                $data['status'] = 1;
                $data['ctime'] = NOW;
                $ret = $this->luckybagactivity->addLuckybagActivity($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加红包活动失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '邀请红包活动', '', '邀请红包活动', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加红包活动成功', array(), '邀请红包活动 ', 'forward', OP_DOMAIN.'/luckybagactivity'));
            }else{
            	$this->load->model('admin_luckybag_model', 'luckybag');
            	$luckybagList = $this->luckybag->getAvailableLuckybag();
                $this->load->view('/luckybagactivity/v_add',array('luckybagList'=>$luckybagList));
            }
        }
    }
    public function edit(){
        $flag=$this->op->checkUserAuthority('邀请红包活动',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '邀请红包活动');
        }else{
            
            if($this->input->request('op') == 'edit'){
               $id = $this->input->post('id');    
               if(!$id){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               } 
               $id = trim($this->input->post('id'));
               $name = trim($this->input->post('name'));
                $type = trim($this->input->post('type'));
                $buymoney = trim($this->input->post('buymoney'));
                $stime = trim($this->input->post('stime'));
                $etime = trim($this->input->post('etime'));
                $lid = trim($this->input->post('lid'));
                $data = array();
                $data['name'] = $name;
                $data['buymoney'] = $buymoney;
                $data['stime'] = strtotime($stime);
                $data['etime'] = strtotime($etime);
                $data['lid'] = $lid;
                $data['type'] = $type;
               $ret = $this->luckybagactivity->updateLuckybagActivityById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '邀请红包活动', '', '修改邀请红包活动', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改邀请红包活动 ', 'forward', OP_DOMAIN.'/luckybagactivity'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $activityList = $this->luckybagactivity->getLuckybagActivityById($id);
                $this->load->model('admin_luckybag_model', 'luckybag');
                $luckybagList = $this->luckybag->getAvailableLuckybag();
                $data['detail'] =$activityList[0];
                $data['luckybagList'] =$luckybagList;
                $this->load->view('/luckybagactivity/v_edit', $data);
            }  
        }
    }
    public function del(){
        $flag=$this->op->checkUserAuthority('邀请红包活动',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '邀请红包活动');
        }else{
            $id = $this->uri->segment(3);
            $ret = $this->luckybagactivity->delLuckybagActivityById($id);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '邀请红包活动', '', '删除红包', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除红包', 'forward', OP_DOMAIN.'/luckybagactivity'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('邀请红包活动',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '邀请红包活动');
    	}else{
	    		$id = $this->uri->segment(3);
	    		$activityList = $this->luckybagactivity->getLuckybagActivityById($id);
	    		if($activityList[0]['etime']<NOW){
	    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'活动已过期')));
	    		}
	    		$luckybagActivityList = $this->luckybagactivity->getOnlineLuckybagActivity($activityList[0]['type']);
	    		if(!empty($luckybagActivityList)){
	    			foreach ($luckybagActivityList as $luckybagActivity){
	    				if($luckybagActivity['etime']>NOW){
	    					if(!($activityList[0]['etime']<$luckybagActivity['stime']||$activityList[0]['stime']>$luckybagActivity['etime'])){
		    					exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'与已有活动时间冲突')));
	    					}
	    				}
	    			}
	    		}
	    		$data['status'] = 2;
	    		$ret = $this->luckybagactivity->updateLuckybagActivityById($id,$data);
	    		if(!$ret){
	    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发布失败')));
	    		}
    		}
    	$log = $this->op->actionData($this->getSession('name'), '邀请红包活动', '', '发布红包', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布红包', 'forward', OP_DOMAIN.'/luckybagactivity'));
    }
    public function downLine(){
    	$flag=$this->op->checkUserAuthority('邀请红包活动',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '邀请红包活动');
    	}else{
    		$id = $this->uri->segment(3);
    		$data['status'] = 3;
    		$ret = $this->luckybagactivity->updateLuckybagActivityById($id,$data);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'下架失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '邀请红包活动', '', '下架红包', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '下架成功', array(), '下架红包', 'forward', OP_DOMAIN.'/luckybagactivity'));
    }
}