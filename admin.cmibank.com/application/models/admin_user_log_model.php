<?php

//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_user_log_model extends Basemodel{

    private $_table = 'cmibank.cmibank_user_log_';
    
     
    public function addUserLog($uid, $data){
        $table = $this->getTableIndex($uid, $this->_table);
        $data['ctime'] = time();
        $where = array('uid' => $uid, 'status' => 0);        
        $insertid = $this->insertDataSql($data, $table);
        $data['id'] = $insertid;
        $key = _KEY_REDIS_USER_LOG_PREFIX_.$uid;
        $logs = self::$container['redis_app_r']->get($key);
        $logs = json_decode($logs, true);
        $logs[] = $data;
        $logs = self::$container['redis_app_r']->save($key, json_encode($logs));
        return $insertid;
    }
    
    public function getUserLoglist($where,$order_by="",$limit=""){
        $_table_index = $where['uid'] % 16;
        $table = $this->_table . $_table_index;
        return $this->selectDataListSql($table,$where,$order_by,$limit);
    }
    
    public function inmoney($uid){
        $_table_index = $uid%16;
        $sql = "SELECT SUM(money) FROM cmibank.cmibank_user_log_".$_table_index." WHERE action =11 and uid=".$uid;
        return $this->executeSql($sql);
    }
    public function outmoney($uid){
        $_table_index = $uid%16;
        $sql = "SELECT SUM(money) FROM cmibank.cmibank_user_log_".$_table_index." WHERE action =13 and uid=".$uid;   
        return $this->executeSql($sql);
    }
    public function getUserLogListByCondition($searchParam,$offset,$psize){
        $table = $this->getTableIndex($searchParam['uid'], $this->_table);
    	$sql = "Select * from $table where uid=".$searchParam['uid'];
        if(!empty($searchParam['type'])){
            if($searchParam['type']==1){
                $sql =$sql." and action in (0, 4, 14, 13, 5, 6, 7) ";
            }else{
                $sql =$sql." and action in (1, 11, 2) ";
            }
        }
    	$sql .= " and ctime >".$searchParam['stime']." and ctime<".$searchParam['etime'].' order by id desc limit '.$offset.','.$psize;
    	return $this->executesql($sql);
    }
    public function countUserLogListByCondition($searchParam){
        $table = $this->getTableIndex($searchParam['uid'], $this->_table);
        $sql = "Select count(id) as count from $table where uid=".$searchParam['uid'];
        if(!empty($searchParam['type'])){
            if($searchParam['type']==1){
                $sql =$sql." and action in (0, 4, 14, 13, 5, 6, 7) ";
            }else{
                $sql =$sql." and action in (1, 11, 2) ";
            }
        }
        $sql .= " and ctime >".$searchParam['stime']." and ctime<".$searchParam['etime'];
           $ret = $this->executesql($sql);
        return $ret[0]['count'];
    }
    
    public function sumFanxin($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "select sum(money) as sum_money from $table where uid=$uid and pname='购买送现金活动'";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }

    public function suminvite($uid){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "select sum(money) as sum_money from $table where uid=$uid and pname='邀请好友奖励'";
    	$ret = $this->executesql($sql);
    	return $ret[0]['sum_money'];
    }
    
    public function getInviteUserlogList($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "select * from $table where uid=$uid and pname='邀请好友奖励'";
        return $this->executesql($sql);
    }
    public function getFanxinUserlogList($uid){
        $table = $this->getTableIndex($uid, $this->_table);
        $sql = "select * from $table where uid=$uid and pname='购买送现金活动'";
        return $this->executesql($sql);
    }
    
    public function sum_money_by_action($uid, $action, $show = false){
    	$table = $this->getTableIndex($uid, $this->_table);
    	$sql = "SELECT sum(money) as summoney FROM " . $table . " WHERE uid = " . $uid . " AND action = " . $action;
    	$data = $this->executeSql($sql);
    	return $data[0]['summoney'];
    }
}
