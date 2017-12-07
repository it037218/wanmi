<?php
/**
 * base
 */
require_once APPPATH.'libraries/memsession.lib.php';

class baseController extends CI_Controller{
	public function __construct(){
		parent :: __construct();
	}
	
	
	protected function isPost() {
		return ($_SERVER['REQUEST_METHOD'] == 'POST');
	}
	
	protected function getHost() {
		return "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	}
	
	protected function getIP(){		
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		        $onlineip = getenv('HTTP_CLIENT_IP');
		} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		        $onlineip = getenv('REMOTE_ADDR');
		} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		        $onlineip = $_SERVER['REMOTE_ADDR'];
		}
		if(preg_match("#^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}#",$onlineip)){
	        return $onlineip;
	    }else{
	    	$onlineip = $_SERVER['REMOTE_ADDR'];
	    	return $onlineip;
	    }
	}

	protected function getUserAgent(){
		return $_SERVER['HTTP_USER_AGENT'];
	}

	protected function getSession($key){
		if(isset($_SESSION[$key])){
			return $_SESSION[$key];
		}else if(isset($_COOKIE[$key])){
		    return $this->getCookie($key);
		}
		return '';
	}
	
	protected function setSession($key,$val = null){
		if(is_array($key)){
			foreach($key as $k=>$v){
				$_SESSION[$k] = $v;
			}
			return true;
		}
		$_SESSION[$key] = $val;		
	}
	
	protected function unsetSession($key){
		if(is_array($key)){
			foreach($key AS $k){
				if(isset($_SESSION[$k])){
					unset($_SESSION[$k]);
				}
			}			
		}else if(isset($_SESSION[$key])){
			unset($_SESSION[$key]);
		}
		return true;
	}
	
	protected function getCookie($name){
		if (isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		} 
		return false;
	}

	protected function setCookie($name, $val, $expire = 0){
		include APPPATH . 'config/config.php';
		$path	= $config['cookie_path'];
		$domain = $config['cookie_domain'];		
		$expire = ($expire == 0)? $config['cookie_expire'] : time() + $expire;
		return setcookie($name, $val, $expire, $path, $domain);		
	}
	
	protected function unsetCookie($name){
	    if(is_array($name)){
	        foreach ($name as $_name){
	            $this->setcookie($_name, "", time()-24*60*60);
	        }	        
	    }else{
	        $this->setcookie($name, "", time()-24*60*60);
	    }
		
	}
	
	/**
	 * 错误信息提示
	 * @param string $url   提示错误后跳转地址、
	 * @param string $msg   提示信息
	 * @param int $time     页面停留时间
	 *
	 */
	protected function Error($msg, $url, $time = 2){
		$data = array(
			'msg' => $msg,
			'url' => $url,
			'time' => $time
		);
		$this->load->view('error_msg', $data);
	}

}