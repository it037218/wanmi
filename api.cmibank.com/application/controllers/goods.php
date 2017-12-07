<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class goods extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('base/goods_base', 'goods_base');
        $this->load->model('base/user_jifeng_duihuan_base', 'duihuan_base');
        $this->load->model('base/activity_base', 'activity_base');
        $this->load->model('base/user_jifeng_base' , 'jifeng_base');
        $this->check_link();
    }

    public function getOnlineGoodsList(){
    	$shiwusList = $this->goods_base->getOnlineGoodsList(1);
    	$xuniList = $this->goods_base->getOnlineGoodsList(0);
    	$shiwusList = empty($shiwusList)?array():$shiwusList;
    	$xuniList = empty($xuniList)?array():$xuniList;
    	$response = array('error'=> 0, 'shiwusList'=> $shiwusList,'xuniList'=> $xuniList);
    	$this->out_print($response);
    }
    public function getDuihuanList(){
    	$page = $this->input->post('page');
    	$duihuanList = $this->duihuan_base->get_user_duihuan_list($this->uid,$page);
    	if(empty($duihuanList)){
    		$response = array('error'=> 0, 'data'=> array());
    		$this->out_print($response);
    	}else{
    		$response = array('error'=> 0, 'data'=> $duihuanList);
    		$this->out_print($response);
    	}
    }
    
    public function getGoodsDetail(){
        $id = trim($this->input->post('id'));
        $goods = $this->goods_base->getGoodsByCid($id);
        if(empty($goods)){
            $response = array('error'=> 2002, 'msg' => '商品未找到');
            $this->out_print($response);
        }else if($goods['status']!=1){
        	$response = array('error'=> 2001, 'msg' => '商品已下架');
        	$this->out_print($response);
        }
       	$response = array('error'=> 0, 'data'=>$goods);
    	$this->out_print($response);
    }
    
    public function getGoodsDesc(){
    	$id = trim($this->input->request('id'));
    	$goods = $this->goods_base->getGoodsByCid($id);
    	if(empty($goods)){
    		$response = array('error'=> 2002, 'msg' => '商品未找到');
    		$this->out_print($response);
    	}else if($goods['status']!=1){
    		$response = array('error'=> 2001, 'msg' => '商品已下架');
    		$this->out_print($response);
    	}
    	$data['detail'] = $goods;
    	$this->load->view('goodsDetail', $data);
    }
    
    public function duihuan(){
    	$id = trim($this->input->post('id'));
    	$count = trim($this->input->post('count'));
    	if(empty($count)){
    		$count=1;
    	}
    	$goods = $this->goods_base->getGoodsByCid($id);
    	if(!$goods || $goods['status']!=1){
    		$response = array('error'=> 2002, 'msg' => '商品已下架');
    		$this->out_print($response);
    	}
    	if($goods['stock']<$goods['sold']+$count){
    		$response = array('error'=> 2003, 'msg' => '库存不够');
    		$this->out_print($response);
    	}
    	$jifeng = $this->activity_base->get_activity_rank_with_actid_phone(2, $this->account);
    	if(empty($jifeng)){
    		$response = array('error'=> 2005, 'msg' => '积分不够');
    		$this->out_print($response);
    	}else if($jifeng<$goods['jifeng']*$count){
    		$response = array('error'=> 2006, 'msg' => '可用积分不够');
    		$this->out_print($response);
    	}
    	
    	if($goods['stock']<$goods['sold']+$count){
    		$response = array('error'=> 2007, 'msg' => '商品库存不够');
    		$this->out_print($response);
    	}
    	
    	$sold = $this->goods_base->incrSold($id,$count);
    	$totaljf = $goods['jifeng']*$count;
    	$rank_ret = $this->activity_base->set_activity_rank_with_actid(2, $this->account, -$totaljf);
    	if($rank_ret){
    		$goodsUpdate['sold'] = $sold;
    		if($sold==$goods['stock']){
    			$goodsUpdate['status']=2;
    		}
    		
	    	$this->goods_base->updateGoodsById($id,$goodsUpdate);
	    	$jifeng_data = array(
	    			'uid' => $this->uid,
	    			'name' => '积分兑换-'.$goods['name'],
	    			'action' => JIFENG_DUIHUANG,
	    			'value' => $totaljf,
	    			'ctime' => NOW
	    	);
	    	$addJifenglogid = $this->jifeng_base->addJifeng($this->uid,$jifeng_data);
    	}
    	for($index=1;$index<=$count;$index++){
	    	if($goods['type']==1){
	    		$this->load->model('logic/expmoney_activity_logic', 'expmoney_activity_logic');
		    	if($addJifenglogid){
		    		$expmoneyRet = $this->expmoney_activity_logic->sendJifengExpmoney(EXPMONEY_ACTIVITY_JIFENG,$this->uid,$goods['money']);
		    		if($expmoneyRet){
		    			$duihuan = array(
		    					'wid' => $expmoneyRet['insertid'],
		    					'type' => $goods['type'],
		    					'uid' => $this->uid,
		    					'name' => $goods['name'],
		    					'logid' => $addJifenglogid,
		    					'money' => $expmoneyRet['money'],
		    					'realmoney' => $expmoneyRet['realmoney'],
		    					'jifeng' => $goods['jifeng'],
	    						'ctime'=>NOW,
		    					'gid'=>$id,
		    					'img'=>$goods['img']
		    			);
		    			$this->duihuan_base->addDuihuang($this->uid,$duihuan);
		    		}
	    		}
	    	}else if($goods['type']==2){
	    		$this->load->model('logic/coupon_activity_logic', 'coupon_activity_logic');
		    	if($addJifenglogid){
		    		$couponRet = $this->coupon_activity_logic->sendJifengCoupon($this->uid,$goods['wid'],$this->account);
		    		if($couponRet){
		    			$duihuan = array(
		    					'wid' => $couponRet['insertid'],
		    					'type' => $goods['type'],
		    					'uid' => $this->uid,
		    					'name' => $goods['name'],
		    					'logid' => $addJifenglogid,
		    					'money' => $couponRet['money'],
		    					'realmoney' => $couponRet['realmoney'],
		    					'jifeng' => $goods['jifeng'],
	    						'ctime'=>NOW,
		    					'gid'=>$id,
		    					'img'=>$goods['img']
		    			);
		    			$this->duihuan_base->addDuihuang($this->uid,$duihuan);
		    		}
	    		}
	    	}else if($goods['type']==3){
	    		$this->load->model('logic/luckybag_logic', 'luckybag_logic');
	    		$luckbagRet = $this->luckybag_logic->addJifengLuckybagForUser($goods['money'],$this->uid,$this->account);
	    		if($luckbagRet){
	    			$duihuan = array(
	    					'wid' => $luckbagRet,
	    					'type' => $goods['type'],
	    					'uid' => $this->uid,
	    					'name' => $goods['name'],
	    					'logid' => $addJifenglogid,
	    					'money' => $goods['money'],
	    					'realmoney' => $goods['money'],
	    					'jifeng' => $goods['jifeng'],
	    					'ctime'=>NOW,
		    				'gid'=>$id,
		    				'img'=>$goods['img']
	    			);
	    			$this->duihuan_base->addDuihuang($this->uid,$duihuan);
	    		}
	    	}else if($goods['type']==4){
	    		$duihuan = array(
	    				'wid' => date('YmdHis') . mt_rand(1000,9999),
	    				'type' => $goods['type'],
	    				'uid' => $this->uid,
	    				'name' => $goods['name'],
	    				'logid' => $addJifenglogid,
	    				'money' => $goods['money'],
	    				'realmoney' => $goods['money'],
	    				'jifeng' => $goods['jifeng'],
	    				'ctime'=>NOW,
		    			'gid'=>$id,
		    			'img'=>$goods['img']
	    		);
	    		$this->duihuan_base->addDuihuang($this->uid,$duihuan);
	    	}
    	}
    	$response = array('error'=> 0, 'msg' => '兑换成功');
    	$this->out_print($response);
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */