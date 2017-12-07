<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

//定期还款任据
class admin_product_backmoney_model extends Basemodel {

    private $_table = 'cmibank.cmibank_product_backmoney';
    
   	private $contract_table = 'cmibank.cmibank_contract';
     
    private $stock_table = 'cmibank.cmibank_stock_product';
     
    
    public function __construct() {
        parent::__construct();
    }

    public function getProductBackmoneyList($where,$order,$limit=null){
        return $this->selectDataListSql($this->_table, $where, $order, $limit);
    }
    
    
    public function getProductBackmoneyListWichToday($date,$limit=null){
    	$sql = "SELECT * FROM ".$this->_table." where ismail=0 and  UNIX_TIMESTAMP(cietime)< UNIX_TIMESTAMP('".$date."') and UNIX_TIMESTAMP(cietime)>UNIX_TIMESTAMP('".date('Y-m-d',NOW)."') order by cietime ASC";
    	return $this->executeSql($sql);
    }
    
    public function getProductBackmoney($where){
        return $this->selectDataSql($this->_table, $where);
    }
    
    public function getProductBackmoneyByBid($bid){
        return $this->selectDataSql($this->_table, array('bid' => $bid));
    }
    
    public function getProductBackmoneyByCid($cid){
    	return $this->selectDataSql($this->_table, array('cid' => $cid));
    }
    
    public function updatePorductBackmoney($bid, $data){
        
        return $this->updateDataSql($this->_table, $data, array('bid' => $bid));
    }
    
    public function addPorductBackmoney($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
    public function getBackmoneyByCondition($searchParam,$limit = '',$orderby=''){
    	$sql = "select backmoney.*,contract.corname,contract.status as con_status,contract.con_number,contract.ctime,contract.money,contract.interesttime, contract.repaymenttime,contract.con_income,contract.con_money,contract.con_bzjbl,contract.remittime,stock.stockmoney,stock.ctime as stocktime,stock.status as stockstatus from $this->_table as backmoney left join $this->contract_table as contract on backmoney.cid=contract.cid left join $this->stock_table as stock on backmoney.cid=stock.cid where 1=1 ";
    	if(isset($searchParam['searchcorname'])){
    		$sql = $sql." and contract.corname like '%".$searchParam['searchcorname']."%'";
    	}
    	if(isset($searchParam['searchcon_number'])){
    		$sql = $sql." and contract.con_number like '%".$searchParam['searchcon_number']."%'";
    	}
    	if(isset($searchParam['stime'])){
    		$sql = $sql." and UNIX_TIMESTAMP(backmoney.cietime)>=UNIX_TIMESTAMP('".$searchParam['stime']."')";
    	}
    	if(isset($searchParam['etime'])){
    		$sql = $sql." and UNIX_TIMESTAMP(backmoney.cietime)<=UNIX_TIMESTAMP('".$searchParam['etime']."')";
    	}
    	if(isset($searchParam['cietime'])){
    		$sql = $sql." and  cietime='".$searchParam['cietime']."'";
    	}
    	if(isset($searchParam['ismail'])){
    		$sql = $sql." and backmoney.ismail=".$searchParam['ismail'];
    	}
    	if(isset($searchParam['status'])){ 
    	    if($searchParam['status']==3){
        	    $sql=$sql." and backmoney.status=3";
    	    }else{
    	        $sql=$sql." and backmoney.status<3";
    	    }
    	}
    	if(isset($searchParam['today'])){
    		$sql = $sql." and UNIX_TIMESTAMP(backmoney.cietime)<".$searchParam['today']." and UNIX_TIMESTAMP(backmoney.cietime)>".NOW;
    	}
    	if(empty($orderby)){
	    	$sql = $sql." order by contract.repaymenttime ASC,cid desc ";
    	}else{
    		$sql = $sql." order by ".$orderby;
    	}
    	if(!empty($limit)){
    		$sql = $sql .'  limit ' . $limit[1] . ', ' . $limit[0];
    	}
    	return $this->executeSql($sql);
    }
    public function backmoneybetweencietime($stime,$etime,$status,$limit){
        $sql = "SELECT * FROM ".$this->_table." where cietime BETWEEN '$stime' and '$etime'";
        if(!empty($status)){
            if(is_array($status)){
                $status = implode(',', $status);
            }else{
                $status;
            }
            $sql .= " and status in ($status) order by cietime";
        }
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
    public function backmoneybetweencietimeisemail($stime,$etime,$ismail,$limit){
        $sql = "SELECT * FROM ".$this->_table." where cietime BETWEEN '$stime' and '$etime'";
        if(!empty($ismail)){
            $sql .= " and ismail=$ismail order by cietime";
        }
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        return $this->executeSql($sql);
    }
}
