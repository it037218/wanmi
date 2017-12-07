<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * 购买产品
 */
class activity_rank extends Controller {

    public function __construct()
    {
        parent::__construct();
    }
    
    public function test(){
        $this->load->model('base/activity_base', 'activity_base');
        $data = $this->activity_base->get_activity_rank_with_actid(2, 0, -1);
        $rtn = array();
        var_export($data);
//         $data = $this->activity_base->get_activity_weekrank_with_actid(2, '', 0, -1);
//         $rtn = array();
//         print_r($data);
        exit;
    }
    
//     public function getRank111(){
//         $this->load->model('base/activity_base', 'activity_base');
//         $data = $this->activity_base->get_activity_rank_with_actid(2, 0, 29);
//         print_r($data);
//     }
    
    public function getRank(){
        $this->load->model('base/activity_base', 'activity_base');
        $data = $this->activity_base->get_activity_rank_with_actid(2, 0, 29);
        $rtn = array();
        foreach($data as $phone => $interge){
            $_phone = substr($phone, 0, 3) . '****' . substr($phone, -4);
            $rtn[$_phone] = floor($interge);
        }
        echo 'callback('. json_encode($rtn) . ")";
    }
    
    //活动后期 本周变上周
    public function getlastWeekRank(){
        $this->load->model('base/activity_base', 'activity_base');
        $data = $this->activity_base->get_activity_weekrank_with_actid(2, date('W',strtotime('-1 week')), 0, 9);
//         $rtn = array();
//         foreach($data as $phone => $interge){
//             $_phone = substr($phone, 0, 3) . '****' . substr($phone, -4);
//             $rtn[$_phone] = floor($interge);
//         }
        print_r($data);
    }
    
    //活动后期 本周变上周
    public function getWeekRank(){
        $this->load->model('base/activity_base', 'activity_base');
        $data = $this->activity_base->get_activity_weekrank_with_actid(2, '', 0, 9);
        $rtn = array();
        foreach($data as $phone => $interge){
            $_phone = substr($phone, 0, 3) . '****' . substr($phone, -4);
            $rtn[$_phone] = floor($interge);
        }
        echo 'callback('. json_encode($rtn) . ")";
    }
    
    public function getMyRank(){
        $uid = trim($this->input->request('uid'));
        $this->load->model('logic/login_logic', 'login_logic');
        $account = $this->login_logic->getAccountInfo($uid);
        $phone = $account['account'];
        $this->load->model('base/activity_base', 'activity_base');
        $info_data = $this->activity_base->get_activity_rank_with_actid_phone(2, $phone);
        $this->load->model('base/activity_base', 'activity_base');
        $rank = $this->activity_base->get_rank(2, $phone);
        $rtn = array();
        $rtn['score'] = $info_data ? $info_data : 0;
        if($info_data === false){
            $rtn['rank'] = 99999;
        }else{
            $rtn['rank'] = $rank + 1;
        }
        $return = array('rank' => $rtn);
        $info_data = $this->activity_base->get_activity_weekrank_with_actid_phone(2, $phone);
        $this->load->model('base/activity_base', 'activity_base');
        $rank = $this->activity_base->get_weekrank(2, $phone);
        $weekrtn = array();
        $weekrtn['score'] = $info_data ? $info_data : 0;
        if($info_data === false){
            $weekrtn['rank'] = 99999;
        }else{
            $weekrtn['rank'] = $rank + 1;
        }
        $return['weekrank'] = $weekrtn;
        echo 'callback('. json_encode($return) . ")";
    }
    
//     public function getMyRank(){
//         $uid = trim($this->input->request('uid'));
//         $this->load->model('logic/login_logic', 'login_logic');
//         $account = $this->login_logic->getAccountInfo($uid);
//         $phone = $account['account'];
//         $this->load->model('base/activity_base', 'activity_base');
//         $info_data = $this->activity_base->get_activity_rank_with_actid_phone(2, $phone);
//         $this->load->model('base/activity_base', 'activity_base');
//         $rank = $this->activity_base->get_rank(2, $phone);
//         $rtn = array();
//         $rtn['score'] = $info_data ? $info_data : 0;
//         if($info_data === false){
//             $rtn['rank'] = 99999;
//         }else{
//             $rtn['rank'] = $rank + 1;
//         }
//         $return = array('rank' => $rtn);
// //         $info_data = $this->activity_base->get_activity_weekrank_with_actid_phone(2, $phone);
// //         $this->load->model('base/activity_base', 'activity_base');
// //         $rank = $this->activity_base->get_weekrank(2, $phone);
// //         $weekrtn = array();
// //         $weekrtn['score'] = $info_data ? $info_data : 0;
// //         if($info_data === false){
// //             $weekrtn['rank'] = 99999;
// //         }else{
// //             $weekrtn['rank'] = $rank + 1;
// //         }
// //         $return['weekrank'] = $weekrtn;
//         echo 'callback('. json_encode($return) . ")";
//     }

    public function active_first(){
        $type = $this->input->request('type');
        $this->config->load('cfg/festivity_cfg', true, true);
        $festivity = $this->config->item('cfg/festivity_cfg');
        //if(strtotime($festivity['start_time']) <= NOW && strtotime($festivity['end_time']) >= NOW){
            $this->load->model('base/activity_base', 'activity_base');
            $rank['list'] = $this->activity_base->get_active_Rank($festivity,$type);
            if (!empty($type) && $type == '1' || $type == '2'){
                $this->load->view('active_1128_all', $rank);
            }else{
                $this->load->view('active_1128', $rank);
            }
        //}
    }
}

/* End of file test.php */
/* Location: ./application/controllers/test.php */