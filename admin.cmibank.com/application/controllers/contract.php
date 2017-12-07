<?php
/**
 * 合同管理
 * * */
class contract extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '合同管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_contract_model', 'contract');
        $this->load->model('admin_creditor_infomation_model', 'creditor_infomation');
        $this->load->model('admin_corporation_model', 'corporation');
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_product_remit_model','product_remit');
         $this->load->model('admin_product_backmoney_model','product_backmoney');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('合同管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '合同管理');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data = array();
            $count=0;
            $contractList = array();
            $searchParam=array();
            if($this->input->request('op') == "search"){
                $corname = trim($this->input->post('searchcorname'));
                $con_number = trim($this->input->post('searchcon_number'));
                $repaymenttime_star = trim($this->input->post('searchrepaymenttime_star'));
                $repaymenttime_end = trim($this->input->post('searchrepaymenttime_end'));
                $interesttime_star = trim($this->input->post('searchinteresttime_star'));
                $interesttime_end = trim($this->input->post('searchinteresttime_end'));
                $searchmortgagor = trim($this->input->post('searchmortgagor'));
                $shenghe = trim($this->input->post('shenghe'));
                if(!empty($searchmortgagor)){
                	$searchParam['searchmortgagor']=$searchmortgagor;
                	$data['searchmortgagor']=$searchmortgagor;
                }
                if(!empty($corname)){
                	$searchParam['corname']=$corname;
                	$data['corname']=$corname;
                }
                if(!empty($con_number)){
                	$searchParam['con_number']=$con_number;
                	$data['con_number']=$con_number;
                }
                if(!empty($repaymenttime_star)){
                	$searchParam['repaymenttime_star']=$repaymenttime_star;
                	$data['repaymenttime_star']=$repaymenttime_star;
                }
                if(!empty($repaymenttime_end)){
                	$searchParam['repaymenttime_end']=$repaymenttime_end;
                	$data['repaymenttime_end']=$repaymenttime_end;
                }
                if(!empty($interesttime_star)){
                	$searchParam['interesttime_star']=$interesttime_star;
                	$data['interesttime_star']=$interesttime_star;
                }
                if(!empty($interesttime_end)){
                	$searchParam['interesttime_end']=$interesttime_end;
                	$data['interesttime_end']=$interesttime_end;
                }
                if(!empty($shenghe)){
                	$searchParam['shenghe'] = $shenghe;
                	$data['shenghe'] = $shenghe;
                
                }else{
                	$data['shenghe'] = 0;
                }
            }else{
            	$data['shenghe'] = 0;
            }
            $contractList = $this->contract->getContractByCondition($searchParam,$offset,$psize);
            if(!empty($contractList)){
            	$count = $this->contract->countContractByCondition($searchParam);
            	$this->load->model('admin_usercontract_model', 'usercontract');
            	$result = $this->usercontract->getcanUseUsercontract();
            	$usercontract = array();
            	foreach ($result as $_uc){
            		$usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
            	}
            	$sum_money = $this->contract->sumContractByCondition($searchParam);
            	$data['sum_money']=$sum_money;
            	$data['usercontract'] = $usercontract;
            	$data['count']=$count;

            	$data['pageNum']    = $page;
            	$data['numPerPage'] = $psize;
                foreach($contractList as $k=>$v){
                    //获取债权人信息
                  //  var_dump($v['creid']);
                    if($v['creid']){

                        $creditor = $this->creditor_infomation->getInformationByid($v['creid']);
                        $contractList[$k]['creditor']=$creditor['creditor'];
                        $contractList[$k]['identity']=$creditor['identity'];
                        $contractList[$k]['seal']=$creditor['seal'];
                    }else{
                        $contractList[$k]['creditor']="";
                        $contractList[$k]['identity']='';
                        $contractList[$k]['seal']='';
                    }
                    //获取担保法人
                    $corportion = $this->corporation->getCorporationByCid($v['corid']);
                    $contractList[$k]['guar_corp']=$corportion['guar_corp'];
                    $contractList[$k]['guarantee']=$corportion['guarantee'];

                }
             //   exit;
            //    var_dump($contractList);exit;
                $data['list']=$contractList;

            }else{
            	$data['count']=0;
            	$data['list']=array();
            	$data['pageNum']= 0;
            	$data['numPerPage'] = 0;
            	$data['usercontract']=array();
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1031');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '合同管理', $this->getIP(), $this->getSession('uid'));
            $data['uid']=$this->getSession('uid');
            $this->load->view('/contract/v_index', $data);
        }
    }
    
    public function addcontract(){
        $flag = $this->op->checkUserAuthority('合同管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '添加合同');
        } else {
            if($this->input->request('op') == 'addcontract'){
                $corid =trim($this->input->post('corid'));
                $this->load->model('admin_corporation_model', 'corporation');
               $ret_data = $this->corporation->getCorporationByCid($corid);
//                if(!$ret_data['corid']){
//                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'错误的公司名称')));
//                }
             //   $corid = $ret_data['corid'];
                $cname = $ret_data['cname'];
                $con_number = trim($this->input->post('con_number'));
                $con_money = trim($this->input->post('con_money'));
                $con_income = trim($this->input->post('con_income'));
                $interesttime = trim($this->input->post('interesttime'));
                $repaymenttime = trim($this->input->post('repaymenttime'));
                $object_overview = trim($this->input->post('object_overview'));
                $object_desc = trim($this->input->post('object_desc'));
                $object_img = trim($this->input->post('object_img'));
                $capital_overview = trim($this->input->post('capital_overview'));
                $capital_desc = trim($this->input->post('capital_desc'));
                $capital_img = trim($this->input->post('capital_img'));
                $ucid = trim($this->input->post('ucid'));
                $con_bzjbl = trim($this->input->post('con_bzjbl'));
                $remark = trim($this->input->post('remark'));
                $creid=trim($this->input->post('creid'));
                $mortgagor=trim($this->input->post('mortgagor'));

                $data['mortgagor']=$mortgagor;
                $data['creid']=$creid;
                $data['corid'] = $corid;
                $data['corname'] = $cname;
                $data['con_number'] = $con_number;
                $data['con_money'] = $con_money;
                $data['real_money'] = $con_money;
                $data['con_income'] = $con_income;
                $data['interesttime'] = $interesttime;
                $data['repaymenttime'] = $repaymenttime;
                $data['object_overview'] = $object_overview;
                $data['object_desc'] = $object_desc;
                $data['object_img'] = $object_img;
                $data['capital_overview'] = $capital_overview;
                $data['capital_desc'] = $capital_desc;
                $data['capital_img'] = $capital_img;
                $data['remark'] = $remark;
                $data['con_bzjbl'] = $con_bzjbl; //保证金比例
                $data['ucid'] = $ucid;          //用户模板ID
                $data['ctime'] = time();        //创建时间
                $ret = $this->contract->addcontract($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加合同失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '添加合同信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加成功', array(), '添加合同信息 ', 'forward', OP_DOMAIN.'/contract'));
            }else{
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getcanUseUsercontract();
                $data['company']=$this->corporation->getAllCorporation();
                $data['creditorList']= $this->creditor_infomation->creditorList();
                $this->load->view('/contract/v_addcontract', $data);
            }
        }
    }
   
    public function shengheBack(){
    			$cid = $this->uri->segment(3);
    			if(!$cid){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$data['shenghe']=0;
    			$ret = $this->contract->updateContract($cid, $data);
    			if(!$ret){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
    			}
    			$log = $this->op->actionData($this->getSession('name'), '修改合同信息', '', '修改合同信息', $this->getIP(), $this->getSession('uid'));
    			exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '审核合同成功', array(), '审核合同 ', 'forward', OP_DOMAIN.'/contract'));
    }
    		
    public function shenghe() {
    	$flag = $this->op->checkUserAuthority('合同管理', $this->getSession('uid'));   //检测用户操作权限
    	$data = array();
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
    	} else {
    		if($this->input->request('op') == 'shenghe'){
    			$cid = $this->input->post('cid');
    			if(!$cid){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$data['shenghe']=1;
    			$ret = $this->contract->updateContract($cid, $data);
    			if(!$ret){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
    			}
    			$log = $this->op->actionData($this->getSession('name'), '修改合同信息', '', '修改合同信息', $this->getIP(), $this->getSession('uid'));
    			exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '审核合同成功', array(), '审核合同 ', 'forward', OP_DOMAIN.'/contract'));
    		}else{
    			$cid = $this->uri->segment(3);
    			if($cid < 0 || !is_numeric($cid)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$contraceInfo = $this->contract->getcontractByCid($cid);
    			$data['detail'] = $contraceInfo;
    			$this->load->model('admin_usercontract_model', 'usercontract');
    			$data['usercontract'] = $this->usercontract->getcanUseUsercontract();
    			$data['diff_day'] = $this->diff_days(strtotime($contraceInfo['interesttime']), strtotime($contraceInfo['repaymenttime']));
    
    			$data['company']=$this->corporation->getAllCorporation();
    			$data['creids']=$this->creditor_infomation->getInformationByCorporationid($contraceInfo['corid']);
    			$creditor= $this->creditor_infomation->getInformationByid($contraceInfo['creid']);
    			//判断公司下是否有印章
    			$data['creditorList']= $this->creditor_infomation->creditorList();
    			// var_dump($contraceInfo);exit;
    			if(!empty($creditor)){
    				$data['creditor']=$creditor;
    			}else{
    				$data['creditor']=array();
    			}
    			$this->load->view('/contract/v_shenghe', $data);
    		}
    	}
    }
    
    public function editcontract() {
        $flag = $this->op->checkUserAuthority('合同管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑产品');
        } else {
            if($this->input->request('op') == 'editcontract'){

                $cid = $this->input->post('cid');
                if(!$cid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $this->load->model('admin_corporation_model', 'corporation');
                $corid = trim($this->input->post('corid'));
                
                $productList = $this->product->getProductListByCid($cid,'','','');
//                 if(!empty($productList)){
//                 	exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'该合同已建立产品，禁止修改')));
//                 }

                $ret_data = $this->corporation->getCorporationByCid($corid);
              //  var_dump($ret_data);exit;
                $corname=$ret_data['cname'];

                $con_number = trim($this->input->post('con_number'));
                $con_money = trim($this->input->post('con_money'));
                $con_income = trim($this->input->post('con_income'));
                $interesttime = trim($this->input->post('interesttime'));
                $repaymenttime = trim($this->input->post('repaymenttime'));
                $object_overview = trim($this->input->post('object_overview'));
                $object_desc = trim($this->input->post('object_desc'));
                $object_img = trim($this->input->post('object_img'));
                $capital_overview = trim($this->input->post('capital_overview'));
                $capital_desc = trim($this->input->post('capital_desc'));
                $capital_img = trim($this->input->post('capital_img'));
                $ucid = trim($this->input->post('ucid'));
                $con_bzjbl = trim($this->input->post('con_bzjbl'));
                $remark = trim($this->input->post('remark'));
                $creid=$this->input->post('creid');
                $mortgagor=trim($this->input->post('mortgagor'));
                
                $data['mortgagor']=$mortgagor;
                $data['creid']=$creid;
                $data['corid'] = $corid;
                $data['corname'] = $corname;
                $data['con_number'] = $con_number;
                $data['real_money'] = $con_money;
                $data['con_money'] = $con_money;
                $data['con_income'] = $con_income;
                $data['interesttime'] = $interesttime;
                $data['repaymenttime'] = $repaymenttime;
                $data['object_overview'] = $object_overview;
                $data['object_desc'] = $object_desc;
                $data['object_img'] = $object_img;
                $data['capital_overview'] = $capital_overview;
                $data['capital_desc'] = $capital_desc;
                $data['capital_img'] = $capital_img;
                $data['remark'] = $remark;
                $data['con_bzjbl'] = $con_bzjbl; //保证金比率
                $data['ucid'] = $ucid;          //用户模板ID
                $data['ctime'] = time();        //创建时间
                $data['shenghe'] = 0;
                $ret = $this->contract->updateContract($cid, $data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '修改合同信息', '', '修改合同信息', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改合同信息成功', array(), '修改合同信息 ', 'forward', OP_DOMAIN.'/contract'));
            }else{
                $cid = $this->uri->segment(3);
                if($cid < 0 || !is_numeric($cid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $contraceInfo = $this->contract->getcontractByCid($cid);
                $data['detail'] = $contraceInfo;
                $this->load->model('admin_usercontract_model', 'usercontract');
                $data['usercontract'] = $this->usercontract->getcanUseUsercontract();
                $data['diff_day'] = $this->diff_days(strtotime($contraceInfo['interesttime']), strtotime($contraceInfo['repaymenttime']));

                $data['company']=$this->corporation->getAllCorporation();
                $data['creids']=$this->creditor_infomation->getInformationByCorporationid($contraceInfo['corid']);
                $creditor= $this->creditor_infomation->getInformationByid($contraceInfo['creid']);
                //判断公司下是否有印章
                $data['creditorList']= $this->creditor_infomation->creditorList();
               // var_dump($contraceInfo);exit;
                if(!empty($creditor)){
                    $data['creditor']=$creditor;
                }else{
                    $data['creditor']=array();
                }


              //  $data['creditor']=$this->creditor_information->
//                 if($data['detail']['status'] == 1){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已开售合同不能修改')));
//                 }else if($data['detail']['status'] == 2){
//                     exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'该合同已经被关闭，请启用之后再编辑')));
//                 }
                $this->load->view('/contract/v_editontract', $data);
            }
        }
    }
    
    public function showcontractdetail() {
    			$cid = $this->uri->segment(3);
    			if($cid < 0 || !is_numeric($cid)){
    				exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
    			}
    			$contraceInfo = $this->contract->getcontractByCid($cid);
    			$data['detail'] = $contraceInfo;
    			$this->load->model('admin_usercontract_model', 'usercontract');
    			$data['usercontract'] = $this->usercontract->getcanUseUsercontract();
    			$data['diff_day'] = $this->diff_days(strtotime($contraceInfo['interesttime']), strtotime($contraceInfo['repaymenttime']));
    
    			$data['company']=$this->corporation->getAllCorporation();
    			$data['creids']=$this->creditor_infomation->getInformationByCorporationid($contraceInfo['corid']);
    			$creditor= $this->creditor_infomation->getInformationByid($contraceInfo['creid']);
    			//判断公司下是否有印章
    			$data['creditorList']= $this->creditor_infomation->creditorList();
    			// var_dump($contraceInfo);exit;
    			if(!empty($creditor)){
    				$data['creditor']=$creditor;
    			}else{
    				$data['creditor']=array();
    			}
    			$this->load->view('/contract/v_contract', $data);
    		}
    
    public function getContractByCorid($corid){
        
        $data = $this->contract->getContractByCorid($corid);
        $rtn = array();
        foreach ($data as $key => $val){
            $rtn[$key][0] = $val['cid'];
            if(!empty($val['remark'])){
                $rtn[$key][1] = $val['con_number']."--备注：".$val['remark'];
            }else{
                $rtn[$key][1] = $val['con_number'];
            }
            
        }
        echo json_encode($rtn);
        exit;
    }
    
    
    public function getContractByCid($cid){
        $data = $this->contract->getContractByCid($cid);
        echo json_encode($data);
        exit;
    }
    
    public function delcontract($cid){
        $flag = $this->op->checkUserAuthority('合同管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '删除合同');
        } else {
            $data = $this->contract->getContractByCid($cid);
            if(empty($data)){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'不存在的合同ID')));
            }
            if($data['money'] != 0 ){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已开售合同不能删除')));
            }
            $this->contract->delContract($cid);
            $log = $this->op->actionData($this->getSession('name'), '删除合同信息', '', '删除合同信息', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除合同信息成功', array(), '删除合同信息 ', 'forward', OP_DOMAIN.'/contract'));
            
        }
    }

    public function bond() {
        $flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '保证金');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            if($this->input->request('op') == "search"){
                $corname = trim($this->input->post('searchcorname'));
                $con_number = trim($this->input->post('searchcon_number'));
                $repaymentstime = trim($this->input->post('searchrepaymentstime'));
                $repaymentetime = trim($this->input->post('searchrepaymentetime'));
                $intereststime = trim($this->input->post('intereststime'));
                $interestetime = trim($this->input->post('interestetime'));
                $remitstime = trim($this->input->post('remitstime'));
                $remitetime = trim($this->input->post('remitetime'));
                $status = trim($this->input->post('status'));
                $params = array();
                if(!empty($corname)){
                	$params['corname']=$corname;
                }
                if(!empty($con_number)){
                	$params['con_number']=$con_number;
                }
                if(!empty($repaymentstime)){
                	$params['repaymentstime']=$repaymentstime;
                }
                if(!empty($repaymentetime)){
                	$params['repaymentetime']=$repaymentetime;
                }
                if(!empty($intereststime)){
                	$params['intereststime']=$intereststime;
                }
                if(!empty($interestetime)){
                	$params['interestetime']=$interestetime;
                }
                if(!empty($remitstime)){
                	$params['remitstime']=strtotime($remitstime);
                }
                if(!empty($remitetime)){
                	$params['remitetime']=strtotime($remitetime)+86400;
                }
                if(!empty($status)){
                	$params['status']=$status;
                }
                
                $contract = $this->contract->getContractlistsql($params,array($psize, $offset));
                $data['list'] = $contract;
                $temp_contract = $this->contract->getContractlistsql($params);
                $total = 0;
                foreach ($temp_contract as $temp){
                	$total = $total+($temp['con_money']*$temp['con_bzjbl'])/100;
                }
                $count = count($temp_contract);
                $this->load->model('admin_usercontract_model', 'usercontract');
                $result = $this->usercontract->getcanUseUsercontract();
                $usercontract = array();
                foreach ($result as $_uc){
                    $usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
                }
                $data['usercontract'] = $usercontract;
                $data['searchcorname'] = $corname;
                $data['searchcon_number'] = $con_number;
                $data['searchrepaymentstime'] = $repaymentstime;
                $data['searchrepaymentetime'] = $repaymentetime;
                $data['intereststime'] = $intereststime;
                $data['interestetime'] = $interestetime;
                $data['remitstime'] = $remitstime;
                $data['remitetime'] = $remitetime;
                $data['status'] = $status;
                $data['total'] = $total;
            }else{
                $data['list'] = $this->contract->getContractList('', 'interesttime desc', array($psize, $offset));
                $count = $this->contract->getContractCount();
                $this->load->model('admin_usercontract_model', 'usercontract');
                $result = $this->usercontract->getcanUseUsercontract();
                $usercontract = array();
                foreach ($result as $_uc){
                    $usercontract[$_uc['ucid']] = $_uc['tplname'] . '-' . $_uc['tplnumber'];
                }
                $data['usercontract'] = $usercontract;
                $data['status'] = 0;
            }
            if($count>0){
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'contract/index?page=' . $page;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1100');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            //user action log
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $log = $this->op->actionData($this->getSession('name'), '保证金', '', '保证金', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/contract/v_bood', $data);
        }
    }
    public function getBonddetail($cid){
        $flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '保证金');
        } else {
           $product_remit = array();
           $product = $this->product->getProductList(array('cid'=>$cid),'','');
           $contract = $this->contract->getContractByCid($cid);
           $backmoney = $this->product_backmoney->getProductBackmoneyList(array('cid'=>$cid),'','');
           foreach ($product as $key=>$val){
               $product_remit[] = $this->product_remit->getProductRemitByPid($val['pid']);
           }
           $data['list'] = $product_remit; 
           $data['contract'] = $contract;
           $data['backmoney'] = $backmoney;
        }
        $this->load->view('/contract/v_booddetail', $data);
    }
    
    public function uploagimg($cid){
    	$flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '保证金');
    	} else {
    		$contract = $this->contract->getContractByCid($cid);
    		$data['cid'] = $cid;
    		$data['bzjimg'] = $contract['bzjimg'];
    	}
    	$this->load->view('/contract/v_uploadimg', $data);
    }
    
    public function uploagreturnimg($cid){
    	$flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '保证金');
    	} else {
    		$contract = $this->contract->getContractByCid($cid);
    		$data['cid'] = $cid;
    		$data['returnbzjimg'] = $contract['returnbzjimg'];
    	}
    	$this->load->view('/contract/v_uploadreturnimg', $data);
    }
    
    public function uploadimage(){
    	$flag = $this->op->checkUserAuthority('保证金', $this->getSession('uid'));   //检测用户操作权限
    	if ($flag == 0) {
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限', array(), '保证金');
    	} else {
    		 $cid = trim($this->input->post('cid'));
             $bzjimg = trim($this->input->post('bzjimg'));
             $returnbzjimg = trim($this->input->post('returnbzjimg'));
             if(!empty($bzjimg)){
             	$data['bzjimg'] = $bzjimg;
             	$ret = $this->contract->updateContract($cid, $data);
             	if(!$ret){
             		exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'上次失败')));
             	}
             }
             else if(!empty($returnbzjimg)){
             	$data['returnbzjimg'] = $returnbzjimg;
             	$ret = $this->contract->updateContract($cid, $data);
             	if(!$ret){
             		exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'上次失败')));
             	}
             }
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '上传成功', array(), '上传凭证 ', 'forward', OP_DOMAIN.'/contract/bond'));
    	}
    }
    
    private function diff_days($start, $now){
        $a_dt=getdate($start);
        $b_dt=getdate($now);
        $a_new=mktime(12,0,0,$a_dt['mon'],$a_dt['mday'],$a_dt['year']);
        $b_new=mktime(12,0,0,$b_dt['mon'],$b_dt['mday'],$b_dt['year']);
        return abs(($a_new-$b_new)/86400-1);
    }
    //根据债权人ID获取信息
    public function getContrctByCreid(){
        $creid = $this->input->post('creid');
        $ret = $this->contract->getContrctByCreid($creid);
        if($ret){
            echo json_encode(array('code'=>1));
        }else{
            //删除该债权人
            $creditor= $this->creditor_infomation->delete($creid);
            if($creditor){
                echo json_encode(array('code'=>2));
            }else{
                echo json_encode(array('code'=>3));
            }

        }
    }
}