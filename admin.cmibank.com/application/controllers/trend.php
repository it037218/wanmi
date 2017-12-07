<?php
class trend extends Controller{ 
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '数据趋势'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_qs_log_model','qs_log');
    }
    public function index(){
    	$flag = $this->op->checkUserAuthority('数据趋势',$this->getSession('uid'));
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'数据趋势');
    	}else{
	    	$_stime = trim($this->input->post('stime'));
	    	$_etime = trim($this->input->post('etime'));	
    		if(!empty($_stime)){
	    		$stime = strtotime($_stime);
	    		$data['stime'] = $_stime;
	    	}else {
	    		$stime = strtotime(date('Y-m-d',strtotime('-30 day')));
	    		$data['stime'] = date('Y-m-d',strtotime('-30 day'));
	    	}
    		if(!empty($_etime)){
	    		$etime = strtotime($_etime)+86400;
	    		$data['etime'] = $_etime;
	    				
	    	}else{
	    		$etime = NOW;
	    		$data['etime'] = date('Y-m-d',NOW);
	    	} 
	    	
	    	$dataList = $this->qs_log->getListBetweenTime($stime,$etime);
	    	$sevenList = $this->qs_log->getListforSevendays($stime);
	    	$forteenList = $this->qs_log->getListforForteendays($stime);
	    	$_slist = array();
	    	$_flist = array();
	    	$_sevenList = array();
	    	$_forteenList = array();
	    	foreach ($sevenList as $value){
	    		$_sevenList[] = $value['withdraw'];
	    	}
	    	foreach ($forteenList as $value){
	    		$_forteenList[] = $value['withdraw'];
	    	}
	    	$_sevenList = array_reverse($_sevenList);
			$_forteenList = array_reverse($_forteenList);
	    	foreach ($dataList as $value){
	    		array_shift($_sevenList);
				array_push($_sevenList, $value['withdraw']);
				$_slist[] = round(array_sum($_sevenList)/7,2);
				
				array_shift($_forteenList);
				array_push($_forteenList, $value['withdraw']);
				$_flist[] = round(array_sum($_forteenList)/15,2);
	    	}
	    	$data['list'] = $dataList;
	    	$data['sevenList'] = $_slist;
	    	$data['forteenList'] = $_flist;
    		$this->load->view('/trend/v_index',$data);
    	}   	 
    }
}