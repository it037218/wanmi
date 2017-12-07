<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//error_reporting(0);
/**
 * app启动获取版本信息，查看是否需要更新。
 * Class version_check
 */
class plat_mark extends Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('base/install_mark', 'install_mark');
    }

    public function install_mark(){
        $device_num = $this->input->get_post('device_num');
        $device_type = $this->input->get_post('device_type');
        $q_name = $this->input->get_post('qudao');

        //如果上次安装的渠道和本次相同，返回渠道为空。安装次数+1
        $device_one = $this->install_mark->getOne($device_num, $device_type);
        $device_mark = $this->install_mark->getOneMark($device_num, $q_name, $device_type);

        if (!$device_mark) {
            $device_mark['q_name'] = '';
            $device_mark['times'] = '';
        }
        if ($device_one){
            if (floor((time()-$device_one['created_time'])%86400/60) < 10){
                $data = array('msg' => '本次安装和上次安装时间小于10分钟', 'qudao' => '');
                $response = array('error'=> 0, 'data'=> $data);
                $this->out_print($response);
            }
        }

        if ($device_one) {
            if ($device_one['q_name'] == 'cmibank' && $q_name != 'cmibank'){
                $this->check($device_mark['q_name'], $q_name, $device_mark['times'],$device_num, $device_type);
                $data = array('msg' => '上次安装的渠道是cmibank,矫正渠道', 'qudao' => '');
                $response = array('error' => 0, 'data' => $data);
                $this->out_print($response);
            }

            if ($device_one['q_name'] != $q_name) {
                $this->check($device_mark['q_name'], $q_name, $device_mark['times'],$device_num, $device_type);
                $data = array('msg' => '本次安装和上次渠道不同。渠道取上次渠道', 'qudao' => $device_one['q_name']);
                $response = array('error' => 0, 'data' => $data);
                $this->out_print($response);
            }

            if ($device_one['q_name'] == $q_name) {
                $this->check($device_mark['q_name'], $q_name, $device_mark['times'],$device_num, $device_type);
                $data = array('msg' => '本次安装渠道和上次相同,渠道不变', 'qudao' => '');
                $response = array('error' => 0, 'data' => $data);
                $this->out_print($response);
            }
        }else{
            $this->addMark($device_num, $device_type, $q_name);
            $data = array('msg' => '最新安装。', 'qudao' => '');
            $response = array('error' => 0, 'data' => $data);
            $this->out_print($response);
        }
    }

    /**
     * 判断数据库中是否有记录，并作相应处理。
     * @param string $device_qudao
     * @param string $q_name
     * @param string $times
     * @param string $device_num
     * @param string $device_type
     * @return bool
     */
    public function check($device_qudao = '', $q_name = '',$times = '',$device_num ='', $device_type = ''){
        if ($device_qudao == $q_name) {
            $this->addTimes($times, $device_num, $q_name, $device_type);
        }else{
            $this->addMark($device_num, $device_type, $q_name);
        }
        return true;
    }
    /**
     * 数据库修改次数
     * @param string $times
     * @param string $device_num
     * @param string $q_name
     * @param string $device_type
     * @return mixed
     */
    public function addTimes($times = '', $device_num = '', $q_name = '', $device_type = 'Android'){
        return $this->install_mark->addTimes($times, $device_num, $q_name, $device_type);
    }

    /**
     * 数据库添加记录
     * @param string $device_num
     * @param string $device_type
     * @param string $q_name
     * @return mixed
     */
    public function addMark($device_num = '', $device_type = '', $q_name = ''){
        return $this->install_mark->addDeviceInfo($device_num, $device_type, $q_name);
    }
}