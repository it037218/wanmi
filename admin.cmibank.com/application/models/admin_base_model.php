<?php
//require_once ROOTPATH . DS . APPPATH. 'libraries/dbmodel.lib.php';
require_once APPPATH. 'libraries/dbmodel.lib.php';

class admin_base_model extends Dbmodel {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 运营登录
     * @return boolen -1 账号不存在
     *                -2 密码错误
     *                -3 状态未开启
     */
    public function login($name, $password) {
        $user_info = $this->getOne(array('`name`' => $name), 'admin_user');
        if (!$user_info) {
            return -1;
        }
        $time = time();

        if ($user_info['password'] != md5($password)) {
            return -1;
        } else {
            if ($user_info['status'] != 1) {
                $flag = -3;
            } else {
                $userRole = $this->getUserGroupInfo($user_info['id']);
                $this->makeUserMenu($user_info['id']);
                $data = array(
                    'id' => $user_info['id'],
                    'name' => $name,
                    'status' => $user_info['status'],
                    'lastLoginTime' => $user_info['lastLoginTime'],
                    'createTime' => $user_info['createTime'],
                    'loginTimes' => $user_info['loginTimes'] + 1,
                    'realname' => $user_info['realname'],
                    'uid' => $user_info['id'],
                    'group' => $userRole[0],
                    'group_id' => $userRole[0]['group_id'],
                    'is_logged_in' => true
                );
                foreach ($data as $k => $v) {
                     $_SESSION[$k] = $v;
                    
                    $this->setCookie($k, $v, 86400);
                }
                //更新登录信息
                $this->updateManagerInfo(
                        array(
                    '`lastLoginTime`' => $time,
                    '`loginTimes`' => ($user_info['loginTimes'] + 1)
                        ), $user_info['id']);
                $flag = $user_info['id'];
            }
        }

        switch ($flag) {
            case "-2":
                $content = '密码错误';
                $status = '失败';
                break;
            case "-3":
                $content = "账号异常";
                $status = "失败";
                break;
            default:
                $content = "登录成功";
                $status = "成功";
                break;
        }
        //写登录日志
        $ip = $this->getIP();
        $location = $this->convertip($ip);
        $address = $this->ipToArea($location);
        if (false == $address['province'] && false == $address['city']) {
            $addr = $location;
        } else {
            $addr = $address['province'] . " " . $address['city'];
        }
        $this->insertData(
                array(
            'empId' => $user_info['id'],
            'user_ip' => $ip,
            'address' => $addr,
            'loginTime' => $time,
            'content' => $content,
            'status' => $status
                ), 'admin_login_record');

        return $flag;
    }

    protected function setCookie($name, $val, $expire = 86400){
        include APPPATH . 'config/config.php';
        $path	= $config['cookie_path'];
        $domain = $config['cookie_domain'];
        $expire = ($expire == 0)? $config['cookie_expire'] : time() + $expire;
        if(is_array($val)){
            $val = json_encode($val);
        }
        return setcookie($name, $val, $expire, $path, $domain);
    }
    
    
    public function getAdminGroup($where){
        return $this->getone($where, 'admin_group');
    }
    
    /**
     * 判断用户操作权限
     */
    public function checkUserAuthority($name, $uid) {
        $actionlist = $this->getUserAction($uid);
        $list = $this->getAclAction(array('`name`' => $name));
        foreach ($actionlist AS $key => $value) {
            if (isset($list[0]['id']) && $value['action_id'] == $list[0]['id']) {
                $flag = intval($value['visible']) + intval($value['editable']);
                return $flag;
            }
        }
        return 0;
    }
    
    public function getEditable($uid,$function_name){
    	$sql = "SELECT admin_group_action.* FROM `admin_user_group` ".
				"LEFT JOIN `admin_group_action` ON admin_user_group.group_id = admin_group_action.group_id ".
				"LEFT JOIN `admin_action` ON admin_group_action.action_id = admin_action.id ".
				"WHERE admin_user_group.user_id = $uid and admin_action.function_name=$function_name ORDER BY admin_group_action.action_id ASC";
    	$r = $this->querySQL($sql);
    	return $r;
    }

    public function getAllGroupInfo(){
        $sql = "SELECT * FROM admin_group ORDER BY inner_group";
        $r = $this->querySQL($sql);
        return $r;
    }
    
    /**
     * 用户权限组
     */
    public function getUserGroupInfo($uid) {
        $sql = "SELECT admin_group.*, admin_user_group.group_id FROM `admin_user_group` LEFT JOIN `admin_group`" .
                " ON admin_user_group.group_id = admin_group.id" .
                " WHERE admin_user_group.user_id = $uid";
        $r = $this->querySQL($sql);
        return $r;
    }

    public function updateUserGroupInfo($gid, $where){
        $sql = "update admin_user_group set group_id = $gid where $where";
        $r = $this->queryUpdateSQL($sql);
        return $r;
    }
    
    
    
    /**
     * 用户权限-功能
     */
    public function getUserAction($uid) {
        $sql = "SELECT admin_group_action.* FROM `admin_user_group` LEFT JOIN `admin_group_action`" .
                " ON admin_user_group.group_id = admin_group_action.group_id" .
                " WHERE admin_user_group.user_id = $uid";
        $r = $this->querySQL($sql);
        return $r;
    }

    public function userInsert($data) {
        return $this->insertData($data, 'admin_user');
    }

    public function aclUserGroupInsert($data) {
        return $this->insertData($data, 'admin_user_group');
    }

    public function aclUserGroupDelete($where) {
        return $this->deleteData($where, 'admin_user_group');
    }

    public function aclGroupInsert($data) {
        return $this->insertData($data, 'admin_group');
    }

    /**
     * 获取用户导航条
     * @param bool $status true:获取用户权限菜单
     * 						false：更新用户权限菜单
     */
    public function makeUserMenu($group_id, $status = false) {
        /* if($status){
          $menu = Mem::get('op_user_menu_'.$group_id);
          }else{
          $menu = false;
          } */
        $menu = false;
        $marray = array();
        if (false == $menu) {     //缓存中无菜单列表，调取数据库
            $menu = array();
            $sql = "SELECT admin_group_action.* FROM `admin_user_group` LEFT JOIN `admin_group_action`" .
                    " ON admin_user_group.group_id = admin_group_action.group_id" .
                    " WHERE admin_user_group.group_id = $group_id ORDER BY admin_group_action.action_id ASC";
            $r = $this->querySQL($sql);
            $list = $this->getList(array('`status`' => 1), '', '', 'admin_action');
            foreach ($list AS $key => $value) {
                $actionlist[$value['id']] = $value;
            }
            if (empty($r)) {
                return false;
            } else {
                foreach ($r AS $key => $value) {
                    if (isset($actionlist[$value['action_id']]['group_name']) && !in_array($actionlist[$value['action_id']]['group_name'], $marray)) {
                        $marray[] = $actionlist[$value['action_id']]['group_name'];
                        $ary['name'] = $actionlist[$value['action_id']]['group_name'];    //导航名
                        $ary['submenu'] = $this->getSubMenu($value['group_id'], $actionlist[$value['action_id']]['group_name']);    //获取用户左边菜单
                        $ary['url'] = $ary['submenu'][0]['url'];         //导航链接
                        $menu[] = $ary;
                    }
                }
                //Mem::set('op_user_menu_'.$group_id, $menu);      //缓存菜单
            }
        }

        return $menu;
    }

    /**
     * 获取action操作地址
     */
    public function getActionUrl($action_id) {
        $r = $this->getOne(array('`id`' => $action_id), 'admin_action');
        return $r['url'];
    }

    /**
     * 获取用户左边子菜单
     */
    public function getSubMenu($group_id, $name) {
        $sql = "SELECT admin_action.name, admin_action.url FROM `admin_action`" .
                " LEFT JOIN `admin_group_action` ON admin_action.id = admin_group_action.action_id" .
                " WHERE admin_group_action.group_id = $group_id AND admin_action.group_name = '$name'";
        $r = $this->querySql($sql);
        return $r;
    }

    /**
     * 更新后台管理员信息
     */
    public function updateManagerInfo($data, $uid) {
        $r = $this->updateData($data, array('`id`' => $uid), 'admin_user');
        //Mem::delete('manager_'.$uid);
        return $r;
    }

    /**
     * 登录操作记录
     */
    public function getLoginRecord($where = Null, $order = Null, $limit = Null) {
        return $this->getList($where, $order, $limit, 'admin_login_record');
    }

    public function getManagerAccount($where) {
        return $this->getOne($where, 'admin_user');
    }

    
    /**
     * 权限管理组列表
     */
    public function getGroupList($type = 'list') {
        $data = array();
        $sql = "SELECT inner_group FROM admin_group GROUP BY inner_group ORDER BY id ASC";
        $group = $this->querySQL($sql);
        if ($type == 'select') {
            return $group;
        } else {
            foreach ($group AS $key => $value) {
                $list = $this->getList(array('`inner_group`' => $value['inner_group']), 'id ASC', '', 'admin_group');
                foreach ($list AS $k => $v) {
                    $sql = "SELECT admin_user_group.*, admin_user.realname FROM admin_user_group" .
                            " LEFT JOIN admin_user ON admin_user_group.user_id = admin_user.id" .
                            " WHERE admin_user_group.group_id = " . $v['id'];
                    $v['member'] = $this->querySQL($sql);
                    $value['list'][] = $v;
                }
                $data[] = $value;
            }
            return $data;
        }
    }

    /**
     * 根据分组获取岗位列表
     */
    public function getPostList($group, $field = array()) {
        $default = '`name`';
        if(!empty($field) && is_array($field)){
            $field[] = $default;
            $field = implode(',', $field);
        }
        $this->selectField($field);
        return $this->getList(array('`inner_group`' => $group), 'id ASC', '', 'admin_group');
    }

    public function getManagerInfo($where) {
        return $this->getOne($where, 'admin_user');
    }

    public function getManagerMem($uid) {
        //$user = Mem::get('manager_'.$uid);
        $user = false;
        if (false == $user) {
            $r = $this->getOne(array('`id`' => $uid), 'admin_user');
            $sql = "SELECT admin_group.name,admin_group.inner_group FROM admin_user_group" .
                    " LEFT JOIN admin_group ON admin_user_group.group_id = admin_group.id" .
                    " WHERE admin_user_group.user_id = $uid";
            $res = $this->querySQL($sql);
            $r['post'] = $res[0]['name'];
            $r['inner_group'] = $res[0]['inner_group'];
            //Mem::set('manager_'.$uid, $r);
            $user = $r;
        }
        return $user;
    }

    public function getGroupInfo($where) {
        return $this->getOne($where, 'admin_group');
    }

    /**
     * 管理员列表
     */
    public function getManagerList($where='', $order = '', $limit = '') {
        $this->selectField('`id`,`name`,`realname`,createTime');
        $list = $this->getList($where, $order, $limit, 'admin_user');
        $data = array();
        if (!empty($list)) {
            foreach ($list AS $key => $value) {
                $r = $this->getUserGroupInfo($value['id']);
                $value['inner_group'] = isset($r[0]['inner_group']) ? $r[0]['inner_group'] : '';
                $value['inner_group_id'] = isset($r[0]['id']) ? $r[0]['id'] : '';
                $value['post'] = isset($r[0]['name']) ? $r[0]['name'] : '';
                $data[] = $value;
            }
        }
        return $data;
    }
    
    public function getManagerListByLikeName($name, $limit = '') {
        $sql = 'SELECT `id`,`name`,`realname`,`createTime` FROM admin_user WHERE `realname` like "%'.$name .'%" order by createTime desc';
        if(!empty($limit)){
            $sql .= ' limit ' . $limit[1] . ', ' . $limit[0];
        }
        $list = $this->querySQL($sql);
        $data = array();
        if (!empty($list)) {
            foreach ($list AS $key => $value) {
                $r = $this->getUserGroupInfo($value['id']);
                $value['inner_group'] = isset($r[0]['inner_group']) ? $r[0]['inner_group'] : '';
                $value['inner_group_id'] = isset($r[0]['id']) ? $r[0]['id'] : '';
                $value['post'] = isset($r[0]['name']) ? $r[0]['name'] : '';
                $data[] = $value;
            }
        }
        return $data;
    }

    /**
     * 获取组权限
     */
    public function getActionList($group_id) {
        //$list = Mem::get('action_list');
        $list = false;
        if (false == $list) {
            $list = array();
            $sql = "SELECT group_name FROM admin_action GROUP BY group_name ORDER BY id ASC";
            $group_name = $this->querySQL($sql);
            foreach ($group_name AS $key => $value) {
                $r = $this->getList(array('`group_name`' => $value['group_name']), 'id ASC', '', 'admin_action');
                $value['list'] = $r;
                $list[] = $value;
            }
//			Mem::set('action_list',$list);
        }
        return $list;
    }

    public function getActionByGroup($group_id) {
        return $this->getList(array('`group_id`' => $group_id), '', '', 'admin_group_action');
    }

    public function getAclActionOne($where) {
        return $this->getOne($where, 'admin_action');
    }

    public function getAclAction($where = Null, $order = Null, $limit = Null) {
        return $this->getList($where, $order, $limit, 'admin_action');
    }

    public function delAclAction($where) {
        return $this->deleteData($where, 'admin_action');
    }

    public function getAclActionGroupName() {
        $list = array();
        $sql = "SELECT group_name FROM admin_action GROUP BY group_name ORDER BY id ASC";
        $group_name = $this->querySQL($sql);
        foreach ($group_name AS $value) {
            $list[] = $value['group_name'];
        }

        return $list;
    }

    public function insertAclAction($data) {
       return $this->insertData($data, 'admin_action');
    }

    public function updateAclAction($data, $where) {
       return $this->updateData($data, $where, 'admin_action');
    }

    public function AclGroupActionInsert($data) {
        return $this->insertData($data, 'admin_group_action');
    }

    public function AclGroupActionDelete($where) {
        return $this->deleteData($where, 'admin_group_action');
    }

    public function getAclUserGroup($where, $debug = false) {
        $data = $this->getOne($where, 'admin_user_group');
        if($debug){
            echo $this->lastQuery();
        }
        return $data;
    }

    public function getAclUserGroupList($where = Null, $order = Null, $limit = Null) {
        return $this->getList($where, $order, $limit, 'admin_user_group');
    }

    /**
     * 用户组基本设置更新
     */
    public function aclGroupUpdate($data, $user_id) {
        $where = array('user_id' => $user_id);
        $group_id = $data['group_id'];
        return  $this->userGroupUpdate($data, $where);

    }

    public function userGroupUpdate($data, $where){
        return $this->updateData($data, $where, 'admin_user_group');
    }
    
    /**
     * 删除分组
     */
    public function aclGroupDelete($data) {
        $this->deleteData($data, 'admin_group');
    }

    public function userDelete($where) {
        $this->deleteData($where, 'admin_user');
    }

    public function userUpdate($data, $where) {
        return $this->updateData($data, $where, 'admin_user');
    }

    /**
     * 根据用户ip获取用户地址
     */
    public function convertip($ip) {
        //IP数据文件路径，请根据情况自行修改
        $dat_path = SYSLIBPATH . '/tools/qqwry.dat';
        
        //检查IP地址
        if (!preg_match("/^([0-9]{1,3}.){3}[0-9]{1,3}$/", $ip)) {
            return 'IP Address Error';
        }
        //打开IP数据文件
        if (!$fd = @fopen($dat_path, 'rb')) {
            return 'IP date file not exists or access denied';
        }
        //分解IP进行运算，得出整形数
        $ip = explode('.', $ip);
        $ipNum = $ip[0] * 16777216 + $ip[1] * 65536 + $ip[2] * 256 + $ip[3];
        //获取IP数据索引开始和结束位置
        $DataBegin = fread($fd, 4);
        $DataEnd = fread($fd, 4);
        $ipbegin = implode('', unpack('L', $DataBegin));
        if ($ipbegin < 0)
            $ipbegin += pow(2, 32);
        $ipend = implode('', unpack('L', $DataEnd));
        if ($ipend < 0)
            $ipend += pow(2, 32);
        $ipAllNum = ($ipend - $ipbegin) / 7 + 1;
        $BeginNum = 0;
        $EndNum = $ipAllNum;
        $ipAddr2 = $ipAddr1 = '';
        //使用二分查找法从索引记录中搜索匹配的IP记录
        do {
            $Middle = intval(($EndNum + $BeginNum) / 2);
            //偏移指针到索引位置读取4个字节
            fseek($fd, $ipbegin + 7 * $Middle);
            $ipData1 = fread($fd, 4);
            if (strlen($ipData1) < 4) {
                fclose($fd);
                return 'System Error';
            }
            //提取出来的数据转换成长整形，如果数据是负数则加上2的32次幂
            $ip1num = implode('', unpack('L', $ipData1));
            if ($ip1num < 0)
                $ip1num += pow(2, 32);
            //提取的长整型数大于我们IP地址则修改结束位置进行下一次循环
            if ($ip1num > $ipNum) {
                $EndNum = $Middle;
                continue;
            }
            //取完上一个索引后取下一个索引
            $DataSeek = fread($fd, 3);
            if (strlen($DataSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $DataSeek = implode('', unpack('L', $DataSeek . chr(0)));
            fseek($fd, $DataSeek);
            $ipData2 = fread($fd, 4);
            if (strlen($ipData2) < 4) {
                fclose($fd);
                return 'System Error';
            }
            $ip2num = implode('', unpack('L', $ipData2));
            if ($ip2num < 0)
                $ip2num += pow(2, 32);
            //没找到提示未知
            if ($ip2num < $ipNum) {
                if ($Middle == $BeginNum) {
                    fclose($fd);
                    return 'Unknown';
                }
                $BeginNum = $Middle;
            }
        } while ($ip1num > $ipNum || $ip2num < $ipNum);
        //下面的代码读晕了，没读明白，有兴趣的慢慢读
        $ipFlag = fread($fd, 1);
        if ($ipFlag == chr(1)) {
            $ipSeek = fread($fd, 3);
            if (strlen($ipSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipSeek = implode('', unpack('L', $ipSeek . chr(0)));
            fseek($fd, $ipSeek);
            $ipFlag = fread($fd, 1);
        }
        if ($ipFlag == chr(2)) {
            $AddrSeek = fread($fd, 3);
            if (strlen($AddrSeek) < 3) {
                fclose($fd);
                return 'System Error';
            }
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return 'System Error';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0))
                $ipAddr2 .= $char;
            $AddrSeek = implode('', unpack('L', $AddrSeek . chr(0)));
            fseek($fd, $AddrSeek);
            while (($char = fread($fd, 1)) != chr(0))
                $ipAddr1 .= $char;
        } else {
            fseek($fd, -1, SEEK_CUR);
            while (($char = fread($fd, 1)) != chr(0))
                $ipAddr1 .= $char;
            $ipFlag = fread($fd, 1);
            if ($ipFlag == chr(2)) {
                $AddrSeek2 = fread($fd, 3);
                if (strlen($AddrSeek2) < 3) {
                    fclose($fd);
                    return 'System Error';
                }
                $AddrSeek2 = implode('', unpack('L', $AddrSeek2 . chr(0)));
                fseek($fd, $AddrSeek2);
            } else {
                fseek($fd, -1, SEEK_CUR);
            }
            while (($char = fread($fd, 1)) != chr(0)) {
                $ipAddr2 .= $char;
            }
        }
        fclose($fd);
        //最后做相应的替换操作后返回结果
        if (preg_match('/http/i', $ipAddr2)) {
            $ipAddr2 = '';
        }
        $ipaddr = "$ipAddr1 $ipAddr2";
        $ipaddr = preg_replace('/CZ88.Net/is', '', $ipaddr);
        $ipaddr = preg_replace('/^s*/is', '', $ipaddr);
        $ipaddr = preg_replace('/s*$/is', '', $ipaddr);
        if (preg_match('/http/i', $ipaddr) || $ipaddr == '') {
            $ipaddr = 'Unknown';
        }
        $ipaddr = iconv('GB2312', 'UTF-8', $ipaddr);
        return $ipaddr;
    }

    /**
     * 截取省市信息
     */
    public function ipToArea($string) {
        $data = array(
            'province' => '',
            'city' => ''
        );

        $province_pos = strpos($string, '省');
        if ($province_pos) {
            $data['province'] = substr($string, 0, $province_pos) . '省';
        }
        $city_pos = strpos($string, '市');
        if ($city_pos) {
            if ($province_pos) {
                $data['city'] = substr($string, ($province_pos + 3), ($city_pos - $province_pos - 3)) . '市';
            } else {
                $data['city'] = substr($string, 0, $city_pos) . '市';
            }
        }
        return $data;
    }

    private function getIP() {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $onlineip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $onlineip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $onlineip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $onlineip = $_SERVER['REMOTE_ADDR'];
        }
        if(preg_match("#^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#",$onlineip)){
	        return $onlineip;
	    }else{
	    	$onlineip = $_SERVER['REMOTE_ADDR'];
	    	return $onlineip;
	    }
    }

    //插入一个栏目类型
    public function insertAclGroupType($data) {
        $r = $this->insertData($data, 'admin_group_type');
        return $r;
    }

    //栏目类型名称列表
    public function getAclActionGroupTypeName() {
        $list = array();
        $sql = "SELECT name FROM admin_group_type GROUP BY name ORDER BY id ASC";
        $group_name = $this->querySQL($sql);
        foreach ($group_name AS $value) {
            $list[] = $value['name'];
        }
        return $list;
    }

    //栏目类型列表
    public function getAclGroupType($where = Null, $order = Null, $limit = Null) {
        return $this->getList($where, $order, $limit, 'admin_group_type');
    }

    //修改栏目类型
    public function updateAclGroupType($data, $where) {
        $this->updateData($data, $where, 'admin_group_type');
    }

    //获取一个栏目类型内容
    public function getAclGroupTypeOne($where) {
        return $this->getOne($where, 'admin_group_type');
    }

    //删除栏目类型
    public function delAclGroupType($where) {
        return $this->deleteData($where, 'admin_group_type');
    }

    //操作记录
    public function actionData($username = Null, $action= Null, $id= Null, $model= Null, $ip= Null, $uid= Null,$reason= '') {
        $logdata = array(
            'username' => $username,
            'do_time' => time(),
            'action' => $action,
            'action_id' => $id,
            'model' => $model,
            'ip' => $ip,
            'userid' => $uid,
            'reason' => $reason
        );
        return $this->userActionInsert($logdata);
    }

    //记录用户操作
    public function userActionInsert($data) {
        $r = $this->insertData($data, 'admin_user_action_log');
//            echo $this->lastQuery();
        return $r;
    }

    //栏目类型列表
    public function getLoglist($where = Null, $order = Null, $limit = Null) {
        return $this->getList($where, $order, $limit, 'admin_user_action_log');
    }
    
    //获取某个条件的一条操作日志
    public function getLogInfo($where) {
        return $this->getOne($where, 'admin_user_action_log');
    }
    
    //删除相关条件的数据
    public function aclDeleteLog($where){
        return $this->deleteData($where , 'admin_user_action_log');
    }
    
    //找出相关条件的数据
    public function selectLogBy($where){
        $r = $this->querySQL("SELECT id FROM `admin_user_action_log` WHERE $where");
        return $r;
    }
    
    public function deleteLogBy($where){
        $sql = "DELETE FROM `admin_user_action_log` WHERE " . $where ;
        $r = $this->deleteDataBySql($sql);
        return $r;
    }
    
    //count
    public function getLoglist_count($where = '') {
        return $this->getCount($where, 'admin_user_action_log');
    }
    //根据条件和对应的表，查总数
    public function getList_count($where, $table) {
         $data = $this->getCount($where, $table);
         return $data;
    }
    
        /*
     * 类别列表，默认为友情链接类别
     */

    public function getTypeList($where = '', $order = '', $limit = '', $table = '') {
        $this->selectField('`id`,`name`,`pid`,`depth`,`rank`');
        $list = $this->getList($where, $order, $limit, $table);
        $data = array();
        if (!empty($list)) {
            foreach ($list AS $key => $value) {
                $data[] = $value;
            }
        }
        return $data;
    }
    
    //树状结构列表
    public function getCateList($where = '', $order = '', $limit = '',$table = '') {
        $this->selectField('`id`,`name`,`pid`,`depth`,`rank`');
        $list = $this->getList($where, $order, $limit, $table);
        $data = array();
        if (!empty($list)) {
            foreach ($list AS $key => $value) {
                $data[] = $value;
            }
        }
        return $this->getTypeTree($data);
    }

    public function typeHtml($data, $r_name, $se_value = '', $default_op = '请选择') {
        if (!is_array($data))
            return;
        $drop = "<select id='$r_name' name='$r_name' style='height: 26px;'>";
        $drop .= "<option value='0'>$default_op</option>";
        $selected = "";
        foreach ($data as $val) {
            if ($se_value == $val['id']) {
                $selected = " selected='selected' ";
            }
            $drop.="<option $selected value='$val[id]' pid='$val[pid]'>$val[name]</option>";
            $selected = "";
        }
        $drop.="</select>";
        return $drop;
    }

    public function getChars($depth) {
        $char = '';
        if ($depth > 0) {
            for ($i = 0; $i < $depth; $i++) {
                $char.="&nbsp;";
            }
            $char.='&nbsp;|--';
        }
        return $char;
    }

    public function getTypeTree($ary, $dep = 0, $pid = 0, $aray = array()) {
        $depth = $dep + 1;
        foreach ($ary as $key => $val) {
            if ($val['depth'] == $dep && $val['pid'] == $pid) {
                $c = $this->getChars($val['depth']);
                $val['name'] = $c . $val['name'];
                $aray[] = $val;
                $data = $this->getTypeTree($ary, $depth, $val['id'], $aray);
                $aray = $data;
                if ($depth > 5) {
                    exit('死循环');
                }
            }
        }
        return $aray;
    }

    public function getType($where = '', $order = '', $limit = '', $table = '') {
        $this->selectField('`id`,`name`,`pid`,`depth`');
        $list = $this->getList($where, $order, $limit, $table);
        $html = $this->getTypeTree($list);
        return $html;
    }
    
    //新增一个软件更新信息
    public function insertClient_update($data) {
        return $this->insertData($data, 'client_update');
    }
    
    //修改软件更新信息
    public function updateClient_update($data, $where) {
      return  $this->updateData($data, $where, 'client_update');
    }

    //获取一个软件更新信息
    public function getClient_update($where) {
        return $this->getOne($where, 'client_update');
    }
    
    public function insertAdminUserDev_game($data) {
        $r = $this->insertData($data, 'admin_user_dev_game');
        return $r;
    }
    
    public function delAdminUserDev_game($where) {
        $this->deleteData($where, 'admin_user_dev_game');
    }
    
    public function getTableList($where = Null, $order = Null, $limit = Null,$tableName) {
        return $this->getList($where, $order, $limit, $tableName);
    }
    
    //查询分页分组岗位列表数据
    public function getAdminGroupList($where = NULL, $order_by = NULL ,$limit = NULL, $show_sql = false){

        return $this->getList('admin_group', $where, $order_by, $limit, $show_sql);
    }
}
