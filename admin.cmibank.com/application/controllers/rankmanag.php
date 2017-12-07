<?php
/**
 *用户取现记录
 * * */
class rankmanag extends Controller{ 
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '添加积分'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_activity_model', 'admin_activity_model');
        $this->load->model('admin_jifeng_model', 'admin_jifeng_model');
    }
    public function index(){
    	$flag = $this->op->checkUserAuthority('添加积分',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'添加积分');
    	}else{
    		if($this->input->request('op') == 'search'){
    			$phone = trim($this->input->post('phone'));
    			$score = trim($this->input->post('score'));
    			$type = trim($this->input->post('type'));
    			$this->load->model('admin_account_model', 'account');
    			$uid = $this->account->getUidByAccount($phone);
    			$typeDesc = '积分奖励';
    			if($type==2){
    				$typeDesc = '积分补偿';
    			}
    			if(!empty($uid)){
	    			$this->admin_activity_model->set_activity_rank_with_actid(2,$phone,$score);
	    			$jifeng_data = array(
	    					'uid' => $uid[0]['uid'],
	    					'name' => $typeDesc,
	    					'action' => 5,
	    					'value' => $score,
	    					'ctime' => NOW
	    			);
	    			$this->admin_jifeng_model->addJifeng($uid[0]['uid'],$jifeng_data);
    			}
    		}
    		$edatable = $this->op->getEditable($this->getSession('uid'),'6329');
    		if(!empty($edatable)){
    			$data['editable'] = $edatable[0]['editable'];
    		}else{
    			$data['editable']=0;
    		}
    		$this->load->view('/rankmanag/v_index',$data);
    	}   	 
    }
}