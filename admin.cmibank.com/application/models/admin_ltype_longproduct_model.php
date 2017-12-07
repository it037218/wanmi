<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_ltype_longproduct_model extends Basemodel {

    private $_table = 'cmibank.cmibank_ltype_longproduct';
    
    public function __construct() {
        parent::__construct();
    }

    public function addLtypelongproduct($data){
        if(!$this->insertDataSql($data, $this->_table)){
            return false;
        }
        $this->rebuildLtypeLongproductListRedisCache($data['ptid'], $data['odate']);
        return true;
    }
    
    public function getMaxRindxByPtid($ptid, $odate){
        $where = array('ptid' => $ptid);
        $sql = "select max(rindex) as maxrindex from $this->_table where ptid = $ptid and odate='$odate'  and stype = 0 and `status` = 0";
        $data = $this->executeSql($sql);
        return $data[0]['maxrindex'] ? $data[0]['maxrindex'] : 0;
    }
    
    public function getLtypeLongproduct($where){
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getminrindex($ptid, $odate){
        $sql = "select min(rindex) as minrindex from $this->_table where odate = '$odate'  and stype = 0 and `status` = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
    public function get_the2_min_rindex($ptid, $odate, $min_rindex){
        $sql = "select min(rindex) as minrindex from $this->_table where `ptid` = $ptid  and  `odate` = '$odate' and rindex > $min_rindex  and stype = 0  and status = 0";
        $data = $this->executeSql($sql);
        return $data[0]['minrindex'];
    }
    
    public function getupLongproduct($rindex, $ptid, $odate, $minrindex){
        $sql = "select * from $this->_table where rindex < $rindex and ptid = '$ptid' and `odate` = '$odate'  and `stype` = 0 and `rindex` != '$minrindex' and `status` = 0 order by rindex desc limit 1";
        $data = $this->executeSql($sql);
        return isset($data[0]) ? $data[0] : false;
    }
    
    public function getdownLongproduct($rindex, $ptid, $odate){
        $sql = "select * from $this->_table where rindex > $rindex and ptid = '$ptid' and odate = '$odate'  and `stype` = 0 and `status` = 0 order by `rindex` limit 1";
        $data = $this->executeSql($sql);
        return isset($data[0]) ? $data[0] : false;
    }
    
    public function updateAllLtypeLongproductRindex($odate){
        $sql = "update $this->_table set rindex = rindex + 1 where odate = '$odate' and stype = 0 and status = 0";
        return  $this->executeSql($sql);
    }
    
    public function getAllltypeLongproductListByPtid($ptid, $odate){
        $where = array('ptid' => $ptid, 'odate' => $odate, 'status' => 0);
        return $this->selectDataListSql($this->_table, $where, 'rindex', array(1000, 0));
    }
    
    public function getLtypeLongproductList($ptid, $odate, $getyugao = false){
        $where = array('ptid' => $ptid, 'odate' => $odate,'status' => 0);
        if($getyugao){
            $where['stype'] = 1;
        }else{
            $where['stype'] = 0;
        }
        //这里有个bug 后面会自动limit 1 所以先limit 1000
        return $this->selectDataListSql($this->_table, $where, 'rindex', array(1000, 0));
    }
    
    public function rebuildLtypeLongproductListRedisCache($ptid, $odate) {
        $key = _KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_ . $odate. '_' .$ptid;
        $yugaokey = _KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_ . $odate. '_' .$ptid;
        self::$container['redis_default']->delete($key);
        self::$container['redis_default']->delete($yugaokey);
        $result = $this->getAllltypeLongproductListByPtid($ptid, $odate);
        foreach ($result as $_ltypelongproduct){
            if($_ltypelongproduct['status'] == 1){
                continue;
            }
            if($_ltypelongproduct['stype'] == 1){
                $yugaodata = array();
                $this->load->model('admin_longproduct_model', 'longproduct');
                $longproduct = $this->longproduct->getLongproductByPid($_ltypelongproduct['pid']);
                //$yugaodata['yugaotime'] = strtotime($longproduct['yugaotime']);
                $yugaodata['online_time'] = strtotime($longproduct['online_time']);
                $yugaodata['pid'] = $longproduct['pid'];
                $yugaodata['ptid'] = $longproduct['ptid'];
                self::$container['redis_default']->setAdd($yugaokey, json_encode($yugaodata), 1, $yugaodata['online_time']);
            }else{
                self::$container['redis_default']->listPush($key, $_ltypelongproduct['pid'], 1);
            }
        }
        return true;
    }
    
    public function updateLtypeLongporduct($data){
        return $this->updateDataSql($this->_table, $data, array('pid' => $data['pid'], 'odate' => $data['odate']));
    }
    
    public function updateLongorductByPid($pid, $data){
        return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
    }
    
    public function getTypeLongProductByPid($pid){
        return $this->selectDataSql($this->_table, array('pid' => $pid));   
    }
    
    public function delLtypeLongProduct($pid){
        if(!$this->deleteDataSql($this->_table, array('pid' => $pid))){
            return false;
        }
        return true;
    }
    
}
