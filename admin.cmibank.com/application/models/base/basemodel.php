<?php
require_once BASEPATH . '/core/Model.php';
# 引入 Redis 类
//require (ROOTPATH . DS . APPPATH . 'libraries/Featureredis.php');
require (APPPATH . 'libraries/Featureredis.php');

# 引入 Memcache 类
//require (ROOTPATH . DS . APPPATH . 'libraries/Featurememcache.php');
require (APPPATH . 'libraries/Featurememcache.php');

# 引入 pimple
// require (ROOTPATH . DS . APPPATH . 'libraries/Pimple.php');
require (APPPATH . 'libraries/Pimple.php');

class Basemodel extends CI_Model {
    //缓存时间
    const CACHE_NO          = 0;
    const CACHE_SECONDS     = 1;
    const CACHE_MINUTE      = 60;
    const CACHE_TEN_MINUTE  = 600;
    const CACHE_HOURS       = 3600;//60*60
    const CACHE_DAY         = 86400;//60*60*24;
    const CACHE_MONTH       = 2592000;//60*60*24*30

    //默认分页条数
    const PAGE_LIST     = 10;

	# 初始化 container 容器
	public static $container;

	public $DBR;
	public $DBW;
	public $Redis;

	function __construct() {
		parent::__construct();
		if (empty(self::$container)) $this->getInstanceServer(); 

		$this->DBR = self::$container['db_r'];
		$this->DBW = self::$container['db_w'];
		$this->Redis = self::$container['redis_default'];
	}

	public function getInstanceServer() {
	    
		$container = new Pimple();
		# 兼容 php 5.3+版本
		$self = $this;
        
		$container['redis_default'] = $container->share(function() use ($self) {
		    return $self->getRedisServer('redis_default');
		});
		
	    # 初始化 redis_default_read 机器
	    $container['redis_default_read'] = $container->share(function() use ($self) {
	        return $self->getRedisServer('redis_default_read');
	    });
		
		# 初始化 redis_app 读 机器
		$container['redis_app_r'] = $container->share(function() use ($self) {
			return $self->getRedisServer('redis_app' , 'read');
		});

		# 初始化 redis_app 写 机器
		$container['redis_app_w'] = $container->share(function() use ($self) {
			return $self->getRedisServer('redis_app' , 'write');
		});
        
	    # 初始化 redis_tx 腾讯云REDIS测试机
// 	    $container['redis_tx'] = $container->share(function() use ($self) {
// 	        return $self->getRedisServer('redis_tx');
// 	    });
		
		# 初始化 读 db机器
		$container['db_r'] = $container->share(function() use ($self) {
			return $self->load->database('dbr', true);
		});

		# 初始化 写 db机器
		$container['db_w'] = $container->share(function() use ($self) {
			return $self->load->database('dbw', true);
		});

//         # 初始化 memcache server
//         $container['memcache_server'] = $container->share(function() use ($self) {
//             return $self->getMemcacheServer();
//         });

        if ($this->config->load('cacheparams', true, true)) {
			$container['cacheParams'] = $this->config->item('cacheparams');
			$this->cacheParams = $this->config->item('cacheparams');
		}

		self::$container = $container;

		return $container;
	}

	public function getRedisServer($name = 'redis_default' , $ismixed = 'default') {
		if ($this->config->load('redis', true, true)) {
			$cacheConfig = $this->config->item('redis'); 
			if ( ! empty($cacheConfig['redis_default'])) {
				if ( ! empty($cacheConfig[$name])) {
                    
					if (('default' == $ismixed) OR ('write' == $ismixed)) { #获取的是混合类型
						$defineConfig = $cacheConfig[$name][$ismixed];

					} else {
						$mixedConfig = $cacheConfig[$name][$ismixed];
						if('read' == $ismixed) { #为读服务器
							$index = array_rand($mixedConfig,  1);
							$defineConfig = $mixedConfig[$index];
						} else {
							$defineConfig = $mixedConfig;
						}
					}
					
					$config = array_merge($cacheConfig['redis_default']['default'] , $defineConfig);
					return new Featureredis($config);
				} else {
					log_message('debug', 'Cache: Redis connection refused. No such name for Redis Server . Check the config.');
				}
			} else {
				log_message('debug', 'Cache: Redis connection refused. No default Redis Server . Check the config.');
			}
		}
	}

    public function getMemcacheServer(){
    	if ($this->config->load('cache', true, true)) {

            $cacheConfig = $this->config->item('cache');

            if ( ! empty($cacheConfig['memcache_server'])) {

                $config = $cacheConfig['memcache_server'];

                return new Featurememcache($config);

            } else {
                //log_message('debug', 'Cache: Redis connection refused. No such name for Redis Server . Check the config.');
            }
        } else {
            //log_message('debug', 'Cache: Redis connection refused. No default Redis Server . Check the config.');
        }
    }

	//@uses 获取数据库读服务
	public function getDBRServer() {
		return self::$container['db_r'];
	}

	//@uses 获取数据库写服务
	public function getDBWServer() {
		return self::$container['db_w'];
	}

	//@uses 获取所有服务
	public function getServerContainer() {
		return self::$container;
	}

	/**
	 *  cache remember 方法
	 *
	 * @param  string   $key
	 * @param  int      $minutes
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function remember($key, $minutes, Closure $callback , $type = _REDIS_DATATYPE_STRING , $redisR= '' , $redisW = '') {

		$redisR = $redisR ? $redisR : self::$container['redis_default'];
		$redisW = $redisW ? $redisW : $redisR;

		switch ($type) {
    		case _REDIS_DATATYPE_STRING:
    			$cache = $redisR->get($key);
    			break;
    
    		case _REDIS_DATATYPE_HASH:
    			$cache = $redisR->hashGet($key);
    		default:
    			break;
		}

		$isclear = $this->input->get_post('isclear'); #post get都支持
		$cache = (1 == $isclear) ? false : $cache;

		if ($cache) return $cache;
		$value = $callback();
		if(!empty($value)){
    		switch ($type) {
                case _REDIS_DATATYPE_STRING:                    
                    $redisW->save($key, $value , $minutes);
                    break;
                case _REDIS_DATATYPE_HASH:                
                    $redisW->hashSet($key, $value);
                    break;
                default:
                    break;
    		}
		}
		return $value;
	}

    /************************数据库操作 start**************************/
    /**
     * 通用执行sql方法
     * 注：次方法只给多表链表查询使用
     *
     * @param $sql
     * @return bool
     */
    public function executeSql($sql){
        //判断sql语句类型
        $sql_type = strtolower(substr($sql, 0, 6));

        switch($sql_type){
            case 'select':
                //$this->DBR->limit(1);
                $query = $this->DBR->query($sql);

                return $query->result_array();
                break;
            case 'inster':
                $insert_id = $this->DBW->query($sql);

                if($insert_id == 0){
                    $affect = $this->DBW->affected_rows();
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
                $this->DBW->query($sql);

                return $this->DBW->affected_rows();
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
    public function insertDataSql($data , $tableName, $show_sql = false){

        $this->DBW->insert($tableName,$data);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->DBW->last_query();
        }

        $insert_id = $this->DBW->insert_id();

        if($insert_id == 0){
            $affect = $this->DBW->affected_rows();
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
    public function updateDataSql($tableName, $data = NULL, $where = NULL, $show_sql = false){

        if (!empty($data)) {
            foreach(($data) as $k => $v){
                if(is_array($v)){
                    if($v['type']){
                        $this->DBW->set($v['field'], $v['field'] . $v['type'] . $v['val'], FALSE);
                    }else{
                        $this->DBW->set($v['field'], $v['val'], FALSE);
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
                $this->DBW->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->DBW->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->DBW->or_where($vk, $vv);
                    }
                }
            }
        }

        $this->DBW->update($tableName, $data);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->DBW->last_query();
        }

        return $this->DBW->affected_rows();
    }

    /**
     * 统一删除方法
     * @param $tableName
     * @param $where
     * @param $show_sql
     * @return mixed
     */
    public function deleteDataSql($tableName, $where = NULL, $show_sql = false){

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
                $this->DBW->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->DBW->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->DBW->or_where($vk, $vv);
                    }
                }
            }
        }else{//没有条件是返回空，避免误操作
            return false;
        }

        $this->DBW->delete($tableName);

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->DBW->last_query();
        }

        return $this->DBW->affected_rows();
    }

    /**
     * 获取单条数据
     * @param $tableName
     * @param $where
     * @param null $order_by
     * @param bool $show_sql
     * @return mixed
     */
    public function selectDataSql($tableName, $where = NULL, $order_by = null, $show_sql = false){
        $this->DBR->from($tableName);

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
                $this->DBR->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->DBR->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->DBR->or_where($vk, $vv);
                    }
                }
            }
        }

        if(!empty($order_by)){
            if(is_array($order_by)){
                foreach((array)$order_by as $k=>$v){
                    if($v){
                        $this->DBR->order_by(trim($k), $v);
                    }else{
                        $this->DBR->order_by(trim($k));
                    }
                }
            }else{
                $this->DBR->order_by(trim($order_by));
            }
        }

        if(!empty($limit)){
            $this->DBR->limit(1);
        }
        $query = $this->DBR->get();

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->DBR->last_query();
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
    public function selectDataListSql($tableName, $where = NULL, $order_by = NULL ,$limit = NULL, $show_sql = false){
        $this->DBR->from($tableName);

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
                $this->DBR->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->DBR->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->DBR->or_where($vk, $vv);
                    }
                }
            }
        }

        if(!empty($order_by)){
            if(is_array($order_by)){
                foreach((array)$order_by as $k=>$v){
                    if($v){
                        $this->DBR->order_by(trim($k), $v);
                    }else{
                        $this->DBR->order_by(trim($k));
                    }
                }
            }else{
                $this->DBR->order_by(trim($order_by));
            }
        }
        if(!empty($limit)){
            $this->DBR->limit($limit['0'],$limit['1']);
        }
        $query = $this->DBR->get();

        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->DBR->last_query();
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
    public function selectDataCountSql($tableName, $where = NULL, $show_sql = false){

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
                $this->DBR->where($where_other);
            }

            if (!empty($where_in)) {
                foreach((array)$where_in as $k => $v){
                    $this->DBR->where_in($k, $v);
                }
            }

            if (!empty($where_or)) {
                foreach((array)$where_or as $k => $v){
                    foreach((array)$v as $vk => $vv){
                        $this->DBR->or_where($vk, $vv);
                    }
                }
            }
        }
        $ret = $this->DBR->count_all_results($tableName);
        //是否打印sql到当前页面
        if($show_sql == true){
            echo $this->DBR->last_query();
        }
        return $ret;
    }
    
    public function getTableIndex($id, $table){
        return $table . ($id % 16);
    }
    
    public function getTablePagLog($trxId,$table){
        $trxId = mb_substr( $trxId, 0, 8, 'utf-8');
        $year = mb_substr( $trxId, 0, 4, 'utf-8');
        $num =date('W',strtotime($trxId));
        return $table .$year.'_'.$num;
    }
    /************************数据库操作 end**************************/
}
