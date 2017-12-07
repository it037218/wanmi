<?php

class activity_management extends Controller
{

    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '活动管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_activity_model', 'activity');
    }

    public function index(){
        $flag = $this->op->checkUserAuthority('活动管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '活动管理');
        }else {
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $act_value = $this->input->request('act_value');
            $insert_into = $this->input->request('search_into');
            $start_time = $this->input->request('start_time');
            $end_time = $this->input->request('end_time');
            $this->config->load('cfg/festivity_cfg', true, true);
            $festivity_cfg = $this->config->item('cfg/festivity_cfg');

            switch ($act_value){
                case '0':
                    $act_name = '';
                    break;
                case '1':
                    $act_name = '月庆活动排行';
                    if ($insert_into == 1) {
                        if (!empty($start_time) && !empty($end_time)) {
                            $this->activity->result_array(strtotime($start_time), strtotime($end_time), $festivity_cfg);
                        } else {
                            exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '时间不能为空!')));
                        }
                    }
                    break;
                case '2':
                    $act_name = '单笔投资奖励';
                    break;
                case '3':
                    $act_name = '累计投资奖励';
                    break;
                case '4':
                    $act_name = '夺标之王奖励';
                    break;
                default:
                    $act_name = '';
            }
            $data['list'] = $this->activity->get_db_activity_result($act_name, array($psize, $offset));
            $count = $this->activity->get_db_count($act_name);
            if ($count > 0) {
                $data['pageNum'] = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'activity_management/index?page=' . $page;
            } else {
                $data['list'] = $data['page'] = '';
                $data['pageNum'] = $data['numPerPage'] = $data['count'] = 0;
            }
            $data['op_value'] = $act_value ? $act_value : 0;
//          $log = $this->op->actionData($this->getSession('name'), '活动管理', '', '活动管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/activity/v_1128index', $data);

        }
    }

    /**
     * 审核发布
     */
    public function reviewed(){
        $flag = $this->op->checkUserAuthority('活动管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '活动管理');
        }else {
            $id = $this->input->request('id');
            $status = $this->input->request('status');
            $ids = $this->input->request('check_all');
            if (empty($id) && empty($ids)) exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '数据不存在!')));
            $condition = $ids ? $ids : $id;
            $result = $this->activity->batch_audit($condition, $status);
            if ($result) {
                exit(json_encode(array('statusCode' => self::AJ_RET_SUCC, 'message' => '审核成功')));
            } else {
                exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '审核失败')));
            }
        }
    }

    /**
     * 更新
     */
    public function edit(){
        $flag = $this->op->checkUserAuthority('活动管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '活动管理');
        }else {
            $id = $this->input->request('id');
            if (empty($id)) exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '数据不存在!')));
            if ($this->input->request('op') == 'saveedit') {
                $data = $this->input->post();
                $result = $this->activity->edit_luck_result($data);
                if ($result) {
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '更新成功', array(), '更新成功', 'forward', OP_DOMAIN . '/activity_management/index'));
                } else {
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '修改信息失败')));
                }
            } else {
                if ($id < 0 || !is_numeric($id)) {
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '缺少必要的参数')));
                }
                $data['detail'] = $this->activity->get_db_one($id);
                $this->load->view('/activity/v_edit', $data);
            }
        }
    }

    /**
     * 删除
     */
    public function del(){
        $flag = $this->op->checkUserAuthority('活动管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '活动管理');
        }else {
            $id = $this->input->request('id');
            $ids = $this->input->request('check_all');
            if (empty($id) && empty($ids)) exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message' => '数据不存在!')));
            $condition = $ids ? $ids : $id;
            $result = $this->activity->del_luck_result($condition);
            if ($result) {
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '删除成功', array(), '删除成功', 'forward', OP_DOMAIN . '/activity_management/index'));
            }
        }
    }
}