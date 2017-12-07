<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_activity_model extends Basemodel {
    private $_luck_result_table = 'cmibank_activity.cmibank_activity_luck_result';
    private $_product_buy_info = 'cmibank.cmibank_product_buy_info_';
    private $_product = 'cmibank.cmibank_product';
    private $_ptype = 'cmibank.cmibank_ptype';
    private $key = _KEY_REDIS_USER_RANK_ACTIVITY_MARK_INFO_;

	public function set_activity_rank_with_actid($actid, $uid, $value){
		$key = _KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_ . $actid;
		return self::$container['redis_default']->setScore($key, $uid, $value);
	}
	
	public function set_activity_weekRank_with_actid($actid, $uid, $value, $week = ''){
		$week = $week ? $week : date('W');
		$key = _KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_ . $actid . ':' . $week;
		return self::$container['redis_default']->setScore($key, $uid, $value);
	}

    /**
     * 获取11月28日的数据。
     * @param string $start_time
     * @param string $end_time
     * @param array $config
     * @return array
     */
	public function result_array($start_time = '',$end_time = '',$config = array()){
	    $user_buy_product_info = array();
        $user_buy_product_rank = array();
	    if (!empty($start_time) && !empty($end_time)){
            foreach ($config['product_tpid'] as $key => $tpid) {
                $db_return_result = $this->achieve_db_data($key, $tpid, $start_time, $end_time);
                $user_buy_product_info[$key] = $this->tidy_up_data($db_return_result, $key, $tpid);
                $user_buy_product_info[$key]['all_money'] = $this->sum_all_money($user_buy_product_info[$key]);
                if (isset($config['fake_data']) && is_array($config['fake_data'])){
                    if(!empty($user_buy_product_info[$key])) {
                        $user_buy_product_info[$key] = array_merge($user_buy_product_info[$key], $config['fake_data'][$key]);
                    }else{
                        $user_buy_product_info[$key] = $config['fake_data'][$key];
                    }
                }
                $satisfy_conditional_data[$key] = $this->filter_condition($user_buy_product_info[$key],$key,$config);
                $user_buy_product_rank[$key] = $this->array_rank($satisfy_conditional_data[$key], 'money');
                //真假数据分离
                if (isset($config['fake_data']) && !empty($config['fake_data'])) {
                    foreach ($user_buy_product_rank[$key] as $i => $fake_info) {
                        if (in_array($fake_info['uid'], array_keys($config['fake_data'][$key]))) {
                            $user_buy_product_rank[$key][$i]['is_true'] = 0;
                        }
                    }
                }
                //数据写入数据库
                $this->insert_into_db($user_buy_product_rank[$key],$end_time);
            }
        }
        return $user_buy_product_rank;
    }

    /**
     * 过滤条件
     * one条件是活动期间总条件
     * two 是活动期间个人用户条件
     * @param array $data
     * @param string $key
     * @param array $config
     * @return array
     */
    public function filter_condition($data = array(),$key = '',$config = array()){
        if ($data['all_money'] < $config['two_rule']['one'][$key]){
            $data = array();
        }else{
            if (!empty($data) && is_array($data)){
                unset($data['all_money']);
                foreach ($data as $k => $v){
                    if ($v['money'] < $config['two_rule']['two'][$key]){
                        unset($data[$k]);
                    }
                }
            }
        }
        unset($data['all_money']);
        return array_values($data);
    }

    /**
     * 通过购买记录表查询用户购买金额
     * @param string $key
     * @param string $tpid
     * @param string $start_time
     * @param string $end_time
     * @return array|bool
     */
    public function achieve_db_data($key = '',$tpid ='',$start_time = '',$end_time = ''){
        $result = array();
        for ($i=0; $i <= 15; $i++) {
            $sql = "select a.uid,a.account,sum(a.money) as money from $this->_product_buy_info$i AS a LEFT JOIN $this->_product AS b ON a.pid=b.pid WHERE b.ptid=$tpid AND a.ctime BETWEEN $start_time AND $end_time GROUP BY a.uid";
            $result[$key][] = $this->executeSql($sql);
        }
        return $result ? $result : false;
    }

    /**
     * 把不同产品的同一个用户购买信息合并
     * @param $result_data
     * @param $key
     * @param $tpid
     * @return bool|mixed
     */
    public function tidy_up_data($result_data, $key, $tpid){
        $rs = array();
        foreach ($result_data[$key] as $k => $item){
            foreach($item as $r=>$v){
                $rs[$key][$v['uid']]['uid'] = $v['uid'];
                $rs[$key][$v['uid']]['account'] = $v['account'];
                $rs[$key][$v['uid']]['product_tpid'] = $tpid;
                @$rs[$key][$v['uid']]['money'] += $v['money'];
                $rs[$key][$v['uid']]['is_true'] = 1;
            }
        }

        return $rs ? $rs[$key] : false;
    }

    /**
     * 统计总金额
     * @param array $data
     * @return bool|string
     */
    public function sum_all_money($data = array()){
        $all_money = '';
        if (!empty($data) && is_array($data)){
            foreach ($data as $value) {
                $all_money += $value['money'];
            }
        }

        return $all_money ? $all_money : false;
    }

    /**
     * 利用冒泡排序法按照money进行排序
     * @param array $arr
     * @param string $key
     * @return array
     */
    public function array_rank($arr = array(),$key = '') {
        $len=count($arr);
        for($i=1;$i<$len;$i++) {
            for($k=0;$k<$len-$i;$k++) {
                if($arr[$k][$key]>$arr[$k+1][$key]) {
                    $tmp=$arr[$k+1];
                    $arr[$k+1]=$arr[$k];
                    $arr[$k]=$tmp;
                }
            }
        }

        return array_reverse($arr);
    }

    /**
     * @param array $data
     * @param string $end_time
     * @return bool
     */
    public function insert_into_db($data = array(),$end_time = ''){
        foreach ($data as $key => $d) {
            if (isset($d['product_tpid'])) {
                $select_all_rank = $this->selectDataSql($this->_luck_result_table, array('ptid' => $d['product_tpid']));
                empty($select_all_rank['all_rank']) ? $all_rank = json_encode($data) : $all_rank = '';
                $result_data = array(
                    'act_name' => '月庆活动排行',
                    'ptid' => isset($d['product_tpid']) ? $d['product_tpid'] : 0,
                    'uid' => isset($d['uid']) ? $d['uid'] : 0,
                    'account' => isset($d['account']) ? $d['account'] : '',
                    'rank' => $key,
                    'award_money' => 0,
                    'money' => isset($d['money']) ? $d['money'] : '',
                    'prize' => '',
                    'all_rank' => $all_rank,
                    'luck_time' => $end_time,
                    'is_prize' => 0,
                    'is_true' => isset($d['is_true']) ? $d['is_true'] : '',
                    'status' => 0,
                    'ctime' => NOW
                );
                $this->insertDataSql($result_data, $this->_luck_result_table);
            }
        }
        $act_cache = $this->redis_cache($this->key);
        if ($act_cache){
            $this->redis_cache($this->key,'','','',1);
            $cache = $this->redis_cache($this->key,$data,'',0);
        }else{
            $cache = $this->redis_cache($this->key,$data,'',0);
        }
        return $cache ? true : false;
    }

    public function edit_luck_result($data = array()){
        $result = '';
        if (!empty($data) && is_array($data)){
            $id = $data['id'];
            unset($data['op']);unset($data['id']);
            $result = $this->updateDataSql($this->_luck_result_table,$data,array('id' => $id));
        }
//        if ($result){
//            $this->redis_cache($this->key,$data);
//        }
        return $result ? $result : false;
    }

    /**
     * 审核
     * @param string $ids
     * @param string $status
     * @return bool
     */
    public function batch_audit($ids = '',$status = ''){
        if (!empty($ids)){
            $now_status = 1;
            if (!empty($status)) {
                if ($status == '0') {
                    $now_status = 1;

                }else{
                    $now_status = 0;
                }
            }
            $sql = "update $this->_luck_result_table set status=$now_status WHERE id IN ($ids)";
            return $this->executeSql($sql);
        }
    }

    /**
     * 删除
     * @param string $id
     * @return bool|mixed
     */
    public function del_luck_result($id = ''){
        $sql = "delete from $this->_luck_result_table WHERE id IN ($id)";
        $result = $this->executeSql($sql);
//        if ($result){
//            $this->redis_cache($this->key);
//        }
        return $result ? $result : false;
    }

    /**
     * @param string $act_name
     * @param array $limit
     * @return mixed
     */
    public function get_db_activity_result($act_name = '',$limit = array()){
        $where = 'where 1=1';
        if (!empty($act_name)){
            $where .= " AND act_name = '{$act_name}'";
        }
        if (!empty($limit)){
            $limit = " limit $limit[1],$limit[0]";
        }
        $sql = "select a.*,b.name from $this->_luck_result_table AS a LEFT JOIN $this->_ptype AS b ON a.ptid=b.ptid $where ORDER BY ctime ASC $limit";
        return $this->executeSql($sql);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get_db_one($id = ''){
        return $this->selectDataSql($this->_luck_result_table,array('id' => $id));
    }

    /**
     * @param string $act_name
     * @return mixed
     */
    public function get_db_count($act_name = ''){
        if (!empty($act_name)){
            $where = array('act_name' => $act_name);
        }else{
            $where = null;
        }
        return $this->selectDataCountSql($this->_luck_result_table,$where);
    }

    /**
     * 处理缓存相关信息
     * @param $key
     * @param string $data
     * @param int $expire
     * @param int $is_true
     * @param int $del
     * @return mixed
     */
    public function redis_cache($key,$data='',$expire=60, $is_true = 1,$del = 0) {
        if($data){
            $data = is_array($data) ? json_encode($data) : $data;
            $rtn = self::$container['redis_app_w']->save($key,$data);
            if ($is_true) {
                self::$container['redis_app_w']->expire($key, $expire);
            }
            return $rtn;
        }else{
            $rtn = self::$container['redis_app_w']->get($key);
            if ($del){
                $rtn = self::$container['redis_app_w']->delete($key);
            }
            $rtn = json_decode($rtn) ? json_decode($rtn,true) : $rtn;
            return $rtn;
        }
    }
}