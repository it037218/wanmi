<?php

require_once 'basemodel.php'; 

class error_log_base extends Basemodel{

    private $_table = 'cmibank_log.cmibank_error_log';
    
    
    public function addLog($msg, $error_data){
        $data = array();
        $data['uid'] = $error_data['uid'];
        $data['errorcode'] = $msg['error'];
        $data['msg'] = $msg['msg'];
        $data['data'] = json_encode($error_data);
//         print_r($data);
        $table = $this->getPayTableIndex();
        return $this->insertDataSql($data, $table);
    }
    
    public function getPayTableIndex(){
        $data_y =  date("Y");
        $data_w =  date("W");
        if($data_w == 52){
            $data_y = '2016';
        }
        return $this->_table . '_' . $data_y . '_' . $data_w;
    }
    
}
