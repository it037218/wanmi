<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_account_model extends Basemodel {
    
    private $_table = 'cmibank.cmibank_account';
    
    public function getAccountByUid($uid){
         return $this->selectDataSql($this->_table, array('uid' => $uid));
    }
    
    public function editAccount($uid, $data){
        $key = _KEY_REDIS_ACCOUNT_INFO_PREFIX_ . $uid;
        #删除key
        self::$container['redis_default']->delete($key);
        return $this->updateDataSql($this->_table, $data, array('uid'=>$uid));
    }
    public function getAccountlist($start,$end,$pt){
         //2015-08-11 00;00;00
         if(empty($pt)){
            $sql = "SELECT uid,ctime,account FROM $this->_table WHERE ctime BETWEEN unix_timestamp('$start') and unix_timestamp('$end')";
         }else{
             $sql = "SELECT uid,ctime,account FROM $this->_table WHERE ctime BETWEEN unix_timestamp('$start') and unix_timestamp('$end') AND plat ='$pt'";
         }
         

         return $this->executeSql($sql);
    }
    
    public function getGROUPplat($odate){
//         $odate = date('Y-m-d',strtotime('-1 day'));
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT count(*) as register,plat FROM $this->_table WHERE ctime >= $start_time  AND ctime <  $end_time GROUP BY plat";
        return $this->executeSql($sql);
    }
    
    public function getGroupByplat($plat,$odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT uid FROM $this->_table WHERE ctime >= $start_time  AND ctime <  $end_time and plat = '$plat'";
        $aa = $this->executeSql($sql);
        foreach ($aa as $key=>$val){
            $bb[$key] = $val['uid'];
        } 
        return implode(',',$bb); 

    }
    public function getAccountListByLike($searchregphone){
        $sql = "SELECT uid FROM $this->_table WHERE `account` like '%$searchregphone%'";
        $aa = $this->executeSql($sql);
        if(!empty($aa)){
            foreach ($aa as $val){
                $bb[] = $val['uid'];
            }
            return  implode(",", $bb);
        }else{
            return null;
        }
         
    }
    
    public function getAccountInUids($uids){
        $sql = "SELECT uid,account,plat,ctime,ltime,forbidden FROM $this->_table WHERE `uid` in ($uids)";
        return $this->executeSql($sql);
    }
    
    public  function getAccountCount(){
        return $this->selectDataCountSql($this->_table);
    }
    
    public function getUidsByIos(){
        $sql = "SELECT uid FROM $this->_table where plat like '%appStore%'";
        $aa = $this->executeSql($sql);
        foreach ($aa as $key=>$val){
            $bb[] = $val['uid'];
        }
         return implode(",", $bb);
    }
    
    public function delUserCache($uid, $phone){
        $key = _KEY_REDIS_ACCOUNT_INFO_PREFIX_ . $uid;
        #删除key
        self::$container['redis_default']->delete($key);
        $key  = _KEY_REDIS_ACCOUNT_UID_PREFIX_ . $phone;
        self::$container['redis_default']->delete($key);
        return true;
    }
 
    public function getAccountInfoByPhones($phone){
        $ret = $this->selectDataListSql('cmibank.cmibank_account', array('account' => $phone));
        return $ret;
    }
    
    public function getAccountUidList($offset, $psize){
    	$sql = "select account,uid from " . $this->_table . " limit $offset, $psize";
    	$data = $this->executeSql($sql);
    	return $data;
    }
    
    public function getUidByAccount($account){
    	$sql = "select uid from " . $this->_table . " where account=".$account;
    	$data = $this->executeSql($sql);
    	return $data;
    }
    public function getAccountListForCount($plat,$stime,$etime,$offset,$rows){
    	$sql = "select * from $this->_table where plat='".$plat."' and ctime<=$etime and ctime>=$stime order by ctime desc limit $offset , $rows";
    	$data = $this->executeSql($sql);
    	return $data;
    }
    public function countAccountListForCount($plat,$stime,$etime){
    	$sql = "select count(*) as counts from $this->_table where ctime<=$etime and ctime>=$stime and plat='".$plat."'";
    	$data = $this->executeSql($sql);
    	return $data[0]['counts'];
    }
}