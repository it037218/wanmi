<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_product_model extends Basemodel {

    private $_table = 'cmibank.cmibank_product';
    
    private $_product_arr = array();
    
    public function __construct() {
        parent::__construct();
    }

    public function getProductListByCid($cid,$searchpname,$searchstart,$searchend){
        $sql = "select * from " . $this->_table . " where cid = '$cid' ";
        if($searchpname != ""){
            $sql .= " and pname like '%$searchpname%'";
        }
        if($searchstart != "" && $searchend != ""){
            $sql .= " and uistime between '$searchstart' and '$searchend' ";
        }
        return $this->executeSql($sql);
    }
    
    public function getProductWithWhere($where){
        return $this->selectDataListSql($this->_table, $where);
    }
    
    public function getProductList($where,$order,$limit=''){
        return $this->selectDataListSql($this->_table, $where,$order,$limit);
    }
    
    public function getProductByPid($pid){
        return $this->selectDataSql($this->_table, array('pid' => $pid));
    }
    
    public function delProductByPid($pid){
        return $this->deleteDataSql($this->_table, array('pid' => $pid));
    }
    
    public function updatePorduct($pid, $data){
        $ret = $this->updateDataSql($this->_table, $data, array('pid' => $pid));
        if($ret){
            $key = _KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_  . $pid;
            self::$container['redis_app_w']->delete($key);
        }
        return $ret;
    }
    
    public function addPorduct($data){
        return $this->insertDataSql($data, $this->_table);
    }
    
	public function addProductListRedislist($ptid, $pid) {
	    $key = _KEY_REDIS_SYSTEM_PRODUCT_LIST_PREFIX_ . $ptid;
	    $ret = self::$container['redis_app_w']->listPush($key, $pid, 1, 0);
	    return $ret;
	}
    
	public function getTodayRepayment(){
	    $sql = "SELECT * FROM " . $this->_table . " WHERE `uietime` = '" . date("Y-m-d", strtotime('-1 day')) . "' AND `sellmoney` > 0 AND status in(5, 6)  order by cid desc";
	    return $this->executeSql($sql);
	}
	public function getTodayRepaymentWhere($searchpname,$cid,$corcid){
	    $sql = "SELECT * FROM " . $this->_table . " WHERE `uietime` = '" . date("Y-m-d", strtotime('-1 day')) . "' AND `sellmoney` > 0 AND status in(5, 6)";
	    if(!empty($searchpname) && $searchpname != '请输入搜索内容'){
	        $sql .= " and pname like '%$searchpname%'";
	    }
	    if(!empty($cid)){
	        $sql .= " and cid in ($cid)";
	    }
	    if(!empty($corcid)){
	        $sql .= " and corcid in ($corcid)";
	    }
	    $sql .= " order by cid desc";
	    return $this->executeSql($sql);
	}
	
	public function getNoRepaymentWhere($searchpname,$startcietime,$endcietime,$cid,$corcid){
	    $sql = "SELECT * FROM " . $this->_table . " WHERE `sellmoney` > 0 AND status in(2,3,4,5,6) and `sellmoney` !=2 ";
	    if($searchpname != '请输入搜索内容'){
	        $sql .=  " and pname like '%$searchpname%'";
	    }
	    if($startcietime != '请输入开始日期' && $endcietime != '请输入结束日期'){
	        $sql .= " and uietime between '$startcietime' and '$endcietime'";
	    }
	    if(!empty($cid)){
	        $sql .= " and cid in ($cid)";
	    }
	    if(!empty($corcid)){
	        $sql .= " and corcid in ($corcid)";
	    }
	    $sql .= " order by status desc,uietime desc";
	    return $this->executeSql($sql);
	}
	public function getNoRepayment($limit = ''){
	    $data = $this->selectDataListSql($this->_table, array('sellmoney >' => 0, 'status' => array(2,3,4,5,6), 'repayment_status !=' => 2), 'status desc, uietime ', $limit);
	    return $data;
	    //$sql = "SELECT * FROM " . $this->_table . " WHERE `sellmoney` > 0 AND status in(5, 6) order by status";
	    //return $this->executeSql($sql);
	}
	
	public function CountNoRepayment(){
	    $data = $this->selectDataCountSql($this->_table, array('sellmoney >' => 0, 'status' => array(2,3,4,5,6), 'repayment_status !=' => 2 ));
	    return $data;
	}
	
	public function getRepaymentedwhere($searchpname,$startcietime,$endcietime,$cid,$corcid){
	    $sql = "SELECT * FROM " . $this->_table . " WHERE `sellmoney` > 0 AND status in(6) and `repayment_status` =2 ";
	    if($searchpname != '请输入搜索内容'){
	        $sql .=  " and pname like '%$searchpname%'";
	    }
	    if($startcietime != '请输入开始日期' && $endcietime != '请输入结束日期'){
	        $sql .= " and uietime between '$startcietime' and '$endcietime'";
	    }
	    if(!empty($cid)){
	        $sql .= " and cid in ($cid)";
	    }
	    if(!empty($corcid)){
	        $sql .= " and corcid in ($corcid)";
	    }
	    $sql .= " order by uietime desc";
	    return $this->executeSql($sql);
	}
	
	public function getRepaymented($limit = ''){
	    $data = $this->selectDataListSql($this->_table, array('sellmoney >' => 0, 'status' => 6, 'repayment_status' => 2), 'uistime desc', $limit);
	    return $data;
	    //$sql = "SELECT * FROM " . $this->_table . " WHERE `sellmoney` > 0 AND status in(5, 6) order by status";
	    //return $this->executeSql($sql);
	}
	
	public function CountRepaymented(){
	    $data = $this->selectDataCountSql($this->_table, array('sellmoney >' => 0, 'status' => 6, 'repayment_status' => 2));
	    return $data;
	}
	
	
	public function getProductInPid($pid){
	    $sql = "SELECT pname,cistime,cietime,uistime,uietime,sellmoney,income FROM ". $this->_table ." where pid in ($pid);";
	    return $this->executeSql($sql);
	}
	
	public function _flushProductDetailRedisCache($pid){
		$key = _KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_ . $pid;
	    self::$container['redis_app_w']->delete($key);
		$ret = $this->getRedisProductDetailInfo($pid);
	    return $ret;
	}
	
	public function getRedisProductDetailInfo($pid) {
	    if(isset($this->_product_arr[$pid])){
	        return $this->_product_arr[$pid];
	    }
		$key = _KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_  . $pid;
		$self = $this;
		$return = $this->remember($key, 0 , function() use($self , $pid) {
			$productInfo = $self->getProductByPid($pid);
			if(empty($productInfo)) return false;
			return json_encode($productInfo);
		} , _REDIS_DATATYPE_STRING);
		$this->_product_arr[$pid] = json_decode($return, true);
		return $this->_product_arr[$pid];
	}
	
	public function getProductCount(){
	    return $this->selectDataCountSql($this->_table);
	}
	
	//
	public function moveOnlineProduct($ptid, $pid, $odate = ''){
	    $odate = $odate ? $odate : date("Y-m-d");
	    $key = _KEY_REDIS_SYSTEM_ONLINE_PRODUCT_LIST_PREFIX_ .  $odate .  '_'  . $ptid;
	    self::$container['redis_app_w']->listRemove($key, $pid);
	}
	
	public function movetomorrowOnlineProduct($ptid, $pid){
	    $key = _KEY_REDIS_SYSTEM_ONLINE_PRODUCT_LIST_PREFIX_ .  date("Y-m-d",time()+84600) .  '_'  . $ptid;
	    self::$container['redis_app_w']->listRemove($key, $pid);
	}
	
	public function moveYugaoProduct($ptid,$pid){
	    $key = _KEY_REDIS_SYSTEM_YUGAO_PRODUCT_LIST_PREFIX_ . date("Y-m-d") .  '_'  . $ptid;
	    $product = $this->getProductByPid($pid);
	    $new_data = array('online_time'=>strtotime($product['online_time']),'pid'=>$product['pid'],'ptid'=>$ptid);
	    self::$container['redis_app_w']->setMove($key,json_encode($new_data) ,1);
	}
	public function addProductToSellOutList($pid, $odate = ''){
	    $odate = $odate ? $odate : date("Y-m-d");
	    $sellOutkey = _KEY_REDIS_SYSTEM_SELLOUT_PRODUCT_LIST_PREFIX_ . $odate;
	    $product = $this->getProductDetail($pid);
	    return self::$container['redis_default']->setAdd($sellOutkey, json_encode($product), 1, $product['sellouttime']);
	}
	
	/**
	 *
	 * @param unknown $pid
	 * @param unknown $status 2下架,  3售罄,  4停售
	 */
	public function updateProductStatus($pid, $data){
	    $key = _KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_ . $pid;
	    self::$container['redis_app_w']->delete($key);
	    return $this->updateDataSql($this->_table, $data, array('pid' => $pid));
	}
	
	public function getProductDetail($pid){
	    $key = _KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_ . $pid;
	    $self = $this;
	    $return = $this->remember($key, 0 , function() use($self , $pid) {
	        $productInfo = $self->_get_db_product_detail($pid);
	        if(empty($productInfo)) return false;
	        return json_encode($productInfo);
	    } , _REDIS_DATATYPE_STRING, self::$container['redis_app_r'], self::$container['redis_app_w']);
	    return json_decode($return , true);
	}
	
	public function _get_db_product_detail($pid){
	    return $this->DBR->select('*')->from($this->_table)->where('pid',$pid)->get()->row_array();
	}
	
	public function countSellMoneyByCid($cid){
	    $sql = "SELECT sum(sellmoney) as countsellmoney FROM " . $this->_table . " WHERE cid = " . $cid . " AND `status` not in(0, 1)";
	    $data = $this->executeSql($sql);
	    return $data[0]['countsellmoney'] ? $data[0]['countsellmoney'] : 0;
	}
	
	public function countXiafengMoneyByCid($cid,$pid){
		$sql = "SELECT sum(sellmoney) as countmoney FROM " . $this->_table . " WHERE cid = " . $cid." and pid!=".$pid;
		$data = $this->executeSql($sql);
		return $data[0]['countmoney'] ? $data[0]['countmoney'] : 0;
	}
	
	public function countOnlineProductMoneyByCid($cid){
	    $sql = "SELECT sum(money) as countsellmoney FROM " . $this->_table . " WHERE `cid` = " . $cid . " AND `status` in(0, 1)";
	    $data = $this->executeSql($sql);
	    return $data[0]['countsellmoney'];
	}
	
	public function backmoney($cid){
	    $sql = "SELECT SUM(sellmoney) as sum_sellmoney FROM " . $this->_table . " WHERE cid = $cid ";
	    $data=$this->executeSql($sql);
	    return $data[0]['sum_sellmoney'];
	}
	
	public function getTodayRemitProduct(){
	    return $this->selectDataListSql($this->_table, array('uistime' => date('Y-m-d'), 'remitid =' => 0, 'sellmoney !=' => 0));
	}
	
	public function getTodayRemitProductWhere($cid,$corcid,$searchpname){
	    $uistime = date('Y-m-d');
	    $sql = "SELECT * FROM (`cmibank`.`cmibank_product`) WHERE `uistime` = '$uistime' AND `remitid` = 0 AND `sellmoney` != 0";
	    if(!empty($searchpname) && $searchpname != "请输入搜索内容"){
	        $sql .= " and pname ='$searchpname'";
	    }
	    if(!empty($cid)){
	       $sql .= " and cid in ($cid)";
	    }
	    if(!empty($corcid)){
	        $sql .= " and corcid in ($corcid)";
	    }
	    return $this->executeSql($sql);
	}

	public function getNoRemitProduct($start, $offset){
		$uistime = date('Y-m-d', strtotime('+1 day'));
		$sql ="SELECT * FROM (`cmibank`.`cmibank_product`) WHERE `remitid` = 0 AND UNIX_TIMESTAMP(uistime) <= UNIX_TIMESTAMP('$uistime') AND sellmoney != 0 and status in (2,3) order by uistime desc limit ".$offset.",".$start;
	    return $this->executeSql($sql);
	}
	public function getNoRemitProductWhere($pname,$startcistime,$endcistime,$startcietime,$endcietime,$corname,$con_number){
	    $uistime = date('Y-m-d', strtotime('+1 day'));
	    $sql ="SELECT * FROM (`cmibank`.`cmibank_product`) WHERE (remitid= 0 or is_upload=0) AND UNIX_TIMESTAMP(uistime) <= UNIX_TIMESTAMP('$uistime') AND sellmoney != 0 and status in (2,3,4) ";
	    if($pname !='请输入搜索内容'){
	        $sql .= " and pname like '%$pname%'";
	    }
	    if($startcistime  != "请输入开始日期" or $endcistime != "请输入结束日期"){
	        if($startcistime == "请输入开始日期"){
	            $startcistime = date('Y-m-d');
	        }
	        if($endcistime == "请输入结束日期"){
	            $endcistime = date('Y-m-d');
	        }
	    
	        $sql .= " and `cistime` BETWEEN '$startcistime' and '$endcistime'";
	    }else if($startcietime  != "请输入开始日期" or $endcietime != "请输入结束日期"){
	        if($startcietime == "请输入开始日期"){
	            $startcistime = date('Y-m-d');
	        }
	        if($endcietime == "请输入结束日期"){
	            $endcietime = date('Y-m-d');
	        }
	        $sql .= " and `cietime` BETWEEN '$startcietime' and '$endcietime'";
	    }else{
	    }
	    if($corname !='请输入搜索内容'){
	        $sql .= " and corcid in(SELECT corid FROM `cmibank`.`cmibank_contract` where corname like '%$corname%')";
	    }
	    if($con_number !='请输入搜索内容'){
	        $sql .= " and cid in (SELECT cid FROM `cmibank`.`cmibank_contract` where con_number like '%$con_number%')";
	    }
	    $sql .= " ORDER BY `ctime` desc";
	    return $this->executeSql($sql);
	}
	public function countNoRemitProduct(){
	    $uistime = date('Y-m-d', strtotime('+1 day'));
		$sql ="SELECT count(*) as counts FROM (`cmibank`.`cmibank_product`) WHERE (remitid= 0 or is_upload=0) AND UNIX_TIMESTAMP(uistime) <= UNIX_TIMESTAMP('$uistime') AND sellmoney != 0 and status in (2,3,4)";
	    $ret =$this->executeSql($sql);
	    return $ret[0]['counts'];
	}
	
	public function getRemitedProduct($start, $offset){
	    return $this->selectDataListSql($this->_table, array('remitid !=' => 0, 'sellmoney !=' => 0, 'status !=' => 6), 'ctime desc', array($start, $offset));
	}
	public function getRemitedProductWhere($pname,$startcistime,$endcistime,$startcietime,$endcietime,$corname,$con_number){
	    $sql ="SELECT pro.*,remit.ctime as remit_time FROM `cmibank`.`cmibank_product` as pro left join `cmibank`.`cmibank_product_remit` as remit on remit.pid=pro.pid WHERE pro.`remitid` != 0 AND pro.`sellmoney` != 0";
	    if($pname !='请输入搜索内容'){
	       $sql .= " and pro.pname like '%$pname%'";
	    }
	    if($startcistime  != "请输入开始日期" or $endcistime != "请输入结束日期"){
	        if($startcistime == "请输入开始日期"){
	            $startcistime = date('Y-m-d');
	        }
	        if($endcistime == "请输入结束日期"){
	            $endcistime = date('Y-m-d');
	        }
	     
	        $sql .= " and pro.`cistime` BETWEEN '$startcistime' and '$endcistime'";
	    }else if($startcietime  != "请输入开始日期" or $endcietime != "请输入结束日期"){
	        if($startcietime == "请输入开始日期"){
	            $startcistime = date('Y-m-d');
	        }
	        if($endcietime == "请输入结束日期"){
	            $endcietime = date('Y-m-d');
	        }
	        $sql .= " and pro.`cietime` BETWEEN '$startcietime' and '$endcietime'";
	    }
	    if($corname !='请输入搜索内容'){
	        $sql .= " and pro.corcid in(SELECT corid FROM `cmibank`.`cmibank_contract` where corname like '%$corname%')";
	    }
	    if($con_number !='请输入搜索内容'){
	        $sql .= " and pro.cid in (SELECT cid FROM `cmibank`.`cmibank_contract` where con_number like '%$con_number%')";
	    }
	    $sql .= " ORDER BY pro.`ctime` desc";
	    return $this->executeSql($sql);
	    
	}
	public function countRemitedProduct(){
	    return $this->selectDataCountSql($this->_table, array('remitid !=' => 0, 'sellmoney !=' => 0, 'status !=' => 6));
	}
	
	public function getCanBackMoneyProduct($odate = ''){
	    $odate = $odate ? $odate : date("Y-m-d");
	    return $this->selectDataListSql($this->_table, array('remitid !=' => 0, 'cietime' => $odate), null, array(10000, 0));
	}

	public function getproduct($start,$end,$pname,$type){
	    if($type == 1){
	       $sql = "SELECT * FROM $this->_table WHERE `uistime` BETWEEN '$start' and '$end' and status  in (1,2,3,4,5,6)";
	    }else if($type == 2){
	       $sql = "SELECT * FROM $this->_table WHERE `uietime` BETWEEN '$start' and '$end' and status  in (1,2,3,4,5,6)";
	    }else if($type == 3){
	       $sql = "SELECT * FROM $this->_table WHERE  status  in (1,2,3,4,5,6)";
	    }
	    if($pname != '请输入搜索内容'){
	        $sql .= "and pname like '%$pname%'";
	    }
	    $sql.=' order by `pid` desc';
	    return $this->executeSql($sql);
	}
	public function autotianchong($pname){
	    $sql = "select *  from " .$this->_table. " where pname like '%$pname%' order by pid desc limit 1";
	    return $this->executeSql($sql);
	}
	
	public function getProductListbyPname($pname,$limit){
	    $sql = "select *  from " .$this->_table. " where pname like '%$pname%' and status  in (1,2,3) ORDER BY uistime desc";
	    if(!empty($limit)){
	        $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
	    }
	    return $this->executeSql($sql);
	}

	public function addrepaymentlock($pid){
	    $key = 'admin:repayment:' . $pid;
	    $ttl = 100;
	    $ret = self::$container['redis_app_w']->save($key, 1, $ttl, 0, 1);
	    if($ret){
	        self::$container['redis_app_w']->expire($key , $ttl);
	    }
	    return $ret;
	}
	
	public function getRepaymentLock($pid){
	    $key = 'admin:repayment:' . $pid;
	    return self::$container['redis_app_w']->get($key);
	}
	
	public function delrepaymentlock($pid){
	    $key = 'admin:repayment:' . $pid;
	    return self::$container['redis_app_w']->delete($key);
	}
	
}
