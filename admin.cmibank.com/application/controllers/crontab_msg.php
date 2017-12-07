<?php
/**
 *还款短信提醒
 */
class crontab_msg extends Controller{
    
    public function __construct(){
        parent::__construct();
        include(APPPATH . 'libraries/submail.lib.php');
        //$this->load->model('admin_crontab_model', 'crontab');
    }
    
    private function _sendmsg($phone, $code,$msg_tpl_num){
        $submail = new submail();
        $rtn = $submail->send_msg($phone,$code,$msg_tpl_num);
        return $rtn;
    }
    
    public function send_msg(){
        $this->load->model('admin_product_model', 'product');
        $product = $this->product->getTodayRepayment();
        $count = 0;
        foreach ($product as $_product){
            if($_product['status'] != 6 || !in_array($_product['repayment_status'], array(1,2))){
                $count++;
            }
        }
        echo $count;
        if($count > 0){
            $this->_sendmsg('17612159262', date('Y-m-d H:i:s'), 'tpPUZ4');
            $this->_sendmsg('13301920950', date('Y-m-d H:i:s'), 'tpPUZ4');
            $this->_sendmsg('15921788018', date('Y-m-d H:i:s'), 'tpPUZ4');
            $this->_sendmsg('15026511179', date('Y-m-d H:i:s'), 'tpPUZ4');
        }
    }
    
    public function send_noback_msg(){
        $this->load->model('admin_product_backmoney_model','product_backmoney');
        $where =array('cietime'=>date('Y-m-d'),'status'=>array(0,1,2));
        $count = count($this->product_backmoney->getProductBackmoneyList($where,'',''));
        echo $count;
        if($count > 0){
            $this->_sendmsg('17612159262', date('Y-m-d H:i:s'), 'udg61');
            $this->_sendmsg('13301920950', date('Y-m-d H:i:s'), 'udg61');
            $this->_sendmsg('15921788018', date('Y-m-d H:i:s'), 'udg61');
            $this->_sendmsg('15026511179', date('Y-m-d H:i:s'), 'udg61');
        }
    }
    
    
    
}
