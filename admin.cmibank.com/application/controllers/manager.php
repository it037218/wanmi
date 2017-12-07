<?php
/**
 * manager管理
 * * */
class Manager extends Controller {

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

    public function index($page = 1) {
        $flag = $this->op->checkUserAuthority('用户管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            $data = array();
            $username = trim($this->input->request('username'));
            $page = max(1, intval($this->input->request('pageNum')));            
            $psize = max(20 , intval($this->input->request('numPerPage')));
            $orderby = htmlspecialchars($this->input->request("orderby"));
            $asc = htmlspecialchars($this->input->request('asc'));
            
            $offset = ($page - 1) * $psize;
            $data = $this->getDefaultData($flag, array('系统管理', '用户管理'));
            $edatable = $this->op->getEditable($this->getSession('uid'),'1007');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            if($username && $username != '请输入搜索内容' && $this->input->request('op') == "search"){
                if (!$orderby) {
                    $orderby = 'createTime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $count = count($this->op->getManagerListByLikeName($username)); //优化成count(主键)
                $data['list'] = $this->op->getManagerListByLikeName($username, array($psize, $offset));
                $data['title'] = $username;
                if(empty($data['list'])){
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有找到相应的数据')));
                }
            }else{
                if (!$orderby) {
                    $orderby = 'createTime';
                }
                if (!$asc) {
                    $asc = 'ASC';
                }
                $count = count($this->op->getManagerList());    //优化成count(主键)
                $data['list'] = $this->op->getManagerList('',$orderby . ' ' . $asc, array($psize, $offset));
            }
            
            if ($count > 0) {
                //dwz分页参数
                $data['pageNum']    = $page;
                $data['numPerPage'] = $psize;
                $data['count'] = $count;
                $data['rel'] = OP_DOMAIN . 'manager/index?page=' . $page . '&orderby=' . $orderby . '&asc=' . $asc ;
                if(!empty($username)){
                    $data['rel'] .= '&title=' . $username;
                }
            } else {
                $data['list'] = $data['page'] = '';
            }
            $data['orderby'] = $orderby;
            $data['asc'] = $asc;
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '查看用户管理列表', '', '用户管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/manager/v_index', $data);
        }
    }

    public function add() {
        $flag = $this->op->checkUserAuthority('用户管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
           
            if (false != $this->input->post('op') && $this->input->post('op') == 'add') {
                $name = htmlspecialchars($this->input->post('name'));
                $realname = htmlspecialchars($this->input->post('realname'));
                $password = htmlspecialchars($this->input->post('password'));
                $group = htmlspecialchars($this->input->post('group'));
                $post = htmlspecialchars($this->input->post('post'));

                if (!($name && $post && $realname && $password && $group && $post)) {
                    $this->Error('请填写完整的资料信息！', OP_DOMAIN . 'manager/add');
                } else {
                    $user_array = array(
                        'name' => $name,
                        'realname' => $realname,
                        'password' => md5($password),
                        'status' => 1,
                        'createTime' => time()
                    );
                    $user_id = $this->op->userInsert($user_array);

                    //user action log
                    $log = $this->op->actionData($this->getSession('name'), '添加用户' . $user_id, $user_id, '用户管理', $this->getIP(), $this->getSession('uid'));
                    if ($user_id) {

                        $user_group = array(
                            '`user_id`' => $user_id,
                            '`group_id`' => $post,
                        );
                        $res = $this->op->aclUserGroupInsert($user_group);
                        if ($res) {
                            exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC, 'message'=>'添加成功', 'forwardUrl'=>OP_DOMAIN.'/manager/index', 'callbackType'=>'forward', 'navTabId'=>'navTab')));
                        } else {
                            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'添加失败')));
                        }
                    } else {
                        exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'添加失败')));
                    }
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '用户管理'));
                $data['list'] = $this->op->getGroupList('select');
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看添加用户', '', '用户管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/manager/v_add', $data);
            }
        }
    }


    public function getPostByAjax() {
        $group = htmlspecialchars($this->input->get('group'));
        $list = $this->op->getPostList($group);
        $data = json_encode($list);
        echo $data;
        exit;
    }

    public function delete() {
        $flag = $this->op->checkUserAuthority('用户管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            $idlist = htmlspecialchars($this->input->get("idlist"));
            $list = explode(',', $idlist);
            foreach ($list AS $value) {
                $value = intval($value);
                if ($value) {
                    //user action log
                    $log = $this->op->actionData($this->getSession('name'), '删除用户' . $value, $value, '用户管理', $this->getIP(), $this->getSession('uid'));
                    $this->op->userDelete(array('`id`' => $value));
                    $this->op->aclUserGroupDelete(array('`user_id`' => $value));
                }
            }
            exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'删除成功', 'forwardUrl'=>OP_DOMAIN.'/manager','callbackType'=>'forward','navTabId'=>'navTab')));
        }
    }

    public function modify($uid) {
        $flag = $this->op->checkUserAuthority('用户管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
           
            if (false != $this->input->post('op') && $this->input->post('op') == 'modify') {
               
                $name = htmlspecialchars($this->input->post('name'));
                $realname = htmlspecialchars($this->input->post('realname'));
                $password = htmlspecialchars($this->input->post('password'));
                $group = htmlspecialchars($this->input->post('classid'));

                if (!($name && $realname && $group)) {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'编辑失败')));
                } else {
                    $user_array = array(
                        'name' => $name,
                        'realname' => $realname
                    );
                    if (false != $password) {
                        $user_array['password'] = md5($password);
                    }
                    $r = $this->op->userUpdate($user_array, array('`id`' => $uid));
                    //user action log
                    $log = $this->op->actionData($this->getSession('name'), '修改用户'. $name.'资料信息', $uid, '用户管理', $this->getIP(), $this->getSession('uid'));
                    if (false !== $r) {
                        $this->op->aclUserGroupDelete(array('`user_id`' => $uid));
                        $user_group = array(
                            '`user_id`' => $uid,
                            '`group_id`' => $group,
                        );
                        $res = $this->op->aclUserGroupInsert($user_group);
                        if ($res) {
                            exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'编辑成功', 'forwardUrl'=>OP_DOMAIN.'/manager','callbackType'=>'forward','navTabId'=>'navTab')));
                        } else {
                            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'编辑失败')));
                        }
                    } else {
                        exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'编辑失败')));
                    }
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '用户管理'));
                $r = $this->op->getManagerInfo(array('`id`' => $uid));
                $list = $this->op->getUserGroupInfo($r['id']);
                if($list){
                    $r['post'] = $list[0]['name'];
                    $r['inner_group'] = $list[0]['inner_group'];
                }
                
                $data['groupInfo'] = $r;
                $data['group'] = $this->op->getGroupList('select');
                if(isset($data['groupInfo']['inner_group'])){
                    $data['groupSons'] = $this->getGroupSons($data['groupInfo']['inner_group'], false);
                }
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看用户资料信息' . $uid, $uid, '用户管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/manager/v_modify', $data);
            }
        }
    }

    
    
    public function checkManagerByName() {
        $name = trim($this->input->post('name'));
        if ($name) {
            $result = $this->op->getManagerAccount(array('name' => $name));
            if ($result) {
                exit('-2');
            } else {
                exit('1');
            }
        } else {
            exit('-1');
        }
    }

    public function checkManagerByRealName() {
        $realname = trim($this->input->post('realname'));
        if ($realname) {
            $result = $this->op->getManagerAccount(array('realname' => $realname));
            if ($result) {
                exit('-2');
            } else {
                exit('1');
            }
        } else {
            exit('-1');
        }
    }

}