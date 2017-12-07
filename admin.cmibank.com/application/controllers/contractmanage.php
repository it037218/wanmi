<?php
/**
 * 合同与产品管理->合同管理
 * * */
class contractmanage extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '合同与产品管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_contract_model', 'contract');
        $this->load->model('admin_product_model','product');
    }
    
    public function index(){
        $flag = $this->op->checkUserAuthority('关于公司信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $searchstatus = trim($this->input->post('searchstatus'));
            $searchcorname = trim($this->input->post('searchcorname'));
            $searchcon_number = trim($this->input->post('searchcon_number'));
            if($searchcorname && $searchcorname != '请输入搜索内容' && $this->input->request('op') == "search"){
                $contract = $this->contract->getContractList(array('corname' =>$searchcorname),'',array($psize, $offset));
                $where = array();
                foreach ($contract as $_val){
                    $where[] = $_val['cid'];
                }
                //单个合同已总售金额
                $rtnsellmoney =array();
                //单个产品账户
                $rtncid = array();
                $product = $this->product->getProductList(array('cid'=>$where), '', '');
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        if(!isset($rtnsellmoney[$__val['cid']])){
                            $rtnsellmoney[$__val['cid']] = 0;
                        }
                        $rtnsellmoney[$__val['cid']] += $__val['sellmoney'];
                        if(!isset($rtncid[$__val['cid']])){
                            $rtncid[$__val['cid']][$key] = 0;
                        }
                        $rtncid[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                //活期采购
                $this->load->model('admin_stock_product_model','stock_product');
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                
                $data['count_stock_money'] = $count_stock_money;
                $data['rtncid'] = $rtncid;
                $data['rtnsellmoney'] = $rtnsellmoney;
                $data['list'] = $contract;
                $count =  count($this->contract->getContractList('','',''));
                $data['searchcorname'] = $searchcorname;
            }else if($searchstatus && $searchstatus != '请输入搜索内容' && $this->input->request('op') == "search"){
                $contract = $this->contract->getContractByStatus(array('status'=>$searchstatus),'',array($psize, $offset));
                $where = array();
                foreach ($contract as $_val){
                    $where[] = $_val['cid'];
                }
                //单个合同已总售金额
                $rtnsellmoney =array();
                //单个产品账户
                $rtncid = array();
                $product = $this->product->getProductList(array('cid'=>$where), '','');
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        if(!isset($rtnsellmoney[$__val['cid']])){
                            $rtnsellmoney[$__val['cid']] = 0;
                        }
                        $rtnsellmoney[$__val['cid']] += $__val['sellmoney'];
                        if(!isset($rtncid[$__val['cid']])){
                            $rtncid[$__val['cid']][$key] = 0;
                        }
                        $rtncid[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                //活期采购
                $this->load->model('admin_stock_product_model','stock_product');
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                
                $data['count_stock_money'] = $count_stock_money;
                $data['rtncid'] = $rtncid;
                $data['rtnsellmoney'] = $rtnsellmoney;
                $data['list'] = $contract;
                $count =  count($this->contract->getContractList('','',''));
                
            }else if($searchcon_number && $searchcon_number != '请输入搜索内容' && $this->input->request('op') == "search"){
                $contract = $this->contract->getContractList(array('con_number' =>$searchcon_number),'',array($psize, $offset));
                $where = array();
                foreach ($contract as $_val){
                    $where[] = $_val['cid'];
                }
                //单个合同已总售金额
                $rtnsellmoney =array();
                //单个产品账户
                $rtncid = array();
                $product = $this->product->getProductList(array('cid'=>$where), '', '');
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        if(!isset($rtnsellmoney[$__val['cid']])){
                            $rtnsellmoney[$__val['cid']] = 0;
                        }
                        $rtnsellmoney[$__val['cid']] += $__val['sellmoney'];
                        if(!isset($rtncid[$__val['cid']])){
                            $rtncid[$__val['cid']][$key] = 0;
                        }
                        $rtncid[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                //活期采购
                $this->load->model('admin_stock_product_model','stock_product');
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                
                $data['count_stock_money'] = $count_stock_money;
                $data['rtncid'] = $rtncid;
                $data['rtnsellmoney'] = $rtnsellmoney;
                $data['list'] = $contract;
                $count =  count($this->contract->getContractList('','',''));
                $data['searchcon_number'] = $searchcon_number;
            }else{
                $contract = $this->contract->getContractList('','',array($psize, $offset));
                $where = array();
                foreach ($contract as $_val){
                    $where[] = $_val['cid'];
                }
                //单个合同已总售金额
                $rtnsellmoney =array();
                //单个产品账户
                $rtncid = array();
                $product = $this->product->getProductList(array('cid'=>$where), '', '');
                foreach ($product as $key=>$__val){
                    if($__val['status']<2){
                        if(!isset($rtnsellmoney[$__val['cid']])){
                            $rtnsellmoney[$__val['cid']] = 0;
                        }
                        $rtnsellmoney[$__val['cid']] += $__val['sellmoney'];
                        if(!isset($rtncid[$__val['cid']])){
                            $rtncid[$__val['cid']][$key] = 0;
                        }
                        $rtncid[$__val['cid']][$key] =$__val['pid'];
                    }
                }
                //活期采购
                $this->load->model('admin_stock_product_model','stock_product');
                $stock_product = $this->stock_product->getStockProductList();
                $count_stock_money = array();
                foreach ($stock_product as $val){
                    if(!isset($count_stock_money[$val['cid']])){
                        $count_stock_money[$val['cid']] = 0;
                    }
                    $count_stock_money[$val['cid']] += $val['stockmoney'];
                }
                
                $data['count_stock_money'] = $count_stock_money;
                $data['rtncid'] = $rtncid;
                $data['rtnsellmoney'] = $rtnsellmoney;
                $data['list'] = $contract;
                $count =  count($this->contract->getContractList('','',''));
            }
            if($count>0){
            
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $this->load->view('/contractmanage/v_index', $data);
            
        }
    }
    
    public function uptoline(){
        $cid = $this->uri->segment(3);
        //$status 设置为1  即为正常
        $ret = $this->contract->updateContractstatus($cid,1);
        if($ret){
            $log = $this->op->actionData($this->getSession('name'), '合同管理', '', '开启', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '开启成功', array(), '合同管理', 'forward',OP_DOMAIN.'/contractmanage'));
        }
    }
    public function downtoline(){
        $this->load->model('admin_ptype_product_model','ptype_product');
        $cid = $this->uri->segment(3);
        //总产品pid
        $rtnproduct = array();
        $product = $this->product->getProductList(array('cid'=>$cid), '', '');
        $ptype_product = array();
        foreach ($product as $key=>$val){
            if($val['status']<2){
                $rtnproduct[]=$val['pid'];
                //更改产品状态   2下架
                $update_data = array('status' => 2, 'downtime' => time());
                $this->product->updateProductStatus($val['pid'], $update_data);
                $this->ptype_product->updatePorductByPid($val['pid'],array('status' =>2));
                $this->product->moveOnlineProduct($val['ptid'], $val['pid']);
            }
        }
        //$status 设置为2  即为关闭
        $ret = $this->contract->updateContractstatus($cid,2);
        $backmoney = $this->product->backmoney($cid);
        $ret = $this->contract->backMoneytoContract($cid, $backmoney);
        if($ret){
            $log = $this->op->actionData($this->getSession('name'), '定期产品采购', '', '下架', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '下架成功', array(), '合同管理', 'forward',OP_DOMAIN.'/contractmanage'));
        }
        
    
    }
}