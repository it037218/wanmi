<?php

/**
 * 权限管理
 * * */
class Clear_cache extends Controller {

    private $cache_arr = array(
        array('name' => '推荐列表缓存', 'uri' => 'clear_cache/clear_recommend_cache'),
        array('name' => '产品项目列表缓存', 'uri' => 'clear_cache/clear_ptype_product_cache')
    );
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '缓存重建') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
       
    }

    public function index() {
        $flag = $this->op->checkUserAuthority('缓存列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '缓存列表');
        } else {
            $data = array();
            $data['list'] = $this->cache_arr;
            $edatable = $this->op->getEditable($this->getSession('uid'),'1011');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '推荐列表缓存重建', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/clear_cache/v_index', $data);
        }
    }
    
    public function clear_recommend_cache() {
        $flag = $this->op->checkUserAuthority('缓存列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '推荐列表缓存重建');
        } else {
            $data = array();
            $this->load->model('admin_recommend_model', 'recommend');
            $ret = $this->recommend->rebuildRecommendListRedisCache();
            if(!$ret){
                exit($this->ajaxDataReturn(self::AJ_RET_,  '重建失败', array(), '推荐列表缓存重建 ', 'forward', OP_DOMAIN.'/clear_cache'));
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '推荐列表缓存重建', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重建成功', array(), '推荐列表缓存重建 ', 'forward', OP_DOMAIN.'/clear_cache'));
        }
    }
    
    
    public function clear_ptype_product_cache() {
        $flag = $this->op->checkUserAuthority('缓存列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '项目产品列表重建');
        } else {
            $data = array();
            $this->load->model('admin_ptype_product_model', 'ptype_product');
            $this->load->model('admin_ptype_model', 'ptype');
            $ptypelist = $this->ptype->getPtypeList();
            
            $odate = date("Y-m-d");
            foreach ($ptypelist as $_ptype){
                $ret = $this->ptype_product->rebuildPtypeProductListRedisCache($_ptype['ptid'], $odate);
            }
            if(!$ret){
                exit($this->ajaxDataReturn(self::AJ_RET_,  '重建失败', array(), '项目产品列表重建', 'forward', OP_DOMAIN.'/clear_cache'));
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '项目产品列表重建', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '重建成功', array(), '项目产品列表重建', 'forward', OP_DOMAIN.'/clear_cache'));
        }
    }
    
}