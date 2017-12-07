<?php

/**
 * 权限管理
 * * */
class Authority extends Controller {

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

    public function index() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理 ');
        } else {
            $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
            $data['list'] = $this->op->getGroupList('list');
            $edatable = $this->op->getEditable($this->getSession('uid'),'1003');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '查看用户分组及权限', '', '权限管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/authority/v_index', $data);
        }
    }

    
    /**
     * 添加部门 
     */
    public function addgroupmaster(){
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '没有权限', array(), '权限管理 ');
        } else {
            if (false != $this->input->post('op') && 'add' == $this->input->post('op')) {
                $name = htmlspecialchars($this->input->post('name'));
                $groupType = htmlspecialchars($this->input->post('groupType'));
                if(empty($name) || empty($groupType)){
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '部门或岗位为空', array(), '添加部门 ');
                    exit;
                }
                $data = array(
                    'name' => $name,
                    'type' => '自定义'
                );
                
                $data['inner_group'] = $groupType;
                
                $r = $this->op->aclGroupInsert($data);
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '添加分组及岗位'.$r, $r, '权限管理', $this->getIP(), $this->getSession('uid'));
                if ($r) {
                    echo $this->ajaxDataReturn(self::AJ_RET_SUCC,  '保存成功', array(), '添加部门 ', 'forward', OP_DOMAIN.'/authority');
                    exit;
                } else {
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '保存失败', array(), '添加部门 ');
                    exit;
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $data['group'] = $this->op->getGroupList('select');
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看添加分组', '', '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_addmaster', $data);
            }
        }
        
    }
    /**
     * 添加岗位
     */
    public function addgroupmem() {
        
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false != $this->input->post('op') && 'add' == $this->input->post('op')) {
                $name = htmlspecialchars($this->input->post('name'));
                $groupType = htmlspecialchars($this->input->post('groupType'));
                if(empty($name) || empty($groupType)){
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '部门或岗位为空', array(), '添加部门 ');
                    exit;
                }
                $data = array(
                    'name' => htmlspecialchars($name),
                    'type' => '自定义'
                );
                if ($this->input->post('groupType') == "选择部门") {
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,  '保存失败', array(), '新建分组');
                    exit;
                } else {
                    $data['inner_group'] = $groupType;
                }
                $r = $this->op->aclGroupInsert($data);
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '添加分组及岗位'.$r, $r, '权限管理', $this->getIP(), $this->getSession('uid'));
                if ($r) {
                    echo $this->ajaxDataReturn(self::AJ_RET_SUCC,  '保存成功', array(), '新建分组', 'forward', OP_DOMAIN.'/authority');
                    exit;
                } else {
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,   '保存失败', array(), '新建分组');
                    exit;
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $data['group'] = $this->op->getGroupList('select');
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看添加分组', '', '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_addmem', $data);
            }
        }
    }

//     public function getGroupSons($group, $is_json = true){
//         $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
//         if ($flag == 0) {
//             $this->Error('没有权限', OP_DOMAIN . 'system');
//         } else {
//             if (false == $group) {
//                 $this->Error('未知ID', OP_DOMAIN . 'authority/index');
//             }
//             $sons = $this->op->getPostList(urldecode($group), array('`id`'));
//             $rtn = array();
//             if(!empty($sons)){
//                 foreach($sons as $key=>$val){
//                     $rtn[$key][0] = $val['id'];
//                     $rtn[$key][1] = $val['name'];
//                 }
//             }
//             if($is_json){
//                 echo json_encode($rtn);
//                 exit;
//             }else{
//                 return $rtn;
//             }
            
//         }
//     }
    
    /**
     * 编辑用户分组
     */
    public function editUserGroup($user_id){
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false == $user_id) {
                $this->Error('未知ID', OP_DOMAIN . 'authority/index');
            }
            if (false != $this->input->post('op') && 'modify' == $this->input->post('op')) {
                $data['group_id'] = $this->input->post('classid');
        
                $r = $this->op->aclGroupUpdate($data, $user_id);
                
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '编辑用户分组设置#岗位'.$user_id, $user_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                if ($r) {          
                    echo $this->ajaxDataReturn(self::AJ_RET_SUCC,  '保存成功', array(), '编辑用户组群');
                    exit;
                } else {
                    echo $this->ajaxDataReturn(self::AJ_RET_FAIL,  '保存失败', array(), '编辑用户组群');
                    exit;
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $data['groupInfo'] = $this->op->getUserGroupInfo($user_id);
                $data['groupInfo'] = $data['groupInfo'][0];
                $group_id = $data['groupInfo']['group_id'];
                $data['user_id'] = $user_id;
                $data['group'] = $this->op->getGroupList('select');
                $data['groupSons'] = $this->getGroupSons($data['groupInfo']['inner_group'], false);
                $log = $this->op->actionData($this->getSession('name'), '编辑用户分组设置#岗位'.$user_id, $user_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_editUserGroup', $data);
            }
        }
    }
    
    
    
    public function modify($group_id = '') {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false == $group_id) {
                $this->Error('未知ID', OP_DOMAIN . 'authority/index');
            }
            if (false != $this->input->post('op') && 'modify' == $this->input->post('op')) {
                $data['group_id'] = $this->input->post('classid');
                
                $r = $this->op->aclGroupUpdate($data, $uid);
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '修改分组设置岗位'.$group_id, $group_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                if ($r) {
                    $this->Error('保存成功', OP_DOMAIN . 'authority');
                } else {
                    $this->Error('保存失败', OP_DOMAIN . 'authority/modify/' . $group_id);
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $data['groupInfo'] = $this->op->getGroupInfo(array('`id`' => $group_id));
                $data['group'] = $this->op->getGroupList('select');
                $data['groupSons'] = $this->getGroupSons($data['groupInfo']['inner_group'], false);
//                 print_r($data['groupInfo']);
//                 print_r($data['group']);
//                 print_r($data['groupSons']);
//                 exit;
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看分组设置岗位'.$group_id, $group_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_modify', $data);
            }
        }
    }

    /**
     * 管理权限
     */
    public function set($group_id) {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
           $group_id = intval($group_id);
            if (false != $this->input->post('op') && 'set' == $this->input->post('op')) {
                $list = $this->op->getAclAction();
                $this->op->AclGroupActionDelete(array('`group_id`' => $group_id));
                foreach ($list AS $key => $value) {
                    $visible = $this->input->post('visible_' . $value['id']);
                    $editable = $this->input->post('editable_' . $value['id']);
                    $data = array();
                    if (isset($visible) && $visible == 1) {
                        $data = array(
                            'group_id' => $group_id,
                            'action_id' => $value['id'],
                            'visible' => 1
                        );
                        if (isset($editable) && $editable == 1) {
                            $data['editable'] = 1;
                        } else {
                            $data['editable'] = 0;
                        }
                    }
                    if (!empty($data)) {
                        $this->op->AclGroupActionInsert($data);
                    }
                }
                $this->op->makeUserMenu($group_id, false);
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '修改用户权限分组'.$group_id, $group_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'编辑成功', 'forwardUrl'=>OP_DOMAIN.'/authority/set/'.$group_id,'callbackType'=>'forward','navTabId'=>'navTab')));
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $data['groupInfo'] = $this->op->getGroupInfo(array('`id`' => $group_id));
                $data['list'] = $this->op->getActionList($group_id);
                $userAction = $this->op->getActionByGroup($group_id);
                if (empty($userAction)) {
                    $data['visible'] = $data['editable'] = array();
                } else {
                    foreach ($userAction AS $key => $value) {
                        if ($value['visible'] == 1) {
                            $data['visible'][] = $value['action_id'];
                        }
                        if ($value['editable'] == 1) {
                            $data['editable'][] = $value['action_id'];
                        }
                    }
                }
                $data['group_id'] = $group_id;
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看分组用户权限'.$group_id, $group_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_set', $data);
            }
        }
    }

    /**
     * 检查是否有子分组
     */
    public function checkSubGroup() {
        $name = htmlspecialchars($this->input->get('name'));
        $type = intval($this->input->get('type'));
        if ($type == 1) {
            $r = $this->op->getGroupInfo(array('`inner_group`' => $name));
            if (!empty($r)) {
                exit('0');
            } else {
                exit('1');
            }
        } else {
            $inner_group = htmlspecialchars($this->input->get('group'));
            $sql = "SELECT admin_user_group.user_id FROM admin_group" .
                    " LEFT JOIN admin_user_group ON admin_user_group.group_id = admin_group.id" .
                    " WHERE admin_group.name = '$name' and admin_group.inner_group='$inner_group'";
            $r = $this->op->querySQL($sql);
            if (isset($r[0]['user_id']) && $r[0]['user_id']) {
                exit('0');
            } else {
                exit('1');
            }
        }
    }
    
    
    public function delGroupSub($gid){
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
           if($gid){
               //取得guest权限id
               $guestInfo = $this->op->getAdminGroup(array('name' => 'guest'));
               $guest_id = $guestInfo['id'];
               $willDelGroupInfo = $this->op->getAdminGroup(array('id' => $gid));
               if($willDelGroupInfo['type'] == '内置'){
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'禁止删除内置类型')));
               }
               $ret = $this->op->AclGroupDelete(array('`id`' => $gid));
               $ret = $this->op->updateUserGroupInfo($guest_id, 'group_id = '.$willDelGroupInfo['id']);
               $log = $this->op->actionData($this->getSession('name'), '删除岗位分组'.$gid, $gid, '权限管理', $this->getIP(), $this->getSession('uid'));
               
               exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'删除成功', 'forwardUrl'=>OP_DOMAIN.'/authority/grouplist/','callbackType'=>'forward','navTabId'=>'navTab')));
           }
           exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'删除失败')));
            
        }
    }
    
    /*
     * 职位列表
     */
    public function grouplist(){
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else {
            
            $group = $this->op->getAllGroupInfo();
            foreach ($group as $info){
                if($info['type'] == '内置'){
                    continue;
                }
                $data['group'][] = $info;
            }
            $this->load->view('authority/v_grouplist', $data);
            
        }
    }

    public function delGroup() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            $name = htmlspecialchars($this->input->get('name'));
            $type = intval($this->input->get('type'));
            if ($type == 1) {
                $this->op->AclGroupDelete(array('`inner_group`' => $name));
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '删除用户分组：'.$name, '', '权限管理', $this->getIP(), $this->getSession('uid'));
            } else {
                $inner_group = htmlspecialchars($this->input->get('group'));
                $this->op->AclGroupDelete(array('`inner_group`' => $inner_group, 'name' => $name));
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '删除用户分组：'.$name, '', '权限管理', $this->getIP(), $this->getSession('uid'));
            }

            if ($r) {
                $this->Error('删除成功', OP_DOMAIN . 'authority/index');
            } else {
                $this->Error('删除失败', OP_DOMAIN . 'authority/index');
            }
        }
    }

    public function delGroupMember() {
       
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            $group_id = intval($this->input->get('gid'));
            $member_id = intval($this->input->get('mid'));
            $r = $this->op->aclUserGroupDelete(array('`group_id`' => $group_id, 'user_id' => $member_id));
            if ($r) {
                $this->op->userDelete(array('`id`' => $member_id));
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '删除分组'.$group_id.'的用户'.$member_id, $group_id, '权限管理', $this->getIP(), $this->getSession('uid'));
                exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'删除用户成功', 'forwardUrl'=>OP_DOMAIN.'/authority','callbackType'=>'forward','navTabId'=>'navTab')));
            } else {
                exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除用户失败')));
            }
        }
    }

    /*
     * 请求列表
     */
    public function column() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
            $data['list'] = $this->op->getAclAction('', 'group_name ASC');
            $edatable = $this->op->getEditable($this->getSession('uid'),'1005');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '查看栏目列表', '', '权限管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/authority/v_column', $data);
        }
    }

    /*
     * 添加请求列
     */
    public function addColumn() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false != $this->input->post('op') && 'add' == $this->input->post('op')) {
                $group_data = htmlspecialchars($this->input->post('group_name'));
                list($group_name, $group_type) = explode('_', $group_data);
                $data = array(
                    'name' => htmlspecialchars($this->input->post('name')),
                    'group_name' => $group_name,
                    'url' => htmlspecialchars($this->input->post('url')),
                    'status' => intval($this->input->post('status')),
                    'group_type' => $group_type
                );
                $r = $this->op->insertAclAction($data);
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '添加栏目'.$r, $r, '权限管理', $this->getIP(), $this->getSession('uid'));
                if ( $r) {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'添加成功', 'forwardUrl'=>OP_DOMAIN.'/authority/column', 'callbackType'=>'forward', 'navTabId'=>'navTab')));
                } else {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'添加失败')));
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                //$data['group'] = $this->op->getAclActionGroupName();
                $data['group'] = $this->op->getAclGroupType();
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看添加栏目', '', '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_addcolumn', $data);
            }
        }
    }

    public function editColumn() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false != $this->input->post('op') && 'edit' == $this->input->post('op')) {
                $data = array(
                    'name' => htmlspecialchars($this->input->post('name')),
                    'group_name' => htmlspecialchars($this->input->post('group_name')),
                    'url' => htmlspecialchars($this->input->post('url')),
                    'status' => intval($this->input->post('status')),
                    'group_type' => intval($this->input->post('group_type'))
                );
                $id = intval($this->input->post('id'));
                $r = $this->op->updateAclAction($data, array('id' =>$id )) ;
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '修改栏目'.$id, $id , '权限管理', $this->getIP(), $this->getSession('uid'));
                if ($r) {
                   exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'编辑成功', 'forwardUrl'=>OP_DOMAIN.'/authority/column','callbackType'=>'forward','navTabId'=>'navTab')));
                } else {
                   exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'编辑失败')));
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $data['group'] = $this->op->getAclGroupType();
                $id = (int) $this->uri->segment(3);
                $data['column'] = $this->op->getAclActionOne(array('id' => $id));
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看栏目'.$id, $id, '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_editcolumn', $data);
            }
        }
    }

    public function delColumn() {
        $id = intval($this->input->get('cid'));
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else if ($id > 0 ) {
            $res_1 = $this->op->delAclAction(array('id' => $id));
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '删除栏目'.$id, $id, '权限管理', $this->getIP(), $this->getSession('uid'));
            $this->op->AclGroupActionDelete(array('action_id' => $id));
           
            if($res_1){
                exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'删除用户成功', 'forwardUrl'=>OP_DOMAIN.'/authority/column','callbackType'=>'forward','navTabId'=>'navTab')));
            }            
        } 
        exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除用户失败')));
    }

    public function addgrouptype() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false != $this->input->post('op') && 'add' == $this->input->post('op')) {
                $data = array(
                    'name' => htmlspecialchars($this->input->post('name')),
                );
                $r = $this->op->insertAclGroupType($data);
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '添加栏目类型'.$r, $r, '权限管理', $this->getIP(), $this->getSession('uid'));
                if ($r) {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'添加栏目类型成功', 'forwardUrl'=>OP_DOMAIN.'/authority/typelist','callbackType'=>'forward','navTabId'=>'navTab')));
                } else {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'添加栏目类型失败')));
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看添加栏目类型', '', '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_addgrouptype', $data);
            }
        }
    }

    public function typelist() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
            $data['list'] = $this->op->getAclGroupType();
            $edatable = $this->op->getEditable($this->getSession('uid'),'1006');
            if(!empty($edatable)){
            	$data['editable'] = $edatable[0]['editable'];
            }else{
            	$data['editable']=0;
            }
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '查看栏目类型管理', '', '权限管理', $this->getIP(), $this->getSession('uid'));
            $this->load->view('/authority/v_grouptype', $data);
        }
    }

    public function editGroupType() {
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false != $this->input->post('op') && 'edit' == $this->input->post('op')) {
                $data = array(
                   'name' => htmlspecialchars($this->input->post('name')),
                );
                $data1 = array(
                    'group_name' => htmlspecialchars($this->input->post('name')),
                );
                $id = intval($this->input->post('id'));
                if ($this->op->updateAclGroupType($data, array('id' => $id)) !== false
                        && $this->op->updateAclAction($data1, array('group_type' => $id)) !== false) {
                    //user action log
                    $log = $this->op->actionData($this->getSession('name'), '修改栏目类型' .$id , $id , '权限管理', $this->getIP(), $this->getSession('uid'));
                    exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'编辑成功', 'forwardUrl'=>OP_DOMAIN.'/authority/typelist','callbackType'=>'forward','navTabId'=>'navTab')));
                } else {
                    exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'编辑失败')));
                }
            } else {
                $data = $this->getDefaultData($flag, array('系统管理', '权限管理'));
                $id = intval($this->uri->segment(3));
                $data['grouptype'] = $this->op->getAclGroupTypeOne(array('id' => $id));
                //user action log
                $log = $this->op->actionData($this->getSession('name'), '查看栏目类型' . $id, $id, '权限管理', $this->getIP(), $this->getSession('uid'));
                $this->load->view('/authority/v_editgrouptype', $data);
            }
        }
    }

    public function delGroupType() {
        $id = intval($this->input->get('cid'));
        $list = $this->op->getAclAction(array('`group_type`' => $id));
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'没有权限')));
        } else if ($id > 0 && $this->op->delAclGroupType(array('id' => $id))) {
            $this->op->delAclAction(array('group_type' => $id));
            //user action log
            $log = $this->op->actionData($this->getSession('name'), '删除栏目类型'.$id, $id, '权限管理', $this->getIP(), $this->getSession('uid'));
            foreach ($list as $key => $val) {
                $this->op->AclGroupActionDelete(array('action_id' => $val['id']));
            }
            exit(json_encode(array('statusCode'=>self::AJ_RET_SUCC,'message'=>'删除成功', 'forwardUrl'=>OP_DOMAIN.'/authority/typelist','callbackType'=>'forward','navTabId'=>'navTab')));
        } else {
            exit(json_encode(array('statusCode'=>self::AJ_RET_FAIL,'message'=>'删除失败')));
        }
    }

}