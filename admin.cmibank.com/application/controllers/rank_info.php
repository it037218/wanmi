<?php

class rank_info extends Controller{

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '用户管理'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_rank_model', 'rank');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('购买排名',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'购买排名');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $name_post = trim($this->input->post('name_post'));
            $sort_post = trim($this->input->post('sort'));
            if (empty($name_post)) {
                $name_post = 'all_money';
            }
            if (empty($sort_post)) {
                $sort_post = 'asc';
            }
            $start_time = trim($this->input->post('start_time'));
            $end_time = trim($this->input->post('end_time'));
            $phone= trim($this->input->post('phone'));
            $select_plat = trim($this->input->post('plat'));
            $re_number = trim($this->input->post('re_number'));
            $re_money = trim($this->input->post('re_money'));

            $all = $this->rank->get_all_rank_list($start_time,$end_time,$phone,$select_plat);
            //排序
            if (!empty($re_number) || !empty($re_money)){
                $all = $this->rank->rebuy_condition($all,$re_number,$re_money);
            }
            if ($all) {
                $slice_all = $this->rank->sort($all, $name_post, $sort_post);
                $data['list'] = array_slice($slice_all, ($page - 1) * $psize, $psize, true);
            }
            $count = count($all);

            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'rank_info/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
                $data['pageNum'] = $data['numPerPage'] = $data['count'] = 0;
            }
            $data['plat'] = $this->rank->count_plat();
            $data['phone'] = $phone;
            $data['back_plat'] = $select_plat;
            $data['name_post'] = $name_post;
            $data['sort'] = $sort_post;
            $data['start_time'] = $start_time;
            $data['end_time'] = $end_time;
            $data['re_number'] = $re_number;
            $data['re_money'] = $re_money;
            $log = $this->op->actionData($this->getSession('name'), '购买排名', '', '用户管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/rank_info/v_index', $data);
        }
    }

    public function detail(){
        $uid = $this->input->request('uid');
        $start_time = $this->input->request('start_time');
        $end_time = $this->input->request('end_time');
        $phone = $this->input->request('phone');
        $plat = $this->input->request('plat');
        $page = max(1, intval($this->input->request('pageNum')));
        $psize = max(20, intval($this->input->request('numPerPage')));
        $offset = ($page - 1) * $psize;

        if (empty($uid)) exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => 'uid不能为空!')));
        if ($start_time && $end_time || !empty($phone) || !empty($plat)){
            $data['list'] = $this->rank->user_re_buy_info($uid,$start_time,$end_time,$phone,$plat,array($psize,$offset));
            $count = $this->rank->get_buy_product_count($uid,$start_time,$end_time,$phone,$plat);
        }else{
            $data['list'] = $this->rank->user_re_buy_info($uid,'','','','',array($psize,$offset));
            $count = $this->rank->get_buy_product_count($uid);
        }

        if($count>0){
            $data['pageNum']    = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $data['rel'] = OP_DOMAIN . 'rank_info/detail?&'.$uid.'&page='.$page;
        }else{
            $data['list'] = $data['page'] = '';
            $data['pageNum'] = $data['numPerPage'] = $data['count'] = 0;
        }
        $data['uid'] = isset($uid) ? $uid : '';
        $data['start_time'] = isset($start_time) ? $start_time : '';
        $data['end_time'] = isset($end_time) ? $end_time : '';
        $data['phone'] = isset($phone) ? $phone : '';
        $data['plat'] = isset($plat) ? $plat : '';
        //$log = $this->op->actionData($this->getSession('name'), '购买排名', '', '用户管理', $this->getIP(), $this->getSession('uid'));
        if ($data) {
            $this->load->view('/rank_info/detail', $data);
        } else {
            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '查询失败')));
        }
    }
}