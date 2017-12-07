<?php

require_once APPPATH.'models/base/basemodel.php'; 
include(APPPATH.'libraries/fuiou.class.php');

class admin_fuioupay_model extends Basemodel {

    private $withdrawTable = 'cmibank_log.cmibank_withdraw_failed_log';
    private $city_table = 'cmibank.cmibank_city_id';
    private $fuiou_out = 'cmibank.cmibank_fuiou_out';

    private $fuioupay;

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        
        $this->config->load('cfg/fuiou_config', true, true);
        $this->config->load('cfg/banklist', true, true);
        $this->config->load('cfg/reminder_banklist', true, true);
        $fuioupay_config = $this->config->item('cfg/fuiou_config');
        $this->banklist = $config =  $this->config->item('cfg/banklist');
        $this->reminder_banklist = $config =  $this->config->item('cfg/reminder_banklist');
        $this->fuioupay = new fuiou($fuioupay_config);
    }

    /**
     * 打款到银行卡
     * @param int $uid
     * @param string $name
     * @param array $post_data
     * @return type
     */
    public function withdraw($uid = 0, $name = '',$post_data = array()) {
        $key = _KEY_REDIS_FUIOU_PAY_OUT_INFO_DETAIL_PREFIX_;
        $data = $this->fuioupay->withdraw($post_data['orderid'], $post_data['bankno'], $post_data['cityno'] , $post_data['account_no'] ,$post_data['accntnm'], $post_data['cost_money']*100);
        //添加到缓存
        if($data){
            $post_data['uid'] = $uid;
            $post_data['username'] = $name;
            $post_data['ret'] = $data['ret'];
            $post_data['memo'] = $data['memo'];
            self::$container['redis_app_w']->setAdd($key, json_encode($post_data),1,$post_data['orderid']);
        }
        return $data;
    }
    
    public function VerifySign($post,$sign) {
        return $this->fuioupay->VerifySign($post,$sign);
    }
    
    public function queryWithDrawOrder($orderno,$startdt,$enddt,$transst) {
        return $this->fuioupay->queryWithDrawOrder($orderno,$startdt,$enddt,$transst);
    }
    
    public function queryWithDrawStatus($orderno,$startdt,$enddt,$transst) {
        return $this->fuioupay->queryWithDrawStatus($orderno,$startdt,$enddt,$transst);
    }
    
    public function queryFailWithDraw($orderid) {
        return $this->selectDataSql($this->withdrawTable,array('orderid' => $orderid));
    }

    /**
     * 获取打款记录
     * @param string $where
     * @param string $order
     * @param array $limit
     * @return bool
     */
    public function getfuiouOutList($where = '',$order = '',$limit = array()){
        if (!empty($where)) {
            $w = "f.order_id like '%" .$where. "%' AND f.show_status=0";
        }else{
            $w = "1=1 AND f.show_status=0";
        }
        if (!empty($order)) $order = "ORDER BY f.$order";
        $sql = "SELECT * FROM $this->fuiou_out AS f LEFT JOIN $this->city_table AS c ON f.cityno = c.city_id WHERE $w $order";
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        $data = $this->executeSql($sql);
        //$data = $this->selectDataListSql($this->fuiou_out,$where,$order,$limit);
        foreach (array_merge($this->banklist, $this->reminder_banklist) as $b){
            foreach ($data as $k => $f){
                if ($b['fuiou_bank_code'] == $f['bankno']){
                    $data[$k]['bankno'] = $b['name'];
                }
            }
        }
        return $data;
    }

    /**
     * 获取一条打款记录
     * @param string $order_id
     * @return mixed
     */
    public function GetFuiouOut($order_id = ''){
        $where = array('order_id' => $order_id);
        return $this->selectDataSql($this->fuiou_out, $where);
    }

    /**
     * 删除打款缓存
     * @param string $order_id
     * @return mixed
     */
    public function delFuiouOutCacheByid($order_id = ''){
        $key = _KEY_REDIS_FUIOU_PAY_OUT_INFO_DETAIL_PREFIX_ ;
        $data = $this->GetFuiouOut($order_id);
        $cache_data = array(
            'orderid' => $data['order_id'],
            'bankno' => $data['bankno'],
            'cityno' => $data['cityno'],
            'account_no' => $data['account_no'],
            'accntnm' => $data['accntnm'],
            'cost_money' => $data['money'],
            'uid' => $data['uid'],
            'username' => $data['username'],
            'ret' => $data['status_code'],
            'memo' => $data['status'],
        );
        return self::$container['redis_app_w']->setMove($key, json_encode($cache_data),1);
    }

    /**
     * 删除数据库打款记录
     * @param $order_id
     * @return bool
     */
    public function delFuiouOut($order_id){
        $data['show_status'] = 1;
        if(!$this->updateDataSql($this->fuiou_out, $data, array('order_id' => $order_id))){
            return false;
        }
        return true;
    }
    /**
     * 获取省
     * @return bool
     */
    public function returnProvince(){
        $sql = "SELECT DISTINCT province_code `province_code`,`province_name` from $this->city_table";
        return $this->executeSql($sql);
    }

    /**
     * 获取市
     * @param string $province_id
     * @return string
     */
    public function returnCity($province_id = ''){
        $sql = "SELECT * from $this->city_table WHERE `province_code` = '{$province_id}'";
        return $this->executeSql($sql);
    }

    /**
     * 添加打款记录到表中
     * @param int $uid
     * @param string $username
     * @param string $status_code
     * @param string $status
     * @param array $post_data
     * @param int $created_time
     * @return bool
     */
    public function addlog($uid = 0, $username = '', $status_code = '',$status = '',$post_data = array(), $created_time = 0){
        $data_info = array(
            'uid' => $uid,
            'username' => $username,
            'status_code' => $status_code,
            'status' => $status,
            'created_time' => $created_time,
        );
        $post_data['order_id'] = $post_data['orderid'];
        $post_data['money'] = $post_data['cost_money'];
        unset($post_data['orderid']);
        unset($post_data['cost_money']);
        $all_data = array_merge($data_info, $post_data);
        return $this->insertDataSql($all_data, $this->fuiou_out);
    }
    /**
     * 生成随机订单号
     * @param int $param
     * @return string
     */
    public function getOrder($param = 4){
        $str="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $key = "";
        for($i=0;$i<$param;$i++)
        {
            $key .= $str{mt_rand(0,32)};
        }
        return time().$key;
    }
    
}