<?php
/**
 * 公告管理
 * * */
class notice extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '公告管理'){
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_notice_model', 'notice');
    }
    
    public function index(){
        $flag = $this->op->checkUserAuthority('公告管理',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'公告管理');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $aboutustitle = trim($this->input->post('aboutustitle'));
            if($aboutustitle && $aboutustitle != '请输入搜索内容' && $this->input->request('op') == "search_aboutustitle"){
                
            }else{
                $notice = $this->notice->getNoticeList('','ctime desc',array($psize, $offset));
                $data['list'] = $notice;
                $count = count($this->notice->getNoticeList('','',''));
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'notice/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1061');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
        }
       $this->load->view('/notice/v_index',$data);
    }
    public function delNotice(){
        $flag = $this->op->checkUserAuthority('公告管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'公告管理');
        }else{
            $nid = $this->uri->segment(3);
            $this->notice->delNoticeCacheBynid($nid);
            $ret = $this->notice->delNotice($nid);
            if($ret){
                $log = $this->op->actionData($this->getSession('name'), '公告管理', '', '删除公告管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除公告管理', 'forward', OP_DOMAIN.'/notice'));
            }
        }
    }
    public function editNotice(){
       $flag = $this->op->checkUserAuthority('公告管理',$this->getSession('uid'));
       if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'公告管理');
       }else{
           if($this->input->request('op') == 'editNotice'){
               $title = trim($this->input->post('title'));
               $content = trim($this->input->post('content'));
               $type = trim($this->input->post('type'));
               $phonetype = trim($this->input->post('phonetype'));
               $yugaotime = trim($this->input->post('yugaotime'));
               $nid = trim($this->input->post('nid'));
                
               $data['title'] = $title;
               $data['content'] = $content;
               $data['type'] = $type;
               $data['phonetype'] = $phonetype;
               $data['yugaotime'] = $yugaotime;
               $this->notice->delNoticeCacheBynid($nid);
               $ret = $this->notice->updateNotice($nid,$data);
               
               if(!$ret){
                   exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加公告失败')));
               }
               $this->notice->_flushNoticeDetailRedisCache($nid);
               $log = $this->op->actionData($this->getSession('name'), '公告管理', '', '公告管理', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加公告成功', array(), '公告管理','no', OP_DOMAIN.'/notice'));
           }else{
               $nid = $this->uri->segment(3);
               $notice = $this->notice->getNoticeBynid($nid);
               $data['detail'] = $notice;
               $this->load->view('/notice/v_editNotice', $data);
           }  
       }
    }
    public function addNotice(){
        $flag = $this->op->checkUserAuthority('公告管理',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'公告管理');
        }else{
            if($this->input->request('op') == 'addNotice'){
                $title = trim($this->input->post('title'));
                $content = trim($this->input->post('content'));
                $yugaotime = trim($this->input->post('yugaotime'));
                $status = trim($this->input->post('status'));
                $type = trim($this->input->post('type'));
                $phonetype = trim($this->input->post('phonetype'));
                
                $data['title'] = $title;
                $data['content'] = $content;
                $data['yugaotime'] = $yugaotime;
                $data['status'] = $status;
                $data['type'] = $type;
                $data['phonetype'] = $phonetype;
                $data['ctime'] = time();
                
                $ret = $this->notice->addNotice($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加公告失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '公告管理', '', '公告管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加公告成功', array(), '公告管理','forward', OP_DOMAIN.'/notice'));
            }
            $this->load->view('/notice/v_addnotice',$data);
        }
    }
    public function uptoline(){
       $flag=$this->op->checkUserAuthority('公告管理',$this->getSession('uid'));
       if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'公告管理');
       }else{
           $nid = $this->uri->segment(3);
           $data['onlinetime'] = time();
           $data['status'] = 1;
           $ret = $this->notice->updateNotice($nid, $data);
           if(!$ret){
               exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'更新上线时间失败')));
           }
           $notice = $this->notice->getNoticeBynid($nid);
           $this->notice->addNoticeCache($notice);
           exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布', 'forward', OP_DOMAIN.'/notice'));
       }     
    }
}