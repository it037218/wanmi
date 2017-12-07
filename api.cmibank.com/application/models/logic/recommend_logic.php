<?php

class recommend_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('base/ptype_base', 'ptype_base');
        $this->load->model('base/product_base' , 'product_base');
        $this->load->model('base/longproduct_base' , 'longproduct_base');
        $this->load->model('base/recommend_base' , 'recommend_base');
    }
    
    public function getrecommend($odate, $uid = 0){
        $today = date('Y-m-d');
        $recommend = $this->recommend_base->getrecommend();
        if(!is_numeric($recommend)){
            $recommend = 0;
        }
        $ptypeList = $this->ptype_base->getPtypeList();
        //先从定期中的推荐找
        $yugaokeys = array_keys($ptypeList);
        $yugao_array = array();
        $recommend_product = array();       //推荐产品
        $currently_product = array();       //当前产品
        $product_detail_array = array();
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        if($uid && strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW){
            $this->load->model('base/user_identity_base', 'user_identity_base');
            $userIdentity = $this->user_identity_base->getUserIdentity($uid);
            $this->load->model('base/user_base' , 'user_base');
            $account = $this->user_base->getAccountInfo($uid);
        }
        foreach ($ptypeList as $ptid => $value){
            $size = $this->product_base->getOnlineProductListSize($ptid, $odate);
            if($size > 0){
                $productid = $this->product_base->getOnlineProductListFirstMem($ptid, $odate);
                $product_detail = $this->product_base->getProductDetail($productid);
                
                if($product_detail['recommend']){
                    $recommend_product[$productid] = $product_detail['income'];
                }else{
                    $currently_product[$productid] = $product_detail['income'];
                }
                if($odate != $today || NOW < mktime(1, 0, 0)){
                    $product_detail['online_time'] = $odate . ' 01:00';
                }
                if($uid
                    && strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW
                    && $userIdentity['isnew'] == 1 && $account['plat'] == 'invite'
                    && $product_detail['ptid'] != NEW_USER_PTID
                    ){
                    $product_detail['standard_icon'] = 'xinshoubiao_hong';
                    $product_detail['operation_tag'] = '首投送现金';
                    $product_detail['text_url'] = STATIC_DOMAIN.'banner/banneryqhy_abcdefghgodefg.html';
                }
                $product_detail_array[$productid] = $product_detail;
            }
        }
        if(isset($currently_product[$recommend])){
            return $product_detail_array[$recommend];
        }
        if($recommend_product){
            if(isset($recommend_product[$recommend])){
                $product_id = $recommend;
            }else{
                $max_income = max($recommend_product);
                $product_id = array_search($max_income, $recommend_product);
            }
            return $product_detail_array[$product_id];
        }
        $yugaokeys = array_keys($ptypeList);
        $recommend_yugaoproduct = array();
        $currently_yugaoproduct = array();
        foreach ($yugaokeys as $ptid){
            $size = $this->product_base->getYuGaoProductListSize($ptid, $odate);
            if($size > 0){
                $yugaoinfo = $this->product_base->getYuGaoProductListFirstMem($ptid, $odate);
                if(empty($yugaoinfo)){
                    continue;
                }
                $product_detail = $this->product_base->getProductDetail($yugaoinfo['pid']);
                $productid = $yugaoinfo['pid'];
                if($product_detail['recommend']){
                    $recommend_yugaoproduct[$productid] = $product_detail['income'];
                }else{
                    $currently_yugaoproduct[$productid] = $product_detail['income'];
                }
                $product_detail_array[$productid] = $product_detail;
            }
        }
        if($recommend_yugaoproduct){
            if(isset($recommend_yugaoproduct[$recommend])){
                $product_id = $recommend;
            }else{
                $max_income = max($recommend_yugaoproduct);
                $product_id = array_search($max_income, $recommend_yugaoproduct);
            }
            return $product_detail_array[$product_id];
        }
        //定期如果没有，就推荐活期
        $size = $this->longproduct_base->getOnlineLongProductListSize(14, $odate);
        if($size > 0){
            $longproductid = $this->longproduct_base->getOnlineLongProductListFirstMem(14, $odate);
            $longproduct = $this->longproduct_base->getLongProductDetail($longproductid);
            $longproduct['longproduct'] = 1;
            if($odate != $today){
                $longproduct['online_time'] = $odate . ' 01:00';
            }
            if($longproduct){
                return $longproduct;        //活期产品
            }
        }
        $size = $this->longproduct_base->getYuGaoLongProductListSize(14, $odate);
        if($size > 0){
            $yugaoinfo = $this->longproduct_base->getYuGaoLongProductListFirstMem(14, $odate);
            $longproduct = $this->longproduct_base->getLongProductDetail($yugaoinfo['pid']);
            $longproduct['longproduct'] = 1;
            if($longproduct){
                return $longproduct;        //活期产品
            }
        }
        //如果活期也没有，就从普通定期产品里取最高的
        if($currently_product){
            $max_income = max($currently_product);
            $product_id = array_search($max_income, $currently_product);
            return $product_detail_array[$product_id];
        }
        if($currently_yugaoproduct){
            $max_income = max($currently_product);
            $product_id = array_search($max_income, $currently_yugaoproduct);
            return $product_detail_array[$product_id];
        }
        return array();
    }
    
}


   
