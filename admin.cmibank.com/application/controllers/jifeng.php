<?php
class jifeng extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '体验金统计') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_jifeng_model', 'jifeng');
        $this->load->model('admin_useridentity_model', 'useridentity');
        $this->load->model('admin_account_model', 'account');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('积分记录', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '体验金统计');
            exit;
        } else {
        	if($this->input->request('op') == 'search'){
	            $data = array();
	            $page = max(1, intval($this->input->request('pageNum')));
	            $psize = max(20, intval($this->input->request('numPerPage')));
	            $offset = ($page - 1) * $psize;
	            
	            $account = trim($this->input->post('account'));
	            $type = trim($this->input->post('type'));
	            $stime = trim($this->input->post('stime'));
	            $etime = trim($this->input->post('etime'));
	            $queryparam = array();
	            if(!empty($account)){
	            	$uid = $this->account->getUidByAccount($account);
	            	if(!empty($uid)){
	            		$queryparam['uid'] = $uid[0]['uid'];
	            	}else{
		            	 exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'未找到电话号码')));
	            	}
	            }
	            $data['type'] = $type;
	            if(!empty($type)){
		            $queryparam['type'] = $type;
	            }
	            $data['account'] = $account;
	            $queryparam['account'] = $account;
	            if(!empty($stime)){
	            	$searchparam['stime'] = strtotime($stime);
	            	$data['stime'] = $stime;
	            }
	            if(!empty($etime)){
	            	$searchparam['etime'] = strtotime($etime)+86400;
	            	$data['etime'] = $etime;
	            }
	            $jifengList = $this->jifeng->getUserJifengByCondition($queryparam,array($offset,$psize));
	            $names = array();
	            if(!empty($jifengList)){
	            	foreach ($jifengList as $jifeng){
		            	if(!array_key_exists($jifeng['uid'],$names)){
		            		$user = $this->useridentity->getUseridentityByUid($jifeng['uid']);
		            		if(!empty($user)){
			            		$names[$jifeng['uid']] = $user['realname'];
		            		}
		            	}
	            	}
	            }
	            $count = count($this->jifeng->getUserJifengByCondition($queryparam));
	            $total = $this->jifeng->getTotalJifeng($queryparam['uid']);
	            $totalduihuang = $this->jifeng->getTotalUsedJifeng($queryparam['uid']);
	            
	            $data['total']    = $total;
	            $data['totalduihuang']    = $totalduihuang;
	            $data['left'] = $total-$totalduihuang;
	            if($count>0){
	                $data['pageNum']    = $page;
	                $data['numPerPage'] = $psize;
	                $data['count'] = $count;
	                $data['list'] = $jifengList;
	                $data['names'] = $names;
	            }else{
	                $data['pageNum'] = 1;
	                $data['numPerPage'] = 0;
	                $data['count'] = 0;
	                $data['list'] = $data['page'] = '';
	            }
	            $log = $this->op->actionData($this->getSession('name'), '体验金统计', '', '体验金统计', $this->getIP(), $this->getSession('uid'));
	            $this->load->view('/jifeng/v_index', $data);
        	}else{
        		$data['pageNum']    = 1;
        		$data['numPerPage'] = 20;
        		$data['type'] = 0;
        		$this->load->view('/jifeng/v_index',$data);
        	}
        }
    }
    
}
