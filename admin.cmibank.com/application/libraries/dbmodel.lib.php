<?php
require_once BASEPATH . '/core/Model.php';
//require_once APPPATH . 'libraries/CoreRedis.php';

class DbModel extends CI_Model{

	public $dbr;
	public $dbw;
	public $tableName = '';
    protected $_redisObj = null;

	function __construct(){
		parent::__construct();
		$this->dbr = $this->load->database('dbr', true);
		$this->dbw = $this->load->database('dbw', true);
        //$this->_redisObj = CoreRedis::getInstance();  //默认是Write
	}
	/**
	 * 返回数据库资源连接
	 * 
	 * **/
	public function getDBLink($id , $prefix_name = 'app_xyapplist'){
		$str 	 = md5($id);
        $dbstr   = substr($str,0,1);
        $db_name = $prefix_name."_".$dbstr;
		return $this->load->database('apps', true , null , $db_name);
	}
	
	/**
	 * 查询
	 * @deprecated
	 */
	public function querySQL($sql){
		$_sql = strtolower(trim($sql));
// 		if(strpos($_sql,'select') !== 0){
// 			return false;
// 		}
		$list = $this->dbr->query($sql)->result_array();
		return $list;
	}
    
	
	
	public function queryUpdateSQL($sql){
		$_sql = strtolower(trim($sql));
		return $this->dbw->query($sql);
	}

	/**
	 * 增加记录
	 * @param $data array
	 * @return integer
	 */
	protected function insertData($data , $tableName = ''){
		$this->setTableName($tableName);
		$this->dbw->insert($this->tableName,$data);
		$insert_id = $this->dbw->insert_id();
		if($insert_id==0){
			$affect = $this->dbw->affected_rows();
			if ($affect > 0) {
				return true;
			}else{
				return false;
			}
		}
		return $this->dbw->insert_id();
	}

	/**
	 * 更新
	 * @param $data array
	 * @param $where mix array or string
	 * @return integer
	 */
	protected function updateData($data , $where , $tableName = ''){
		$this->setTableName($tableName);
		$this->dbw->update($this->tableName, $data, $where);
		return $this->dbw->affected_rows();
	}

	/**
	 * 删除
	 * @param $where mix array or string
	 * @return integer
	 */
	protected function deleteData($where , $tableName = ''){
		$this->setTableName($tableName);
		$this->dbw->delete($this->tableName, $where);
		return $this->dbw->affected_rows();
	}

	protected function deleteDataBySql($sql){
	    return $this->dbw->query($sql);
	}
	
	
	/**
	 * 获取单行记录
	 * @param $where mix array or string
	 * @return array
	 */
	protected function getOne($where , $tableName = '' , $orderby = ''){
		$this->setTableName($tableName);
		if($orderby){
			$row = $this->dbr->from($this->tableName)->where($where)->order_by(trim($orderby))->limit(1)->get()->row_array();	
		}else{
			$row = $this->dbr->from($this->tableName)->where($where)->limit(1)->get()->row_array();
		}
		return $row;
	}

	/**
	 * 获取多行记录
	 * @param $where mix array or string
	 * @param $order string 
	 * @param $limit array array(10,20)=>limit 20,10
	 * @return array
	 */
	protected function getList($where = NULL, $order = NULL ,$limit = NULL , $tableName = ''){
		$this->setTableName($tableName);
		$this->dbr->from($this->tableName);
		if (!empty($where)) {
			$this->dbr->where($where);
		}
		if(!empty($order)){
			$this->dbr->order_by(trim($order));
		}
		if(!empty($limit)){
			$this->dbr->limit($limit['0'], $limit['1']);
		}
		$query = $this->dbr->get();
		$rearr = $query->result_array();
		return $rearr;
	}
	
	protected function getDistinctCount($colName , $where = '' , $tableName = ''){
		$this->setTableName($tableName);
		if($where){
			$sql = "select count(distinct $colName) totalcount from ".$this->tableName .' where '. $where;
		}else{
			$sql = "select count(distinct $colName) totalcount from ".$this->tableName;
		}
		$totalcount = $this->dbr->query($sql)->row_array();
		return is_array($totalcount) ? $totalcount['totalcount']:0;
	}
	
	protected function getCount($where = '' , $tableName = ''){
		$this->setTableName($tableName);
		if($where){
			$sql = "select count(*) totalcount from ".$this->tableName .' where '. $where;
		}else{
			$sql = "select count(*) totalcount from ".$this->tableName;
		}
		$totalcount = $this->dbr->query($sql)->row_array();
		return is_array($totalcount) ? $totalcount['totalcount']:0;
	}
	
	public function selectField($feild){ 
		$this->dbr->select($feild);
	}

	/**
	 * 根据条件统计记录行数，并重命名
	 * @param $where mix array or string 
	 * @param $field string 新的字段名称
	 * @return array
	 */
	protected function getSum($where,$field , $tableName = ''){
		$this->setTableName($tableName);
		$this->dbr->from($this->tableName);
		if (!empty($where)) {
			$this->dbr->where($where,null,false);
		}
		$this->dbr->select_sum($field);
		$query = $this->dbr->get();
		return $query->row_array();
	}
	
	private function setTableName($tableName = ''){ 
		$this->tableName = $tableName;
	}
	protected function setCache($key, $val){
		$options = array('flag'=>0,'expire'=>0);
		return Mem::set($key, $val, $options);
	}

	protected function getCache($key){
		return Mem::get($key);
	}

	protected function delCache($key){
		return Mem::delete($key);
	}

	public function lastQuery(){
		return $this->dbr->last_query();
	}
}