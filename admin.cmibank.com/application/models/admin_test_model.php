<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_test_model extends Basemodel {
        
    public function getuserproductby($uid,$start_time,$end_time){
        $_table_index = $uid % 16;
        $sql = "SELECT sum(money) FROM cmibank.cmibank_userproduct_$_table_index where uid in ($uid) and buytime >= unix_timestamp('$start_time')  AND buytime <  unix_timestamp('$end_time')";
        return $this->executeSql($sql);
    }
    
    public function getuserlongproductby($uid,$start_time,$end_time){
        $_table_index = $uid % 16;
        $sql = "SELECT sum(money) FROM cmibank.cmibank_userlongproduct_$_table_index where uid in ($uid) and buytime >= unix_timestamp('$start_time')  AND buytime <  unix_timestamp('$end_time')";
        return $this->executeSql($sql);
    }
    
    public function get_11_yue_berfor(){
        $sql = "select uid from cmibank.cmibank_account where ctime <1448899199 limit 0 ,40";
        $aa = $this->executeSql($sql);
        foreach ($aa as $key=>$val){
            $bb[$key] = $val['uid'];
        }
        
        return implode(',',$bb);
    }
    public function get_product_buy_info_by_uids($index,$uids){
        $sql ="SELECT SUM(money) FROM cmibank.cmibank_product_buy_info_$index where uid in ($uids)";
        $aa = $this->executeSql($sql);
        foreach ($aa as $key=>$val){
           $bb = $val['SUM(money)'];
        }
        return $bb;
    }

    
    public function insertbi($data){
        $tableName= 'cmibank_yunying.cmibank_bi';
        return $this->insertDataSql($data , $tableName,$show_sql = false);
    }
    
    public function insertbiWeek($data){
        $tableName= 'cmibank_yunying.cmibank_bi_week';
        return $this->insertDataSql($data , $tableName,$show_sql = false);
    }
    
    public function daybuyuser($index,$type,$odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        if($type == 'd'){
            $tableName= "cmibank.cmibank_userproduct_$index";
            $sql = "SELECT uid FROM ".$tableName." WHERE buytime >= $start_time  AND buytime <  $end_time";
        }else{
            $tableName= "cmibank.cmibank_userlongproduct_$index";
            $sql = "SELECT uid FROM ".$tableName." WHERE buytime >= $start_time  AND buytime <  $end_time";
        }
        $aa = $this->executeSql($sql);
        $bb = array();
        foreach ($aa as $val){
            $bb[] = $val['uid'];
        }
        return $bb;  
    }
    public function getdaybuyuser($uids){
        $sql = "SELECT uid,plat FROM cmibank.cmibank_account where uid in($uids)";
        return $this->executeSql($sql);
    }
    
    public function getdaybuyusers($uids){
        $sql = "SELECT COUNT(*) as daybuyuser,plat FROM cmibank.cmibank_account where uid in($uids) GROUP BY plat";
        return $this->executeSql($sql);
    }
    
    public function getolduser($odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT uid,plat FROM cmibank_yunying.cmibank_olduser WHERE ctime >= $start_time  AND ctime <  $end_time";
        return $this->executeSql($sql);
    }
    
    public function getoldusers($odate){
        $time = strtotime($odate);
        $start_time = $time - 86400;
        $end_time = $time;
        $sql = "SELECT COUNT(*) as oldnum ,plat FROM cmibank_yunying.cmibank_olduser WHERE ctime >= $start_time  AND ctime <  $end_time GROUP BY plat";
        return $this->executeSql($sql);
    }
    
    public function getBireport($mon,$sun){
        $sql = "SELECT * FROM cmibank_yunying.cmibank_bi WHERE cdate between '".$mon."' and '".$sun."'";
        return $this->executeSql($sql);
    }
       public function getPay_log($index){
           $bb = array();
           $sql = "SELECT uid,SUM(amt) FROM cmibank_log.`cmibank_pay_log_2015_$index` where `status` >=1 GROUP BY uid";
           $aa = $this->executeSql($sql);
           foreach ($aa as $key=>$val){
               $bb[$val['uid']] = $val['SUM(amt)'];
           }
           return $bb;
       }
//     public function getUserLongProduct($index){
//        $sql = "SELECT distinct uid FROM cmibank.cmibank_userlongproduct_$index";
//        $aa =  $this->executeSql($sql);
//        foreach ($aa as $key=>$val){
//           $bb[$key] = $val['uid'];
//        }
//        $cc = implode(',', $bb);
//        return $cc;
//     }
//     public function search_cmibank_user_identity($uids){
//         $sql = "select * from cmibank.cmibank_user_identity where isnew = 1 and uid in ($uids)";
//         return $this->executeSql($sql);
//     }
       public function sumProductByIos($index,$uids){
           $sql ="SELECT sum(money) FROM cmibank.cmibank_product_buy_info_$index where uid in ($uids)";
           $aa = $this->executeSql($sql);
           return $aa[0]['sum(money)'];
       }
       public function sumLongProductByIos($index,$uids){
           $sql ="SELECT sum(money) FROM cmibank.cmibank_longproduct_buy_info_$index where uid in ($uids)";
           $aa = $this->executeSql($sql);
           return $aa[0]['sum(money)'];
       }
       
       public function countProductByNum($index,$uids){
           $sql ="SELECT COUNT(DISTINCT(uid)) FROM cmibank.cmibank_longproduct_buy_info_$index where uid in($uids)";
           $aa = $this->executeSql($sql);
           return $aa[0]['COUNT(DISTINCT(uid))'];
       }
       //
       //public function countProductByNum($index,$uids){
       //    $sql ="SELECT uid FROM cmibank.cmibank_longproduct_buy_info_$index where uid in($uids)";
       //    $aa = $this->executeSql($sql);
       //    return $aa[0]['COUNT(DISTINCT(uid))'];
       //}
        
       
       //获取某一个时间段的邀请
       public function invitetime(){
           $sql ="SELECT uid FROM cmibank.`cmibank_invite` where itime BETWEEN 1448899200 and 1450886399";
           $aa = $this->executeSql($sql);
            foreach ($aa as $key=>$val){
                $bb[$key] = $val['uid'];
            }
           return implode(',',$bb);
       }
       public function moneydayu($index,$uids){
           $sql="SELECT count(*) as aa FROM cmibank_log.`cmibank_buy_log_2015_$index` where ctime BETWEEN 1448899200 and 1450886399 and amt>2000 and uid in ($uids)";
           $aa = $this->executeSql($sql);
           return $aa[0]['aa'];
         
       }
       
       public function buyProductNumber($index,$start,$end){
          $start = strtotime($start);
          $end = strtotime($end);
          $sql ="SELECT count(distinct(uid)) FROM cmibank.`cmibank_userproduct_$index` where buytime BETWEEN $start and $end";
          $aa = $this->executeSql($sql);
          foreach ($aa as $key=>$val){
              $bb[$key] = $val['count(distinct(uid))'];
          }
          return $bb[0];  
       }
       
       
       
       public function buyLongProductNumber($index,$start,$end){
           $start = strtotime($start);
           $end = strtotime($end);
           $sql ="SELECT count(distinct(uid))  FROM cmibank.`cmibank_userlongproduct_$index` where buytime BETWEEN $start and $end";
           $aa = $this->executeSql($sql);
           foreach ($aa as $key=>$val){
               $bb[$key] = $val['count(distinct(uid))'];
           }
           return $bb[0];
       }
       
       
       //二次购买
       public function buyProductNumber2($index){
           $start = strtotime($start);
           $end = strtotime($end);
           $sql ="select COUNT(*) from (SELECT uid, count(*) as a  FROM cmibank.`cmibank_userproduct_$index` where buytime BETWEEN $start and $end group by uid ) as b where a >=2";
           $aa = $this->executeSql($sql);
           foreach ($aa as $key=>$val){
               $bb[$key] = $val['COUNT(*)'];
           }
           return $bb[0];
       }
       public function buyLongProductNumber2($index){
           $start = strtotime($start);
           $end = strtotime($end);
           $sql ="select COUNT(*) from (SELECT uid, count(*) as a  FROM cmibank.`cmibank_userlongproduct_$index` where buytime BETWEEN $start and $end group by uid) as b where a >=2";
           $aa = $this->executeSql($sql);
           foreach ($aa as $key=>$val){
               $bb[$key] = $val['COUNT(*)'];
           }
           return $bb[0];
       }

       //
       public function buyProductUId($index){
           $start = strtotime($start);
           $end = strtotime($end);
           $sql ="SELECT distinct(uid) FROM cmibank.`cmibank_userproduct_$index` where buytime BETWEEN $start and $end order by uid desc";
           $aa = $this->executeSql($sql);
           foreach ($aa as $key=>$val){
               $bb[] = $val['uid'];
           }
           print_r(implode(',',$bb).",");
       }
       public function buyLongProductUid($index){
           $start = strtotime($start);
           $end = strtotime($end);
           $sql ="SELECT distinct(uid) FROM cmibank.`cmibank_userlongproduct_$index` where buytime BETWEEN $start and $end";
            $aa = $this->executeSql($sql);
           foreach ($aa as $key=>$val){
               $bb[] = $val['uid'];
           }
           print_r(implode(',',$bb).",");
       }
    
}