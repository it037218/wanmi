<?php
/**
 * 权限管理
 * * */
class Recommend extends Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        if (false == $this->menu) {
            redirect(OP_DOMAIN . 'login', 'location');
        } else {
            foreach ($this->menu AS $key => $value) {
                if ($value['name'] == '金融产品') {
                    $this->submenu = $value['submenu'];
                }
            }
        }
        $this->load->model('admin_product_model', 'product');
        $this->load->model('admin_longproduct_model', 'longproduct');
        $this->load->model('admin_recommend_model', 'recommend');
    }

    public function index() {
        die('已禁止调用');
        $flag = $this->op->checkUserAuthority('推荐列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '推荐列表');
        } else {
            $data = array();
            $data['list'] = $this->recommend->getRecommendList();
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '推荐列表', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/recommend/v_index', $data);
        }
    }
    
    public function addtorecommend($pid, $ptype) {
        die('已禁止调用');
        $flag = $this->op->checkUserAuthority('推荐列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '推荐列表');
        } else {
            if($ptype == 1){
                $detail = $this->longproduct->getLongProductByPid($pid);
            }else{
                $detail = $this->product->getProductByPid($pid);
            }
            $data = array(
                'pid' => $pid,
                'pname' => $detail['pname'],
                'ptype' => $ptype,
                'addtime' => time(),
                'rtype' => 1
            );
            $ret = @$this->recommend->addrecommend($data);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'已在列表中')));
            }
            
            $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '添加推荐产品', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '添加成功', array(), '产品推荐 ', 'forward', OP_DOMAIN.'/recommend'));
        }
    }
    
    public function delrecommend($pid, $ptype) {
        die('已禁止调用');
        $flag = $this->op->checkUserAuthority('推荐列表', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '推荐列表');
        } else {
            $data = array(
                'pid' => $pid,
                'ptype' => $ptype
            );
            $ret = @$this->recommend->delrecommend($data);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除失败')));
            }
            $log = $this->op->actionData($this->getSession('name'), '金融产品', '', '删除推荐产品', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '删除成功', array(), '产品推荐 ', 'forward', OP_DOMAIN.'/recommend'));
        }
    }
    
    public function addtoCompetitive($pid){
        //die('已禁止调用');
        $flag = $this->op->checkUserAuthority('定期产品发布', $this->getSession('uid'));            //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL, '没有权限', array(), '定期产品发布');
        } else {
            $ret = $this->recommend->addCompetitive($pid);
            if(!$ret){
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL, 'message'=>'添加错误，请联系管理员!')));
            }
            $log = $this->op->actionData($this->getSession('name'), '定期产品发布', '', '添加精品推荐', $this->getIP(), $this->getSession('uid'));
            exit($this->ajaxDataReturn(self::AJ_RET_SUCC,  '推荐成功', array(), '产品推荐 ', 'no', OP_DOMAIN.'/product'));
        }
    }
    
}