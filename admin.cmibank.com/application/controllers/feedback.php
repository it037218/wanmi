<?php
/**
 * aboutus管理
 * * */
class feedback extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '意见反馈'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_feedback_model', 'feedback');
    }
    
    public function index(){
        $flag = $this->op->checkUserAuthority('意见反馈',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'意见反馈');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            if (!$orderby) {
                $orderby = 'ctime';
            }
            if (!$asc) {
                $asc = 'ASC';
            }
            $data['list'] = $this->feedback->getFeedbackList('','ctime desc',array($psize, $offset));
            $count = count($this->feedback->getFeedbackList('','',''));
            $data['orderby'] = $orderby;
            
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'aboutus/index?page=' . $page;
                if(!empty($aboutustitle)){
                    $data['rel'] .= '&title=' . $aboutustitle;
                }
            }else{
                $data['list'] = $data['page'] = '';
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1071');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '意见反馈', '', '意见反馈', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/feedback/v_index', $data);
        }   
    }
    
    public function Handle($id=''){
        $data = array('status'=>'1');
        $this->feedback->updateFeedback($id, $data);
        $log = $this->op->actionData($this->getSession('name'), '意见反馈', '', '处理意见', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '处理成功', array(), '处理意见 ', 'forward',OP_DOMAIN.'/feedback'));
    }
}