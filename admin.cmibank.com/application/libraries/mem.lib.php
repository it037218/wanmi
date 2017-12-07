<?php
class Mem{
	static private $_memcache = null;

	private function __construct(){}
	private function __clone(){}

	/**
	 * 注：php 5.3.0 以上版本才支持静态方法调用重载。
	 */
   	private static function _init(){
		if (is_null(self::$_memcache) || !isset(self::$_memcache)) {
			try {
				$cfg =  include SYSLIB . 'config/memcache.php';
				self::$_memcache = new Memcache();
				foreach ($cfg as $server){
					self::$_memcache->addserver($server['ip'],$server['port']);
				}
			} catch (Exception $e) {
				
			}
        }
	}
	
	public static function set($key, $val, $options = array()){
//		return false;
		self::_init();
		if(!empty($options)){	
			return self::$_memcache->set($key, json_encode($val), $options['flag'], $options['expire']);
		}
		return self::$_memcache->set($key,json_encode($val));
	}
	
	public static function get($key, $is_json = true){
//		return false;
		self::_init();
		$result = self::$_memcache->get($key);
		if (empty($result)) {
			return $result;
		}
		return $is_json ? json_decode($result,true) : $result;
	}
	
	public static function delete($key = NULL){
		self::_init();
		if (empty($key)) {
			return false;
		}
		return self::$_memcache->delete($key);
	}
	
	public static function increment($key,$step = 1){
		self::_init();
		return self::$_memcache->increment($key,$step);
	}
	
	public static function decrement($key,$step = 1){
		self::_init();
		return self::$_memcache->decrement($key,$step);
	}
	
	public function flut(){
		self::$_memcache->flush();
	}
}