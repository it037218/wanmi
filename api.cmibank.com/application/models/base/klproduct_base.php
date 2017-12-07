<?php

require_once 'basemodel.php'; 

class klproduct_base extends Basemodel{

    public $_table = 'cmibank.cmibank_klproduct';
   
    public function getKlProductSellmoney($odate){
        $sql = "SELECT sum(sellmoney) as sum_sellmoney FROM " . $this->_table . " WHERE odate = '" . $odate . "'";
        $data = $this->executeSql($sql);
        return $data[0]['sum_sellmoney'] ? $data[0]['sum_sellmoney'] : 0;
    }
    
    public function getKlProductDetail($pid){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_ . $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $klproductInfo = $self->_get_db_klproduct_detail($pid);
            if(empty($klproductInfo)) return false;
            return json_encode($klproductInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
        return json_decode($return , true);
    }
    
    public function _get_db_klproduct_detail($pid){
        return $this->DBR->select('*')->from($this->_table)->where('pid',$pid)->get()->row_array();
    }
    
    public function setKlProductCache($klproduct){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_ . $klproduct['pid'];
        return self::$container['redis_app_w']->save($key, json_encode($klproduct));
    }
    
    public function rsyncKlProductSellMoney($pid){
        $this->load->model('base/klproduct_buy_info_base', 'klproduct_buy_info_base');
        $sellmoney = $this->klproduct_buy_info_base->CountKlProductBuyMoney($pid);
        $sql = "UPDATE $this->_table SET `sellmoney` = " . $sellmoney . " WHERE `pid` =" . $pid;
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        $ret = $this->executeSql($sql);
        return $sellmoney;
    }
    
    public function delOnlineKlProductList($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . $odate . '_' . $ltid;
        return self::$container['redis_app_r']->delete($key);
    }
    
    public function getOnlineKlProductListSize($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . $odate . '_' . $ltid;
        return self::$container['redis_app_r']->listSize($key);
    }
    
    public function getOnlineKlProductListFirstMem($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        return self::$container['redis_app_r']->listGet($key);
    }
    
    public function getYuGaoKlProductListSize($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_KLPRODUCT_LIST_PREFIX_ . $odate. '_' . $ltid;
        return self::$container['redis_default']->setSize($key, 1);
    }
    
    public function getYuGaoKlProductListFirstMem($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_KLPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        $data = self::$container['redis_default']->setRange($key, 0, 0 );
        return json_decode($data[0], true);
    }
    
    public function moveOnlineKlProduct($ltid, $pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        self::$container['redis_app_w']->listRemove($key, $pid);
    }
    
    public function getOnlineKlProductListAllMem($ltid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ltid;
        return self::$container['redis_app_r']->listGet($key, 0, -1);
    }
    
    public function getminrindex($ltid, $odate){
        $sql = "select min(rindex) as minrindex from cmibank.cmibank_ltype_longproduct where odate = '$odate'  and stype = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
    public function moveYuGaoToKlProduct($ltid, $value){
        $pid = $value['pid'];
        
        $key = _KEY_REDIS_SYSTEM_YUGAO_KLPRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ltid;
        self::$container['redis_default']->zSetDelete($key, json_encode($value));
        $key = _KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ltid;
        $ret = self::$container['redis_default']->listPush($key, $pid, 0);
//          var_dump($ret);
//         exit;
        $this->load->model('base/kltype_klproduct_base', 'kltype_klproduct_base');
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
        $this->kltype_klproduct_base->updateKltypeKlProduct($data, $where);
        return true;
    }
    
    
    /**
     * 
     * @param unknown $pid
     * @param unknown $status 2下架,  3售罄,  4停售
     */
    public function updateKlProductStatus($pid, $data){
        $key = _KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
    public function addKlProductToSellOutList($pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_KLPRODUCT_LIST_PREFIX_ . $odate;
        $longproduct = $this->getKlProductDetail($pid);
        return self::$container['redis_default']->setAdd($sellOutkey, json_encode($longproduct), 1, $longproduct['sellouttime']);
    }
    
    public function getSelloutKlProduct($odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_KLPRODUCT_LIST_PREFIX_ . $odate;
        return self::$container['redis_default']->setRange($sellOutkey, 0, -1, 1);
    }
    
    
}
