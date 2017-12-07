<?php
/**
 *  每日--对比
 * * */
class duibi extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '每日对比误差') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_qs_log_model','qs_log');
       
    }


    public function index(){
        $flag = $this->op->checkUserAuthority('每日对比误差',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'每日汇总');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $offset = ($page - 1) * $psize;
            $list = $this->qs_log->getList(null ,'odate desc', array($psize, $offset));
            $count = $this->qs_log->getCount();
            $data = array();
            $odate_list = array();
            
            foreach ($list as $_list){
                $odate_list[] = $_list['odate'];
            }
            $odate = max($odate_list);
            $sum_list = $this->qs_log->getSumWithOdate($odate);
            $data['sum_list'] = $sum_list[0];
            $data['list'] = $list;
            $data['pageNum'] = $page;
            $data['numPerPage'] = $psize;
            $data['count'] = $count;
            $log = $this->op->actionData($this->getSession('name'), '每日对比误差', '', '查看', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/duibi/v_index', $data);
        }
    }
    
    
    
}