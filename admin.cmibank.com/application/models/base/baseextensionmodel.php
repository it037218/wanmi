<?php
//require_once ROOTPATH . DS . APPPATH.'models/base/basemodel.php';
require_once APPPATH.'models/base/basemodel.php';

/**
 * 推广 信息/分类/排期 操作
 *
 * Class Baseextensionmodel
 * @date 2014/07/22 10:08:22
 * @author zhouyang
 */
class Baseextensionmodel extends Basemodel{
    //缓存时间
    const CACHE_NO      = 0;
    const CACHE_SECONDS = 1;
    const CACHE_MINUTE  = 60;
    const CACHE_HOURS   = 3600;         //60*60
    const CACHE_DAY     = 86400;        //60*60*24;
    const CACHE_MONTH   = 2592000;      //60*60*24*30

    //默认分页条数
    const PAGE_LIST     = 10;

    //数据容器
    public $extension_rdb;
    public $extension_wdb;
    public $extension_redis;

    public function __construct() {
        parent::__construct();

        //初始化 db 容器
        $this->extension_rdb    = self::$container['db_r'];
        $this->extension_wdb    = self::$container['db_w'];

        //初始化 redis 容器
        $this->extension_redis  = self::$container['redis_default'];
    }

    /************************推广信息 start**************************/

    /**
     * 根据推广编号，显示排期信息
     *
     * @param $extension_ids
     * @param null $nums
     * @param int $source
     * @return bool
     */
    public function getJoinExtensionListByExtensionids($extension_ids, $nums = null, $source = 1){
        if($source == 1){
            $ret = $this->_getRawJoinExtensionListByExtensionids($extension_ids, $nums);
        }else{
            $ret = $this->_getInitedJoinExtensionListByExtensionids($extension_ids, $nums);
        }

        return $ret;
    }

    /**
     * 根据推广编号，显示排期信息 ->DB
     *
     * @param $extension_ids
     * @return bool
     */
    public function _getRawJoinExtensionListByExtensionids($extension_ids, $nums = null){
        if($extension_ids && (is_numeric($extension_ids) || is_array($extension_ids))){
            if(is_numeric($extension_ids)){
                $sql = "SELECT e.type_id,e.title,e.link,e.img,e.sort,e.status,e.has_date,d.start_time,d.end_time FROM extension_date as d right join extension as e on d.extension_id = e.id  where e.type_id = " . $extension_ids . " order by e.sort desc" . ((int)$nums > 0 ? ' limit 0,'.$nums : '');
            }else{
                $sql = "SELECT e.type_id,e.title,e.link,e.img,e.sort,e.status,e.has_date,d.start_time,d.end_time FROM extension_date as d right join extension as e on d.extension_id = e.id  where e.type_id in (" . implode(',', $extension_ids) . ") order by e.sort desc";
            }

        }else{
            return false;
        }

        $ret = $this->querySql($sql);

        return $ret;
    }

    /**
     * 根据推广编号，显示排期信息 -> redis
     *
     * @param $extension_ids
     * @return bool
     */
    public function _getInitedJoinExtensionListByExtensionids($extension_ids){
        return false;
    }

    /**
     * 分页获取推广信息
     *
     * @param $where
     * @param $page
     * @param $page_list
     * @param $order_by
     * @return mixed
     */
    public function getExtensionList($where = array(), $page = 1, $page_list = null, $order_by = null){
        $ret = $this->_getRawExtensionList($where, $page, $page_list, $order_by);

        return $ret;
    }

    /**
     * 分页获取推广信息 ->DB
     *
     * @param array $where
     * @param int $page
     * @param int $page_list
     * @param null $order_by
     * @return mixed
     */
    public function _getRawExtensionList($where = array(), $page = 1, $page_list = 30, $order_by = null){
        $page               = $page > 0 ? (int)$page : 1;

        $limit              = ($page - 1) * $page_list;
        $ret['curr_page']   = $page;
        $ret['page_list']   = $page_list;

        $ret['list'] = $this->selectDataList('extension', $where, $order_by ,array($page_list, $limit), false);
        $ret['count'] = $this->selectDataCount('extension', $where, false);

        return $ret;
    }

    /**
     * 添加推广信息
     * @param $data
     * @return bool
     */
    public function addExtension($data){
        $ret = $this->_addRawExtension($data);

        return $ret;
    }

    /**
     * 添加推广分类 ->DB
     * @param $data
     * @return bool
     */
    public function _addRawExtension($data){
        $ret = $this->insertData($data , 'extension', false);

        return $ret;
    }

    /**
     * 根据推广信息编号删除删除推广信息
     *
     * @param $extension_id
     * @return bool|mixed
     */
    public function deleteExtensionByExtensionid($extension_id){
        $ret = $this->_deleteRawExtensionByExtensionid($extension_id);

        return $ret;
    }

    /**
     * 根据推广信息编号删除删除推广信息 ->DB
     *
     * @param $extension_id
     * @return bool|mixed
     */
    public function _deleteRawExtensionByExtensionid($extension_id){
        if($extension_id && is_numeric($extension_id)){
            $where['id'] = $extension_id;
        }else{
            return false;
        }

        $ret = $this->deleteData('extension', $where);

        return $ret;
    }

    /**
     * 更新推广信息
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function updateExtension($data, $where){
        $ret = $this->_updateRawExtension($data, $where);

        return $ret;
    }

    /**
     * 更新推广信息 ->DB
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function _updateRawExtension($data, $where){
        if(empty($where)){
            return false;
        }

        $ret = $this->updateData('extension', $data, $where, false);

        return $ret;
    }

    /**
     * 获取推广信息
     * @param $extension_id
     * @return mixed|string
     */
    public function getExtensionInfoByExtensionid($extension_id){
        $info = $this->_getRawExtensionByExtensionid($extension_id);

        return $info;
    }

    /**
     * 获取推广信息-db
     * @param $extension_id
     * @return string
     */
    protected function _getRawExtensionByExtensionid($extension_id){
        if($extension_id && is_numeric($extension_id)){
            $where['id'] = $extension_id;
        }else{
            return false;
        }

        return $this->selectData('extension', $where, false);
    }

    /**
     * 获取推广信息-redis
     * @param $extension_id
     * @return mixed
     */
    protected function _getInitedExtensionByExtensionid($extension_id){

    }

    /**
     * 重置推广信息
     * @param $extension_id
     */
    protected function rebuildExtensionInfoCache($extension_id){
        $key = '' . $extension_id;
        $this->Redis->delete($key);
    }
    /************************推广信息 end**************************/

    /************************推广分类 start**************************/
    /**
     * 分页获取推广分类信息
     *
     * @param $where
     * @param $page
     * @param $page_list
     * @param $order_by
     * @return mixed
     */
    public function getExtensionTypeList($where = array(), $page = 1, $page_list = 30, $order_by = null){
        $ret = $this->_getRawExtensionTypeList($where, $page, $page_list, $order_by);

        return $ret;
    }

    /**
     * 分页获取推广分类信息 ->DB
     *
     * @param array $where
     * @param int $page
     * @param int $page_list
     * @param null $order_by
     * @return mixed
     */
    public function _getRawExtensionTypeList($where = array(), $page = 1, $page_list = 30, $order_by = null){
        $page               = $page > 0 ? (int)$page : 1;

        $limit              = ($page - 1) * $page_list;

        $ret['list'] = $this->selectDataList('extension_type', $where, $order_by ,array($page_list, $limit), false);
        $ret['count'] = $this->selectDataCount('extension_type', $where, false);
        $ret['curr_page']   = $page;
        $ret['page_list']   = $page_list;

        return $ret;
    }

    /**
     * 添加推广分类
     * @param $data
     * @return bool
     */
    public function addExtensionType($data){
        $ret = $this->_addRawExtensionType($data);

        return $ret;
    }

    /**
     * 添加推广分类 ->DB
     * @param $data
     * @return bool
     */
    public function _addRawExtensionType($data){
        $ret = $this->insertData($data , 'extension_type', false);

        return $ret;
    }

    /**
     * 根据推广分类编号删除分类信息
     * @param $type_id
     * @return bool|mixed
     */
    public function deleteExtensionTypeByTypeid($type_id){
        $ret = $this->_deleteRawExtensionTypeByTypeid($type_id);

        return $ret;
    }

    /**
     * 根据推广分类编号删除分类信息 ->DB
     * @param $type_id
     * @return bool|mixed
     */
    public function _deleteRawExtensionTypeByTypeid($type_id){
        if($type_id && is_numeric($type_id)){
            $where['id'] = $type_id;
        }else{
            return false;
        }

        $ret = $this->deleteData('extension_type', $where);

        return $ret;
    }

    /**
     * 更新推广分类信息
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function updateExtensionType($data, $where){
        $ret = $this->_updateRawExtensionType($data, $where);

        return $ret;
    }

    /**
     * 更新推广分类信息 ->DB
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function _updateRawExtensionType($data, $where){
        if(empty($where)){
            return false;
        }

        $ret = $this->updateData('extension_type', $data, $where, false);

        return $ret;
    }

    /**
     * 获取推广分类信息
     * @param $type_id
     * @return mixed
     */
    public function getExtensionTypeByTypeid($type_id){
        $ret = $this->_getRawExtensionTypeByTypeid($type_id);

        return $ret;
    }

    /**
     * 获取推广分类信息 ->DB
     * @param $type_id
     * @return mixed
     */
    public function _getRawExtensionTypeByTypeid($type_id){
        if($type_id && is_numeric($type_id)){
            $where['id'] = $type_id;
        }else{
            return false;
        }

        return $this->selectData('extension_type', $where, false);
    }
    /************************推广分类 end**************************/

    /************************推广排期 start**************************/
    /**
     * 根据推广内容查询推广排期信息
     *
     * @param $extension_id
     * @return mixed
     */
    public function getExtensionDataListByExtensionid($extension_id){
        $ret = $this->_getRawExtensionDataListByExtensionid($extension_id);

        return $ret;
    }

    /**
     * 根据推广内容查询推广排期信息 ->DB
     *
     * @param $extension_id
     * @return mixed
     */
    public function _getRawExtensionDataListByExtensionid($extension_id){
        $ret_list = $this->selectDataList('extension_date', array('extension_id' => $extension_id));

        return $ret_list;
    }
    /**
     * 分页获取推广排期信息
     *
     * @param $where
     * @param $page
     * @param $page_list
     * @param $order_by
     * @return mixed
     */
    public function getExtensionDateList($where = array(), $page = 1, $page_list = null, $order_by = null){
        $ret = $this->_getRawExtensionDateList($where, $page, $page_list, $order_by);

        return $ret;
    }

    /**
     * 分页获取推广排期信息 ->DB
     *
     * @param array $where
     * @param int $page
     * @param int $page_list
     * @param null $order_by
     * @return mixed
     */
    public function _getRawExtensionDateList($where = array(), $page = 1, $page_list = 30, $order_by = null){
        $page               = $page > 0 ? (int)$page : 1;
        $limit              = ($page - 1) * $page_list;

        $ret['list'] = $this->selectDataList('extension_date', $where, $order_by ,array($page_list, $limit), false);
        $ret['count'] = $this->selectDataCount('extension_date', $where, false);
        $ret['curr_page']   = $page;
        $ret['page_list']   = $page_list;

        return $ret;
    }

    /**
     * 添加推广排期
     * @param $data
     * @return bool
     */
    public function addExtensionDate($data){
        $ret = $this->_addRawExtensionDate($data);

        return $ret;
    }

    /**
     * 添加推广排期 ->DB
     * @param $data
     * @return bool
     */
    public function _addRawExtensionDate($data){
        $ret = $this->insertData($data , 'extension_date', false);

        return $ret;
    }

    /**
     * 根据推广排期编号删除排期信息
     * @param $date_id
     * @return bool|mixed
     */
    public function deleteExtensionDateByDateid($date_id){
        $ret = $this->_deleteRawExtensionDateByDateid($date_id);

        return $ret;
    }

    /**
     * 根据推广排期编号删除排期信息 ->DB
     * @param $date_id
     * @return bool|mixed
     */
    public function _deleteRawExtensionDateByDateid($date_id){
        if($date_id && is_numeric($date_id)){
            $where['id'] = $date_id;
        }else{
            return false;
        }

        $ret = $this->deleteData('extension_date', $where);

        return $ret;
    }

    /**
     * 根据推广编号删除排期信息
     * @param $extension_id
     * @return bool|mixed
     */
    public function deleteExtensionDatesByExtensionid($extension_id){
        $ret = $this->_deleteRawExtensionDatesByExtensionid($extension_id);

        return $ret;
    }

    /**
     * 根据推广编号删除排期信息 ->DB
     * @param $extension_id
     * @return bool|mixed
     */
    public function _deleteRawExtensionDatesByExtensionid($extension_id){
        if($extension_id && is_numeric($extension_id)){
            $where['extension_id'] = $extension_id;
        }else{
            return false;
        }

        $ret = $this->deleteData('extension_date', $where);

        return $ret;
    }

    /**
     * 更新推广排期信息
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function updateExtensionDate($data, $where){
        $ret = $this->_updateRawExtensionDate($data, $where);

        return $ret;
    }

    /**
     * 更新推广排期信息 ->DB
     * @param $data
     * @param $where
     * @return bool|mixed
     */
    public function _updateRawExtensionDate($data, $where){
        if(empty($where)){
            return false;
        }

        $ret = $this->updateData('extension_date', $data, $where, false);

        return $ret;
    }

    /**
     * 获取推广排期信息
     * @param $date_id
     * @return mixed
     */
    public function getExtensionDateByDateid($date_id){
        $ret = $this->_getRawExtensionDateByDateid($date_id);

        return $ret;
    }

    /**
     * 获取推广排期信息 ->DB
     * @param $date_id
     * @return mixed
     */
    public function _getRawExtensionDateByDateid($date_id){
        if($date_id && is_numeric($date_id)){
            $where['id'] = $date_id;
        }else{
            return false;
        }

        return $this->selectData('extension_date', $where, false);
    }
    /************************推广排期 end**************************/


    /************************数据库操作 start**************************/
    /**
     * 通用执行sql方法
     * 注：次方法只给多表链表查询使用
     *
     * @param $sql
     * @return bool
     */
    public function querySql($sql){
        //判断sql语句类型
        $sql_type = strtolower(substr($sql, 0, 6));

        switch($sql_type){
            case 'select':
                $this->extension_rdb->limit(1);
                $query = $this->extension_rdb->query($sql);

                return $query->result_array();
                break;
            case 'inster':
                $insert_id = $this->extension_wdb->query($sql);

                if($insert_id == 0){
                    $affect = $this->extension_wdb->affected_rows();
                    if ($affect > 0) {
                        return true;
                    }else{
                        return false;
                    }
                }

                return $insert_id;
                break;
            case 'update':
            case 'delete':
                $this->extension_wdb->query($sql);

                return $this->extension_wdb->affected_rows();
                break;
        }
    }

    /**
     * 统一插入方法
     * @param $data
     * @param $tableName
     * @param bool $show_sql
     * @return bool
     */
    public function insertData($data , $tableName, $show_sql = false){

        $this->extension_wdb->insert($tableName,$data);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->extension_wdb->last_query();
        }

        $insert_id = $this->extension_wdb->insert_id();

        if($insert_id == 0){
            $affect = $this->extension_wdb->affected_rows();
            if ($affect > 0) {
                return true;
            }else{
                return false;
            }
        }

        return $insert_id;
    }

    /**
     * 统一修改方法
     *
     * @param $tableName
     * @param null $data
     * @param null $where
     * @param bool $show_sql
     * @return mixed
     */
    public function updateData($tableName, $data = NULL, $where = NULL, $show_sql = false){

        if (!empty($data)) {
            foreach(($data) as $k => $v){
                if(is_array($v)){
                    if($v['type']){
                        $this->extension_wdb->set($v['field'], $v['field'] . $v['type'] . $v['val'], FALSE);
                    }else{
                        $this->extension_wdb->set($v['field'], $v['val'], FALSE);
                    }

                    unset($data[$k]);
                }
            }
        }

        if(!empty($where)){//加上带上 in 操作判断
            $where_or = array();
            $where_in = array();

            $where_other = array();

            foreach((array)$where as $k=>$v){
                if($k == 'or'){
                    $where_or[] = $v;
                }else{
                    if(is_array($v)){
                        $where_in[$k] = $v;
                    }else{
                        $where_other[$k] = $v;
                    }
                }
                unset($where[$k]);
            }

            if (!empty($where_other)) {
                $this->extension_wdb->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->extension_wdb->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->extension_wdb->or_where($vk, $vv);
                    }
                }
            }
        }

        $this->extension_wdb->update($tableName, $data);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->extension_wdb->last_query();
        }

        return $this->extension_wdb->affected_rows();
    }

    /**
     * 统一删除方法
     * @param $tableName
     * @param $where
     * @param $show_sql
     * @return mixed
     */
    public function deleteData($tableName, $where = NULL, $show_sql = false){

        if($where){
            $where_or = array();
            $where_in = array();

            $where_other = array();

            foreach((array)$where as $k=>$v){
                if($k == 'or'){
                    $where_or[] = $v;
                }else{
                    if(is_array($v)){
                        $where_in[$k] = $v;
                    }else{
                        $where_other[$k] = $v;
                    }
                }
                unset($where[$k]);
            }

            if (!empty($where_other)) {
                $this->extension_wdb->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->extension_wdb->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->extension_wdb->or_where($vk, $vv);
                    }
                }
            }
        }else{//没有条件是返回空，避免误操作
            return false;
        }

        $this->extension_wdb->delete($tableName);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->extension_wdb->last_query();
        }

        return $this->extension_wdb->affected_rows();
    }

    /**
     * 获取单条数据
     * @param $tableName
     * @param $where
     * @param null $order_by
     * @param bool $show_sql
     * @return mixed
     */
    public function selectData($tableName, $where = NULL, $order_by = null, $show_sql = false){
        $this->extension_rdb->from($tableName);

        if(!empty($where)){//加上带上 in 操作判断
            $where_or = array();
            $where_in = array();

            $where_other = array();

            foreach((array)$where as $k=>$v){
                if($k == 'or'){
                    $where_or[] = $v;
                }else{
                    if(is_array($v)){
                        $where_in[$k] = $v;
                    }else{
                        $where_other[$k] = $v;
                    }
                }
                unset($where[$k]);
            }

            if (!empty($where_other)) {
                $this->extension_rdb->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->extension_rdb->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->extension_rdb->or_where($vk, $vv);
                    }
                }
            }
        }

        if(!empty($order_by)){
            if(is_array($order_by)){
                foreach((array)$order_by as $k=>$v){
                    if($v){
                        $this->extension_rdb->order_by(trim($k), $v);
                    }else{
                        $this->extension_rdb->order_by(trim($k));
                    }
                }
            }else{
                $this->extension_rdb->order_by(trim($order_by));
            }
        }

        if(!empty($limit)){
            $this->extension_rdb->limit(1);
        }
        $query = $this->extension_rdb->get();

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->extension_rdb->last_query();
        }

        $ret = $query->row_array();

        return $ret;
    }

    /**
     * 查询分页列表数据
     * @param $tableName
     * @param null $where
     * @param null $order_by
     * @param null $limit
     * @param bool $show_sql
     * @return mixed
     */
    public function selectDataList($tableName, $where = NULL, $order_by = NULL ,$limit = NULL, $show_sql = false){

        $this->extension_rdb->from($tableName);

        if(!empty($where)){//加上带上 in 操作判断
            $where_or = array();
            $where_in = array();

            $where_other = array();

            foreach((array)$where as $k=>$v){
                if($k == 'or'){
                    $where_or[] = $v;
                }else{
                    if(is_array($v)){
                        $where_in[$k] = $v;
                    }else{
                        $where_other[$k] = $v;
                    }
                }
                unset($where[$k]);
            }

            if (!empty($where_other)) {
                $this->extension_rdb->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->extension_rdb->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->extension_rdb->or_where($vk, $vv);
                    }
                }
            }
        }

        if(!empty($order_by)){
            if(is_array($order_by)){
                foreach((array)$order_by as $k=>$v){
                    if($v){
                        $this->extension_rdb->order_by(trim($k), $v);
                    }else{
                        $this->extension_rdb->order_by(trim($k));
                    }
                }
            }else{
                $this->extension_rdb->order_by(trim($order_by));
            }
        }

        if(!empty($limit)){
            $this->extension_rdb->limit($limit['0'],$limit['1']);
        }

        $query = $this->extension_rdb->get();

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->extension_rdb->last_query();
        }

        $ret = $query->result_array();

        return $ret;
    }

    /**
     * 获取记录总数
     * @param $tableName
     * @param string $where
     * @param bool $show_sql
     * @return mixed
     */
    public function selectDataCount($tableName, $where = NULL, $show_sql = false){

        if(!empty($where)){//加上带上 in 操作判断
            $where_or = array();
            $where_in = array();

            $where_other = array();

            foreach((array)$where as $k=>$v){
                if($k == 'or'){
                    $where_or[] = $v;
                }else{
                    if(is_array($v)){
                        $where_in[$k] = $v;
                    }else{
                        $where_other[$k] = $v;
                    }
                }
                unset($where[$k]);
            }

            if (!empty($where_other)) {
                $this->extension_rdb->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->extension_rdb->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->extension_rdb->or_where($vk, $vv);
                    }
                }
            }
        }

        $ret = $this->extension_rdb->count_all_results($tableName);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->extension_rdb->last_query();
        }

        return $ret;
    }
    /************************数据库操作 end**************************/
}

