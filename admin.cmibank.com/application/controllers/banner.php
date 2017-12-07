<?php
/**
 * banner管理
 * * */
class banner extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '广告列表') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_banner_model', 'banner');
    }

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('广告列表', $this->getSession('uid'));        //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '广告列表');
            exit;
        } else {
            $data = array();
            $bannertitle = trim($this->input->request('bannertitle'));
            $stime =strtotime(trim($this->input->request('stime')));
            $etime =strtotime(trim($this->input->request('etime')));
            $page = max(1, intval($this->input->request('pageNum')));
            $psize = max(20, intval($this->input->request('numPerPage')));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $asc = htmlspecialchars($this->input->request('asc'));
            $offset = ($page - 1) * $psize;
            $data = $this->getDefaultData($flag, array('系统管理', '用户管理'));
            
            $bannerKeys = $this->banner->getBannerCacheAllLocation();
            $banner_ids = array();
            foreach ($bannerKeys as $_banner){
                $_banner = json_decode($_banner, true);
                if(strtotime($_banner['endtime']) < NOW ){
                    $this->banner->delBannerCacheByLocation($_banner['location']);
                }
                $banner_ids[] = $_banner['bid'];
                
            }
            if($bannertitle && $bannertitle != '请输入搜索内容' && $this->input->request('op') == "search_bannertitle"){
                if (!$orderby) {
                    $orderby = 'ctime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $count = count($this->banner->getBannerListByLiketitle($bannertitle));
                $data['list'] = $this->banner->getBannerListByLiketitle($bannertitle, array($psize, $offset));
                $data['bannertitle'] = $bannertitle;
                if(empty($data['list'])){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
            }else if($stime && $stime != '请选择开始时间' && $this->input->request('op') == "search_bannertime"){
                if(!$stime || !$etime){
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '请输入开始和结束时间', array(), '广告列表');
                    exit;
                }
				$count = count($this->banner->getBannerbetweenTime($stime,$etime));
                $data['list'] = $this->banner->getBannerbetweenTime($stime,$etime, array($psize,$offset));
                $data['stime'] = $stime;
                $data['etime'] = $etime;
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
                $data['list'] = $this->banner->getBannerList('', 'ctime desc', array($psize, $offset));
                $count=$this->banner->getBannerCount();
            }
            if($count>0){
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'banner/index?page=' . $page . '$orderby=' . $orderby . '$asc=' .$asc ;
                if(!empty($bannertitle)){
                    $data['rel'] .= '&title=' . $bannertitle;
                }
            }else{
                $data['pageNum'] = 1;
                $data['numPerPage'] = 0;
                $data['count'] = 0;
                $data['list'] = $data['page'] = '';
            }
            $data['banner_ids'] = $banner_ids;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1051');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            $log = $this->op->actionData($this->getSession('name'), '广告列表', '', '广告列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/banner/v_index', $data);
        }
    }
    
    public function addbanner(){
        $flag = $this->op->checkUserAuthority('广告列表', $this->getSession('uid'));   //检测用户操作权限
        $data = array();
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '广告列表');
        } else {
            if($this->input->request('op') == 'addbanner'){
                $title = trim($this->input->post('title'));
                $content = trim($this->input->post('content'));
                $img = trim($this->input->post('img'));
                $uri = trim($this->input->post('uri'));
                $type = trim($this->input->post('type'));
                $location = trim($this->input->post('location'));
                $startime=trim($this->input->post('startime'));
                $endtime=trim($this->input->post('endtime'));
                $uri_title=trim($this->input->post('uri_title'));
                $data = array();
                $data['title'] = $title;
                $data['content'] = $content;
                $data['img'] = $img;
                $data['uri'] = $uri;
                $data['type'] = $type;
                $data['location'] = $location;
                $data['ctime'] = time();                    //创建时间
                $data['startime']= $startime;
                $data['endtime']=$endtime;
                $data['uri_title']=$uri_title;
                $ret = $this->banner->addBanner($data);
                if(!$ret){
                    exit(json_encode(array('statusCode' => self::AJ_RET_FAIL, 'message'=>'添加banner失败')));
                }
                $log = $this->op->actionData($this->getSession('name'), '广告列表', '', '广告列表', $this->getIP(), $this->getSession('uid'));
                exit($this->ajaxDataReturn(self::AJ_RET_SUCC, '添加banner成功', array(), '广告列表 ', 'forward', OP_DOMAIN.'/banner'));
            }else{
                $this->load->view('/banner/v_addBanner');
            }
        }
    }
    public function editBanner(){
        $flag=$this->op->checkUserAuthority('广告列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
           echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '广告列表');
        }else{

            if($this->input->request('op') == 'editBanner'){
               $bid = $this->input->post('bid');
               if(!$bid){
                  exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
               }
               $oldbanner = $this->banner->getBannerByBid($bid);
               if(empty($oldbanner)){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'不存在的BANNER')));
               }
               $oldlocation = $oldbanner['location'];
               $title = trim($this->input->post('title'));
               $content = trim($this->input->post('content'));
               $img = trim($this->input->post('img'));
               $uri = trim($this->input->post('uri'));
               $type = trim($this->input->post('type'));
               $startime = trim($this->input->post('startime'));
               $endtime = trim($this->input->post('endtime'));
               $location = trim($this->input->post('location'));  
               $uri_title=trim($this->input->post('uri_title'));


               $data['uri_title']=$uri_title;
               $data['title'] = $title;
               $data['content'] = $content;
               $data['img'] = $img;
               $data['uri'] = $uri;
               $data['type'] = $type;
               $data['location']=$location;
               $data['startime']=$startime;
               $data['endtime']=$endtime;
               $ret = $this->banner->updateBanner($bid, $data);
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'更新失败，请重试')));
               }
               $oldonlineInfo = $this->banner->getBannerCacheByLocation($oldlocation);
               if($oldonlineInfo['bid'] == $bid){
                   $this->banner->delBannerCacheBylocation($oldlocation);
               }
               if(!$ret){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'修改信息失败')));
               }
               $log = $this->op->actionData($this->getSession('name'), '广告列表', '', '修改广告列哦表', $this->getIP(), $this->getSession('uid'));
               exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '修改成功', array(), '修改广告列哦表 ', 'forward', OP_DOMAIN.'/banner'));
            }else{
               $bid = $this->uri->segment(3);
                if($bid < 0 || !is_numeric($bid)){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'缺少必要的参数')));
                }
                $data['detail'] = $this->banner->getBannerByBid($bid);
                $this->load->view('/banner/v_editBanner.php', $data);
            }  
        }
    }
    public function delBanner(){
        $flag=$this->op->checkUserAuthority('广告列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '广告列表');
        }else{
            $bid = $this->uri->segment(3);
            $banner = $this->banner->getBannerByBid($bid);
            if(!$banner){
                echo $this->ajaxDataReturn(self::AJ_RET_FAIL, 'BANNER不存在', array(), '广告列表');
            }
            $location = $banner['location'];
            $ret = $this->banner->delbanner($bid);
            $onlineInfo = $this->banner->getBannerCacheByLocation($location);
            if($onlineInfo && $onlineInfo['bid'] == $bid){
                $this->banner->delBannerCacheBylocation($location);
            } 
            if(!$ret){
                exit(json_decode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'删除信息失败')));
            }
        }
        $log = $this->op->actionData($this->getSession('name'), '广告列表', '', '删除广告', $this->getIP(), $this->getSession('uid'));
        exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '删除广告', 'forward', OP_DOMAIN.'/banner'));
    }
    //广告图片发布
    public function uptoline($bid){
        $flag=$this->op->checkUserAuthority('广告列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '广告列表');
        }else{
            $bannerDetail = $this->banner->getBannerByBid($bid);
            $location = $bannerDetail['location'];
            $info = $this->banner->getBannerCacheByLocation($location);
            if($info){
                exit($this->ajaxDataReturn(self::AJ_RET_FAIL, '该位置已有广告，请下掉再发布', array(), '广告列表'));
            }
            $count = $this->banner->countBannerCache($location);
            if($count >= 10){
                exit($this->ajaxDataReturn(self::AJ_RET_FAIL, '超过广告发布上限', array(), '广告列表'));
            }
            $this->banner->addBannerCacheByLocation($bannerDetail);
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '发布成功', array(), '发布', 'forward', OP_DOMAIN.'/banner'));
        }
    }
    
    //广告图片发布
    public function downtoline($bid){
        $flag=$this->op->checkUserAuthority('广告列表',$this->getSession('uid'));
        $data = array();
        if($flag == 0){
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '广告列表');
        }else{
            $bannerDetail = $this->banner->getBannerByBid($bid);
            $location = $bannerDetail['location'];
            $info = $this->banner->getBannerCacheByLocation($location);
            
            if(!$info){
                exit($this->ajaxDataReturn(self::AJ_RET_FAIL, '该产品末发布', array(), '广告列表'));
            }
            $this->banner->delBannerCacheByLocation($bannerDetail['location']);
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '取消成功', array(), '发布', 'forward', OP_DOMAIN.'/banner'));
        }
    }
    
    
    
    
    
}