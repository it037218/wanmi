<?php

require_once 'basemodel.php'; 

class equalproduct_base extends Basemodel{

    public $_table = 'cmibank.cmibank_equalproduct';
    
    private $_equalproduct;
    
   
    public function getEqualProductDetail($pid){
        if(isset($this->_equalproduct[$pid])){
            return $this->_equalproduct[$pid];
        }
        $key = _KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_ . $pid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $pid) {
            $productInfo = $self->_get_db_equalproduct_detail($pid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_app_w']);
        $product = json_decode($return , true);
        $this->_equalproduct[$pid] = $product;
        return $product;
    }
    
    public function _get_db_equalproduct_detail($pid){
        return $this->DBR->select('*')->from($this->_table)->where('pid',$pid)->get()->row_array();
    }
    
    public function setEqualProductCache($product){
        $key = _KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_ . $product['pid'];
        return self::$container['redis_default']->save($key, json_encode($product));
    }
    
    public function rsyncEqualProductSellMoney($pid){
        $this->load->model('base/equalproduct_buy_info_base', 'equalproduct_buy_info_base');
        $sellmoney = $this->equalproduct_buy_info_base->CountProductBuyMoney($pid);
        $sql = "UPDATE $this->_table SET `sellmoney` = " . $sellmoney . " WHERE `pid` =" . $pid;
        $key = _KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_default']->delete($key);
        $ret = $this->executeSql($sql);
        return $sellmoney;
    }
    
    
    public function addEqualProductToRePayMentList($pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $repaymentkey = _KEY_REDIS_SYSTEM_REPAYMENT_EQUALPRODUCT_LIST_PREFIX_ . $odate;
        $key = _KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_default']->delete($key);
        $product = $this->getEqualProductDetail($pid);
        return self::$container['redis_default']->setAdd($repaymentkey, json_encode($product), 1, $product['repaytime']);
    }
    
    public function deleteRePayMentList(){
        $odate = date("Y-m-d");
        $repaymentkey = _KEY_REDIS_SYSTEM_REPAYMENT_EQUALPRODUCT_LIST_PREFIX_ . $odate;
        return self::$container['redis_default']->delete($repaymentkey);
    }
    
    
    public function addEqualProductTotoDayRealdyRePayMentList($product, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $repaymentkey = _KEY_REDIS_SYSTEM_REPAYMENT_PRODUCT_LIST_PREFIX_ . $odate;
        $product['status'] = 6;
        $product['repayment_status'] = 1;
        return self::$container['redis_default']->setAdd($repaymentkey, json_encode($product), 1, $product['repaytime']);
    }
    
    public function getRePayMentList($odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $repaymentkey = _KEY_REDIS_SYSTEM_REPAYMENT_PRODUCT_LIST_PREFIX_ . $odate;
        return self::$container['redis_default']->setRange($repaymentkey, 0, -1);
    }
    
    public function addEqualProductToSellOutList($pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_EQUALPRODUCT_LIST_PREFIX_ . $odate;
        $key = _KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_default']->delete($pid);
        $product = $this->getEqualProductDetail($pid);
        return self::$container['redis_default']->setAdd($sellOutkey, json_encode($product), 1, $product['sellouttime']);
    }
    
    public function getSelloutList($start, $end, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_SELLOUT_EQUALPRODUCT_LIST_PREFIX_ . $odate;
        return self::$container['redis_default']->setRange($key, $start, $end, 1, 1);
    }
    
    public function getSelloutEqualProduct($odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_EQUALPRODUCT_LIST_PREFIX_ . $odate;
        return self::$container['redis_default']->setRange($sellOutkey, 0, -1, 1);
    }
    
    
    public function getOnlineEqualProductListSize($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_ . $odate . '_' . $ptid;
        return self::$container['redis_default']->listSize($key);
    }
    
    public function getOnlineEqualProductListFirstMem($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_default']->listGet($key);
    }
    
    public function getYuGaoEqualProductListSize($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_EQUALPRODUCT_LIST_PREFIX_ . $odate. '_' . $ptid;
        return self::$container['redis_default']->setSize($key, 1);
    }
    
    public function getYuGaoEqualProductListFirstMem($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        $data = self::$container['redis_default']->setRange($key, 0, 0);
        return json_decode($data[0], true);
    }
    
    public function moveOnlineEqualProduct($ptid, $pid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_default']->listRemove($key, $pid);
    }
    
    public function getOnlineEqualProductListAllMem($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_default']->listGet($key, 0, -1);
    }
    

    
    public function moveYuGaoToEqualProduct($ptid, $value, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $pid = $value['pid'];
        $key = _KEY_REDIS_SYSTEM_YUGAO_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        self::$container['redis_default']->zSetDelete($key, json_encode($value));
        $key = _KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
//         var_dump($pid);
        //$ret = self::$container['redis_default']->listPush($key, $pid, 0, 1);
        $ret = self::$container['redis_default']->listPush($key, $pid);
//         var_dump($ret);
//         exit;
        $this->load->model('base/equalptype_equalproduct_base', 'equalptype_equalproduct_base');
        $data = array();
        $odate =  date('Y-m-d');
        $minindex = $this->equalptype_equalproduct_base->getminrindex($ptid, $odate);
        $data['status'] = 0;
        $data['stype'] = 0;
        $data['rindex'] = --$minindex;
        $where = array();
        $where['ptid'] = $ptid;
        $where['pid'] = $pid;
        $where['odate'] =$odate;
        $this->equalptype_equalproduct_base->updateEqualPtypeEqualProduct($data, $where);
        return true;
    }
    
    //取出所有今日可以计算利息的产品
    public function getEqualProductListWithCountProfit($odate = ''){
        $odate = $odate ? $odate : date('Y-m-d');
        $sql = "SELECT * FROM " . $this->_table . " WHERE `uistime` <= '" . $odate . "' AND `uietime` >= '" . $odate . "' AND status in(1, 2, 3, 4, 5)";
        return $this->executeSql($sql);
    }
    
    public function getEqualProductListWithStatus($in, $odate = ''){
        if(empty($in)){
            exit("error! not params 'in' on product_base->getProductListWithStatus");
        }
        $odate = $odate ? $odate : date('Y-m-d');
        $sql = "SELECT * FROM " . $this->_table . " WHERE `uietime` = '" . $odate . "' AND status in(" . $in . ")";
        return $this->executeSql($sql);
    }
    
    
    /**
     * @param unknown $pid
     * @param unknown $status 2下架,  3售罄,  4停售,  5已回款,  6已还款  
     */
    public function updateEqualProductStatus($pid, $data){
        
        $ret = $this->updateDataSql($this->_table, $data, array('pid' => $pid));
        $key = _KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_ . $pid;
        self::$container['redis_app_w']->delete($key);
        $this->_product[$pid] = null;
        return $ret;
    }
    
    public function countSellMoneyByCid($cid){
        $sql = "SELECT sum(sellmoney) as countsellmoney FROM " . $this->_table . " WHERE `cid` = " . $cid . " AND `status` not in(0, 1)";
        $data = $this->executeSql($sql);
        return $data[0]['countsellmoney'];
    }
   
    public function countOnlineEqualProductMoneyByCid($cid){
        $sql = "SELECT sum(money) as countsellmoney FROM " . $this->_table . " WHERE `cid` = " . $cid . " AND `status` in(0, 1)";
        $data = $this->executeSql($sql);
        return $data[0]['countsellmoney'];
    }
    
    public function moveOnlineEqualProductByPtid($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_default']->delete($key);
    }
    
    public function moveYugaoProductByPtid($ptid, $odate = ''){
        $odate = $odate ? $odate : date("Y-m-d");
        $key = _KEY_REDIS_SYSTEM_YUGAO_EQUALPRODUCT_LIST_PREFIX_ . $odate .  '_'  . $ptid;
        return self::$container['redis_default']->delete($key);
    }
    
    public function updateEqualProductStatusWithSql($pid){
        $sql = 'UPDATE ' . $this->_table . ' SET `status` = 2 WHERE `pid` = ' . $pid . ' AND status <= 1';
        $this->executeSql($sql);
    }
    
    public function sum_sellout_equalproduct($odate){
        $sql = 'select sum(sellmoney) as sum_money from ' . $this->_table . " where uistime = '$odate' ";
        $data = $this->executeSql($sql);
        return $data[0]['sum_money'];
    }
    
}
