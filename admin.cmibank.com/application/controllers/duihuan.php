<?php
class duihuan extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '兑换列表') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_duihuan_model', 'duihuan');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_account_model', 'account');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('兑换列表', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '兑换列表');
            exit;
        } else {
	        $queryparam = array();
            $data = array();
	        $page = max(1, intval($this->input->request('pageNum')));
	        $psize = max(20, intval($this->input->request('numPerPage')));
	        $offset = ($page - 1) * $psize;
        	if($this->input->request('op') == 'search'){
	            
	            $account = trim($this->input->post('account'));
	            $type = trim($this->input->post('type'));
	            $stime = trim($this->input->post('stime'));
	            $etime = trim($this->input->post('etime'));
	            $status = trim($this->input->post('status'));
	            if(!empty($account)){
	            	$uid = $this->account->getUidByAccount($account);
	            	if(!empty($uid)){
	            		$queryparam['uid'] = $uid[0]['uid'];
	            	}
	            }
	            $data['type'] = $type;
	            if(!empty($type)){
		            $queryparam['type'] = $type;
	            }
	            $data['account'] = $account;
	            $data['status'] = $status;
	            $queryparam['account'] = $account;
	            if(!empty($stime)){
	            	$queryparam['stime'] = strtotime($stime);
	            	$data['stime'] = $stime;
	            }
	            if(!empty($etime)){
	            	$queryparam['etime'] = strtotime($etime)+86400;
	            	$data['etime'] = $etime;
	            }
	            if($status!=3){
	            	$queryparam['status'] = $status;
	            }
        	}else{
        		$data['status'] = 3;
        	}
        	if($data['type'] == 4){
	            $duihuanList = $this->duihuan->getUserJifengByCondition($queryparam,array($offset,$psize),'status asc, ctime desc');
        	}else{
        		$duihuanList = $this->duihuan->getUserJifengByCondition($queryparam,array($offset,$psize));
        	}
            $names = array();
            $phones = array();
            if(!empty($duihuanList)){
            	foreach ($duihuanList as $duihuan){
	            	if(!array_key_exists($duihuan['uid'],$names)){
	            		$user = $this->useridentity->getUseridentityByUid($duihuan['uid']);
	            		if(!empty($user)){
		            		$names[$duihuan['uid']] = $user['realname'];
		            		$phones[$duihuan['uid']] = $user['phone'];
	            		}
	            	}
            	}
            }
            $count = $this->duihuan->countUserJifengByCondition($queryparam);
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $duihuanList;
                $data['names'] = $names;
                $data['phones'] = $phones;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'10200');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '兑换列表', '', '兑换列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/duihuan/v_index', $data);
        	}
        }
        
        public function initDuihuan(){
        	$flag=$this->op->checkUserAuthority('兑换列表',$this->getSession('uid'));
        	$data = array();
        	if($flag == 0){
        		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '补偿申请');
        	}else{
        		$id = $this->uri->segment(3);
        		if($id < 0 || !is_numeric($id)){
        			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
        		}
        		$_data = $this->duihuan->getDuihuanByid($id);
        		if(!$_data){
        			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'不存在的补尝单据!')));
        		}
        		if($_data[0]['status'] == 1){
        			exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'此单已兑!')));
        		}
        		$data['detail'] = $_data[0];
        		$this->load->view('/duihuan/v_init', $data);
        	}
        }
        public function doDuihuan(){
        	$flag=$this->op->checkUserAuthority('兑换列表',$this->getSession('uid'));
        	if($flag == 0){
        		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '补偿申请');
        	}else{
        		$id = trim($this->input->post('id'));
        		$money = trim($this->input->post('money'));
        		if($id < 0 || !is_numeric($id)){
        			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
        		}
        		$data['money'] = $money;
        		$data['realmoney'] = $money;
        		$data['status'] = 1;
        		$ret = $this->duihuan->updateDuihuanById($id,$data);
        		exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '兑换成功', array(), '兑换列表 ', 'forward', OP_DOMAIN.'/duihuan'));
        	}
        }
        
        public function getuserDuihuanList(){
        	$uid = $this->uri->segment(3);
        	$queryparam['type'] = 4;
        	$data['type'] = 4;
        	$queryparam['status'] = 1;
        	$data['status'] = 1;
        	$queryparam['uid']=$uid;
        	$duihuanList = $this->duihuan->getUserJifengByCondition($queryparam,array(0,20),'status asc, ctime desc');
        	$count = $this->duihuan->countUserJifengByCondition($queryparam);
        	$names=array();
        	$phones=array();
        	$user = $this->useridentity->getUseridentityByUid($uid);
        	if(!empty($user)){
        		$names[$uid] = $user['realname'];
        	}
        	$account = $this->account->getAccountByUid($uid);
        	if(!empty($account)){
        		$phones[$uid] = $account['account'];
        		$data['account'] = $account['account'];
        	}
        	$data['pageNum']    = 1;
        	$data['numPerPage'] = 20;
        	$data['count'] = $count;
        	$data['list'] = $duihuanList;
        	$data['names'] = $names;
        	$data['phones'] = $phones;
        	$data['phones'] = $phones;
        	$data['editable']=0;
        	$this->load->view('/duihuan/v_index', $data);
        }
}
