<?php
/**
 * goods管理
 * * */
class goods extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '商品管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_goods_model', 'goods');
        $this->load->model('admin_coupon_model', 'coupon');
        $this->load->model('admin_expmoneyactivity_model', 'expmoney');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('商品管理', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '商品管理');
            exit;
        } else {
            $data = array();
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            
            $goodsList = $this->goods->getGoodsList(array('deleted'=>0),array($psize, $offset));
            foreach ($goodsList as $key=>$value){
            	 if($value['type']==2){
            		$coupon = $this->coupon->getCouponById($value['wid']);
            		if(!empty($coupon)){
	            		$goodsList[$key]['wp'] = $coupon[0]['name'].','.$coupon[0]['sendmoney'].','.$coupon[0]['pnames'];
            		}
            	}
            }
            $count = $this->goods->getGoodsCount(array('deleted'=>0));
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['list'] = $goodsList;
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'10000');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '商品管理', '', '商品管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/goods/v_index', $data);
        }
    }
    
    public function addgoods(){
        $flag = $this->op->checkUserAuthority('商品管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '商品管理');
        } else {
            if($this->input->request('op') == 'addgoods'){
                $name = trim($this->input->post('name'));
                $jifeng = trim($this->input->post('jifeng'));
                $yuanjifeng = trim($this->input->post('yuanjifeng'));
                $type = trim($this->input->post('type'));
                $coupon_jifeng = trim($this->input->post('coupon_jifeng'));
                $expmoney_jifeng = trim($this->input->post('expmoney_jifeng'));
                $money = trim($this->input->post('money'));
                $img=trim($this->input->post('img'));
                $rank=trim($this->input->post('rank'));
                $desc=trim($this->input->post('desc'));
                $stock=trim($this->input->post('stock'));
                $data = array();
                $data['name'] = $name;
                $data['jifeng'] = $jifeng;
                $data['yuanjifeng'] = $yuanjifeng;
                $data['type'] = $type;
                $data['money'] = $money;
                if($type==2){
                	$data['wid'] = $coupon_jifeng;
                	$coupon = $this->coupon->getCouponById($coupon_jifeng);
                	$data['money'] = $coupon[0]['sendmoney'];
                }
                $data['img'] = $img;
                $data['stock']=$stock;
                $data['rank'] = $rank;
                $data['desc'] = $desc;
                $data['ctime'] = NOW;
                $ret = $this->goods->addGoods($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加商品失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '商品管理', '', '商品管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加商品成功', array(), '商品管理 ', 'forward', OP_DOMAIN.'/goods'));
            }else{
            	$couponList = $this->coupon->getAvailableCouponForJifeng();
                $this->load->view('/goods/v_add',array('couponList'=>$couponList));
            }
        }
    }
    public function editGoods(){
        $flag=$this->op->checkUserAuthority('商品管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '商品管理');
        }else{
            
            if($this->input->request('op') == 'editGoods'){
            	$id = trim($this->input->post('id'));
            	$goods = $this->goods->getGoodsById($id);
                $name = trim($this->input->post('name'));
                $jifeng = trim($this->input->post('jifeng'));
                $yuanjifeng = trim($this->input->post('yuanjifeng'));
                $type = trim($this->input->post('type'));
                $coupon_jifeng = trim($this->input->post('coupon_jifeng'));
                $expmoney_jifeng = trim($this->input->post('expmoney_jifeng'));
                $money = trim($this->input->post('money'));
                $img=trim($this->input->post('img'));
                $rank=trim($this->input->post('rank'));
                $desc=trim($this->input->post('desc'));
                $stock=trim($this->input->post('stock'));
                $data = array();
                $data['name'] = $name;
                $data['jifeng'] = $jifeng;
                $data['yuanjifeng'] = $yuanjifeng;
                $data['type'] = $type;
                $data['money'] = $money;
                $data['stock']=$goods[0]['sold']+$stock;
                if($type==2){
                	$data['wid'] = $coupon_jifeng;
                	$coupon = $this->coupon->getCouponById($coupon_jifeng);
                	$data['money'] = $coupon[0]['sendmoney'];
                }
                if(!empty($img)){
	                $data['img'] = $img;
                }
                $data['rank'] = $rank;
                $data['desc'] = $desc;
               $ret = $this->goods->updateGoodsById($id, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '商品列表', '', '修改商品', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '商品管理 ', 'forward', OP_DOMAIN.'/goods'));
            }else{
               $id = $this->uri->segment(3);
                if($id < 0 || !is_numeric($id)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $this->load->model('admin_coupon_model', 'coupon');
                $couponList = $this->coupon->getAvailableCouponForJifeng();
                 $rec= $this->goods->getGoodsById($id);
                 $data['detail'] = $rec[0];
                 $data['couponList'] = $couponList;
                $this->load->view('/goods/v_edit', $data);
            }  
        }
    }
    public function delGoods(){
        $flag=$this->op->checkUserAuthority('商品管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '商品管理');
        }else{
            $id = $this->uri->segment(3);
            $data['deleted'] = 1;
            $ret = $this->goods->updateGoodsById($id, $data);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '商品管理', '', '删除商品', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除商品', 'forward', OP_DOMAIN.'/goods'));
    }
    
    public function onLine(){
    	$flag=$this->op->checkUserAuthority('商品管理',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '商品管理');
    	}else{
    		$id = $this->uri->segment(3);
    		$goods = $this->goods->getGoodsById($id);
    		if(empty($goods)){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'未找到商品')));
    		}else if($goods[0]['stock']<=$goods[0]['sold']){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'库存不够，无法上架。')));
    		}
    		$data['status'] = 1;
    		$ret = $this->goods->updateGoodsById($id,$data);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'发布失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '商品管理', '', '发布商品', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布商品', 'forward', OP_DOMAIN.'/goods'));
    }
    public function downLine(){
    	$flag=$this->op->checkUserAuthority('商品管理',$this->getSession('uid'));
    	$data = array();
    	if($flag == 0){
    		echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '商品管理');
    	}else{
    		$id = $this->uri->segment(3);
    		$data['status'] = 2;
    		$ret = $this->goods->updateGoodsById($id,$data);
    		if(!$ret){
    			exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'下架失败')));
    		}
    	}
    	$log = $this->op->actionData($this->getSession('name'), '商品管理', '', '下架商品', $this->getIP(), $this->getSession('uid'));
    	exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '下架成功', array(), '下架商品', 'forward', OP_DOMAIN.'/goods'));
    }
}