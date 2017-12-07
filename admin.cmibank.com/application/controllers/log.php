<?php

class Log extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '系统管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
    }

    /**
     * 操作记录
     */
    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('操作记录', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            $data = $this->getDefaultData($flag, array('系统管理', '操作记录'));
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));            
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $asc = htmlspecialchars($this->input->request('asc'));
            $username = htmlspecialchars($this->input->request('search_keyword'));
            if (!$orderby) {
                $orderby = 'do_time';
            }
            if (!$asc) {
                $asc = 'DESC';
            }
            $offset = ($page - 1) * $psize;
            
            if($username && $username != '请输入搜索内容'){
                $where = array('username' => $username);
                $whereCount = '`username` = "' . $username . '"';
                $data['title'] = $username;
            }else{
                $where = '';
                $whereCount = '';
            }
            $data['list'] = $this->op->getLoglist($where, $orderby . ' ' . $asc, array($psize, $offset));
           
            $edatable = $this->op->getEditable($this->getSession('uid'),'1004');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            
            $count = $this->op->getLoglist_count($whereCount);
            if(empty($data['list'])){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'暂无数据')));
            }
            
            if ($count > 0) {
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'log/index?page=' . $page . '&orderby=' . $orderby . '&asc=' . $asc;
                
            } else {
                $data['list'] = $data['page'] = '';
            }
            $data['orderby'] = $orderby;
            $data['asc'] = $asc;
            $this->load->view('/log/v_index', $data);
        }
    }

    
    
    public function delete() {
        $flag = $this->op->checkUserAuthority('操作记录', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            $data = $this->getDefaultData($flag, array('系统管理', '操作记录'));
            $stime= $this->input->post('stime');
            if($stime){
                $stime = strtotime($stime);
                if(!$stime){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'日期错误')));
                }
                $where = "do_time<".$stime;
                
                $list = $this->op->deleteLogBy($where);
                if($list){                    
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'操作成功','forwardUrl'=>OP_DOMAIN.'/log','callbackType'=>'forward','navTabId'=>'navTab')));
                }  else {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'操作失败')));
                }
            }else{
                $this->load->view('/log/v_delete', $data);
            }
            
        }
    }
}