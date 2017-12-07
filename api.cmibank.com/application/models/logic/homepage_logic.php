<?php
class homepage_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();  
        
    }

    public function homepage_product_list($uid = 0){
        $stoptime = mktime(23,40,0);
        $odate = date('Y-m-d');
        if(NOW > $stoptime){
            $odate = date('Y-m-d', strtotime("+1 day"));
        }
        return $this->getProductHomePagePtypeList($odate, $uid);
    }
    
    public function getProductHomePagePtypeList($odate = '', $uid = 0){
        $begin_odate = $odate;
        $this->load->model('base/ptype_base', 'ptype_base');
        $this->load->model('base/product_base', 'product_base');
        $this->load->model('logic/product_logic', 'product_logic');
 //       $start =  microtime(1);
        $ptypeList = $this->ptype_base->getPtypeList();

        $yugaokeys = array_keys($ptypeList);
        $yugao_array = array();
        $next_day = false;
        
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        
        foreach ($yugaokeys as $ptid){
            if($next_day == true){
                $odate = $begin_odate;
            }
            
            yugao:
            $size = $this->product_base->getYuGaoProductListSize($ptid, $odate);
            if($size == 0){
                $odate = date('Y-m-d', strtotime("+1 day"));
                $size = $this->product_base->getYuGaoProductListSize($ptid, $odate);
                $next_day = true;
            }
            if($size > 0){
                $old_productid = 0;
                $yugaoinfo = $this->product_base->getYuGaoProductListFirstMem($ptid, $odate);
                if(!$yugaoinfo){
                    break;
                }
                if($old_productid == $yugaoinfo['pid']){
                    break;
                }
                $old_productid = $yugaoinfo['pid'];
                if($yugaoinfo['online_time'] <= NOW){
                    //从预告队列中去除、移致产品销售队列~数据库ptype_product
                    $this->product_base->moveYuGaoToProduct($ptid, $yugaoinfo, $odate);
                    goto yugao;
                }
                $product_detail = $this->product_base->getProductDetail($yugaoinfo['pid']);
                if($product_detail){
                    $yugao_array[] = $product_detail;
                }
            }
        }
        $rtn_array = array();
        $next_day = false;
        $odate = $begin_odate;
        $userIdentity = array('isnew' => 0);
        if($uid && strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW ){
            $this->load->model('base/user_identity_base', 'user_identity_base');
            $userIdentity = $this->user_identity_base->getUserIdentity($uid);
            $this->load->model('base/user_base' , 'user_base');
            $account = $this->user_base->getAccountInfo($uid);
            
        }
        foreach ($ptypeList as $ptid => $value){
            if($next_day == true){
                $odate = $begin_odate;
            }
            product:
            $size = $this->product_base->getOnlineProductListSize($ptid, $odate);

            if($size == 0){     //如果没有就上明天的  并设为预告标
                $odate = date('Y-m-d', strtotime("+1 day"));
                $size = $this->product_base->getOnlineProductListFirstMem($ptid, $odate);
                $next_day = true;
            }
            if($size > 0){
                $old_productid = 0;
                $productid = $this->product_base->getOnlineProductListFirstMem($ptid, $odate);
                if(!$productid){
                    break;
                }
                if($productid == $old_productid){
                    break;
                }
                $old_productid = $productid;
                $product_detail = $this->product_base->getProductDetail($productid);
                if(empty($product_detail)){
                    break;
                }
                if($odate == date('Y-m-d', strtotime("+1 day"))){
                    $product_detail['online_time'] = $odate . ' 01:00';
                }else if($product_detail['online_time'] == ''){
                    if(NOW < mktime(1,0,0)){
                        $product_detail['online_time'] = $odate . ' 01:00';
                    }
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
                if(isset($product_detail['sellmoney']) && $product_detail['sellmoney'] >= $product_detail['money'] || $product_detail['status'] >= 3){
                    $this->product_logic->setProductSellOut($ptid, $productid);
                    goto product;
                }
                if($product_detail){
                    $rtn_array[] = $product_detail;
                }
            }
        }
        $data = array_merge($rtn_array, $yugao_array);
        return $data;
    }
    
    public function homepage_longproduct_list($uid = 0){
        $stoptime = mktime(23,40,0);
        $odate = '';
        if(NOW > $stoptime){
            $odate = date('Y-m-d', strtotime("+1 day"));
        }
        $today_date = date('Y-m-d');
        $longproductlist =  $this->getLongProductHomePagePtypeList($odate, $uid);
        if(empty($longproductlist)){
            if( NOW > mktime(23, 0, 0)){
                $odate = date('Y-m-d', strtotime("+1 day"));
                $longproductlist =  $this->getLongProductHomePagePtypeList($odate, $uid);
                foreach ($longproductlist as $key => &$_lp){
                    if($longproductlist[$key]['online_time'] == ''){
                        $longproductlist[$key]['online_time'] = $odate . ' 01:00';
                    }
                }
            }            
        }else{
            foreach ($longproductlist as $key => &$_lp){
                if($_lp['online_time'] == '' && $_lp['odate'] != $today_date){
                    $_lp['online_time'] = $odate . ' 01:00';
                }
                if(mktime(1,0,0) > NOW && $_lp['online_time'] == ''){
                    $_lp['online_time'] = $today_date . ' 01:00';
                }
            }
        }
        return $longproductlist;
    }
    
    
    public function homepage_klproduct_list(){
        $stoptime = mktime(23,40,0);
        $odate = '';
        if(NOW > $stoptime){
            $odate = date('Y-m-d', strtotime("+1 day"));
        }
        $today_date = date('Y-m-d');
        $klproductlist =  $this->getKlProductHomePagePtypeList($odate);
        if(empty($klproductlist)){
            if( NOW > mktime(23, 0, 0)){
                $odate = date('Y-m-d', strtotime("+1 day"));
                $klproductlist =  $this->getKlProductHomePagePtypeList($odate);
                foreach ($klproductlist as $key => &$_klp){
                    if($klproductlist[$key]['online_time'] == ''){
                        $klproductlist[$key]['online_time'] = $odate . ' 01:00';
                    }
                }
            }
        }else{
            //             $longproductlist =  $this->getLongProductHomePagePtypeList($odate);
            foreach ($klproductlist as $key => &$_klp){
                if($_klp['online_time'] == '' && $_klp['odate'] != $today_date){
                    $_klp['online_time'] = $odate . ' 01:00';
                }
                //                 var_dump(mktime(1,0,0) > NOW);
                if(mktime(1,0,0) > NOW && $_klp['online_time'] == ''){
                    $_klp['online_time'] = $today_date . ' 01:00';
                }
            }
        }
        return $klproductlist;
    }
    
    public function getKlProductHomePagePtypeList($odate = ''){
        $this->load->model('base/kltype_base', 'kltype_base');
        $this->load->model('base/Klproduct_base', 'klproduct_base');
        $this->load->model('logic/Klproduct_logic', 'klproduct_logic');
        $kltypeList = $this->kltype_base->getKltypeList();
        $yugaokeys = array_keys($kltypeList);
        $yugao_array = array();
        foreach ($yugaokeys as $kltid){
            yugao:
            $size = $this->klproduct_base->getYuGaoKlProductListSize($kltid, $odate);
            if($size > 0){
                $yugaoinfo = $this->klproduct_base->getYuGaoKlProductListFirstMem($kltid, $odate);
                if(!$yugaoinfo){
                    break;
                }
                if($yugaoinfo['online_time'] <= NOW){
                    //echo $yugaoinfo['online_time'] . '-' . NOW . '-' . $ptid . '-' . $yugaoinfo['pid'] . '<br />';
                    //从预告队列中去除、移致产品销售队列~数据库ptype_product
                    $this->klproduct_base->moveYuGaoToKlProduct($kltid, $yugaoinfo);
                    goto yugao;
                }
                $product_detail = $this->klproduct_base->getKlProductDetail($yugaoinfo['pid']);
                $yugao_array[] = $product_detail;
            }
        }
        $rtn_array = array();
        foreach ($kltypeList as $kltid => $value){
            longproduct:
            $size = $this->klproduct_base->getOnlineKlProductListSize($kltid, $odate);
            if($size > 0){
                $klproductid = $this->klproduct_base->getOnlineKlProductListFirstMem($kltid, $odate);
                $klproduct_detail = $this->klproduct_base->getKlProductDetail($klproductid);
                if(!$klproduct_detail){
                    break;
                }
                if(isset($klproduct_detail['sellmoney']) && $klproduct_detail['sellmoney'] >= $klproduct_detail['money']){
                    $this->klproduct_logic->setKlProductSellOut($kltid, $klproductid);
                    goto longproduct;
                }
                $rtn_array[] = $klproduct_detail;
            }
        }
        $data = array_merge($rtn_array, $yugao_array);
        return $data;
    }
    
    
    public function getLongProductHomePagePtypeList($odate = '', $uid = 0){
        $this->load->model('base/ltype_base', 'ltype_base');
        $this->load->model('base/longproduct_base', 'longproduct_base');
        $this->load->model('logic/longproduct_logic', 'longproduct_logic');
        $ltypeList = $this->ltype_base->getLtypeList();
        $yugaokeys = array_keys($ltypeList);
        $yugao_array = array();
//        $h_new = true;
//         if($uid){
//             $this->load->model('base/user_identity_base', 'user_identity_base');
//             $userIdentity = $this->user_identity_base->getUserIdentity($uid);
//             if($userIdentity['isnew'] == 1 && $userIdentity['h_isnew'] == 1){
//                 $h_new = true;
//             }else{
//                 $h_new = false;
//             }
//         }else{
//             $h_new = true;
//         }
        foreach ($yugaokeys as $ltid){
//             if($h_new == true && $ltid == LONGPRODUCT_PTID){
//                 continue;
//             }else if($h_new == false && $ltid == NEW_LONGPRODUCT_PTID){
//                 continue;
//             }
            yugao:
            $size = $this->longproduct_base->getYuGaoLongProductListSize($ltid, $odate);
            if($size > 0){
                $yugaoinfo = $this->longproduct_base->getYuGaoLongProductListFirstMem($ltid, $odate);
                if(!$yugaoinfo){
                    break;
                }
                if($yugaoinfo['online_time'] <= NOW){
                    //echo $yugaoinfo['online_time'] . '-' . NOW . '-' . $ptid . '-' . $yugaoinfo['pid'] . '<br />';
                    //从预告队列中去除、移致产品销售队列~数据库ptype_product
                    $this->longproduct_base->moveYuGaoToLongProduct($ltid, $yugaoinfo);
                    goto yugao;
                }
                $product_detail = $this->longproduct_base->getLongProductDetail($yugaoinfo['pid']);
                //活期不预告 只有新手专享的出来
                if($ltid == NEW_LONGPRODUCT_PTID){
                    $yugao_array[] = $product_detail;
                }
            }
        }
        $userIdentity = array();
        
        $rtn_array = array();
        
        foreach ($ltypeList as $ltid => $value){
//             if($h_new == true && $ltid == LONGPRODUCT_PTID){
//                 continue;
//             }else if($h_new == false && $ltid == NEW_LONGPRODUCT_PTID){
//                 continue;
//             }
            longproduct:
            $size = $this->longproduct_base->getOnlineLongProductListSize($ltid, $odate);
            if($size > 0){
                $longproductid = $this->longproduct_base->getOnlineLongProductListFirstMem($ltid, $odate);
                $longproduct_detail = $this->longproduct_base->getLongProductDetail($longproductid);
                if(!$longproduct_detail){
                    break;
                }
                if(isset($longproduct_detail['sellmoney']) && $longproduct_detail['sellmoney'] >= $longproduct_detail['money']){
                    $this->longproduct_logic->setLongProductSellOut($ltid, $longproductid);
                    goto longproduct;
                }
                $rtn_array[] = $longproduct_detail;
            }
        }
        $data = array_merge($rtn_array, $yugao_array);
        return $data;
    }
    
    
    public function homepage_equalproduct_list($uid = 0){
        $stoptime = mktime(23,40,0);
        $odate = date('Y-m-d');
        if(NOW > $stoptime){
            $odate = date('Y-m-d', strtotime("+1 day"));
        }
        return $this->getEqualProductHomePagePtypeList($odate, $uid);
    }
    
    public function getEqualProductHomePagePtypeList($odate = '', $uid = 0){
        $begin_odate = $odate;
        $this->load->model('base/equalptype_base', 'equalptype_base');
        $this->load->model('base/equalproduct_base', 'equalproduct_base');
        $this->load->model('logic/equalproduct_logic', 'equalproduct_logic');
        //       $start =  microtime(1);
        $ptypeList = $this->equalptype_base->getPtypeList();
        $yugaokeys = array_keys($ptypeList);
        $yugao_array = array();
        $next_day = false;
    
//         $this->config->load('cfg/invite_cfg', true, true);
//         $invite_cfg = $this->config->item('cfg/invite_cfg');
    
        foreach ($yugaokeys as $ptid){
            if($next_day == true){
                $odate = $begin_odate;
            }
    
            equalyugao:
            $size = $this->equalproduct_base->getYuGaoEqualProductListSize($ptid, $odate);
            if($size == 0){
                $odate = date('Y-m-d', strtotime("+1 day"));
                $size = $this->equalproduct_base->getYuGaoEqualProductListSize($ptid, $odate);
                $next_day = true;
            }
            if($size > 0){
                $old_productid = 0;
                $yugaoinfo = $this->equalproduct_base->getEqualYuGaoProductListFirstMem($ptid, $odate);
                if(!$yugaoinfo){
                    break;
                }
                if($old_productid == $yugaoinfo['pid']){
                    break;
                }
                $old_productid = $yugaoinfo['pid'];
                if($yugaoinfo['online_time'] <= NOW){
                    //从预告队列中去除、移致产品销售队列~数据库ptype_product
                    $this->equalproduct_base->moveYuGaoToEqualProduct($ptid, $yugaoinfo, $odate);
                    goto equalyugao;
                }
                $product_detail = $this->equalproduct_base->getEqualProductDetail($yugaoinfo['pid']);
                if($product_detail){
                    $yugao_array[] = $product_detail;
                }
            }
        }
        $rtn_array = array();
        $next_day = false;
        $odate = $begin_odate;
        $userIdentity = array('isnew' => 0);
//         if($uid && strtotime($invite_cfg['buff_stime']) <= NOW && strtotime($invite_cfg['buff_etime']) >= NOW ){
//             $this->load->model('base/user_identity_base', 'user_identity_base');
//             $userIdentity = $this->user_identity_base->getUserIdentity($uid);
//             $this->load->model('base/user_base' , 'user_base');
//             $account = $this->user_base->getAccountInfo($uid);
    
//         }
        foreach ($ptypeList as $ptid => $value){
            if($next_day == true){
                $odate = $begin_odate;
            }
            equalproduct:
            $size = $this->equalproduct_base->getOnlineEqualProductListSize($ptid, $odate);
            if($size == 0){     //如果没有就上明天的  并设为预告标
                $odate = date('Y-m-d', strtotime("+1 day"));
                $size = $this->equalproduct_base->getOnlineEqualProductListFirstMem($ptid, $odate);
                $next_day = true;
            }
            if($size > 0){
                $old_productid = 0;
                $productid = $this->equalproduct_base->getOnlineEqualProductListFirstMem($ptid, $odate);
                if(!$productid){
                    break;
                }
                if($productid == $old_productid){
                    break;
                }
                $old_productid = $productid;
                $product_detail = $this->equalproduct_base->getEqualProductDetail($productid);
                if(empty($product_detail)){
                    break;
                }
                if($odate == date('Y-m-d', strtotime("+1 day"))){
                    $product_detail['online_time'] = $odate . ' 01:00';
                }else if($product_detail['online_time'] == ''){
                    if(NOW < mktime(1,0,0)){
                        $product_detail['online_time'] = $odate . ' 01:00';
                    }
                }
                if(isset($product_detail['sellmoney']) && $product_detail['sellmoney'] >= $product_detail['money'] || $product_detail['status'] >= 3){
                    $this->equalproduct_logic->setEqualProductSellOut($ptid, $productid);
                    goto equalproduct;
                }
                if($product_detail){
                    $rtn_array[] = $product_detail;
                }
            }
        }
        $data = array_merge($rtn_array, $yugao_array);
        return $data;
    }
    
    
    public function getCompleteProduct(){
        $this->load->model('base/product_base', 'product_base');
        $data = $this->product_base->getRePayMentList();
        $rtn = array();
        foreach ($data as $key => $_value){
            $rtn[$key] = json_decode($_value, true);
        }
        return $rtn;
    }
    
    public function getSelloutProduct(){
        $this->load->model('base/product_base', 'product_base');
        $data = $this->product_base->getSelloutProduct();
        $yesterday_data = $this->product_base->getSelloutProduct(date('Y-m-d', strtotime('-1 day')));
        $todaybeforyesterday_data = $this->product_base->getSelloutProduct(date('Y-m-d', strtotime('-2 day')));
        $rtn = array();
        $num = 0;
        foreach ($data as $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        foreach ($yesterday_data as $_key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        foreach ($todaybeforyesterday_data as $_key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
         return $rtn;
    }
    
    public function getSelloutLongProduct(){
        $this->load->model('base/longproduct_base', 'longproduct_base');
        $data = $this->longproduct_base->getSelloutLongProduct();
        $yesterday_data = $this->longproduct_base->getSelloutLongProduct(date('Y-m-d', strtotime('-1 day')));
        $rtn = array();
        $num = 0;
        foreach ($data as $key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        foreach ($yesterday_data as $key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        return $rtn;
    }
    
    public function getSelloutKlProduct(){
        $this->load->model('base/klproduct_base', 'klproduct_base');
        $data = $this->klproduct_base->getSelloutKlProduct();
        $yesterday_data = $this->klproduct_base->getSelloutKlProduct(date('Y-m-d', strtotime('-1 day')));
        $rtn = array();
        $num = 0;
        foreach ($data as $key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        foreach ($yesterday_data as $key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        return $rtn;
    }
    
    
    public function getequalSelloutEqualProduct(){
        $this->load->model('base/equalproduct_base', 'equalproduct_base');
        $data = $this->equalproduct_base->getSelloutEqualProduct();
        $yesterday_data = $this->equalproduct_base->getSelloutEqualProduct(date('Y-m-d', strtotime('-1 day')));
        $rtn = array();
        $num = 0;
        foreach ($data as $key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        foreach ($yesterday_data as $key => $_value){
            $rtn[$num] = json_decode($_value, true);
            $num++;
        }
        return $rtn;
        
    }
}

   
