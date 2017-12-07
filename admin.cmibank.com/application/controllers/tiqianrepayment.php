<?php
class tiqianrepayment extends Controller {

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
        $this->load->model('admin_product_model','product');
        $this->load->model('admin_product_remit_model','product_remit');
        $this->load->model('admin_product_backmoney_model','product_backmoney');
        $this->load->model('admin_stock_product_model','stock_product');
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('提前回款', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '提前回款');
        } else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $data = array();
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $count=0;
            $contractList = array();
            $searchParam = array();
            if($this->input->request('op') == "search"){
                $corname = trim($this->input->post('corname'));
                $connumber = trim($this->input->post('connumber'));
                if(!empty($corname)){
                	$searchParam['cor_name']=$corname;
                }
                if(!empty($connumber)){
                	$searchParam['cor_number']=$connumber;
                }
                $contractList = $this->contract->getYetRepayment($searchParam,$offset,$psize);
            }else{
            	$contractList = $this->contract->getYetRepayment(array(),$offset,$psize);
            }
            if(!empty($contractList)){
                $count= $this->contract->countYetRepayment($searchParam);
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                	if(!isset($count_stock_money[$val['cid']])){
                		$count_stock_money[$val['cid']] = 0;
                	}
                	$count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                $where = array();
                foreach ($contractList as $_val){
                	$where[] = $_val['cid'];
                }
                $rtnstatus =array();
                $product = $this->product->getProductList(array('cid'=>$where), '', '');
                foreach ($product as $key=>$__val){
                	if($__val['status']<2){
                		$rtnstatus[$__val['cid']][$key] =$__val['pid'];
                	}
                }
                $data['rtnstatus'] = $rtnstatus;
                $data['count_stock_money'] = $count_stock_money;
            }
            if($count>0){
            	$data['list'] = $contractList;
                $data['count'] = $count;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1450');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '提前回款', '', '提前回款', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/repayment/v_tiqian', $data);
        }
    }
    
    public function editcontract() {
        $flag = $this->op->checkUserAuthority('提前回款', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '提前回款');
        } else {
            if($this->input->request('op') == 'editcontract'){
            	$this->load->model('admin_ptype_product_model','ptype_product');
                $cid = $this->input->post('cid');
                $repaymenttime = trim($this->input->post('repaymenttime'));
                $remark = trim($this->input->post('remark'));
                if(!$cid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                
                $pro_backmoney = $this->product_backmoney->getProductBackmoneyByCid($cid);
               	$backmoney_data['cietime'] = $repaymenttime;
                $product_backmoney = $this->product_backmoney->updatePorductBackmoney($pro_backmoney['bid'],$backmoney_data);
                if(!empty($pro_backmoney['pids'])){
	                $pids = explode(',', $pro_backmoney['pids']);
	                $product_data = array('status' => 5);
	                $this->product->updatePorduct($pids, $product_data);
	                $product = $this->product->getProductList(array('cid'=>$cid), '', '');
	                foreach ($product as $key=>$val){
	                	if($val['status'] < 2){
	                		//更改产品状态   2下架
	                		$update_data = array('status' => 2, 'downtime' => time(),'uietime'=>$repaymenttime,'cietime'=>$repaymenttime);
	                		$this->product->updateProductStatus($val['pid'], $update_data);
	                		$this->ptype_product->updatePorductByPid($val['pid'],array('status' =>2));
	                		$this->product->moveOnlineProduct($val['ptid'], $val['pid']);
	                	}else{
	                		$update_data = array('uietime'=>$repaymenttime,'cietime'=>$repaymenttime);
	                		$this->product->updateProductStatus($val['pid'], $update_data);
	                	}
	                }
                }
                $data['repaymenttime'] = $repaymenttime;
                $data['remark'] = $remark;
                $data['status'] = 3;
                $ret = $this->contract->updateContract($cid, $data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '修改提前回款', '', '修改提前回款', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改提前回款成功', array(), '修改提前回款 ', 'forward', OP_DOMAIN.'/tiqianrepayment'));
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
                $this->load->view('/repayment/v_editontract', $data);
            }
        }
    }
    
    private function diff_days($start, $now){
        $a_dt=getdate($start);
        $b_dt=getdate($now);
        $a_new=mktime(12,0,0,$a_dt['mon'],$a_dt['mday'],$a_dt['year']);
        $b_new=mktime(12,0,0,$b_dt['mon'],$b_dt['mday'],$b_dt['year']);
        return abs(($a_new-$b_new)/86400-1);
    }
}