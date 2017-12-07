<?php

require_once 'basemodel.php'; 

class invite_base extends Basemodel{
    
    private $_table = 'cmibank.cmibank_invite';
    private $_table2 = 'invite_count.cmibank_invite';
    
    private $start_time = '2017-09-01 00:00:00';
    
    private $end_time = '2017-11-03 23:55:55';
    
    private $cache_name = 'invite_rank_200';
    
    private $cache_name_be_invite = 'invite_1111_my_detail';

    public $insert_center = array(
        '4'=>array(
            'uid' => 1,
            'u_account' => 13017481750,
            'invite_uid' => 116016,
            'invite_account' => 13017481750,
            'itime' => 1509514347,
            'buytime' => 1509514622,
            'rewardmoney' => 5,
            'count' => 6,
            'subbuyamout' => 100.00,
        ),
        '2'=>array(
            'uid' => 2,
            'u_account' => 13301920950,
            'invite_uid' => 116017,
            'invite_account' => 13301920950,
            'itime' => 1509514342,
            'buytime' => 1509514622,
            'rewardmoney' => 5,
            'count' => 6,
            'subbuyamout' => 200.00,
        ),
        '6'=>array(
            'uid' => 3,
            'u_account' => 15821335510,
            'invite_uid' => 116017,
            'invite_account' => 15821335510,
            'itime' => 1509514342,
            'buytime' => 1509514622,
            'rewardmoney' => 5,
            'count' => 6,
            'subbuyamout' => 200.00,
        ),
//        '5'=>array(
//            'uid' => 4,
//            'u_account' => 15150150936,
//            'invite_uid' => 116017,
//            'invite_account' => 15150150936,
//            'itime' => 1509514342,
//            'buytime' => 1509514622,
//            'rewardmoney' => 5,
//            'count' => 6,
//            'subbuyamout' => 200.00,
//        )
    );

    //添加邀请人
    public function add_invite($uid, $u_account, $invite_uid, $invite_account){
        $data = array(
            'uid' => $uid,
            'u_account' => $u_account,
            'invite_uid' => $invite_uid,
            'invite_account' => $invite_account,
            'itime' => NOW,
        );
        $this->insertDataSql($data, $this->_table);
        $key = _KEY_REDIS_MY_INVITE . $invite_uid;
        return self::$container['redis_default']->delete($key);
    }
    
    
    public function addInviteForLuckybag($data,$uid){
    	$this->insertDataSql($data, $this->_table);
    	$key = _KEY_REDIS_MY_INVITE . $uid;
    	return self::$container['redis_default']->delete($key);
    }
    
    public function _db_update_my_buytime($data, $where){
        $ret = $this->updateDataSql($this->_table, $data, $where);
        if($ret){
            $key = _KEY_REDIS_INVITE_MY . $where['uid'];
            self::$container['redis_default']->delete($key);
            $key = _KEY_REDIS_MY_INVITE . $where['invite_uid'];
            self::$container['redis_default']->delete($key);
            $key = _KEY_REDIS_MY_INVITE_COUNT . $where['invite_uid'];
            self::$container['redis_default']->delete($key);
            $key = _KEY_REDIS_MY_INVITE_REWARD . $where['invite_uid'];
            self::$container['redis_default']->delete($key);
            $key = _KEY_REDIS_MY_INVITE_REWARD . $where['uid'];
            self::$container['redis_default']->delete($key);
        }
        return $ret;
    }
    
    //取得邀请我的人
    public function _db_get_invite_my($uid){
        $data = self::$container['db_r']->select('*')
        ->from($this->_table)
        ->where('uid', $uid)
        ->get()
        ->row_array();
        return $data;
    }
    
    //取得邀请我的人
    public function get_invite_my($uid){
        $key = _KEY_REDIS_INVITE_MY . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid){
            $invite = $self->_db_get_invite_my($uid);
            if(empty($invite)) return false;
            return json_encode($invite);
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return json_decode($return , true);
    }
    
    //取得我邀请的人
    public function _db_get_my_invite($uid){
        $data =self::$container['db_r']->select('*')
        ->from($this->_table)
        ->where('invite_uid', $uid)
        ->get()
        ->result_array();
        return $data;
    }
    
    public function init_my_invite($uid){
        $result = $this->_db_get_my_invite($uid);
        if($result){
            $key = _KEY_REDIS_MY_INVITE . $uid;
            foreach ($result as $value){
                self::$container['redis_default']->setAdd($key, json_encode($value), 1, $value['itime']);
            }
        }
        return true;
    }
    
    public function get_my_invite($uid){
        $key = _KEY_REDIS_MY_INVITE . $uid;
        $data = self::$container['redis_default']->setRange($key, 0, -1);
        if(empty($data)){
            $this->init_my_invite($uid);
            $data = self::$container['redis_default']->setRange($key, 0, -1);
        }
        $rtn = array();
        if($data){
            foreach ($data as $key => $_v){
                $rtn[$key] = json_decode($_v, true);
            }
        }
        return $rtn;
    }
    
    public function _count_db_user_invite_money($uid){
        $sql = "SELECT sum(rewardmoney) as s_money FROM " . $this->_table . " WHERE invite_uid = " . $uid ;
        $data = $this->executeSql($sql);
        return $data[0]['s_money'] ? $data[0]['s_money'] : 0;
    }
    
    public function count_user_invite_money($uid){
        $key = _KEY_REDIS_MY_INVITE_COUNT . $uid;
        $self = $this;
        $return = $this->remember($key, 0 , function() use($self , $uid) {
            $money = $self->_count_db_user_invite_money($uid);
            if(empty($money)) return false;
            return $money;
        } , _REDIS_DATATYPE_STRING, self::$container['redis_default'], self::$container['redis_default']);
        return $return;
    }
    
    public function sum_invite_rewardmoney_with_buytime($odate){
        $time = strtotime($odate);
        $start_time = $time;
        $end_time = $time + 86400;
        $sql = "SELECT sum(rewardmoney) as sum_rewardmoney FROM " . $this->_table . " WHERE buytime >= " . $start_time . " AND buytime < " . $end_time;
        $data = $this->executeSql($sql);
        return $data[0]['sum_rewardmoney'] ? $data[0]['sum_rewardmoney'] : 0;
    }
    
    public function top_rank($type = false) {
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        $admaster = implode(',', $invite_cfg['ad_master']);
        $channel_user = implode(',', $invite_cfg['channel']);
        $notuid = '';
        if($channel_user && $admaster){
            $notuid = $channel_user.','.$admaster;
        }else{
            //暂不考虑
        }
        
        $start_time = strtotime($invite_cfg['buff_stime']);
        $end_time = strtotime($invite_cfg['buff_etime']);
        
        if($type == 1){
            $sql = "SELECT count(*) AS sum FROM ".$this->_table.' WHERE itime>'.$start_time." AND itime<$end_time" ;
            $data = $this->executeSql($sql);
        } elseif($type == 2){
            $sql = "SELECT * FROM ".$this->_table.' WHERE itime>'.$start_time." AND itime<$end_time" ;
            $data = $this->executeSql($sql);
        } elseif($type == 3){
            $sql = "SELECT * FROM ".$this->_table.' WHERE itime>'.$start_time." AND itime<$end_time GROUP BY invite_uid" ;
            $data = $this->executeSql($sql);
        } elseif($type == 4){
            $sql = "SELECT *,count(*) AS count FROM ".$this->_table.' WHERE itime>'.$start_time." AND itime<$end_time AND buytime !=0 AND buymoney>1000 AND invite_uid NOT IN($notuid) GROUP BY invite_uid ORDER BY count DESC LIMIT 200" ;
            $data = $this->executeSql($sql);
        } else{
            $sql = "SELECT A.*,sum(B.money) AS sum FROM ".$this->_table." AS A LEFT JOIN cmibank.cmibank_product_buy_info AS B ON A.uid=B.uid WHERE A.itime>$start_time AND A.itime<$end_time AND B.ctime>$start_time AND B.ctime<$end_time AND B.ctime=A.buytime LIMIT 1000" ;
            $data = $this->executeSql($sql);
            //116051
            //116051
        }
        return $data;
    }
    public function top_rank2() {
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        $admaster = implode(',', $invite_cfg['ad_master']);
        $channel_user = implode(',', $invite_cfg['channel']);
        $notuid = array_merge($invite_cfg['ad_master'],$invite_cfg['channel']);
        
        $start_time = strtotime($invite_cfg['buff_stime']);
        $end_time = strtotime($invite_cfg['buff_etime']);
        
        $sql = "SELECT * FROM ".$this->_table;
        $data = $this->executeSql($sql);
        
        $new_array = array();
        
//        foreach ($data as $key => $value) {
//            $buytime = substr($value['buytime'],-7);
//            $_buytime = substr($value['buytime'],0,3);
//            if($buytime> 0329600 && $buytime < 3007999 && $_buytime ==151){
//                $new_array[] = $value;
//            }
//        }
        
        
//        print_r(count($new_array));//4919
        
        foreach ($data as $key => $value) {
            if(in_array($value['invite_uid'], $notuid)){
                continue;
            }
            $buytime = $value['buytime'];
            $dateym = date("Ym",$buytime);
            $dated = date("d",$buytime);
            if($dateym == '201711' && $dated>=11 && $value['buymoney'] >= 1000){
                $new_array[] = $value;
            }
            if($dateym == '201712' && $dated<=12 && $value['buymoney'] >= 1000){
                $new_array[] = $value;
            }
        }
        
        $array_invite = array();
        foreach ($new_array as $key => $value) {
            $array_invite[$value['invite_account']][] = $value;
        }
        
//        print_r($array_invite);
        
        $stat_array = array();
        foreach ($array_invite as $key2 => $value2) {
            $sub_value2['count'] = count($value2);
            $subbuyamout = 0;
            foreach ($value2 as $key3 => $value3) {
                $subbuyamout += $value3['buymoney'];
            }
            $sub_value2['invite_uid'] = $value2[0]['invite_uid'];
            $sub_value2['invite_account'] = $key2;
            $sub_value2['subbuyamout'] = $subbuyamout;
//            $sub_value2['detail'] = $value2;
            $stat_array[] = $sub_value2;
        }
        
//        $neeee = array();
//        foreach ($stat_array as $key4 => $value4) {
//            $neeee[$key4] = $value4;
//            if($key4 == 20){
//                break;
//            }
//        }
//        print_r($neeee);
        
        
//        print_r($array_invite);
//        foreach ($new_array as $key2 => $value2) {
//            $new_array[$key2]['count'] = count($array_invite[$value2['invite_account']]);
//        }
        
        return $stat_array;
       //4915
        
//        return $data;
    }
    
    public function getFirstInvenst($top_rank) {
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        
        $cachekey = $this->cache_name;
        $top_rank_cache = $this->redis_cache($cachekey);
        $top_rank_cache = array();
        if($top_rank_cache){
            $top_rank = $top_rank_cache;
        } else {
            $start_time = strtotime($invite_cfg['buff_stime']);
            $end_time = strtotime($invite_cfg['buff_etime']);
        
            foreach ($top_rank as $key => $val) {
                
//                $sql = "SELECT B.*,from_unixtime(B.ctime) AS f_ctime,sum(B.money) AS buymoney FROM ".$this->_table." AS A LEFT JOIN cmibank.cmibank_product_buy_info AS B ON A.uid=B.uid WHERE B.ctime>$start_time AND B.ctime<$end_time AND B.ctime=A.buytime AND A.invite_uid={$val['invite_uid']}";
//                    $data = $this->executeSql($sql);
//                    $buymoney += $data[0]['buymoney'];
                
                    $sql = "SELECT sum(buymoney) AS subbuyamout FROM ".$this->_table." WHERE buytime>$start_time AND buytime<$end_time AND invite_uid={$val['invite_uid']} AND buymoney>1000";
                    $data = $this->executeSql($sql);
                    
                
//                $buymoney = 0;
//                for($i = 0;$i<=15;$i++){
//                    $sql = "SELECT B.*,from_unixtime(B.ctime) AS f_ctime,sum(B.money) AS buymoney FROM ".$this->_table." AS A LEFT JOIN cmibank.cmibank_product_buy_info_{$i} AS B ON A.uid=B.uid WHERE B.ctime>$start_time AND B.ctime<$end_time AND B.ctime=A.buytime AND A.invite_uid={$val['invite_uid']}";
//                    $data = $this->executeSql($sql);
//                    $buymoney += $data[0]['buymoney'];
//                }
                
                $top_rank[$key]['subbuyamout'] = $data[0]['subbuyamout']; 
            }
            
            $insert_center = $this->insert_center;
            foreach ($insert_center as $key2 => $value2) {
                $top_rank = $this->insertPosition($top_rank, $key2, $value2);
            }
            foreach ($top_rank as $key3 => $value3) {
                $top_rank[$key3]['_invite_account'] = substr($value3['invite_account'], 0, 3) . '****' . substr($value3['invite_account'], -4);
            }
//            print_r($top_rank);
            $new_top_rank = array();
            foreach ($top_rank as $key => $newvalue) {
                $new_top_rank[$key]=$newvalue['count'];
            }
            arsort($new_top_rank);
//            print_r($new_top_rank);
            $new_top_rank2 = array();
            foreach ($new_top_rank as $key2 => $newvalue2) {
                $new_top_rank2[] = $top_rank[$key2];
            }
            $top_rank = $new_top_rank2;
//            print_r($top_rank);
            //去除多余数量
            if(count($top_rank) > 200){
                $insert_count = count($insert_center);
                for($i = 0;$i<$insert_count;$i++){
                    unset($top_rank[count($top_rank)-1]);
                }
            }
//            print_r($top_rank);
            $this->redis_cache($cachekey,$top_rank,3600*6);
        }
        return $top_rank;
    }
    public function getFirstInvenst2($top_rank) {
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        
        $cachekey = $this->cache_name;
        $top_rank_cache = $this->redis_cache($cachekey);
//        $top_rank_cache = array();
        if($top_rank_cache){
            $top_rank = $top_rank_cache;
        } else {

            
//            print_r($top_rank);
            $new_top_rank = array();
            foreach ($top_rank as $key => $newvalue) {
                $new_top_rank[$key]=$newvalue['count'];
            }
            arsort($new_top_rank);
//            print_r($new_top_rank);
            $new_top_rank2 = array();
            foreach ($new_top_rank as $key2 => $newvalue2) {
                $new_top_rank2[] = $top_rank[$key2];
            }
            $top_rank = $new_top_rank2;
            
            $insert_center = $this->insert_center;
            foreach ($insert_center as $key2 => $value2) {
                $top_rank = $this->insertPosition($top_rank, $key2, $value2);
            }
            
            foreach ($top_rank as $key3 => $value3) {
                $top_rank[$key3]['_invite_account'] = substr($value3['invite_account'], 0, 3) . '****' . substr($value3['invite_account'], -4);
            }
            
            /*再排序*/
            $new_top_rank = array();
            foreach ($top_rank as $key => $newvalue) {
                $new_top_rank[$key]=$newvalue['count'];
            }
            arsort($new_top_rank);
//            print_r($new_top_rank);
            $new_top_rank2 = array();
            foreach ($new_top_rank as $key2 => $newvalue2) {
                $new_top_rank2[] = $top_rank[$key2];
            }
            $top_rank = $new_top_rank2;
            /*再排序end*/
            
            //去除多余数量
            if(count($top_rank) > 200){
                $insert_count = count($insert_center);
                for($i = 0;$i<$insert_count;$i++){
                    unset($top_rank[count($top_rank)-1]);
                }
            }
//            print_r($top_rank);
            $this->redis_cache($cachekey,$top_rank,3600*50);
        }
        return $top_rank;
    }
    
    /**
     * 查询邀请
     * @param int $account 邀请人手机号码
     */
    public function queryBeInvite($account) {
        $this->config->load('cfg/invite_cfg', true, true);
        $invite_cfg = $this->config->item('cfg/invite_cfg');
        
        $cachekey = $this->cache_name_be_invite;
        $data_cache = $this->redis_cache($cachekey);
        
        if($data_cache){
            $data = $data_cache;
        } else {
            $start_time = strtotime($invite_cfg['buff_stime']);
            $end_time = strtotime($invite_cfg['buff_etime']);
            $sql = "SELECT B.*,from_unixtime(B.ctime) AS f_ctime FROM ".$this->_table." AS A LEFT JOIN cmibank.cmibank_product_buy_info AS B ON A.uid=B.uid WHERE B.ctime>$start_time AND B.ctime<$end_time AND B.ctime=A.buytime AND A.invite_account={$account}";
            $data = $this->executeSql($sql);
            $this->redis_cache($cachekey,$data);
        }
        
        return $data;
    }
    
    public function insertPosition($top_rank,$pos,$array) {
        $i = 0;
//        print_r($top_rank);
        $arr = array();
        foreach ($top_rank as $key2 => $value) {
            $iplus = $i+1;
            if($iplus == $pos){
                $array['count'] = $top_rank[$key2]['count']+ mt_rand(1, 2);
                $nextpos = $top_rank[$key2+1]['count'];
                if($nextpos >= $array['count']){
                    $array['count'] = $top_rank[$key2]['count'];
                }
                $array['subbuyamout'] = $top_rank[$key2]['subbuyamout']+ (mt_rand(0, 5)*1000);
                $arr[] = $array;
                $i++;
            }
            
            $arr[] = $value;
            $i++;
        }
        return $arr;
    }
    
    /**
     * 缓存数据
     * @param string $key
     * @param string|array $data
     * @param int $expire
     */
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
	
	public function getUserInfo($uid){
        $invite_uid = $this->selectDataSql($this->_table,array('invite_uid' => $uid));
        $user_id = $this->selectDataSql($this->_table, array('uid' => $uid));
        if ($invite_uid || $user_id){
            return true;
        }else{
            return false;
        }
    }
}