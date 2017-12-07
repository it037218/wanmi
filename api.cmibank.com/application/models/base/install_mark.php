<?php

require_once 'basemodel.php';
class install_mark extends Basemodel {

    public $_table = 'cmibank.cmibank_install_check';

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param string $device_num
     * @param string $device_type
     * @return mixed
     */
    public function getOne($device_num = '', $device_type = 'Android'){
        $where = array('device_num' => $device_num, 'device_type' => $device_type);
        return $this->selectDataSql($this->_table, $where, 'created_time desc');
    }

    /**
     * @param string $device_num
     * @param string $q_name
     * @param string $device_type
     * @return mixed
     */
    public function getOneMark($device_num = '', $q_name = '', $device_type = 'Android'){
        $where = array('device_num' => $device_num, 'q_name' => $q_name, 'device_type' => $device_type);
        return $this->selectDataSql($this->_table, $where);
    }

    /**
     * 渠道的安装次数
     * @param string $times
     * @param string $device_num
     * @param string $q_name
     * @param string $device_type
     * @return mixed
     */
    public function addTimes($times = '',$device_num = '', $q_name = '', $device_type = 'Android'){
        //$old_times = $this->getOne($device_num, $q_name);
        $new_times = $times + 1;
        $where = array('device_num' => $device_num, 'device_type' => $device_type, 'q_name' => $q_name);
        return $this->updateDataSql($this->_table,array('times' => $new_times, 'created_time' => time()), $where);
    }

    /**
     * 添加设备安装记录
     * @param string $device_num
     * @param string $device_type
     * @param string $q_name
     * @return bool
     */
    public function addDeviceInfo($device_num = '', $device_type = 'Android', $q_name = ''){
        $data = array(
            'device_num' => $device_num,
            'device_type' => $device_type,
            'q_name' => $q_name,
            'times' => 1,
            'created_time' => time(),
        );
        return $this->insertDataSql($data, $this->_table);
    }
}

   
