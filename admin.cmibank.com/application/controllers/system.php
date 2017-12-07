<?php
class System extends Controller {

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		if(false == $this->menu){
			redirect(OP_DOMAIN.'login','location');
		}else{
			foreach($this->menu AS $key=>$value){
				if($value['name']=='系统管理'){
					$this->submenu = $value['submenu'];
				}
			}
		}
	}

	/**
	 * 我的账号
	 */
	public function index(){
		$flag = $this->op->checkUserAuthority('我的账号', $this->getSession('uid'));   //检测用户操作权限
		if($flag==0){
			$this->Error('没有权限',OP_DOMAIN.'system');
		}else{
		    
		    $page = $this->input->request('pageNum') ? $this->input->request('pageNum') : 1;
		    $psize = $this->input->request('numPerPage') ? $this->input->request('numPerPage') : 20;

			$data         = $this->getDefaultData($flag , array('系统管理','我的账号'));
			$data['name'] = $this->getSession('name');
			$data['lastLoginTime']  = $this->getSession('lastLoginTime');
			$data['loginTimes'] 	= $this->getSession('loginTimes');
			$data['createTime'] 	= $this->getSession('createTime');
			$data['realname'] 		= $this->getSession('realname');
			$data['group'] 			= $this->getSession('group');
			
			$count = count($this->op->getLoginRecord(array("`empId`"=>$this->getSession('uid'))));
			$maxpage = $count/$psize;
			$page = $page > $maxpage ? $maxpage : $page;
			
			$offset = ($page - 1) * $psize;
	
			$data['pageNum']    = $page;
			$data['numPerPage'] = $psize;
			$data['count'] = $count;
			$data['rel'] = OP_DOMAIN . 'system/index';
			
			if($count > 0){
				$offset = ($page - 1) * $psize;
				$data['list'] = $this->op->getLoginRecord(array("`empId`"=>$this->getSession('uid')),'loginTime DESC',array($psize,$offset));
// 				$this->load->library('pagination');
// 				$param = array(
// 				    'base_url' => OP_DOMAIN.'/system/index/',
// 				    'page' => $page,
// 					'total_rows' => $count,
// 					'per_page'   => $psize
// 				);
// 				$data['page'] = $this->pagination->login_record($param , 1);
			}else{
				$data['list'] = $data['page'] = '';
			}
			$data['loginTimes'] = $count;
            //user action log
            $log = $this->op->actionData($this->getSession('name'),'登录后台我的账号', '' ,'我的账号',$this->getIP(),$this->getSession('uid'));
			$this->load->view('/system/v_account', $data);
		}
	}

	/**
	 * 密码修改
	 */
	public function editpass(){
		$flag = $this->op->checkUserAuthority('密码修改', $this->getSession('uid'));   //检测用户操作权限
		if($flag==0){
		    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,  '没有权限', array(), '系统管理');
		    exit;
			
		}else{
			if(false!=$this->input->post('op') && $this->input->post('op')=='password'){
				$old_pass = htmlspecialchars($this->input->post('oldpass'));
				$new_pass = htmlspecialchars($this->input->post('newpass'));
				$check_pass = htmlspecialchars($this->input->post('checkpass'));

				if($new_pass != $check_pass){
				    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,  '修改失败：二次密码输入不一致。', array(), '系统管理');
				    exit;
				}else{
					$checkflag = $this->op->getManagerAccount(array('`id`'=>$this->getSession('uid'),'`password`'=>md5($old_pass)));
					if(!empty($checkflag)){
						$data = array(
						'password' => md5($new_pass)
						);
						$r = $this->op->updateManagerInfo($data, $this->getSession('uid'));
                        //user action log
                        $log = $this->op->actionData($this->getSession('name'),'修改密码用户'.$this->getSession('uid'), $this->getSession('uid') ,'我的账号',$this->getIP(),$this->getSession('uid'));
						if($r){
							echo $this->ajaxDataReturn(self::AJ_RET_SUCC,  '密码修改成功', array(), '系统管理');
                            exit;
						}else{
							echo $this->ajaxDataReturn(self::AJ_RET_FAIL,  '密码修改失败', array(), '系统管理');
                            exit;
						}
					}else{
					    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,  '修改失败：原密码不对', array(), '系统管理');
					    exit;
					}
				}
			}else{
				$data['menu'] = $this->menu;
				$data['submenu'] = $this->submenu;
				$data['managerInfo'] = $this->manager_info;
				$data['flag'] = $flag;
				$data['name'] = $this->getSession('name');

				$data['nav'] = array('系统管理','密码修改');
                                 //user action log
                                $log = $this->op->actionData($this->getSession('name'),'查看密码修改用户'.$this->getSession('uid'), $this->getSession('uid') ,'密码修改',$this->getIP(),$this->getSession('uid'));
				$this->load->view('/system/v_editpass', $data);
			}
		}
	}
}