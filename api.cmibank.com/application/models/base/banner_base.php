<?php

require_once 'basemodel.php'; 

class banner_base extends Basemodel{

    public $_table = 'cmibank.cmibank_banner';
    
    public function getBannerList(){
        $setName = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX;
        $data = self::$container['redis_app_r']->hashGet($setName , array(), 2);
        $rtn = array();
        foreach ($data as $location => $_data){
            $_data = json_decode($_data, true);
            if(strtotime($_data['startime']) > NOW){
                continue;
            }
            
            if(strtotime($_data['endtime']) < NOW){
                $this->delBannerByLocation($location);
                continue;
            }
            $rtn[$location] = $_data;
        }
        ksort($rtn);
        return $rtn;
    }
    
    private function delBannerByLocation($location){
        $setName = _KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX;
        return self::$container['redis_app_w']->hashDel($setName ,$location);
    }
    

    
}
