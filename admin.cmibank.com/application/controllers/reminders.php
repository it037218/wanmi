<?php

class reminders extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '打款到银行卡') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->config->load('cfg/fuiou_config', true, true);
        $this->config->load('cfg/banklist', true, true);
        $this->config->load('cfg/reminder_banklist', true, true);
        $this->fuiou_config = $config =  $this->config->item('cfg/fuiou_config');
        $this->banklist = $config =  $this->config->item('cfg/banklist');
        $this->reminder_banklist = $config =  $this->config->item('cfg/reminder_banklist');
        //$this->notify_url = $config['pay_notify_url'];

        $this->load->model('admin_fuioupay_model', 'fuioupay_model');
    }

    /**
     * 打款记录
     */
    public function index(){
        $flag = $this->op->checkUserAuthority('打款到银行卡',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'打款到银行卡');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $aboutustitle = trim($this->input->post('aboutustitle'));
            if($aboutustitle && $aboutustitle != '请输入搜索订单号' && $this->input->request('op') == "search_aboutustitle"){
                $data['list'] = $this->fuioupay_model->getfuiouOutList(trim($aboutustitle),'','');
                $count = 1;
            }else{
                $fuiou_out = $this->fuioupay_model->getfuiouOutList('','created_time desc',array($psize, $offset));
                $data['list'] = $fuiou_out;
                $count = count($this->fuioupay_model->getfuiouOutList('','',''));
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'reminders/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['pageNum']    = 0;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
        }
        $this->load->view('/reminders/v_index',$data);
    }

    /**
     * 打款到银行卡
     */
    public function remit(){
        $flag = $this->op->checkUserAuthority('打款到银行卡', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '编辑打款');
        }else {
            if ($this->input->request('op') == 'add') {
                $post_data = array(
                    'orderid' => $this->fuioupay_model->getOrder(5),
                    'bankno' => $this->input->post('bank_no'),
                    'cityno' => $this->input->post('city'),
                    'account_no' => $this->input->post('account_id'),
                    'accntnm' => $this->input->post('id_name'),
                    'cost_money' => $this->input->post('cost_money'),
                    'show_status' => 0,
                );
                $return_data = $this->fuioupay_model->withdraw($this->getSession('uid'),$this->getSession('name'),$post_data);
                $this->fuioupay_model->addlog($this->getSession('uid'),$this->getSession('name'),$return_data['ret'],$return_data['memo'],$post_data,time());

                $this->op->actionData($this->getSession('name'), '打款到银行卡', '', '打款到银行卡', $this->getIP(), $this->getSession('uid'));
                if($return_data['ret'] == '000000'){
                    exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '打款成功', array(), '打款到银行卡','forward', OP_DOMAIN.'/reminders/index'));
                }else{
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>"{$return_data['memo']}")));
                }
            }else {
                $data['banklist'] = array_merge($this->banklist, $this->reminder_banklist);
                $data['province'] = $this->getProvince();
                $this->load->view('/reminders/remit',$data);

            }
        }
    }

    /**
     * 删除打款记录
     */
    public function del(){
        $flag = $this->op->checkUserAuthority('打款到银行卡',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'打款记录');
        }else{
            $id = $this->uri->segment(3);
            //$this->fuioupay_model->delFuiouOutCacheByid($id);
            $ret = $this->fuioupay_model->delFuiouOut($id);
            if($ret){
                $log = $this->op->actionData($this->getSession('name'), '打款记录', '', '删除打款记录', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除打款记录', 'forward', OP_DOMAIN.'/reminders'));
            }
        }
    }

    /**
     * 获取省
     * @return mixed
     */
    public function getProvince(){
        return $this->fuioupay_model->returnProvince();
    }

    /**
     * 查询市
     * @param string $province_code
     */
    public function getCity($province_code = ''){
        $data = $this->fuioupay_model->returnCity($province_code);
        $rtn = array();
        foreach ($data as $key => $val){
            $rtn[$key][0] = $val['city_id'];
            $rtn[$key][1] = $val['city_name'];
        }
        echo json_encode($rtn);
        exit;
    }
}