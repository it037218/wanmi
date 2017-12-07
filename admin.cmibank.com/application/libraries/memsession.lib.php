<?php
require_once 'mem.lib.php';
class MemSession {
	public function __construct(){}
	
	public static function set($sid,$val,$expire){
		$options = array(
			'flag'   => 0,
			'expire' => $expire
		);
		return Mem::set($sid,$val,$options);	
	}
	
	public static function get($sid){
		return Mem::get($sid); 
	}
	
	public static function delete($sid){
		return Mem::delete($sid);
	}
}