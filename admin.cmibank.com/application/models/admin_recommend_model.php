<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_recommend_model extends Basemodel {

    private $_table = 'cmibank.cmibank_recommend';
    
    public function __construct() {
        parent::__construct();
    }

    public function addCompetitive($pid){
        $key = _KEY_REDIS_COMPETITIVE_PREFIX_;
        return self::$container['redis_default']->save($key,$pid, 0);
    }
    
    public function exists($pid){
        return self::$container['redis_default']->exists($pid);
    
    }
}
