<?php
/**
 *version管理
 * * */
class repayment extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '更新管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_contract_model', 'contract');
        $this->load->model('admin_ptype_model','ptype');
    }
    
    public function index($page=1){
        $flag = $this->op->checkUserAuthority('今日还款列表',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'今日还款列表');
        }else{
        	$data=array();
            $searchcorname = trim($this->input->post('searchcorname'));
            $searchcon_number = trim($this->input->post('searchcon_number'));
            $searchpname = trim($this->input->post('searchpname'));
            if($this->input->request('op') == "search"){
	            $data['searchpname']=$searchpname;
	            $data['searchcon_number']=$searchcon_number;
	            $data['searchcorname']=$searchcorname;
                $contract = $this->contract->getContractlistWhere($searchcorname,$searchcon_number,'请输入开始日期','请输入结束日期');
                foreach ($contract as $val){
                    $rtncontractcid[] = $val['cid'];
                    $rtncontractcorid[] = $val['corid'];
                }
                $cid = implode(',', $rtncontractcid);
                $corcid = implode(',', $rtncontractcorid);
                $productList = $this->product->getTodayRepaymentWhere($searchpname,$cid,$corcid);
                
                foreach ($productList as $key=>$_p){
                    if(!isset($rtnproductlist['count_sellmoney'])){
                        $rtnproductlist['count_sellmoney'] = 0;
                    }
                    $ptype = $this->ptype->getPtypeByPtid($_p['ptid']);
                    if(!isset($rtnproductlist[$ptype['name']]['sellmoney'])){
                        $rtnproductlist[$ptype['name']]['sellmoney'] = 0;
                    }
                    $rtnproductlist[$ptype['name']]['sellmoney'] += $_p['sellmoney'];
                    $rtnproductlist['count_sellmoney'] += $_p['sellmoney'];
                }
                $data['rtnproduct'] = $rtnproductlist;

            }else{
                $productList = $this->product->getTodayRepayment();
            }
            $this->load->model('admin_ptype_model', 'ptype');
            $ptype = $this->ptype->getPtypeList();
            $ptype_list = array();
            foreach ($ptype as $_val){
                $ptype_list[$_val['ptid']] = $_val['name'];
            }
            $contract_list =  $this->contract->getContractList('','','');
            foreach ($contract_list as $val){
                $contract_list[$val['cid']]['corname'] = $val['corname'];
                $contract_list[$val['cid']]['con_number'] = $val['con_number'];
                $contract_list[$val['cid']]['con_income'] = $val['con_income'];
            }
            $data['ptype_list'] = $ptype_list;
            $data['contract_list'] = $contract_list;
            $data['list'] = $productList;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1111');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '更新管理', '', '今日还款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/repayment/v_index', $data);
        }
    }
    
    public function no_repayment(){
        $flag = $this->op->checkUserAuthority('未还款列表',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'未还款列表');
        }else{
            $this->load->model('admin_ptype_model', 'ptype');
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            
            $searchcorname = trim($this->input->post('searchcorname'));
            $searchcon_number = trim($this->input->post('searchcon_number'));
            $searchpname = trim($this->input->post('searchpname'));
            $startcietime = trim($this->input->post('startcietime'));
            $endcietime = trim($this->input->post('endcietime'));
            
            if($searchcorname == "请输入搜索内容"){$searchcorname =null;}
            if($searchcon_number == "请输入搜索内容"){$searchcon_number =null;}
            if($searchpname == "请输入搜索内容"){$searchpname =null;}
            if($startcietime == "请输入搜索内容"){$startcietime =null;}
            if($endcietime == "请输入搜索内容"){$endcietime =null;}
            
            if($this->input->request('op') == "search"){
                $contract = $this->contract->getContractlistWhere($searchcorname,$searchcon_number,'请输入开始日期','请输入结束日期');
                foreach ($contract as $val){
                    $rtncontractcid[] = $val['cid'];
                    $rtncontractcorid[] = $val['corid'];
                }
                $cid = implode(',', $rtncontractcid);
                $corcid = implode(',', $rtncontractcorid);
                
                $productList = $this->product->getNoRepaymentWhere($searchpname,$startcietime,$endcietime,$cid,$corcid);
                
                $data['searchpname'] = $searchpname;
                $data['startcietime'] = $startcietime;
                $data['endcietime']    = $endcietime;
                $data['searchcorname'] = $searchcorname;
                $data['searchcon_number'] = $searchcon_number;
                $data['none'] = 'none';
                foreach ($productList as $key=>$_p){
                    if(!isset($rtnproductlist['count_sellmoney'])){
                        $rtnproductlist['count_sellmoney'] = 0;
                    }
                    $ptype = $this->ptype->getPtypeByPtid($_p['ptid']);
                    if(!isset($rtnproductlist[$ptype['name']]['sellmoney'])){
                        $rtnproductlist[$ptype['name']]['sellmoney'] = 0;
                    }
                    $rtnproductlist[$ptype['name']]['sellmoney'] += $_p['sellmoney'];
                    $rtnproductlist['count_sellmoney'] += $_p['sellmoney'];
                }
                $data['rtnproduct'] = $rtnproductlist;
                
            }else{
                $productList = $this->product->getNoRepayment(array($psize , $offset));
            }
            $count = $this->product->CountNoRepayment();
            $ptype = $this->ptype->getPtypeList();
            $ptype_list = array();
            foreach ($ptype as $_val){
                $ptype_list[$_val['ptid']] = $_val['name'];
            }
            $contract_list =  $this->contract->getContractList('','','');
            foreach ($contract_list as $val){
                $contract_list[$val['cid']]['corname'] = $val['corname'];
                $contract_list[$val['cid']]['con_number'] = $val['con_number'];
                $contract_list[$val['cid']]['con_income'] = $val['con_income'];
            }
            
            $data['ptype_list'] = $ptype_list;
            $data['contract_list'] = $contract_list;
            $data['list'] = $productList;
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1120');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '更新管理', '', '今日还款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/repayment/v_no_repayment', $data);
        }
    }
    
    public function repaymented(){
        $flag = $this->op->checkUserAuthority('已还款列表',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'已还款列表');
        }else{
            $this->load->model('admin_ptype_model', 'ptype');
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $searchcorname = trim($this->input->post('searchcorname'));
            $searchcon_number = trim($this->input->post('searchcon_number'));
            $searchpname = trim($this->input->post('searchpname'));
            $startcietime = trim($this->input->post('startcietime'));
            $endcietime = trim($this->input->post('endcietime'));
            
            if($searchcorname == "请输入搜索内容"){$searchcorname =null;}
            if($searchcon_number == "请输入搜索内容"){$searchcon_number =null;}
            if($searchpname == "请输入搜索内容"){$searchpname =null;}
            if($startcietime == "请输入搜索内容"){$startcietime =null;}
            if($endcietime == "请输入搜索内容"){$endcietime =null;}
            
            if($this->input->request('op') == "search"){
                $contract = $this->contract->getContractlistWhere($searchcorname,$searchcon_number,'请输入开始日期','请输入结束日期');
                foreach ($contract as $val){
                    $rtncontractcid[] = $val['cid'];
                    $rtncontractcorid[] = $val['corid'];
                }
                $cid = implode(',', $rtncontractcid);
                $corcid = implode(',', $rtncontractcorid);
                $productList = $this->product->getRepaymentedwhere($searchpname,$startcietime,$endcietime,$cid,$corcid);
                $data['searchpname'] = $searchpname;
                $data['startcietime'] = $startcietime;
                $data['endcietime']    = $endcietime;
                $data['searchcorname'] = $searchcorname;
                $data['searchcon_number'] = $searchcon_number;
                $data['none'] = 'none';
                foreach ($productList as $key=>$_p){
                    if(!isset($rtnproductlist['count_sellmoney'])){
                        $rtnproductlist['count_sellmoney'] = 0;
                    }
                    $ptype = $this->ptype->getPtypeByPtid($_p['ptid']);
                    if(!isset($rtnproductlist[$ptype['name']]['sellmoney'])){
                        $rtnproductlist[$ptype['name']]['sellmoney'] = 0;
                    }
                    $rtnproductlist[$ptype['name']]['sellmoney'] += $_p['sellmoney'];
                    $rtnproductlist['count_sellmoney'] += $_p['sellmoney'];
                }
                $data['rtnproduct'] = $rtnproductlist;
                
            }else{
                $productList = $this->product->getRepaymented(array($psize , $offset));
            }
            $count = $this->product->CountRepaymented();
            $ptype = $this->ptype->getPtypeList();
            $ptype_list = array();
            foreach ($ptype as $_val){
                $ptype_list[$_val['ptid']] = $_val['name'];
            }
            $contract_list =  $this->contract->getContractList('','','');
            foreach ($contract_list as $val){
                $contract_list[$val['cid']]['corname'] = $val['corname'];
                $contract_list[$val['cid']]['con_number'] = $val['con_number'];
                $contract_list[$val['cid']]['con_income'] = $val['con_income'];
            }
            $data['ptype_list'] = $ptype_list;
            $data['contract_list'] = $contract_list;
            $data['list'] = $productList;
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1121');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '更新管理', '', '今日还款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/repayment/v_repaymented', $data);
        }
    }
    
    
    
    
    public function productUserList($pid,$createtime){
        $flag = $this->op->checkUserAuthority('今日还款列表',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'今日还款列表');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $productDetail = $this->product->getProductByPid($pid);
            $this->load->model('admin_repayment_log_model', 'repayment_log');
            $where = array('pid' => $pid);
            $count = $this->repayment_log->countLogsByPid($pid,$createtime);
            $productBuyInfo = $this->repayment_log->getLogsLimitByPid($pid,$createtime,$psize, $offset);
//             print_r($productBuyInfo);
            $count_money = $this->repayment_log->sumMoneyLogsByPid($pid,$createtime);
            $count_profit = $this->repayment_log->sumProfitLogsByPid($pid,$createtime);
            $uids = array();
            foreach ($productBuyInfo as $key => $_subinfo){
                if(!in_array($_subinfo['uid'], $uids)){
                    $uids[] = $_subinfo['uid'];
                }
                $productBuyInfo[$key]['profit'] = $_subinfo['profit'];
            }
            $this->load->model('admin_useridentity_model', 'user_identity');
            $user_identity_info = $this->user_identity->getUserIdentityList(array('uid' => $uids));
//             $this->load->model('admin_account_model', 'account');
//             $user_account_info = $this->account->getAccountByUid(array('uid' => $uids));
//             $user_account_array = array();
//             foreach ($user_account_info as $account){
//                 $user_account_array[$account['uid']] = $account['account'];
//             }
            $user_identity_array = array();
            foreach ($user_identity_info as $identity){
                $user_identity_array[$identity['uid']] = $identity;
            }
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['pid'] = $pid;
            $data['list'] = $productBuyInfo;
            $data['count_money'] = $count_money;
            $data['count_profit'] = $count_profit;
//            $data['user_account'] = $user_account_array;
            $data['user_identity'] = $user_identity_array;
            $data['productDetail'] = $productDetail;
            $data['createtime'] = $createtime;
            $log = $this->op->actionData($this->getSession('name'), '更新管理', '', '今日还款列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/repayment/product_user_list', $data);
        }
    }
    
    public function create_repayment_order($pid){
        $flag = $this->op->checkUserAuthority('今日还款列表',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'今日还款列表');
        }else{
            
            //创建锁
            if($this->product->getRepaymentLock($pid)){
                echo $this->ajaxDataReturn(self::AJ_RET_SUCC,'已生成还款列表(RD)',array(),'今日还款列表');
                exit;
            }else{
                $this->product->addrepaymentlock($pid);
            }
            
            $_product = $this->product->getProductByPid($pid);
            if($_product['repaytime'] != 0){
                echo $this->ajaxDataReturn(self::AJ_RET_SUCC,'已生成还款列表(DB)',array(),'今日还款列表');
                exit;
            }
            $this->load->model('admin_product_buy_model', 'product_buy');
            $buy_info = $this->product_buy->getProductBuyInfoByPid(array('pid' => $pid));
            if(empty($buy_info)){//无购买用户 的产品  到期了还是设置为回款
                $data = array('status' => 7, 'repaytime' => time());        //回款
                $this->product->updateProductStatus($pid, $data);
                echo $this->ajaxDataReturn(self::AJ_RET_SUCC,'此产品还款完成',array(),'还款列表');
                exit;
            }
            $days = ((strtotime($_product['uietime']) - strtotime($_product['uistime']))/ 86400)  + 1 ;
            $income = $_product['income'] / 365 / 100;
            $repayment_list = array();
            //把单个产品的用户合到一块
            foreach ($buy_info as $_uinfo){
                $profit = $days * $income * $_uinfo['money'];
                $profit = sprintf("%.2f",substr(sprintf("%.3f", $profit), 0, -1));
                if(!isset($repayment_list[$_uinfo['uid']])){
                    $repayment_list[$_uinfo['uid']]['money'] = 0;
                    $repayment_list[$_uinfo['uid']]['profit'] = 0;
                    $repayment_list[$_uinfo['uid']]['num'] = 0;
                }
                $repayment_list[$_uinfo['uid']]['money'] += $_uinfo['money'];
                $repayment_list[$_uinfo['uid']]['profit'] += $profit;
                $repayment_list[$_uinfo['uid']]['num']++;
            }
            
            $contract = $this->contract->get_db_ContractByCid($_product['cid']);
            $tiqian=0;
            if($contract['status']==3){
            	$tiqian=1;
            }
            $this->load->model('admin_userproduct_model', 'userproduct');
            //记账数组
            foreach($repayment_list as $_uid => $_repayment){
                $money = $_repayment['money'] + $_repayment['profit'];
                $orderid = date("YmdHis") . $_uid . "bm";
                $repayment_log[] = array('uid' => $_uid, 'pid' => $pid, 'pname' => $_product['pname'], 'money' => $_repayment['money'], 'income' => $_product['income'], 'profit' => $_repayment['profit'], 'days' => $days, 'ctime' => time(), 'tiqian' => $tiqian);
                //把产品标为已还款状态
                $data = array('status' => 6, 'repaytime' => time());        //还款
                $this->product->updateProductStatus($pid, $data);
            }
            $this->load->model('admin_repayment_log_model', 'repayment_log');
            foreach ($repayment_log as $_rp_log){
                $this->repayment_log->createLog($_rp_log);
            }
            //消除锁
            $this->product->delrepaymentlock($pid);
            exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'已转成打款订单')));
        }
    }
    
    public function shenghe($pid){
        $flag = $this->op->checkUserAuthority('今日还款列表',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'今日还款列表');
        }else{
            $productDetail = $this->product->getProductByPid($pid);
            $update_data = array('repayment_status' => 1);
            $this->product->updateProductStatus($pid, $update_data);
            $this->load->model('admin_repayment_log_model', 'repayment_log');
            $update_data = array('status' => 1);
            $productBuyInfo = $this->repayment_log->updateLogsStatusByPid($pid, NOW, $update_data);
            exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'审核通过')));
        }
    }
}
