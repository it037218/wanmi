<?php

require_once 'basemodel.php'; 

class longproduct_base extends Basemodel{

    public $_table = 'cmibank.cmibank_longproduct';
   
    public function getLongProductSellmoney($odate){
        $sql = "SELECT sum(sellmoney) as sum_sellmoney FROM " . $this->_table . " WHERE odate = '" . $odate . "'";
        $data = $this->executeSql($sql);
        return $data[0]['sum_sellmoney'] ? $data[0]['sum_sellmoney'] : 0;
    }
    
    public function getLongProductDetail($pid){
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_ . $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $productInfo = $self->_get_db_longproduct_detail($pid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_longproduct_detail($pid){
        return $this->DBR->select('*')->from($this->_table)->where('pid',$pid)->get()->row_array();
    }
    
    public function setLongProductCache($product){
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_ . $product['pid'];
        return self::$container['redis_app_w']->save($key, json_encode($product));
    }
    
    public function rsyncLongProductSellMoney($pid){
        $this->load->model('base/longproduct_buy_info_base', 'longproduct_buy_info_base');
        $sellmoney = $this->longproduct_buy_info_base->CountLongProductBuyMoney($pid);
        $sql = "UPDATE $this->_table SET `sellmoney` = " . $sellmoney . " WHERE `pid` =" . $pid;
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        $ret = $this->executeSql($sql);
        return $sellmoney;
    }
    
    public function delOnlineLongProductList($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate . '_' . $ltid;
        return self::$container['redis_app_r']->delete($key);
    }
    
    public function getOnlineLongProductListSize($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate . '_' . $ltid;
        return self::$container['redis_app_r']->listSize($key);
    }
    
    public function getOnlineLongProductListFirstMem($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        return self::$container['redis_app_r']->listGet($key);
    }
    
    public function getYuGaoLongProductListSize($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_ . $odate. '_' . $ltid;
        return self::$container['redis_default']->setSize($key, 1);
    }
    
    public function getYuGaoLongProductListFirstMem($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        $data = self::$container['redis_default']->setRange($key, 0, 0 );
        return json_decode($data[0], true);
    }
    
    public function moveOnlineLongProduct($ltid, $pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        self::$container['redis_app_w']->listRemove($key, $pid);
    }
    
    public function getOnlineLongProductListAllMem($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        return self::$container['redis_app_r']->listGet($key, 0, -1);
    }
    
    public function getminrindex($ltid, $odate){
        $sql = "select min(rindex) as minrindex from cmibank.cmibank_ltype_longproduct where odate = '$odate'  and stype = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
    public function moveYuGaoToLongProduct($ltid, $value){
        $pid = $value['pid'];
        
        $key = _KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ltid;
        self::$container['redis_default']->zSetDelete($key, json_encode($value));
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ltid;
        $ret = self::$container['redis_default']->listPush($key, $pid, 0);
//          var_dump($ret);
//         exit;
        $this->load->model('base/ltype_longproduct_base', 'ltype_longproduct_base');
        $data = array();
        $odate =  date('Y-m-d');
        $minindex = $this->getminrindex($ltid, $odate);
        $data['status'] = 0;
        $data['stype'] = 0;
        $data['rindex'] = --$minindex;
        $where = array();
        $where['ptid'] = $ltid;
        $where['pid'] = $pid;
        $where['odate'] =$odate;
        $this->ltype_longproduct_base->updateltypeLongProduct($data, $where);
        return true;
    }
    
    
    /**
     * 
     * @param unknown $pid
     * @param unknown $status 2下架,  3售罄,  4停售
     */
    public function updateLongProductStatus($pid, $data){
        $key = _KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
    public function addLongProductToSellOutList($pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_LONGPRODUCT_LIST_PREFIX_ . $odate;
        $longproduct = $this->getlongProductDetail($pid);
        return self::$container['redis_default']->setAdd($sellOutkey, json_encode($longproduct), 1, $longproduct['sellouttime']);
    }
    
    public function getSelloutLongProduct($odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_LONGPRODUCT_LIST_PREFIX_ . $odate;
        return self::$container['redis_default']->setRange($sellOutkey, 0, -1, 1);
    }
}
