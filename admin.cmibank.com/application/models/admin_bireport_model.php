<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_bireport_model extends Basemodel {

    private $_table = 'cmibank_yunying.cmibank_bi';
    
    private $_product_arr = array();
    
    public function __construct() {
        parent::__construct();
    }

    public function getBireport($where,$order,$limit){
        return $this->selectDataListSql($this->_table, $where,$order,$limit);
    }
    
    public function getBireportbetweenTime($stime,$etime, $limit = '') {
        $sql = "SELECT * FROM $this->_table WHERE `cdate` BETWEEN '$stime' and '$etime' ORDER BY daymoney DESC";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    public function getBireportByCondition($type,$stime,$etime, $limit = '') {
    	$sql = "SELECT * FROM $this->_table WHERE 1=1";
    	if(!empty($type)){
    		$sql = $sql." and plat='".$type."' ";
    	}
    	if(!empty($stime)){
    		$sql = $sql." and cdate>='".$stime."' ";
    	}
    	if(!empty($etime)){
    		$sql = $sql." and cdate<='".$etime."' ";
    	}
    	$sql = $sql." order by cdate desc,daymoney desc ";
    	if(!empty($limit)){
    		$sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
    	}
    	return $this->executeSql($sql);
    }
}
