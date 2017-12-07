<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_ptype_product_model extends Basemodel {

    private $_table = 'cmibank.cmibank_ptype_product';
    
    public function __construct() {
        parent::__construct();
    }

    public function addptypeproduct($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->rebuildPtypeProductListRedisCache($data['ptid'], $data['odate']);
        return true;
    }
    public function delPtypeProduct($pid){
        if(!$this->deleteDataSql($this->_table, array('pid' => $pid))){
            return false;
        }
        return true;
    }
    public function getMaxRindxByPtid($ptid, $odate){
        $where = array('ptid' => $ptid);
        $sql = "select max(rindex) as maxrindex from $this->_table where ptid = $ptid and odate='$odate'  and stype = 0 and status = 0";
        $data = $this->executeSql($sql);
        return $data[0]['maxrindex'] ? $data[0]['maxrindex'] : 0;
    }
    
    public function getPtypeProduct($where){
        return $this->selectDataSql($this->_table, $where);
    }
   
    
    public function getminrindex($ptid, $odate){
        $sql = "select min(rindex) as minrindex from $this->_table where `ptid` = $ptid and  `odate` = '$odate'  and stype = 0  and status = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
    public function get_the2_min_rindex($ptid, $odate, $min_rindex){
        $sql = "select min(rindex) as minrindex from $this->_table where `ptid` = $ptid  and  `odate` = '$odate' and rindex > $min_rindex  and stype = 0  and status = 0";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
    public function getupproduct($rindex, $ptid, $odate, $minrindex){
        $sql = "select * from $this->_table where rindex < $rindex and ptid = '$ptid' and odate = '$odate'  and stype = 0 and rindex != '$minrindex'  and status = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return isset($data[0]) ? $data[0] : false;
    }
    
    public function updateAllPtypePorductRindex($odate,$ptid, $rindex){
        $sql = "update $this->_table set rindex = rindex + 1 where odate = '$odate' and ptid = $ptid and stype = 0  and status = 0 and rindex >= $rindex";
        return  $this->executeSql($sql);
    }
    
    public function getdownproduct($rindex, $ptid, $odate){
        $sql = "select * from $this->_table where rindex > $rindex and ptid = '$ptid' and odate = '$odate'  and stype = 0  and status = 0 order by rindex limit 1";
        $data = $this->executeSql($sql);
        return isset($data[0]) ? $data[0] : false;
    }
    
    
    public function getAllptypeProductListByPtid($ptid, $odate){
        $where = array('ptid' => $ptid, 'odate' => $odate);
        return $this->selectDataListSql($this->_table, $where, 'rindex', array(1000, 0));
    }
    
    public function getPtypeProductList($ptid, $odate, $getyugao = false){
        $where = array('ptid' => $ptid, 'odate' => $odate, 'status' => 0);
        if($getyugao){
            $where['stype'] = 1;
        }else{
            $where['stype'] = 0;
        }
        //这里有个bug 后面会自动limit 1 所以先limit 1000
        return $this->selectDataListSql($this->_table, $where, 'rindex', array(1000, 0));
    }
    
    public function getPtypeProductAll($where){
        return $this->selectDataListSql($this->_table, $where);
    }
    
    public function rebuildPtypeProductListRedisCache($ptid, $odate) {
        $key = _KEY_REDIS_SYSTEM_ONLINE_PRODUCT_LIST_PREFIX_ . $odate. '_' .$ptid;
        $yugaokey = _KEY_REDIS_SYSTEM_YUGAO_PRODUCT_LIST_PREFIX_ . $odate. '_' .$ptid;
        $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_PRODUCT_LIST_PREFIX_ . $odate;
        self::$container['redis_default']->delete($key);
        self::$container['redis_default']->delete($yugaokey);
        $result = $this->getAllptypeProductListByPtid($ptid, $odate);
        $this->load->model('admin_product_model', 'product');
        foreach ($result as $_ptypeproduct){
            if($_ptypeproduct['status'] != 0){
//                 $product = $this->product->getProductByPid($_ptypeproduct['pid']);
//                 self::$container['redis_default']->setAdd($sellOutkey, json_encode($product), 1, $product['sellouttime']);
                continue;
            }
            if($_ptypeproduct['stype'] == 1){
                $yugaodata = array();
                $this->load->model('admin_product_model', 'product');
                $product = $this->product->getProductByPid($_ptypeproduct['pid']);
                $yugaodata['online_time'] = strtotime($product['online_time']);
                $yugaodata['pid'] = $product['pid'];
                $yugaodata['ptid'] = $product['ptid'];
                self::$container['redis_default']->setAdd($yugaokey, json_encode($yugaodata), 1, $yugaodata['online_time']);
            }else{
                self::$container['redis_default']->listPush($key, $_ptypeproduct['pid'], 1);
            }
        }
        return true;
    }
    
    public function updatePtypePorduct($data){
        return $this->updateDataSql($this->_table, $data, array('pid' => $data['pid'], 'odate' => $data['odate']));
    }
    
    public function updatePorductByPid($pid, $data){
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
    
    
}
