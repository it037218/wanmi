<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_contract_model extends Basemodel {

    private $_table = 'cmibank.cmibank_contract';
    private $product_table = 'cmibank.cmibank_product';
    private $cmibank_creditor_information = 'cmibank.cmibank_creditor_information';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getContracttoStock(){
    	$sql = "select * from $this->_table where con_money>money order by ctime desc";
    	return $this->executeSql($sql);
    	 
    }
    
    public function getContractIstock($con_number,$is_stock,$limit = ''){
        if($con_number == '请输入搜索内容'){
            $con_number = '';
        }
        if($is_stock == 1){
            $sql = "select * from $this->_table where con_number like '%$con_number%' and is_stock = 1";
        }else{
            $sql = "select * from $this->_table where con_number like '%$con_number%'";
        }
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
       
    }
    public function getContractByinteresttime($corname,$star,$end,$limit=''){
        if($star != '请输入开始日期' && $end != '请输入结束日期'){
            $sql = "SELECT * FROM $this->_table where corname like '%$corname%' and interesttime BETWEEN '$star' and '$end' order by interesttime asc";
            if(!empty($limit)){
                $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
            }    
        }
        return $this->executeSql($sql);
    }
    public function getContractlistsql($params,$limit=''){
    	$sql='';
    	if (isset($params['weidakuang'])){
    		$sql="SELECT DISTINCT con.* FROM $this->_table as con left JOIN $this->product_table as p on con.cid=p.cid where (p.is_upload=0 or con.con_dkje=0) ";
    	}else{
	        $sql = "SELECT * FROM $this->_table as con where 1=1 ";
    	}
    	if (isset($params['corname'])){
    		$sql .= " and con.corname like '%".$params['corname']."%'";
    	}
    	if (isset($params['status'])){
    		if($params['status']==1){
    			$sql .= " and con.bzjimg='' ";
    		}else if($params['status']==2){
    			$sql .= " and con.bzjimg!='' and  con.returnbzjimg=''and UNIX_TIMESTAMP(con.repaymenttime)>".NOW;
    		}else if($params['status']==3){
    			$sql .= " and con.returnbzjimg!='' ";
    		}else if($params['status']==4){
    			$sql .= " and con.bzjimg!='' and  con.returnbzjimg='' and UNIX_TIMESTAMP(con.repaymenttime)<=".NOW;
    		}
    	}
    	if (isset($params['con_number'])){
    		$sql .= " and con.con_number like '%".$params['con_number']."%'";
    	}
    	if (isset($params['repaymentstime'])){
    		$sql = $sql." and UNIX_TIMESTAMP(con.repaymenttime)>=UNIX_TIMESTAMP('".$params['repaymentstime']."')";
    	}
    	if (isset($params['repaymentetime'])){
    		$sql = $sql." and UNIX_TIMESTAMP(con.repaymenttime)<=UNIX_TIMESTAMP('".$params['repaymentetime']."')";
    	}
    	if (isset($params['intereststime'])){
    		$sql = $sql." and UNIX_TIMESTAMP(con.interesttime)>=UNIX_TIMESTAMP('".$params['intereststime']."')";
    	}
    	if (isset($params['interestetime'])){
    		$sql = $sql." and UNIX_TIMESTAMP(con.interesttime)<=UNIX_TIMESTAMP('".$params['interestetime']."')";
    	}
    	if (isset($params['remitstime'])){
    		$sql = $sql." and con.remittime >=".$params['remitstime'];
    	}
    	if (isset($params['remitetime'])){
    		$sql = $sql." and con.remittime <=".$params['remitetime'];
    	}
    	if (isset($params['is_null'])){
    		$sql = $sql." and  con.real_money=con.money";
    	}
        $sql .= " and con.shenghe=1 order by con.repaymenttime asc";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    public function getContractlistWhere($corname,$con_number,$interesttime,$repaymenttime){
        if($corname == '请输入搜索内容'){
            $corname = '';
        }
        if($con_number == '请输入搜索内容'){
            $con_number = '';
        }
        $sql = "SELECT * FROM $this->_table where corname like '%$corname%' and con_number like '%$con_number%'";
        if($interesttime != '请输入开始日期' or $repaymenttime != '请输入结束日期'){
            if($interesttime == '请输入开始日期'){
                $interesttime = date('Y-m-d');
            }else if($repaymenttime == '请输入结束日期'){
                $repaymenttime = date('Y-m-d');
            }
            $sql .= " and interesttime >= '$interesttime' and repaymenttime <='$repaymenttime'";
        }
        $sql .= " order by repaymenttime asc";
        return $this->executeSql($sql);
    }
    public function getContractList($where, $order, $limit){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    public function getContractLikeCon_number($con_number, $limit = ''){
        
        $sql = "SELECT * FROM $this->_table WHERE `con_number` like '%".$con_number ."%' ";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public function getContractLikeCorname($corname, $limit = ''){
    
        $sql = "SELECT * FROM $this->_table WHERE `corname` like '%".$corname ."%' ";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    
    public function getContractByCid($cid) {
        $key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_  . $cid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $cid) {
            $contractInfo = $self->get_db_ContractByCid($cid);
            if(empty($contractInfo)) return false;
            return json_encode($contractInfo);
        } , _REDIS_DATATYPE_STRING);
        return json_decode($return, true);
    }
    
    public function get_db_ContractByCid($cid){
    	$sql = "SELECT * from $this->_table as a left JOIN $this->cmibank_creditor_information as b on a.creid=b.id where a.cid=".$cid;
        $ret=  $this->executeSql($sql);
        return $ret[0]?$ret[0]:false;
    }

    public function getContrctByCreid($creid){
        return $this->selectDataSql($this->_table, array('creid' => $creid));
    }
    
    
    public function updateContract($cid, $data){
        $ret = $this->updateDataSql($this->_table, $data, array('cid' => $cid));
        $this->_flushContractDetailRedisCache($cid);
        return $ret;
    }
    
    public function addContract($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
	public function delContract($cid){
	    $key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_ . $cid;
	    self::$container['redis_app_w']->delete($key);
	    return $this->deleteDataSql($this->_table, array('cid' => $cid));
	}
	public function deContractRedisCache($cid){
	    $key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_ . $cid;
	    return self::$container['redis_app_w']->delete($key);
	}
	public function getContractCount(){
	    return $this->selectDataCountSql($this->_table);
	}
	
	public function getContractByCorid($corid){
	    $sql = "SELECT * FROM " . $this->_table . " WHERE `corid` = " . $corid . "  AND `con_money` > `money` AND `status` IN (0,1) and shenghe=1 ORDER BY ctime";
	    return $this->executeSql($sql);
	}
	
	public function getContractByStatus($where,$order,$limit=null){
	    //print_r($where['status']);
	   return $this->selectDataListSql($this->_table, $where,$order,$limit);
	}
	
	public function updateContractMoney($cid, $money){
	    $sql = "UPDATE " . $this->_table . " SET status=1 , money = money + " . $money . " WHERE cid = " . $cid . " AND con_money > money";
	    $ret = $this->executeSql($sql);
	    $this->_flushContractDetailRedisCache($cid);
	    return $ret;
	}
	
	public function updateContractstatus($cid,$status){
	    $sql = "UPDATE " . $this->_table . " SET status = " . $status . " WHERE cid = " . $cid;
	    return $this->executeSql($sql);
	}
	
    public function _flushContractDetailRedisCache($cid){
		$key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_ . $cid;
	    self::$container['redis_app_w']->delete($key);
		$this->getContractByCid($cid);
	}
	
	public function backMoneytoContract($cid, $sellmoney){
	    $sql = "UPDATE " . $this->_table . " SET  `money` = " . $sellmoney . " WHERE `cid` = " . $cid;
	    $ret = $this->executeSql($sql);
	    if($ret){
	        $key = _KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_ . $cid;
	        self::$container['redis_app_w']->delete($key);
	    }else{
	        return false;
	    }
	    return true;
	}
	
	public function updatebackcontract($cid, $data){
	    return $this->updateDataSql($this->_table, $data, array('cid' => $cid));
	}
	public function get_contract_cids($cids,$limit=''){
	    $sql = "select * from $this->_table where cid in ($cids)";
	    if(!empty($limit)){
	        $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
	    }
	    return $this->executeSql($sql);
	}
    
	public function getRepaymenttimeBigNow(){
	    $t = date("Y-m-d",NOW);
	    $sql = "SELECT DISTINCT(corid) FROM $this->_table where repaymenttime >=$t order by repaymenttime desc";
	    $aa = $this->executeSql($sql);
	   
	    foreach ($aa as $key=>$val){
	        $bb[] = $val['corid'];
	    }
	    return implode(',', $bb);
	}
	
	public function getYetRepayment($searchParam, $offset,$psize){
		$sql = "SELECT * FROM $this->_table where UNIX_TIMESTAMP(repaymenttime)>".NOW;
		if(isset($searchParam['cor_name'])){
			$sql = $sql." and corname like '%".$searchParam['cor_name']."%' ";
		}
		if (isset($searchParam['cor_number'])){
			$sql = $sql." and con_number like '%".$searchParam['cor_number']."%' ";
		}
		$sql = $sql.' limit '.$offset.','.$psize;
		return $this->executesql($sql);
	}
	
	public function countYetRepayment($searchParam){
		$sql = "SELECT count(cid) as count FROM $this->_table where UNIX_TIMESTAMP(repaymenttime)>".NOW;
		if(isset($searchParam['cor_name'])){
			$sql = $sql." and corname like '%".$searchParam['cor_name']."%' ";
		}
		if (isset($searchParam['cor_number'])){
			$sql = $sql." and con_number like '%".$searchParam['cor_number']."%' ";
		}
		$ret = $this->executesql($sql);
		return $ret[0]['count'];
	}
	
	public function getContractByCondition($searchParam, $offset,$psize){
		$sql = "SELECT * FROM $this->_table where 1=1 ";
		$orderby = ' order by UNIX_TIMESTAMP(interesttime) desc,cid desc';
		if(isset($searchParam['corname'])){
			$sql = $sql." and corname like '%".$searchParam['corname']."%' ";
		}
		if (isset($searchParam['con_number'])){
			$sql = $sql." and con_number like '%".$searchParam['con_number']."%' ";
		}
		if (isset($searchParam['repaymenttime_star'])){
			$sql = $sql." and UNIX_TIMESTAMP(repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['repaymenttime_star']."')";
			$orderby= ' order by UNIX_TIMESTAMP(repaymenttime) asc ';
		}
		if (isset($searchParam['repaymenttime_end'])){
			$sql = $sql." and UNIX_TIMESTAMP(repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['repaymenttime_end']."')";
			$orderby= ' order by UNIX_TIMESTAMP(repaymenttime) asc ';
		}
		if (isset($searchParam['interesttime_star'])){
			$sql = $sql." and UNIX_TIMESTAMP(interesttime)>=UNIX_TIMESTAMP('".$searchParam['interesttime_star']."')";
			$orderby= ' order by UNIX_TIMESTAMP(interesttime) asc ';
		}
		if (isset($searchParam['interesttime_end'])){
			$sql = $sql." and UNIX_TIMESTAMP(interesttime)<=UNIX_TIMESTAMP('".$searchParam['interesttime_end']."')";
			$orderby= ' order by UNIX_TIMESTAMP(interesttime) asc ';
		}
		if (isset($searchParam['searchmortgagor'])){
			$sql = $sql." and mortgagor like '%".$searchParam['searchmortgagor']."%'";
			$orderby= ' order by UNIX_TIMESTAMP(interesttime) asc ';
		}
		if(isset($searchParam['shenghe'])){
			if($searchParam['shenghe']=="1"){
				$sql = $sql." and shenghe=1 ";
			}else{
				$sql = $sql." and shenghe=0 ";
			}
		}
		$sql = $sql.$orderby.' limit '.$offset.','.$psize;
		return $this->executesql($sql);
	}
	public function countContractByCondition($searchParam){
		$sql = "SELECT count(cid) as count FROM $this->_table where 1=1 ";
		if(isset($searchParam['corname'])){
			$sql = $sql." and corname like '%".$searchParam['corname']."%' ";
		}
		if (isset($searchParam['con_number'])){
			$sql = $sql." and con_number like '%".$searchParam['con_number']."%' ";
		}
		if (isset($searchParam['repaymenttime_star'])){
			$sql = $sql." and UNIX_TIMESTAMP(repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['repaymenttime_star']."')";
		}
		if (isset($searchParam['repaymenttime_end'])){
			$sql = $sql." and UNIX_TIMESTAMP(repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['repaymenttime_end']."')";
		}
		if (isset($searchParam['interesttime_star'])){
			$sql = $sql." and UNIX_TIMESTAMP(interesttime)>=UNIX_TIMESTAMP('".$searchParam['interesttime_star']."')";
		}
		if (isset($searchParam['interesttime_end'])){
			$sql = $sql." and UNIX_TIMESTAMP(interesttime)<=UNIX_TIMESTAMP('".$searchParam['interesttime_end']."')";
		}
		if (isset($searchParam['searchmortgagor'])){
			$sql = $sql." and mortgagor like '%".$searchParam['searchmortgagor']."%'";
		}
		if(isset($searchParam['shenghe'])){
			if($searchParam['shenghe']=="1"){
				$sql = $sql." and shenghe=1 ";
			}else{
				$sql = $sql." and shenghe=0 ";
			}
		}
		$ret = $this->executesql($sql);
		return $ret[0]['count'];
	}
	public function sumContractByCondition($searchParam){
		$sql = "SELECT sum(con_money) as sum_con_money,sum(money) as sum_money FROM $this->_table where shenghe=1 ";
		if(isset($searchParam['corname'])){
			$sql = $sql." and corname like '%".$searchParam['corname']."%' ";
		}
		if (isset($searchParam['con_number'])){
			$sql = $sql." and con_number like '%".$searchParam['con_number']."%' ";
		}
		if (isset($searchParam['repaymenttime_star'])){
			$sql = $sql." and UNIX_TIMESTAMP(repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['repaymenttime_star']."')";
		}
		if (isset($searchParam['repaymenttime_end'])){
			$sql = $sql." and UNIX_TIMESTAMP(repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['repaymenttime_end']."')";
		}
		if (isset($searchParam['interesttime_star'])){
			$sql = $sql." and UNIX_TIMESTAMP(interesttime)>=UNIX_TIMESTAMP('".$searchParam['interesttime_star']."')";
		}
		if (isset($searchParam['interesttime_end'])){
			$sql = $sql." and UNIX_TIMESTAMP(interesttime)<=UNIX_TIMESTAMP('".$searchParam['interesttime_end']."')";
		}
		if (isset($searchParam['searchmortgagor'])){
			$sql = $sql." and mortgagor like '%".$searchParam['searchmortgagor']."%'";
		}
		$ret = $this->executesql($sql);
		return $ret[0];
	}
	
}
