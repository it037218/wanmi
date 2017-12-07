<?php
class crontab_logic extends CI_Model {

    function __construct() {
        # 继承CI 父类
        parent::__construct();
        $this->load->model('logic/product_logic' , 'product_logic');
        $this->load->model('base/contract_base' , 'contract_base');
    }

    public function setProductDown($pid){
        $product = $this->product_logic->getProductDetail($pid);
        if($product){
            //更改product状态
            $this->product_logic->setProductDownline($product['ptid'], $pid);
        }else{
            return false;
        }
        return true;
    }
    

    
    
}


   
