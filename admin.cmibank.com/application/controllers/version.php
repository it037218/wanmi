<?php
/**
 *version管理
 * * */
class version extends Controller{
    
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        if(false == $this->menu){
            redirect(OP_DOMAIN . 'login', 'location');
        }else{
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '更新管理') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_version_model', 'version');
    }
    
    public function index($page=1){
        $flag = $this->op->checkUserAuthority('版本信息管理',$this->getSession('uid'));
        if($flag ==0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'版本信息管理');
        }else{
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $data = array();
            $offset = ($page - 1) * $psize;
            $asc = htmlspecialchars($this->input->request('asc'));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $features = trim($this->input->post('features'));
            if($features && $features != '请输入搜索内容' && $this->input->request('op') == "search_features"){
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $count=count($this->version->getVersionListByLikefeatures($features));
                $data['list'] = $this->version->getVersionListByLikefeatures($features,array($psize, $offset));
                $data['features'] = $features;
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
                $data['list'] = $this->version->getVersionList('', 'ctime desc', array($psize, $offset));
                $count = $this->version->getVersionCount();
            }
            if($count>0){
            
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'version/index?page=' . $page;
                if(!empty($features)){
                    $data['rel'] .= '&title=' . $features;
                }
            }else{
                $data['list'] = $data['page'] = '';
                $data['pageNum'] = $data['numPerPage'] = $data['count'] = 0;
            }
            $edatable = $this->op->getEditable($this->getSession('uid'),'1081');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '更新管理', '', '版本信息管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/version/v_index', $data);
        }
    }
    
    public function addVersion(){
        $flag = $this->op->checkUserAuthority('版本信息管理',$this->getSession('uid'));
        $data=array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'版本信息管理');
        }else{
            if($this->input->request('op') == 'addversion'){
                 
                $data['number'] = trim($this->input->post('number'));
                $data['features'] = trim($this->input->post('features'));
                $data['linetime'] = trim($this->input->post('linetime'));
                $data['ctime'] = time();
        
                $ret = $this->version->addVersion($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加version失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '更新管理', '', '版本信息管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加version成功', array(), '版本信息管理','forward', OP_DOMAIN.'/version'));
            }else{
                $this->load->view('/version/v_addVersion',$data);
            }
        
        }
    }
    
    public function delVersion(){
        $flag=$this->op->checkUserAuthority('版本信息管理',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '版本信息管理');
        }else{
            $vid = $this->uri->segment(3);
            $ret = $this->version->delVersion($vid);
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '版本信息管理', '', '删除版本信息管理', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除版本信息管理', 'forward', OP_DOMAIN.'/version'));
    }
    
    public function editVersion(){
        $flag = $this->op->checkUserAuthority('版本信息管理', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,'没有权限',array(),'版本信息管理');
        }else{
            if($this->input->request('op') == 'saveedit'){
                $vid = trim($this->input->post('vid'));
                if(!$vid){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
 
                $data['number'] = trim($this->input->post('number'));
                $data['features'] = trim($this->input->post('features'));
                $data['linetime'] = trim($this->input->post('linetime'));
               
    
                $ret = $this->version->editVersion($vid,$data);
                if(!$ret){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '广告列表', '', '修改版本信息管理', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改版本信息管理 ', 'forward', OP_DOMAIN.'/version'));
            }else{
                $vid = $this->uri->segment(3);
                if($vid < 0 || !is_numeric($vid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->version->getVersionByVid($vid);
                $this->load->view('/version/v_editVersion',$data);
            }
        }
    
    }
    
    
    
}
