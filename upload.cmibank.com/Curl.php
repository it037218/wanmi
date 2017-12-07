<?php
/*
+--------------------------------------------------------------------------
|   Curl操作系统扩展
|   =============================================
|   by LiGuoXi
|   =============================================
|		File: /framework/extensions/ECurl.php
+---------------------------------------------------------------------------
|   > $Date: 2010-9-26 $
|   > $Revision: 1 $
|   > $Author: LiGuoXi $
+---------------------------------------------------------------------------
*/

class Curl
{
	/**
	 * cURL资源
	 *
	 * @var resource
	 */
	protected $_ch = null;
	
	/**
	 * URL地址
	 *
	 * @var string
	 */
	protected $_url = '';
	
	/**
	 * 是否启用SSL模式
	 *
	 * @var boolean
	 */
	protected $_ssl = false;
	
	/**
	 * 初始化cURL资源
	 *
	 */
	public function &__construct()
  {
    $returnstr = true;
    $this->_ch = curl_init();
    return $returnstr;
  } 
	
	public function __destruct()
	{
		
  }
  
  /**
	 * 初始化URL
	 *
	 * @param string $url
	 * 
	 * @return boolean [true成功 | false失败]
	 */
	private function _setUrl($url) {
		$this->_url = $url;
		/*
		 * 以下代码在PHP > 5.3有效
		 */
		if (false !== strstr($this->_url, 'https://', true)) {
			$this->_ssl = true;
		}
		return curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
	}
	
	/**
	 * 发送socket连接
	 *
	 * @param string $url
	 * @param array $para
	 * @param boolean $return
	 * 
	 * @return mix [void|string]
	 */
	private function _socket($url, $para, $return) {		
		$this->_setUrl($url);

		/*
		 * 强制转换为boolean类型，这里不使用(boolean)与settype
		 */
		if (false === isset($para['header'])) {
			$para['header'] = false;
		} else {
			$para['header'] = true;
		}
		curl_setopt($this->_ch, CURLOPT_HEADER, $para['header']);

		/*
		 * 处理302
		 */
		if (false === isset($para['location'])) {
			$para['location'] = false;
		} else {
			$para['location'] = true;
		}
		curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, $para['location']);

		unset($para['location']);
		
		if (false === isset($para['cookieFile'])) {
			$para['cookieFile'][0] = '';
			curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $para['cookieFile'][0]);
			curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $para['cookieFile'][0]);
		}

		/*
		 * exec执行结果是否保存到变量中
		 */
		if (true === $return) {
			curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		}

		/*
		 * 是否启用SSL验证
		 */
		if (true === $this->_ssl) {
			curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, true);
		}

		/*
		 * 调用子类处理方法
		 */
		$result = curl_exec($this->_ch);
		curl_close($this->_ch);
		if (true === $return) {
			return $result;
		}
	}
	
	
	//实现post
	function _post($url,$postfield,$proxy="",&$httpinfo = 0)
	{
		$proxy=trim($proxy);
		//$user_agent ="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
		if(!empty($proxy))
		{
			curl_setopt($this->_ch, CURLOPT_PROXY, $proxy);//设置代理服务器
		}
		curl_setopt($this->_ch, CURLOPT_URL, $url); //设置请求的URL
		//curl_setopt($this->_ch, CURLOPT_FAILONERROR, 1); // 启用时显示HTTP状态码，默认行为是忽略编号小于等于400的HTTP信息
		//curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, 1);//启用时会将服务器服务器返回的“Location:”放在header中递归的返回给服务器
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER,1);// 设为TRUE把curl_exec()结果转化为字串，而不是直接输出
		curl_setopt($this->_ch, CURLOPT_POST, 1);//启用POST提交
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postfield); //设置POST提交的字符串
		//curl_setopt($this->_ch, CURLOPT_PORT, 80); //设置端口
		curl_setopt($this->_ch, CURLOPT_TIMEOUT, 25); // 超时时间
		//curl_setopt($this->_ch, CURLOPT_USERAGENT, $user_agent);//HTTP请求User-Agent:头
		//curl_setopt($this->_ch,CURLOPT_HEADER,1);//设为TRUE在输出中包含头信息
		//$fp = fopen("example_homepage.txt", "w");//输出文件
		//curl_setopt($this->_ch, CURLOPT_FILE, $fp);//设置输出文件的位置，值是一个资源类型，默认为STDOUT (浏览器)。
		curl_setopt($this->_ch,CURLOPT_HTTPHEADER,array(
			'Accept-Language: zh-cn',
			'Connection: Keep-Alive',
			'Cache-Control: no-cache'
			));//设置HTTP头信息
		$document = curl_exec($this->_ch); //执行预定义的CURL
		$info=curl_getinfo($this->_ch); //得到返回信息的特性
		//print_r($info);
		//print_r($document);
		curl_close($this->_ch);
		//exit;
		if($info['http_code']=="405" || $info['http_code']=="0" || $info['http_code'] == "404")
		{
			$httpinfo = $info['http_code'];
			return false;
			//echo "bad proxy {$proxy}\n"; //代理出错
			//exit;
		}
		else
		{
			$httpinfo = 0;
			return $document;
		}
	}
	
//-------------------- 对外接口 --------------------

	/**
	 * 发起通信请求接口
	 *
	 * @param string $url
	 * @param array $para
	 * @param boolean $return
	 * 
	 * @return string
	 */
	public function socket($url, $para = array(), $return = true) {
		return $this->_socket($url, $para, $return);
	}
	
	/**
	 * 实现POST
	 *
	 * @param array $para
	 * 
	 * @return void
	 */
	public function post($url , $para = array() , &$httpinfo = 0) {
		$postfield = '';
		$proxy = '';
		if(is_array($para) && count($para) > 0)
		{
			foreach($para as $k => $v)
			{
				if($k <> 'proxy')
				{
					if($postfield == '')
					{
						$postfield = $k . '=' . urlencode($v);
					}
					else
					{
						$postfield .= '&' . $k . '=' . urlencode($v);
					}
				}
				else
				{
					$proxy = $v;
				}
			}
		}
		return $this->_post($url,$postfield,$proxy,$httpinfo);
	}
	
	/**
	 * 实现GET
	 *
	 * @param array $para
	 * 
	 * @return void
	 */
	public function get($para = array()) {
	
	}
}

/*
curl错误 http_code值
$http_code["0"]="Unable to access";
$http_code["100"]="Continue";
$http_code["101"]="Switching Protocols";

//[Successful 2xx]
$http_code["200"]="OK";
$http_code["201"]="Created";
$http_code["202"]="Accepted";
$http_code["203"]="Non-Authoritative Information";
$http_code["204"]="No Content";
$http_code["205"]="Reset Content";
$http_code["206"]="Partial Content";

//[Redirection 3xx]
$http_code["300"]="Multiple Choices";
$http_code["301"]="Moved Permanently";
$http_code["302"]="Found";
$http_code["303"]="See Other";
$http_code["304"]="Not Modified";
$http_code["305"]="Use Proxy";
$http_code["306"]="(Unused)";
$http_code["307"]="Temporary Redirect";

//[Client Error 4xx]
$http_code["400"]="Bad Request";
$http_code["401"]="Unauthorized";
$http_code["402"]="Payment Required";
$http_code["403"]="Forbidden";
$http_code["404"]="Not Found";
$http_code["405"]="Method Not Allowed";
$http_code["406"]="Not Acceptable";
$http_code["407"]="Proxy Authentication Required";
$http_code["408"]="Request Timeout";
$http_code["409"]="Conflict";
$http_code["410"]="Gone";
$http_code["411"]="Length Required";
$http_code["412"]="Precondition Failed";
$http_code["413"]="Request Entity Too Large";
$http_code["414"]="Request-URI Too Long";
$http_code["415"]="Unsupported Media Type";
$http_code["416"]="Requested Range Not Satisfiable";
$http_code["417"]="Expectation Failed";

//[Server Error 5xx]
$http_code["500"]="Internal Server Error";
$http_code["501"]="Not Implemented";
$http_code["502"]="Bad Gateway";
$http_code["503"]="Service Unavailable";
$http_code["504"]="Gateway Timeout";
$http_code["505"]="HTTP Version Not Supported";

*/