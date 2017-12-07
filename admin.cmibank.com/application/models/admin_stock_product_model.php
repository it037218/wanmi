<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_stock_product_model extends Basemodel {
    
     private $_table = 'cmibank.cmibank_stock_product';
     private $c_table = 'cmibank.cmibank_contract';
     private $backmoney_table = 'cmibank.cmibank_product_backmoney';
     public function __construct() {
         parent::__construct();
     }
     
     public function getStockProductList($where=null,$order=null,$limit=null){
         return $this->selectDataListSql($this->_table,$where,$order,$limit);
     }
     
     public function addStockProduct($data){
         return $this->insertDataSql($data, $this->_table);
     }
     
     public function updateStockProduct($cid, $data){
         return $this->updateDataSql($this->_table, $data, array('cid' => $cid));
     }
     
     public function getStockProductByCondition($searchParam, $limit = ''){
     	$sql = "SELECT sp.* from $this->_table as sp left join  $this->c_table as c on sp.cid=c.cid where 1=1 ";
     	if(isset($searchParam['type'])){
     		$sql = $sql." and sp.status = ".$searchParam['type'];
     	}
     	if(isset($searchParam['con_number'])){
     		$sql = $sql." and c.con_number like '%".$searchParam['con_number']."%'";
     	}
     	if(isset($searchParam['stime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['stime']."')";
     	}
     	if(isset($searchParam['etime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['etime']."')";
     	}
     	if(!empty($limit)){
     		$sql = $sql .' order by sp.ctime desc limit ' . $limit[1] . ', ' . $limit[0];
     	}
     	return $this->executeSql($sql);
     }
     public function sumStockProductByCondition($searchParam){
     	$sql = "SELECT sum(c.money) as sum_money,sum(sp.stockmoney) as sum_stockmoney from $this->_table as sp left join  $this->c_table as c on sp.cid=c.cid where 1=1 ";
     	if(isset($searchParam['type'])){
     		$sql = $sql." and sp.status = ".$searchParam['type'];
     	}
     	if(isset($searchParam['con_number'])){
     		$sql = $sql." and c.con_number like '%".$searchParam['con_number']."%'";
     	}
     	if(isset($searchParam['stime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['stime']."')";
     	}
     	if(isset($searchParam['etime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['etime']."')";
     	}
     	$ret = $this->executeSql($sql);
     	return $ret[0];
     }
     public function getSumStockMoney($cid){
         $sql = "SELECT SUM(stockmoney) FROM $this->_table where `status` = 0 and cid =$cid";
         return $this->executeSql($sql);
     }
     public function getAllSumStockMoney($cid){
     	$sql = "SELECT SUM(stockmoney) as sumStockMoney FROM $this->_table where cid =$cid";
     	$ret = $this->executeSql($sql);
     	return $ret[0]['sumStockMoney'];
     }
     
     public function getStockContractByCondition($searchParam, $limit = '', $orderby=''){
     	$sql = "SELECT c.*,stp.stockmoney,stp.ctime as stocktime,stp.status as stockstatus,backmoney.status as backstatus,backmoney.remitmoney,backmoney.backmoney from $this->c_table as c left join $this->_table as stp on stp.cid=c.cid left join $this->backmoney_table as backmoney on c.cid=backmoney.cid where 1=1 ";
     	if(isset($searchParam['type'])){
     		if($searchParam['type']==3){
    	 		$sql = $sql." and backmoney.status=3";
     		}else{
     			$sql = $sql." and backmoney.status<3";
     		}
     	}
     	if(isset($searchParam['corname'])){
     		$sql = $sql." and c.corname like '%".$searchParam['corname']."%'";
     	}
     	if(isset($searchParam['con_number'])){
     		$sql = $sql." and c.con_number like '%".$searchParam['con_number']."%'";
     	}
     	if(isset($searchParam['dkstime'])){
     		$sql = $sql." and c.remittime>=UNIX_TIMESTAMP('".$searchParam['dkstime']."')";
     	}
     	if(isset($searchParam['dketime'])){
     		$sql = $sql." and c.remittime<='".$searchParam['dketime']."'";
     	}
     	if(isset($searchParam['stime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.interesttime)>=UNIX_TIMESTAMP('".$searchParam['stime']."')";
     	}
     	if(isset($searchParam['etime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.interesttime)<=UNIX_TIMESTAMP('".$searchParam['etime']."')";
     	}
     	if(isset($searchParam['sjtime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['sjtime']."')";
     	}
     	if(isset($searchParam['ejtime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['ejtime']."')";
     	}
     	if(isset($searchParam['corid'])){
     		$sql = $sql." and c.corid=".$searchParam['corid'];
     	}
     	if(!empty($orderby)){
     		$sql = $sql .' order by ' . $orderby;
     	}else{
     		$sql = $sql .' order by UNIX_TIMESTAMP(c.repaymenttime) asc ';
     	}
     	if(!empty($limit)){
     		$sql = $sql .' limit ' . $limit[1] . ', ' . $limit[0];
     	}
     	return $this->executeSql($sql);
     }
     
     public function sumStockContractByCondition($searchParam){
     	$sql = "SELECT sum(c.con_money)as total_con_money,sum(stp.stockmoney) as sum_stockmoney, sum(money) as sum_money from $this->c_table as c left join $this->_table as stp on stp.cid=c.cid left join $this->backmoney_table as backmoney on c.cid=backmoney.cid where 1=1 ";
     	if(isset($searchParam['type'])){
     		if($searchParam['type']==3){
     			$sql = $sql." and backmoney.status=3";
     		}else{
     			$sql = $sql." and backmoney.status<3";
     		}
     	}
     	if(isset($searchParam['corname'])){
     		$sql = $sql." and c.corname like '%".$searchParam['corname']."%'";
     	}
     	if(isset($searchParam['con_number'])){
     		$sql = $sql." and c.con_number like '%".$searchParam['con_number']."%'";
     	}
     	if(isset($searchParam['dkstime'])){
     		$sql = $sql." and c.remittime>=UNIX_TIMESTAMP('".$searchParam['dkstime']."')";
     	}
     	if(isset($searchParam['dketime'])){
     		$sql = $sql." and c.remittime<='".$searchParam['dketime']."'";
     	}
     	if(isset($searchParam['stime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.interesttime)>=UNIX_TIMESTAMP('".$searchParam['stime']."')";
     	}
     	if(isset($searchParam['etime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.interesttime)<=UNIX_TIMESTAMP('".$searchParam['etime']."')";
     	}
     	if(isset($searchParam['sjtime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)>=UNIX_TIMESTAMP('".$searchParam['sjtime']."')";
     	}
     	if(isset($searchParam['ejtime'])){
     		$sql = $sql." and UNIX_TIMESTAMP(c.repaymenttime)<=UNIX_TIMESTAMP('".$searchParam['ejtime']."')";
     	}
     	if(isset($searchParam['corid'])){
     		$sql = $sql." and c.corid=".$searchParam['corid'];
     	}
     	$ret = $this->executeSql($sql);
     	return $ret[0];
     }
}