<?php
/**
 * chongzhi管理
* * */
class chongzhi extends Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		if (false == $this->menu) {
			redirect(OP_DOMAIN . 'login', 'location');
		} else {
			foreach ($this->menu AS $key => $value) {
				if ($value['name'] == '充值') {
					$this->submenu = $value['submenu'];
				}
			}
		}
		$this->load->model('admin_chongzhi_model', 'chongzhi');
	}

	public function index($page = 1) {
		$flag = $this->op->checkUserAuthority('充值', $this->getSession('uid'));        //检测用户操作权限
		if ($flag == 0) {
			echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '充值列表');
			exit;
		} else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $chongzhiList='';
            $count=0;
            
			if($this->input->request('op') == 'search'){
				$type = trim($this->input->post('type'));
				$stime = trim($this->input->post('stime'));
				$etime = trim($this->input->post('etime'));
				
				$where = array();
				$where['type']= $type;
				$where['stime']= $stime;
				$where['etime']= $etime;
				
				$data['type']= $type;
				$data['stime']= $stime;
				$data['etime']= $etime;
				$chongzhiList = $this->chongzhi->getChongzhiByCondition($where, $offset,$psize);
				$count = $this->chongzhi->countChongzhiByCondition($where);
			}else{
	            $chongzhiList = $this->chongzhi->getChongzhiList(array($psize, $offset));
	            $count = $this->chongzhi->getChongzhiCount();
	            $data['type']= 0;
			}
            
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $chongzhiList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1300');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '充值', '', '充值', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/chongzhi/v_index', $data);
		}
	}

	public function addchongzhi(){
		$flag = $this->op->checkUserAuthority('充值', $this->getSession('uid'));   //检测用户操作权限
		$data = array();
		if ($flag == 0) {
			echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '充值');
		} else {
			if($this->input->request('op') == 'addchongzhi'){
				$type = trim($this->input->post('type'));
				$money = trim($this->input->post('money'));
				$remark = trim($this->input->post('remark'));
				$url = trim($this->input->post('service_image'));
				$data = array();
				$data['type'] = $type;
				$data['money'] = $money;
				$data['remark'] = $remark;
				$data['ctime']=NOW;
				$data['url'] = $url;
				$ret = $this->chongzhi->addChongzhi($data);
				if(!$ret){
					exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'充值失败')));
				}
				$log = $this->op->actionData($this->getSession('name'), '充值', '', '充值', $this->getIP(), $this->getSession('uid'));
				exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '充值成功', array(), '充值 ', 'forward', OP_DOMAIN.'/chongzhi'));
			}else{
                $this->load->view('/chongzhi/v_addChongzhi');
            }
		}
	}
		public function editChongzhi(){
		$flag=$this->op->checkUserAuthority('充值',$this->getSession('uid'));
		$data = array();
		if($flag == 0){
			echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '充值');
		}else{

			if($this->input->request('op') == 'editchongzhi'){
				$id = trim($this->input->post('id'));
				$type = trim($this->input->post('type'));
				$money = trim($this->input->post('money'));
				$remark = trim($this->input->post('remark'));
				$url = trim($this->input->post('service_image'));
				$data = array();
				$data['type'] = $type;
				$data['money'] = $money;
				$data['remark'] = $remark;
				$data['url'] = $url;
				$ret = $this->chongzhi->updateChongzhiById($id, $data);
				if(!$ret){
					exit(json_encode(array('statusCode'=> self::AJ_RET_FAIL,'message'=>'修改信息失败')));
				}
				$log = $this->op->actionData($this->getSession('name'), '充值', '', '修改充值列表', $this->getIP(), $this->getSession('uid'));
				exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改充值列表 ', 'forward', OP_DOMAIN.'/chongzhi'));
			}else{
				$id = $this->uri->segment(3);
				if($id < 0 || !is_numeric($id)){
					exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
				}
				$rec= $this->chongzhi->getChongzhiById($id);
				$data['detail'] = $rec[0];
				$this->load->view('/chongzhi/v_editChongzhi', $data);
			}
		}
	}
	
		public function uploagimg($cid){
			$flag = $this->op->checkUserAuthority('充值', $this->getSession('uid'));   //检测用户操作权限
			if ($flag == 0) {
				echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '充值');
			} else {
				$contract = $this->chongzhi->getChongzhiByCid($cid);
				$data['cid'] = $cid;
				$data['bzjimg'] = $chongzhi['bzjimg'];
			}
			$this->load->view('/chongzhi/v_addChongzhi', $data);
		}
		
		public function delChongzhi(){
			$flag=$this->op->checkUserAuthority('充值',$this->getSession('uid'));
			$data = array();
			if($flag == 0){
				echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '充值');
			}else{
				$id = $this->uri->segment(3);
				$ret = $this->chongzhi->delChongzhiById($id);
				if(!$ret){
					exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
				}
			}
			$log = $this->op->actionData($this->getSession('name'), '充值列表', '', '删除充值券', $this->getIP(), $this->getSession('uid'));
			exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除充值券', 'forward', OP_DOMAIN.'/chongzhi'));
		}
	}