<?php
require_once APPPATH. 'models/base/basemodel.php';

class admin_rank_model extends Basemodel {

    private $product_table = 'cmibank.cmibank_userproduct_';
    private $long_product_table = 'cmibank.cmibank_userlongproduct_';
    private $user_identity = 'cmibank.cmibank_user_identity';
    private $user_account = 'cmibank.cmibank_account';
    private $profit_table = 'cmibank_log.cmibank_up_profit_log_';
    private $balance_table = 'cmibank.cmibank_balance';
    private $expmoney_table = 'cmibank.cmibank_user_expmoney_';
    private $_fix = 32;

    private $cache_key = 'bankend_rank:user_rank_info';
     public function __construct() {
         parent::__construct();
     }

     public function get_all_rank_list($start_time = '',$end_time = '',$phone = '',$plat = ''){
         //$cache = $this->redis_cache($this->cache_key);
         $cache = false;
         if (! $cache) {
             //定期信息
             $product_money = $this->user_product(1, '', '0',$start_time,$end_time,$phone,$plat);
             //活期
             $product_long_money = $this->user_product(0, '', '',$start_time,$end_time,$phone,$plat);
             //获取定期复投次数
             $re_case = $this->re_case_info(array_keys($product_money),$start_time,$end_time,$phone,$plat);
             //合并定期活期数组
             $rs = $this->getMerge(array($product_money, $product_long_money,$re_case));
             //计算总投资金额
             $all_money = $this->getAllMoney($rs);
             //获取用户信息
             $user_info = $this->getUserInfo(array_keys($rs));
             //获取余额
             $blance = $this->get_user_balance(array_keys($rs));
             //获取定期收益
             $profit = $this->get_user_profit(array_keys($rs));
             //获取体验金
             $expmoney = $this->get_expmoeny(array_keys($rs));
             //总资产
             $all_asset = $this->all_asset($all_money, $blance, $profit, $expmoney);
             //所有数据集合
             $all = $this->getMerge(array($rs, $all_money, $user_info, $blance, $profit, $expmoney, $all_asset));
             $this->redis_cache($this->cache_key,$all, $ttl = 1800);
         }else{
             $all = $cache;
         }
         return $all ? $all : false;
     }

    /**
     * @param string $uid
     * @param string $start_time
     * @param string $end_time
     * @param string $phone
     * @param string $plat
     * @param array $limit
     * @return bool
     */
     public function user_re_buy_info($uid = '',$start_time = '',$end_time = '',$phone = '', $plat = '',$limit = array()){
         $where = 'where 1 =1';
         if ($start_time && $end_time){
             $where .= " AND a.buytime between ".strtotime($start_time) ." AND " .strtotime($end_time);
         }
         if ($phone){
             $where .= " AND c.account like '%$phone%'";
         }
         if ($plat){
             $where .= " AND c.plat = '{$plat}'";
         }
         if (! empty($limit)){
             $where .= ' limit '.$limit[1].','.$limit[0];
         }

         $table = $this->getTableIndex($uid,$this->product_table);
         $sql = "select a.uid,a.pid,a.ptid,a.pname,a.money,a.buytime,a.status,b.bankcode,b.phone,b.realname,b.idCard,b.cardno,c.plat from $table AS a LEFT JOIN $this->user_identity AS b ON a.uid=b.uid LEFT JOIN $this->user_account AS c ON a.uid=c.uid $where";
         $result = $this->executeSql($sql);
         return $result ? $result : false;
     }

    /**
     * @param string $uid
     * @param string $start_time
     * @param string $end_time
     * @param string $phone
     * @param string $plat
     * @return bool
     */
     public function get_buy_product_count($uid = '',$start_time = '',$end_time = '',$phone = '', $plat = ''){
         $where = 'where 1 =1';
         if ($start_time && $end_time){
             $where .= " AND a.buytime between ".strtotime($start_time) ." AND " .strtotime($end_time);
         }
         if ($phone){
             $where .= " AND b.account like '%$phone%'";
         }
         if ($plat){
             $where .= " AND b.plat = '{$plat}'";
         }
         $table = $this->getTableIndex($uid,$this->product_table);
         $sql = "select count(*) as number from $table AS a LEFT JOIN $this->user_account AS b ON a.uid=b.uid $where";
         $result = $this->executeSql($sql);

         return $result ? $result[0]['number'] :false;
     }

    /**
     * @param string $type
     * @param string $uid
     * @param string $status
     * @param string $start_time
     * @param string $end_time
     * @param string $phone
     * @param string $plat
     * @return array
     */
     public function user_product($type = '', $uid = '', $status = '',$start_time = '', $end_time = '',$phone = '',$plat = ''){
         if ($type == 1){
             $table = $this->product_table;
             $money = 'product_money';
         }else{
             $table = $this->long_product_table;
             $money = 'product_long_money';
         }
         $where = 'where 1=1';
         if (!empty($uid)){
             $where .= " AND a.uid = $uid";
         }
         if(!empty($status)){
             $where .= " AND a.status = $status";
         }
         if (!empty($start_time) && !empty($end_time)){
             $start_time = strtotime($start_time);
             $end_time = strtotime($end_time);
             $where .= " AND a.buytime between $start_time AND $end_time";
         }
         if (!empty($phone)){
             $where .= " AND b.account like '%$phone%'";
         }
         if (!empty($plat)){
             $where .= " AND b.plat = '{$plat}'";
         }

         $data = $this->get_product_info($table, $where, $money);
         return $this->getKey($data);
     }

    /**
     * @param string $table
     * @param string $where
     * @param string $money
     * @return array|bool
     */
    public function get_product_info($table = '', $where = '', $money = ''){
        $data = array();
        for ($i = 0; $i < 16; $i++) {
            $sql = "select a.uid,sum(money) as $money from $table$i AS a LEFT JOIN $this->user_account AS b ON a.uid=b.uid $where group by a.uid";
            $data[] = $this->executeSql($sql);
        }
        return $data ? $data : false;
    }

    /**
     * @param array $data 把多个要合并的数组放在同一个数组中。
     * @return array
     */
     public function getMerge($data = array()){
         $num = count($data);
         $arr = array();
         for ($i = 0; $i < $num; $i++){
             if (is_array($data[$i])) {
                 foreach ($data[$i] as $k => $r) {
                     foreach ($r as $k1 => $r1) {
                         $arr[$r['uid']][$k1] = $r1;
                     }
                 }
             }
         }
         return $arr;
     }

    /**
     * 获取用户身份信息
     * @param array $uids
     * @return array
     */
     public function getUserInfo($uids =array()){
         $result = array();
         if (!empty($uids) && is_array($uids)){
             $u_str = implode(',', $uids);
             $sql = "select a.uid,a.phone,a.realname,a.idCard,b.plat from $this->user_identity as a LEFT JOIN $this->user_account AS b ON  a.uid=b.uid WHERE a.uid IN ($u_str)";
             $user_info = $this->executeSql($sql);
             foreach ($user_info as $user){
                 $result[$user['uid']] = $user;
             }
         }
         return $result ? $result : false;
     }

    /**
     * 计算用户总投资金额
     * @param array $arr
     * @return array
     */
     public function getAllMoney($arr = array()){
         foreach ($arr as $key => $value){
             if (empty($value['product_money'])){
                 $arr[$key]['all_money'] = $value['product_long_money'];
             }elseif (empty($value['product_long_money'])){
                 $arr[$key]['all_money'] = $value['product_money'];
             }elseif (empty($value['product_money']) && empty($value['product_long_money'])){
                 $arr[$key]['all_money'] = '';
             }else{
                 $arr[$key]['all_money'] = $value['product_money'] + $value['product_long_money'];
             }
         }

         return $arr;
     }

    /**
     * @param array $arr
     * @param string $keys
     * @param string $order
     * @return array|bool
     */
    function sort($arr = array(), $keys = '', $order = '') {
        if (!is_array($arr)) {
            return false;
        }
        $keysvalue = array();
        foreach($arr as $key => $val) {
            $keysvalue[$key] = @$val[$keys];
        }
        if($order == 'asc'){
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
            $new_array[] = $arr[$val];
        }
        return $new_array;
    }

    /**
     * 获取余额
     * @param array $uids
     * @return array
     */
    public function get_user_balance($uids = array()){
        $result = array();
        if (!empty($uids) && is_array($uids)){
            $u_str = implode(',', $uids);
            $sql = "select * from $this->balance_table WHERE `uid` IN ($u_str)";
            $blance = $this->executeSql($sql);
            foreach ($blance as $b){
                $result[$b['uid']] = $b;
            }
        }
        return $result ? $result : false;
    }

    /**
     * 获取收益
     * @param array $uids
     * @return array
     */
    public function get_user_profit($uids = array()){
        $rs = array();
        $data = array();
        foreach ($uids as $uid) {
            $table = $this->getTableIndex($uid, $this->profit_table, $this->_fix);
            $sql = "SELECT `uid`,sum(`profit`) as count_profit FROM " . $table . " WHERE `uid` = " . $uid;
            $data[$uid] = $this->executeSql($sql);
        }

        foreach ($data as $k => $v){
            $rs[$k] = $v[0];
            if ($rs[$k]['uid'] == null){
                $rs[$k]['uid'] = $k;
            }
        }
        return $rs;
    }

    /**
     * 表前缀
     * @param $id
     * @param $table
     * @param int $fix
     * @return string
     */
    public function getTableIndex($id, $table, $fix = 16){
        return $table . ($id % $fix);
    }

    /**
     * 统计复投信息
     * @param array $keys
     * @param string $start_time
     * @param string $end_time
     * @param string $phone
     * @param string $plat
     * @return array
     */
    public function re_case_info($keys = array(),$start_time = '',$end_time = '',$phone = '',$plat = ''){
        $data = array();
        $result = array();
        if (!empty($start_time) && !empty($end_time)){
            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            $where_on = " AND a.buytime between $start_time AND $end_time";
        }else{
            $where_on = '';
        }
        if (!empty($phone)){
            $where_on .= " AND b.account like '%$phone%'";
        }
        if (!empty($plat)){
            $where_on .= " AND b.plat = '{$plat}'";
        }
        for ($i = 0;$i < count($keys); $i++){
            $table_name = $this->getTableIndex($keys[$i],$this->product_table);
            $sql = "select a.uid,a.money,a.buytime from $table_name AS a LEFT JOIN $this->user_account AS b ON a.uid=b.uid WHERE a.uid=$keys[$i] $where_on ORDER BY a.buytime ASC ";
            $data[] = $this->executeSql($sql);
        }

        $res = array();
        if ($data) {
            foreach ($data as $value) {
                array_shift($value);
                foreach ($value as $key => $item) {
                    $res[$item['uid']][$item['buytime']] = $item;
                }
            }
            foreach ($res as $r) {
                foreach ($r as $k => $v) {
                    $result[$v['uid']]['num'] = count($r);
                    $result[$v['uid']]['uid'] = $v['uid'];
                    @$result[$v['uid']]['re_money'] += $v['money'];
                }

            }
        }
        return $result ? $result : array();
    }

    /**
     * @param array $array
     * @return array
     */
    public function getKey($array = array()){
        $result = array();
        foreach ($array as $value){
            foreach ($value as $key => $item){
                $result[$item['uid']] = $item;
            }
        }
        return $result;
    }
    /**
     * 获取体验金
     * @param array $uids
     * @return array
     */
    public function get_expmoeny($uids = array()){
        $res = array();
        $one_ = array();
        foreach ($uids as $uid){
            $table = $this->getTableIndex($uid, $this->expmoney_table);
            $sql = "SELECT * FROM ".$table." where status<2 and uietime >".NOW." and uid= $uid order by uietime desc";
            $one_[$uid] = $this->executeSql($sql);
        }

        foreach ($one_ as $k => $item){
            foreach ($item as $value){
                @$res[$value['uid']]['uid'] = $value['uid'];
                @$res[$value['uid']]['expmoney'] += $value['money'];
            }
        }
        return $res;
    }

    /**
     * 统计总资产
     * @param array $all_money
     * @param array $balance
     * @param array $profit
     * @param array $expmoney
     * @return array
     */
    public function all_asset($all_money = array(), $balance = array(), $profit = array(), $expmoney = array()){
        $asset_one = array();
        $all_asset = array();
        $data = array($all_money,$balance,$profit,$expmoney);
        $key = array('all_money','balance','count_profit','expmoney');
        if (is_array($data)) {
            for ($i = 0; $i < count($data); $i++) {
                if (is_array($data[$i])) {
                    foreach ($data[$i] as $k => $item) {
                        $asset_one[$k][$key[$i]] = $data[$i][$k][$key[$i]];
                    }
                }
            }

            foreach ($asset_one as $index => $value) {
                $all_asset[$index]['uid'] = $index;
                $all_asset[$index]['all_asset'] = array_sum($value);
            }
        }
        return $all_asset;
    }

    /**
     * 统计渠道
     * @return bool
     */
    public function count_plat(){
        $sql = "select DISTINCT plat  from $this->user_account WHERE uid IS NOT NULL";
        $result = $this->executeSql($sql);
        return $result ? $result : false;
    }

    /**
     * 复投条件结果
     * @param array $data
     * @param string $re_number
     * @param string $re_money
     * @return array|bool
     */
    public function rebuy_condition($data = array(),$re_number = '',$re_money = ''){
        if (!empty($re_number) && !empty($re_money)) {
            $num_result = $this->condition_result($data,'num',$re_number);
            $data = $this->condition_result($num_result,'re_money',$re_money);
        }else{
            if (empty($re_number)){
                $data = $this->condition_result($data,'re_money',$re_money);
            }else{
                $data = $this->condition_result($data,'num',$re_number);
            }
        }

        return $data ? $data : false;

    }

    /**
     * 处理复投条件
     * @param array $data
     * @param string $condition
     * @param string $param
     * @return array|bool
     */
    public function condition_result($data = array(),$condition = '',$param = ''){
        $result = array();
        if ($data) {
            if (stristr($param, '-')) {
                list($min, $max) = explode('-', $param);
                foreach ($data as $key => $value) {
                    $number = isset($value[$condition]) ? $value[$condition] : 0;
                    if ($number >= $min && $number <= $max) {
                        $result[$key] = $value;
                    }
                }
            } else {
                foreach ($data as $key => $value) {
                    if (@$value[$condition] == $param) {
                        $result[$key] = $value;
                    }
                }
            }
        }
        return $result ? $result : false;
    }

    public function redis_cache($key,$data='',$expire=60) {
        if($data){
            $data = is_array($data) ? json_encode($data) : $data;
            $rtn = self::$container['redis_app_w']->save($key,$data);
            self::$container['redis_app_w']->expire($key , $expire);
            return $rtn;
        }else{
            $rtn = self::$container['redis_app_w']->get($key);
            $rtn = json_decode($rtn) ? json_decode($rtn,true) : $rtn;
            return $rtn;
        }
    }
}