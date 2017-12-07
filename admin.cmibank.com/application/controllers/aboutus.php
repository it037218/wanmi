<?php
/**
 * aboutus管理
 * * */
class aboutus extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach($this->menu AS $key=>$value){
                if($value['name'] == '关于我们'){
                   $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_aboutus_model', 'aboutus');
    }
    public function index(){
        $flag = $this->op->checkUserAuthority('关于公司信息管理',$this->getSession('uid'));
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $aboutustitle = trim($this->input->post('aboutustitle'));
            if($aboutustitle && $aboutustitle != '请输入搜索内容' && $this->input->request('op') == "search_aboutustitle"){
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $count=count($this->aboutus->getAboutusListByLiketitle($aboutustitle));
                $data['list'] = $this->aboutus->getAboutusListByLiketitle($aboutustitle,array($psize, $offset));
                $data['aboutustitle'] = $aboutustitle;
                if(empty($data['list'])){

                      exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
            }else{
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }                
                $data['list'] = $this->aboutus->getAboutusList('', 'ctime desc', array($psize, $offset));
                $count = $this->aboutus->getAboutusCount();
            }
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
                $data['pageNum'] = $data['numPerPage'] = $data['count'] = 0;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1050');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '关于我们', '', '关于公司信息管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/aboutus/v_index', $data);
        }
        
     
    }
    
    public function addAboutus(){
        $flag = $this->op->checkUserAuthority('关于公司信息管理',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
             if($this->input->request('op') == 'addaboutus'){
                 
                $data['title'] = trim($this->input->post('title'));
                $data['content'] = trim($this->input->post('content'));
                $data['ctime'] = time();
                
                $ret = $this->aboutus->addAboutus($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加aboutus失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '关于我们', '', '关于公司信息管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加aboutus成功', array(), '关于公司信息管理','forward', OP_DOMAIN.'/aboutus'));
            }else{
                $this->load->view('/aboutus/v_addAboutUs',$data);
            }
            
        }
        
    }
    public function editAboutus(){
        $flag = $this->op->checkUserAuthority('关于公司信息管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'关于公司信息管理');
        }else{
            if($this->input->request('op') == 'saveedit'){
                $aid = trim($this->input->post('aid'));
                if(!$aid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $title = trim($this->input->post('title'));
                $content = trim($this->input->post('content'));

                $data['title'] = $title;
                $data['content'] = $content;
                    
                $ret = $this->aboutus->editAboutus($aid,$data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '广告列表', '', '修改关于我们列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改关于我们列表 ', 'forward', OP_DOMAIN.'/aboutus'));
            }else{
                $aid = $this->uri->segment(3);
                if($aid < 0 || !is_numeric($aid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->aboutus->getAboutusByAid($aid);
                $this->load->view('/aboutus/v_editAboutUs',$data);
            }    
        }
        
    }
    public function delAboutus(){
        $flag=$this->op->checkUserAuthority('广告列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '关于我们');
        }else{
            $aid = $this->uri->segment(3);
            $ret = $this->aboutus->delAboutus($aid);
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '关于公司信息管理', '', '删除关于公司信息管理', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除关于公司信息管理', 'forward', OP_DOMAIN.'/aboutus'));
    }
}