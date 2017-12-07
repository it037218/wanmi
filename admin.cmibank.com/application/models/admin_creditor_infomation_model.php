<?php
//require_once ROOTPATH . DS . APPPATH. 'models/base/basemodel.php';
require_once APPPATH. 'models/base/basemodel.php';

class admin_creditor_infomation_model extends Basemodel
{

    private $_table = 'cmibank.cmibank_creditor_information';

    public function __construct()
    {
        parent::__construct();
    }

    public function getInformationByCorporationid($corporationid){
        return $this->selectDataListSql($this->_table, array('corporationid' => $corporationid));

    }
    public function getInformationByid($id){
        return $this->selectDataSql($this->_table, array('id' => $id));
    }
    //新增债权人
    public function insertCreditor($corporationid,$data){
        //
        foreach($data as $key=> $value){
            $value['corporationid'] = $corporationid;
            $this->insertDataSql( $value,$this->_table);
        }

    }
    //根据corporationid删除债权人修改，
    public function delete($corporationid){
        //
       return $this->deleteDataSql($this->_table, array('corporationid' => $corporationid));
    }
    //更新
    public function updateCreditor($corporationid,$data){
       foreach($data as $key=> $value){
           if($value['id']==0){
               unset($value['id']);
               $value['corporationid']=$corporationid;
               $this->insertDataSql( $value,$this->_table);
           }else{
               $this->updateDataSql($this->_table, $value,array('id'=>$value['id']));
           }
       }

    }
    //获取列表
    public function creditorList(){
        return $this->selectDataListSql($this->_table);
    }
}