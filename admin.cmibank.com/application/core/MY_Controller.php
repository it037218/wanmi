<?php

require_once APPPATH . 'libraries/base.lib.php';

/**
 * controller
 */
class Controller extends baseController {
    //cookie 前缀
    const COOKIE_PREFIX     = 'admin_cookie_';

    //返回值状态
    const AJ_RET_SUCC = 200;
    const AJ_RET_FAIL = 300;
    const AJ_RET_FORB = 300;
    const AJ_RET_NOLOGIN = 301;


    // 返回值类型
    const JSON = 'application/json';
    const HTML = 'text/html';
    const JAVASCRIPT = 'text/javascript';
    const JS = 'text/javascript';
    const TEXT = 'text/plain';
    const XML = 'text/xml';

    public function __construct() {

        parent :: __construct();
        $session_name = session_name();
        
        if (isset($_POST[$session_name])) {
            session_id($_POST[$session_name]);
        }
        
        if (!isset($_SESSION)){
            session_start();
        }
        
        $this->load->model('admin_base_model', 'op');
        $uid = $this->getSession('uid');
//         $this->uid=$uid;
//         if($this->uid){
//             $this->load->model('base/lock_base', 'lock_base');
        
//             $lock_ret = $this->lock_base->addredislock($this->uid, $this->uri->uri_string);
//             $this->lock_request = true;
//             if(!$lock_ret){
//                 exit($this->ajaxDataReturn(self::AJ_RET_FAIL,'请求太快了！',array(),''));
//             }
//         }
        if (false != $uid && false != $this->getSession('is_logged_in')) {
            $r = $this->op->getAclUserGroup(array('`user_id`' => $uid));
            if ($r) {
                $this->menu = $this->op->makeUserMenu($r['group_id']);
                if($_SERVER['SERVER_ADDR'] == '127.0.0.1'){
                    foreach ($this->menu as $key => $detail_arr) {
                        $this->menu[$key]['url'] = OP_DOMAIN . $this->menu[$key]['url'];
                        foreach ($detail_arr['submenu'] as $sub_key => $val) {
                           $this->menu[$key]['submenu'][$sub_key]['url'] = OP_DOMAIN . $this->menu[$key]['submenu'][$sub_key]['url'];
                        }
                    }
                }
                
            } else {
                $this->menu = false;
            }
            $this->manager_info = $this->op->getManagerMem($uid);
           
        } else {
            $this->menu = false;
        }
    }

    public function getGroupSons($group, $is_json = true){
        $flag = $this->op->checkUserAuthority('权限管理', $this->getSession('uid'));   //检测用户操作权限
        if ($flag == 0) {
            $this->Error('没有权限', OP_DOMAIN . 'system');
        } else {
            if (false == $group) {
                $this->Error('未知ID', OP_DOMAIN . 'authority/index');
            }
            $sons = $this->op->getPostList(urldecode($group), array('`id`'));
            $rtn = array();
            if(!empty($sons)){
                foreach($sons as $key=>$val){
                    $rtn[$key][0] = $val['id'];
                    $rtn[$key][1] = $val['name'];
                }
            }
            if($is_json){
                echo json_encode($rtn);
                exit;
            }else{
                return $rtn;
            }
    
        }
    }
    
    /**
     * login validate
     */
    protected function _validate() {
        return (bool) $this->user_info;
    }

    protected function getDefaultData($flag, $nav) {
        $data['managerInfo'] = $this->manager_info;
        $data['flag'] = $flag;
        $data['menu'] = $this->menu;
        if(!empty($this->submenu)){
            $data['submenu'] = $this->submenu;
        }
        $data['nav'] = $nav;
        return $data;
    }

    /**
     * 修改 分页方法
     * @param  $ajaxmethod -- ajax方法时，传的是function名 url时传的是分页前的部分url  如 http://abc.com/s.php?page=1&id=6则传值"http://abc.com/s.php?page=" 若带/的，把/也需传入，如http://abc.com/p/1/abc则传值为"http://abc.com/p/"  后半部分用$lasturl传入
     * @param  $total_rows -- 总记录的条数
     * @param  $cur_page   -- 当前的页码
     * @param  $per_page   -- 每页的条数
     * @param  $ftype-0-ajax分页,1-URL分页
     * @param  $lasturl url链接时用到（分页的后半部分）
     * 
     * <div class="page"><span class="disabled">上一页</span><span class="current">1</span><a href="/system/index/2">2</a><a href="/system/index/3">3</a><a href="/system/index/2">下一页</a></div>
     * 
     * * */
    public function returnpagenum($ajaxmethod, $total_rows, $cur_page, $per_page = 10, $ftype = 0, $lasturl = '') {
        $allpagenum = ceil($total_rows / $per_page);
        $vp = '<div class="page">';
        if ($cur_page > 1) {
            if ($ftype == 0) {
                $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . ($cur_page - 1) . ');">上一页</a> ';
            } else {
                $vp .= '<a href="' . $ajaxmethod . ($cur_page - 1) . $lasturl . '">上一页</a> ';
            }
        } else {
            $vp .= '<span class="disabled">上一页</span> ';
        }
        if ($allpagenum <= 5) {
            for ($i = 1; $i <= $allpagenum; $i++) {
                if ($i == $cur_page) {
                    $vp .= '<span class="current">' . $i . '</span>';
                } else {
                    if ($ftype == 0) {
                        $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $i . ');">' . $i . '</a> ';
                    } else {
                        $vp .= '<a href="' . $ajaxmethod . $i . $lasturl . '">' . $i . '</a> ';
                    }
                }
            }
        } else {
            if ($cur_page == 1 || $cur_page == 2) {
                for ($i = 1; $i < 4; $i++) {
                    if ($i == $cur_page) {
                        $vp .= '<span class="current">' . $i . '</span>';
                    } else {
                        if ($ftype == 0) {
                            $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $i . ');">' . $i . '</a> ';
                        } else {
                            $vp .= '<a href="' . $ajaxmethod . $i . $lasturl . '">' . $i . '</a> ';
                        }
                    }
                }
                $vp .= '<span>...</span> ';
                if ($ftype == 0) {
                    $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $allpagenum . ');">' . $allpagenum . '</a> ';
                } else {
                    $vp .= '<a href="' . $ajaxmethod . $allpagenum . $lasturl . '">' . $allpagenum . '</a> ';
                }
            } else if ($cur_page >= $allpagenum || $cur_page == ($allpagenum - 1)) {
                if ($ftype == 0) {
                    $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(1);">1</a> ';
                } else {
                    $vp .= '<a href="' . $ajaxmethod . '1' . $lasturl . '">1</a> ';
                }
                $vp .= '<span>...</span> ';
                for ($i = $allpagenum - 2; $i <= $allpagenum; $i++) {
                    if ($i == $cur_page) {
                        $vp .= '<span class="current">' . $i . '</span>';
                    } else {
                        if ($ftype == 0) {
                            $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $i . ');">' . $i . '</a> ';
                        } else {
                            $vp .= '<a href="' . $ajaxmethod . $i . $lasturl . '">' . $i . '</a> ';
                        }
                    }
                }
            } else if ($cur_page == 3) {
                for ($i = 1; $i <= 4; $i++) {
                    if ($i == $cur_page) {
                        $vp .= '<a class="now" style="cursor:pointer;">' . $i . '</a>';
                    } else {
                        if ($ftype == 0) {
                            $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $i . ');">' . $i . '</a> ';
                        } else {
                            $vp .= '<a href="' . $ajaxmethod . $i . $lasturl . '">' . $i . '</a> ';
                        }
                    }
                }
                $vp .= '<span>...</span> ';
                if ($ftype == 0) {
                    $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $allpagenum . ');">' . $allpagenum . '</a> ';
                } else {
                    $vp .= '<a href="' . $ajaxmethod . $allpagenum . $lasturl . '">' . $allpagenum . '</a> ';
                }
            } else if ($cur_page == ($allpagenum - 2)) {
                if ($ftype == 0) {
                    $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(1);">1</a> ';
                } else {
                    $vp .= '<a href="' . $ajaxmethod . '1' . $lasturl . '">1</a> ';
                }
                $vp .= '<span>...</span> ';
                for ($i = $allpagenum - 3; $i <= $allpagenum; $i++) {
                    if ($i == $cur_page) {
                        $vp .= '<span class="current">' . $i . '</span>';
                    } else {
                        if ($ftype == 0) {
                            $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $i . ');">' . $i . '</a> ';
                        } else {
                            $vp .= '<a href="' . $ajaxmethod . $i . $lasturl . '">' . $i . '</a> ';
                        }
                    }
                }
            } else {
                $from = $cur_page - 1;
                $get = $cur_page + 1;
                if ($ftype == 0) {
                    $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(1);">1</a> ';
                } else {
                    $vp .= '<a href="' . $ajaxmethod . '1' . $lasturl . '">1</a> ';
                }
                $vp .= '<span>...</span> ';
                for ($i = $from; $i <= $get; $i++) {
                    if ($i == $cur_page) {
                        $vp .= '<span class="current">' . $i . '</span>';
                    } else {
                        if ($ftype == 0) {
                            $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $i . ');">' . $i . '</a> ';
                        } else {
                            $vp .= '<a href="' . $ajaxmethod . $i . $lasturl . '">' . $i . '</a> ';
                        }
                    }
                }
                $vp .= '<span>...</span> ';
                if ($ftype == 0) {
                    $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . $allpagenum . ');">' . $allpagenum . '</a> ';
                } else {
                    $vp .= '<a href="' . $ajaxmethod . $allpagenum . $lasturl . '">' . $allpagenum . '</a> ';
                }
            }
        }
        if ($cur_page < $allpagenum) {
            if ($ftype == 0) {
                $vp .= '<a href="javascript:void(0);" onclick="' . $ajaxmethod . '(' . ($cur_page + 1) . ');">下一页</a> ';
            } else {
                $vp .= '<a href="' . $ajaxmethod . ($cur_page + 1) . $lasturl . '">下一页</a> ';
            }
        } else {
            $vp .= '<a href="javascript:void(0);" >下一页</a> ';
        }
//		$vp .= "</div>";
        $vp .= '&nbsp;共' . $allpagenum . '页&nbsp;共' . $total_rows . '条&nbsp;</div>';
        return $vp;
    }

    /**
     * Enter description here...
     *
     * @param $sqlarray 数组 array('col1' => '123','col2' => '231')
     * @param $table 表名
     * @param $type 生成的sql类型($type=0:select;1:insert;2:update;3:delete)
     * @return sql
     */
    public function createsql($sqlarray = array(), $table = "", $type = 0, $where = "") {
        if ($table == "") {
            return "";
        } elseif (($type == 0 || $type == 2 || $type == 3) && $where == "") {
            return "";
        } else {
            $sqlstrcol = "";
            $sqlstrvalue = "";
            if (is_array($sqlarray) && count($sqlarray) > 0) {
                foreach ($sqlarray as $k => $v) {
                    $v = trim($v);
                    if ($v <> "") {
                        $v = $this->mysqlstring($v);
                        if ($type == 0) {
                            if ($sqlstrcol == "") {
                                $sqlstrcol = "" . $k . "";
                            } else {
                                $sqlstrcol .= " , " . $k . "";
                            }
                        } elseif ($type == 1) {  //insert
                            if ($sqlstrcol == "") {
                                $sqlstrcol = "" . $k . "";
                            } else {
                                $sqlstrcol .= " , " . $k . "";
                            }
                            if ($sqlstrvalue == "") {
                                $sqlstrvalue = "'" . $v . "'";
                            } else {
                                $sqlstrvalue .= " , '" . $v . "'";
                            }
                        } elseif ($type == 2) {
                            if ($sqlstrcol == "") {
                                $sqlstrcol = "" . $k . " = '" . $v . "'";
                            } else {
                                $sqlstrcol .= " , " . $k . " = '" . $v . "'";
                            }
                        } elseif ($type == 3) {
                            $sqlstrcol = "";
                        }
                    }
                }
            }
            if ($type == 0) {
                if ($sqlstrcol == "") {
                    $sqlstrcol = "*";
                }
                if ($where == "") {
                    $sqlstr = "select " . $sqlstrcol . " from " . $table;
                } else {
                    $sqlstr = "select " . $sqlstrcol . " from " . $table . " where " . $where;
                }
            } elseif ($type == 1) {
                if ($sqlstrcol <> "" && $sqlstrvalue <> "") {
                    $sqlstr = "insert into " . $table . " (" . $sqlstrcol . ") values (" . $sqlstrvalue . ")";
                } else {
                    return "";
                }
            } elseif ($type == 2) {
                if ($sqlstrcol <> "") {
                    if ($where == "") {
                        return "";
                    } else {
                        $sqlstr = "update " . $table . " set " . $sqlstrcol . " where " . $where;
                    }
                } else {
                    return $sqlstr;
                }
            } elseif ($type == 3) {
                if ($where == "") {
                    return "";
                } else {
                    $sqlstr = "delete from " . $table . " where " . $where;
                }
            }
            //记录出log
            return $sqlstr;
        }
    }

    //sql
    public function mysqlstring($str = "") {
        if (trim($str) == "") {
            return "";
        } else {
            $str = addslashes($str);
            return $str;
        }
    }
   
    //一个页面多地方上传方法
    public function img_upload() {
        $file_name = $this->input->get_post('file_name');

        if ($_FILES[$file_name]) {
            include_once ROOTPATH . DS . APPPATH. 'libraries/curl.lib.php';
            $path = $_FILES[$file_name]['tmp_name'];
            $uid = $this->getSession('uid');
            $imagepost = base64_encode(file_get_contents($path));
            $w_h_arr = array();
            $para = array(
                'uid' => $uid,
                'imagepost' => $imagepost,
                'method' => 'post',
                'jsonwh' => json_encode($w_h_arr),
                'folder' => "upload"
            );
            $curlobj = new Curl();
            $r = $curlobj->post(UPLOAD_IMAGE, $para);
            $re_arr = json_decode($r, true);
            if ($re_arr['error'] == 0 && $re_arr['reason'] == 0) {
                $json['err'] = 0;
                $json['file'] = $re_arr['picarr']['url'][0];
                $json['filename'] = $re_arr['picarr']['url'][0];
            } else {
                $json['err'] = 1;
                $json['error'] = $re_arr['reason'];
            }
        } else {
            $json['err'] = 2;
            $json['error'] = '获取上传文件信息失败';
        }

        if($file_name == 'filedata'){
            echo json_encode(array('err' => '', 'msg' => $re_arr['picarr']['url'][0]));
        }else{
            echo $re_arr['picarr']['url'][0];
        }

    }
    
    public function doGenerateHtml() {
    	if ($_FILES['titlepic_file']) {
    		//             include_once ROOTPATH . DS . APPPATH . 'libraries/curl.lib.php';
    		include_once APPPATH . 'libraries/curl.lib.php';
    		$path = $_FILES['titlepic_file']['tmp_name'];
    		$uid = $this->getSession('uid');
    		$imagepost = base64_encode(file_get_contents($path));
    		$w_h_arr = array();
    		$para = array(
    				'uid' => $uid,
    				'imagepost' => $imagepost,
    				'method' => 'generateHtml',
    				'jsonwh' => json_encode($w_h_arr),
    				'folder' => "upload",
    				'title' => $_POST['title']
    		);
    		$curlobj = new Curl();
    		$r = $curlobj->post(UPLOAD_IMAGE, $para);
    		$re_arr = json_decode($r, true);
    		if ($re_arr['error'] == 0 && $re_arr['reason'] == 0) {
    			$json['err'] = 0;
    			$json['file'] = $re_arr['picarr'];
    			$json['filename'] = $re_arr['picarr'];
    		} else {
    			$json['err'] = 1;
    			$json['error'] = $re_arr['reason'];
    		}
    		echo $re_arr['picarr'];
    	} else {
    		$json['err'] = 2;
    		$json['error'] = '获取上传文件信息失败';
    		echo json_encode($json);
    	}
    	 
    }
    
    public function doUpload() {
        if ($_FILES['titlepic_file']) {
//             include_once ROOTPATH . DS . APPPATH . 'libraries/curl.lib.php';
            include_once APPPATH . 'libraries/curl.lib.php';
            $path = $_FILES['titlepic_file']['tmp_name'];            
            $uid = $this->getSession('uid');
            $imagepost = base64_encode(file_get_contents($path));
            $w_h_arr = array();
            $para = array(
                'uid' => $uid,
                'imagepost' => $imagepost,
                'method' => 'post',
                'jsonwh' => json_encode($w_h_arr),
                'folder' => "upload"
            );
            $curlobj = new Curl();
            $r = $curlobj->post(UPLOAD_IMAGE, $para);           
            $re_arr = json_decode($r, true);
            if ($re_arr['error'] == 0 && $re_arr['reason'] == 0) {
                $json['err'] = 0;
                $json['file'] = $re_arr['picarr']['url'][0];
                $json['filename'] = $re_arr['picarr']['url'][0];
            } else {
                $json['err'] = 1;
                $json['error'] = $re_arr['reason'];
            }
            echo $re_arr['picarr']['url'][0];
        } else {
            $json['err'] = 2;
            $json['error'] = '获取上传文件信息失败';
            echo json_encode($json);
        }
       
    }

    /**
     * 公共返回数据处理方法
     *
     * @param int $code
     * @param null $msg
     * @param array $data
     * @param null $rel
     * @param null $url
     * @param string $callbackType
     * @param string $data_type
     * @return string
     */
    public function ajaxDataReturn($code = Controller::AJ_RET_SUCC, $msg = null, $data = array(), $rel = null, $callbackType = 'closeCurrent', $url = null, $data_type = Controller::JSON) {
        if ($data_type !== null) {
            header("Content-type: " . $data_type);
        }

        if (is_object($data)) {
            $data = get_object_vars($data);
        } else if (!is_array($data)) {
            $data = array(
                'flag' => $data
            );
        }

        $data['statusCode'] = $code;
        $data['message'] = $msg;
        $data['navTabId'] = md5($rel);
        $data['rel'] = md5($rel);

        if ($callbackType != 'no') {
            $data['callbackType'] = $callbackType;

            if ($callbackType == 'forward' && $url) {
                $data['forwardUrl'] = $url;
            } else {
                $data['callbackType'] = 'closeCurrent';
            }
        }
        
//         if($this->lock_request == true){
//             $this->load->model('base/lock_base', 'lock_base');
//             $lock_ret = $this->lock_base->delredislock($this->uid, $this->uri->uri_string);
//         }
        return htmlspecialchars(json_encode($data), ENT_NOQUOTES);
    }

    public function ajaxDataReturnParams($code = Controller::AJ_RET_SUCC, $msg = null, $data = array(), $rel = null, $callbackType = 'closeCurrent', $url = null, $data_type = Controller::JSON) {
        if ($data_type !== null) {
            header("Content-type: " . $data_type);
        }
    
        if (is_object($data)) {
            $data = get_object_vars($data);
        } else if (!is_array($data)) {
            $data = array(
                'flag' => $data
            );
        }
    
        $data['statusCode'] = $code;
        $data['message'] = $msg;
        $data['navTabId'] = md5($rel);
        $data['rel'] = md5($rel);
    
        if ($callbackType != 'no') {
            $data['callbackType'] = $callbackType;
    
            if ($callbackType == 'forward' && $url) {
                $data['forwardUrl'] = $url;
            } else {
                $data['callbackType'] = 'closeCurrent';
            }
        }
        return json_encode($data);
    }
    
    /**
     * 设置cookie值
     *
     * @param $key
     * @param $value
     * @param null $time
     */
    public function setAdminCookie($key, $value, $time = null){
        if($time == null){
            $time = time() + 60*60*24*7;
        }else{
            $time = time() + $time;
        }
        setcookie(Controller::COOKIE_PREFIX.$key, $value, $time, '/' , 'xyzs.com');
    }

    /**
     * 获取cookie值
     *
     * @param $key
     * @return mixed]
     */
    public function getAdminCookie($key){
        $key = $this->input->cookie(Controller::COOKIE_PREFIX.$key, true);

        return $key;
    }

    /**
     * 删除cookies方法
     *
     * @param $keys
     */
    public function delAdminCookie($keys){
        if(is_array($keys)){
            foreach((array)$keys as $v){
                setcookie(Controller::COOKIE_PREFIX.$v, "", time()-60*60*24*60, '/' , 'xyzs.com');
            }
        }else{
            setcookie(Controller::COOKIE_PREFIX.$keys, "", time()-60*60*24*60, '/' , 'xyzs.com');
        }
    }
    
    public function count_profit($starttime, $endtime, $income, $money){
        $days = (($endtime - $starttime)/86400) + 1;
        return round(($income/100/360 * $days * $money), 2);
    }

}