<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_buy_log_model extends Basemodel {
    //cmibank_pay_log_2015_27
    private $_table= 'cmibank_log.cmibank_buy_log_';
    
    
    public function getBuyLog($ordid){
        $tableName = $this->getTablePagLog($ordid,$this->_table);
        return $this->selectDataListSql($tableName,array('ordid' => $ordid));
    }
    
    public function getBulylogUidsbyQiantian($odate){
        $startime = strtotime($odate)-(86400*2);
        $endtime = strtotime($odate)-86400;
        $year = mb_substr( date('Y-m-d',$startime), 0, 4, 'utf-8');
        $num = str_pad(date('W',$startime),2,"0",STR_PAD_LEFT);
        if($num == 52){
            $year = 2016;
        }
        $sql = "SELECT DISTINCT uid FROM cmibank_log.cmibank_buy_log_".$year."_".$num." WHERE ctime >= $startime  AND ctime <  $endtime";
        $aa = $this->executeSql($sql);
        $bb = array();
        foreach ($aa as $val){
            $bb[] = $val['uid'];
        }
        return implode(',',$bb);
    }
    
    public function getBulylogUidsbyZuotian($odate){
        $startime = strtotime($odate)-86400;
        $endtime = strtotime($odate);
        $year = mb_substr( date('Y-m-d',$startime), 0, 4, 'utf-8');
        $num = str_pad(date('W',$startime),2,"0",STR_PAD_LEFT);
        if($num == 52){
            $year = 2016;
        }
        $sql = "SELECT DISTINCT uid FROM cmibank_log.cmibank_buy_log_".$year."_".$num." WHERE ctime >= $startime  AND ctime <  $endtime";
        $aa = $this->executeSql($sql);
        $bb = array();
        foreach ($aa as $val){
            $bb[] = $val['uid'];
        }
        return implode(',',$bb);
    }
    
    public function getBuylogUidsNextnextWeek($odate){
       
        $cur = $this->lastNWeek(strtotime($odate), 2);;
        $year = mb_substr($cur[0], 0, 4, 'utf-8');
        
        $num =str_pad(intval(date('W',strtotime($cur[0]))),2,"0",STR_PAD_LEFT);
        $startime = strtotime($cur[0]);
        $endtime = strtotime($cur[1])+86400;
        
        $sql = "SELECT DISTINCT uid FROM cmibank_log.cmibank_buy_log_".$year."_".$num."";
        $aa = $this->executeSql($sql);
        $bb = array();
        foreach ($aa as $val){
            $bb[] = $val['uid'];
        }
        return implode(',',$bb);
    }
    
    public function getBuylogUidsNextWeek($odate){
        $cur = $this->lastNWeek(strtotime($odate), 1);
        $year = mb_substr($cur[0], 0, 4, 'utf-8');
        $num =str_pad(intval(date('W',strtotime($cur[0]))),2,"0",STR_PAD_LEFT);
        $startime = strtotime($cur[0]);
        $endtime = strtotime($cur[1])+86400;
        $sql = "SELECT DISTINCT uid FROM cmibank_log.cmibank_buy_log_".$year."_".$num."";
        $aa = $this->executeSql($sql);
        $bb = array();
        foreach ($aa as $val){
            $bb[] = $val['uid'];
        }
        return implode(',',$bb);
    }
    //获取那一周
    function lastNWeek($ts, $n, $format = '%Y-%m-%d') {
        $ts = intval($ts);
        $n  = abs(intval($n));

        // 周一到周日分别为1-7
        $dayOfWeek = date('w', $ts);
        if (0 == $dayOfWeek)
        {
            $dayOfWeek = 7;
        }
        $lastNMonday = 7 * $n + $dayOfWeek - 1;
        $lastNSunday = 7 * ($n - 1) + $dayOfWeek;
        return array(
            strftime($format, strtotime("-{$lastNMonday} day", $ts)),
            strftime($format, strtotime("-{$lastNSunday} day", $ts))
        );
    }
    
}