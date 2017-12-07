<?php

require_once 'basemodel.php'; 

class activity_base extends Basemodel{

    private $_table = 'cmibank.cmibank_user_activity_';
    private $_product_info = 'cmibank.cmibank_product_buy_info_';
    private $_product = 'cmibank.cmibank_product';
    private $_active_log = 'cmibank_log.cmibank_rank_active_log';
    private $_active_luck_table = 'cmibank_activity.cmibank_activity_luck_result';

    private $cache_one_week = 'activity:count_money:one_week';
    private $cache_two_week = 'activity:count_money:two_week';

    public function get_db($uid, $actid){
        $tableName = $this->getTableIndex($uid, $this->_table);
        return $this->selectDataSql($tableName, array('uid' => $uid, 'actid' => $actid));
    }
    
    public function get($uid, $actid){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_PREFIX_ . $actid . ':' . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self, $uid, $actid) {
            $productInfo = $self->get_db($uid, $actid);
            if(empty($productInfo)) return false;
            return json_encode($productInfo);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    public function add($uid, $actid, $data){
        if(!isset($data['content'])){
            return false;
        }
        $data['uid'] = $uid;
        $data['actid'] = $actid;
        $data['ctime'] = NOW;
        $tableName = $this->getTableIndex($uid, $this->_table);
        $ret = $this->insertDataSql($data, $tableName);
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_PREFIX_ . $actid . ':' . $uid;
        self::$container['redis_default']->delete($key);
        return $ret;
    }
    
    public function update($uid, $actid, $data){
        $data['uid'] = $uid;
        $data['actid'] = $actid;
        $data['ctime'] = NOW;
        $tableName = $this->getTableIndex($uid, $this->_table);
        $ret = $this->updateDataSql($tableName, $data, array('uid' => $uid, 'actid' => $actid));
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_PREFIX_ . $actid . ':' . $uid;
        self::$container['redis_default']->delete($key);
        return $ret;
    }
    
    //818总排名
    public function set_activity_rank_with_actid($actid, $uid, $value){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_ . $actid;
        return self::$container['redis_default']->setScore($key, $uid, $value);
    }
    
    //818周排名
    public function set_activity_weekRank_with_actid($actid, $uid, $value, $week = ''){
        $week = $week ? $week : date('W');
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_ . $actid . ':' . $week;
        return self::$container['redis_default']->setScore($key, $uid, $value);
    }
    
    //取排名
    public function get_activity_rank_with_actid($actid, $start = 0, $end = -1){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_ . $actid;
        return self::$container['redis_default']->setRange($key, $start, $end, 1, 1);
//         return self::$container['redis_default']->setRevRangeBySorce($key, 100000000, 5000);
    }
    
    //818周排名
    public function get_activity_weekRank_with_actid($actid, $week = '', $start = 0, $end = -1){
        $week = $week ? $week : date('W');
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_ . $actid . ':' . $week;
        return self::$container['redis_default']->setRange($key, $start, $end, 1, 1);
    }
    
    public function remove_activity_rank_with_actid($actid){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_ . $actid;
        return self::$container['redis_default']->delete($key);
    }
    
    public function get_activity_rank_with_actid_phone($actid, $where){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_ . $actid;
        return self::$container['redis_default']->setScore($key, $where);
    }
    
    public function get_activity_weekrank_with_actid_phone($actid, $where, $week = ''){
        $week = $week ? $week : date('W');
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_ . $actid . ':' . $week;
        return self::$container['redis_default']->setScore($key, $where);
    }
    
    public function get_rank($actid, $where){
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_ . $actid;
        return self::$container['redis_default']->zRevRank($key, $where);
    }
    
    public function get_weekrank($actid, $where, $week = ''){
        $week = $week ? $week : date('W');
        $key = _KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_ . $actid . ':' . $week;
        return self::$container['redis_default']->zRevRank($key, $where);
    }

    /**
     * @param array $config
     * @param string $type
     * @return array
     */
    public function get_active_Rank($config = array(),$type = ''){
        $res = array();
        $result = array();
        $mid_time = strtotime($config['mid_time']);
        $time1 = strtotime($config['start_time']);
        $time2 = strtotime($config['end_time']);
        if (empty($type) || $type == '1') {
            if (NOW >= $time1 && NOW < $mid_time){
                $start_time = $time1;
                $end_time = $mid_time;
                $cache = $this->cache_one_week;
            }elseif(NOW >= $mid_time && NOW <= $time2){
                $start_time = $mid_time;
                $end_time = $time2;
                $cache = $this->cache_two_week;
            }else{
                $start_time = '';
                $end_time = '';
                $cache = '';
            }
            if ($start_time && $end_time && $config) {
                $result = $this->get_db_rank_active_two($config, $start_time, $end_time, $cache);
            }

        }elseif ($type == '2'){
            if (NOW >= $mid_time && NOW < $time2){

                $start_time = $time1;
                $end_time = $mid_time;
                $cache = $this->cache_one_week;
            }elseif (NOW >= $time2){
                $start_time = $mid_time;
                $end_time = $time2;
                $cache = $this->cache_two_week;
            }else{
                $start_time = '';
                $end_time = '';
                $cache = '';
            }
            $result_cache = $this->redis_cache($cache);
            if ($result_cache) {
                $result = json_decode($result_cache['content'], true);
            }else{
                if ($start_time && $end_time) {
                    $result = $this->get_db_active_rank_log($start_time, $end_time);
                }
            }
        }
        if ($result) {
            foreach ($result as $key => $rs) {
                unset($rs['all_money']);
                $res[$key] = array_values($rs);
                foreach ($res[$key] as $n => $r){
                    if($key == 'three_product_tpid' && $r['money'] < 100000){
                        unset($res[$key][$n]);
                    }elseif($key == 'six_product_tpid' && $r['money'] < 60000){
                        unset($res[$key][$n]);
                    }elseif($key == 'year_product_tpid' && $r['money'] < 30000){
                        unset($res[$key][$n]);
                    }
                }
            }
        }
        return $res;
    }

    /**
     * 从数数据库获取上周排行记录
     * @param string $start_time
     * @param string $end_time
     * @return bool|mixed
     */
    public function get_db_active_rank_log($start_time = '',$end_time = ''){
        $sql = "select * from $this->_active_log WHERE `ctime` BETWEEN $start_time AND $end_time ORDER BY ctime DESC limit 1";
        $data = $this->executeSql($sql);

        return $data[0]['content'] ? json_decode($data[0]['content'], true) : false;
    }

    /**
     * 从数据获取数据并保存在缓存中
     * @param array $config
     * @param string $start_time
     * @param string $end_time
     * @param string $cache
     * @return array|bool
     */
    public function get_db_rank_active_two($config = array(),$start_time = '', $end_time = '',$cache = ''){
        $cache_data = $this->redis_cache($cache);
        if (! $cache_data) {
            $sort_desc = array();
            foreach ($config['product_tpid'] as $key => $tpid) {
                $result = $this->get_product_info($key, $tpid, $start_time, $end_time);
                $sort_desc[$key] = $this->get_array($result, $key, $tpid);
                //$sort_desc[$key] = $this->array_sort($rs[$key], 'money', 1);
                $sort_desc[$key]['all_money'] = $this->sum_money($sort_desc[$key]);
                if (isset($config['fake_data']) && is_array($config['fake_data'])){
                    if(!empty($sort_desc[$key])) {
                        $sort_desc[$key] = array_merge($sort_desc[$key], $config['fake_data'][$key]);
                    }else{
                        $sort_desc[$key] = $config['fake_data'][$key];
                    }
                }
                $sort_desc[$key] = $this->array_sort($sort_desc[$key], 'money', 1);
                //真假数据分离
//                $db_redis_pre[$key] = $sort_desc[$key];
//                foreach ($db_redis_pre[$key] as $i => $db_redis){
//                    if (in_array($db_redis['uid'], array_keys($config['fake_data'][$key]))){
//                        unset($db_redis_pre[$key][$i]);
//                    }
//                }
            }

            //日志存入数据库
            $this->add_active_log(json_encode($sort_desc));
            if ($sort_desc['three_product_tpid']['all_money'] >= $config['two_rule']['three'] || $sort_desc['six_product_tpid']['all_money'] >= $config['two_rule']['six'] || $sort_desc['year_product_tpid']['all_money'] >= $config['two_rule']['year']) {
                $this->redis_cache($cache, array('content' => json_encode($sort_desc),'ctime' => NOW), 1800,1);
                return $sort_desc;
            } else {
                return array();
            }
        }else{
            return json_decode($cache_data['content'], true);
        }
    }

    /**
     * 获取15个表的数据
     * @param string $key
     * @param string $tpid
     * @param string $start_time
     * @param string $end_time
     * @return array|bool
     */
    public function get_product_info($key = '',$tpid ='',$start_time = '',$end_time = ''){
        $result = array();
        for ($i=0; $i <= 15; $i++) {
            $sql = "select a.uid,a.account,sum(a.money) as money from $this->_product_info$i AS a LEFT JOIN $this->_product AS b ON a.pid=b.pid WHERE b.ptid=$tpid AND a.ctime BETWEEN $start_time AND $end_time GROUP BY a.uid";
            $result[$key][] = $this->executeSql($sql);
        }

        return $result ? $result : false;
    }
    
    /**
     * @param array $result
     * @param string $key
     * @param string $tpid
     * @return bool|mixed
     */
    public function get_array($result = array(), $key = '', $tpid = ''){
        $rs = array();
        foreach ($result[$key] as $k => $item){
            foreach($item as $r=>$v){
                $rs[$key][$v['uid']]['uid'] = $v['uid'];
                $rs[$key][$v['uid']]['account'] = $v['account'];
                $rs[$key][$v['uid']]['product_tpid'] = $tpid;
                @$rs[$key][$v['uid']]['money'] += $v['money'];
            }
        }

        return $rs ? $rs[$key] : false;
    }

    /**
     * 按照降序排序数组
     * @param $arr
     * @param $keys
     * @param int $order
     * @return array|bool
     */
    function array_sort($arr, $keys, $order=0) {
        if (!is_array($arr)) {
            return false;
        }
        $keysvalue = array();
        foreach($arr as $key => $val) {
            $keysvalue[$key] = $val[$keys];
        }
        if($order == 0){
            asort($keysvalue);
        }else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach($keysvalue as $key => $vals) {
            $keysort[$key] = $key;
        }
        $new_array = array();
        foreach($keysort as $key => $val) {
            $new_array[$key] = $arr[$val];
        }
        return $new_array;
    }

    /**
     * 统计总金额
     * @param array $arr
     * @return array|bool
     */
    public function sum_money($arr = array()){
        $all_money = '';
        if (!empty($arr) && is_array($arr)){
            foreach ($arr as $value) {
                $all_money += $value['money'];
            }
        }

        return $all_money ? $all_money : false;
    }

    /**
     * 添加数据到数据库
     * @param string $json_str
     */
    public function add_active_log($json_str = ''){
        if (!empty($json_str)){
            $data = array(
                'content' => $json_str,
                'ctime' => NOW,
            );
            $this->insertDataSql($data, $this->_active_log);
        }
    }

    /**
     * 添加活动中奖结果
     * @param $data
     * @return bool
     */
    public function add_activity_luck_result($data){
        if (!empty($data)){
            return $this->insertDataSql($data,$this->_active_luck_table);
        }
    }

    /**
     * 缓存数据
     * @param $key
     * @param string $data
     * @param int $expire
     * @param int $is_true
     * @return mixed
     */
    public function redis_cache($key,$data='',$expire=60, $is_true = 1) {
        if($data){
            $data = is_array($data) ? json_encode($data) : $data;
            $rtn = self::$container['redis_app_w']->save($key,$data);
            if ($is_true) {
                self::$container['redis_app_w']->expire($key, $expire);
            }
            return $rtn;
        }else{
            $rtn = self::$container['redis_app_w']->get($key);
            $rtn = json_decode($rtn) ? json_decode($rtn,true) : $rtn;
            return $rtn;
        }
    }
}
