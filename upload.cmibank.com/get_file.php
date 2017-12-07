<?php
header("content-type:text/html;charset=utf-8");
@ini_set("memory_limit","1024M");
set_time_limit(0);

$dbname    = isset($argv[1])?$argv[1]:'';
$tablename = isset($argv[2])?$argv[2]:'';
if(!$dbname){
	die("dbname error!");

}
if(!$tablename){
	die('tablename error');
}
//$posturl = 'http://image.xyzs.com/g_img.php';
//$posturl = 'http://ig.iosuu.com/appimg/g_img.php';
$posturl = 'http://127.0.0.1:8087/appimg/g_img.php';


$db['h'] = 'localhost';
$db['u'] = 'xyapp';
$db['p'] = 'xyapp7pk#';
//$db['u'] = 'root';
//$db['p'] = '';
$db['d'] = 'app';
$db['t'] = 'applist_new';

$links   = mysql_connect($db['h'] , $db['u'] , $db['p']);
mysql_query('set names utf8' , $links);

$ln      = mysql_select_db($dbname , $links);
$sqlstr  = "select url,itunesid,img,iphoneimg,ipadimg,status from " . $tablename . ' where status = 0 ';

$result  = mysql_query($sqlstr , $links);
$i = 0;
while($row = mysql_fetch_array($result)){
	$itunesid  = $row['itunesid'];
	$img       = $row['img'];
	$url       = $row['url'];
	$iphoneimg = unserialize($row['iphoneimg']);
	$ipadimg   = unserialize($row['ipadimg']);

	$str_path = substr(md5($itunesid),0,2) . "/" . substr(md5($itunesid) , 2 , 2);
	$root_path = "D:/wamp/www/appimg/app/".$str_path;
	$path 	   = "app/" .  $str_path .  "/"  .  $itunesid;
	$filename  = md5("logo_" . $itunesid ).".jpg"; //这里加密一次
	// img log
	if(!file_exists($root_path . "/" . $itunesid . "/" . $filename)){
		$file     = gethtmlone($img , $url);
		$picfile  = urlencode(base64_encode($file));
		$postarr  = array(
			'file' => $picfile,
			'path' => $path,
			'filename' => $filename,
		);
		$s = new Curl;
		$re = $s->post($posturl , $postarr);	
	}
	//详情的 图片
	$new_iphoneimg = array();
	$new_ipadimg   = array();
	// iphone 的图片
	if($iphoneimg){
		$count = 0;
		foreach ($iphoneimg as $key=>$value){
			$path = "app/" . $str_path  .  "/"  .  $itunesid;
			$count ++;
			$filename = md5("iphoneimg_" . $itunesid) . "_i".$count.".jpg";
			// img log
			if(!file_exists($root_path . "/" . $itunesid . "/" . $filename)){
				$file     = gethtmlone($value , $url);
				$picfile  = urlencode(base64_encode($file));
				$postarr  = array(
					'file' => $picfile,
					'path' => $path,
					'filename' => $filename,
				);
				$s = new Curl;
				$re = $s->post($posturl , $postarr);
				usleep(500);// 500 毫秒	
			}
		}
	}
	// ipad 的图片
	if($ipadimg){
		$count = 0;
		foreach ($ipadimg as $key=>$value){
			$path = "app/" . $str_path  .  "/"  .  $itunesid;
			$count ++;
			$filename = md5("ipadimg_" . $itunesid) . "_ii".$count.".jpg";
//			 img log
//			if(file_exists($root_path . "/" . $itunesid . "/" . $filename)){
				$file     = gethtmlone($value , $url);
				$picfile  = urlencode(base64_encode($file));
				$postarr  = array(
					'file' => $picfile,
					'path' => $path,
					'filename' => $filename,
				);
				$s = new Curl;
				$re = $s->post($posturl , $postarr);
				usleep(500);// 500 毫秒	
//			}
		}
	}
	$i++;
	if($i % 40 == 0)
	{
		$ttn = rand(1000 , 8000);
		usleep($ttn);
	}
//	exit;
}
mysql_close($links);

function gethtmlone($url , $refer)
{
	$chtml = getimgbody($url , $refer);
	return $chtml;
}

function getimgbody($url , $refer)
{
	$ip = rand(211,222) . '.68.' . rand(1,255) . '.' . rand(1,255);
	$useragent="Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; QQDownload 1.7; TencentTraveler 4.0";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $ip, 'CLIENT-IP:' . $ip));//IP
	curl_setopt($ch, CURLOPT_REFERER, $refer);
	curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

	$htmlcontent = curl_exec($ch);
	curl_close($ch);
	return $htmlcontent;
}


class Curl
{
	protected $_ch = null;

	protected $_url = '';

	protected $_ssl = false;

	private $refer     = '';
	private $ip        = '';
	private $useragent = '';
	private $port      = '';
	private $isturnto  = 0;
	private $isnobody  = 0;
	private $isshowheader = 0;

	public function __construct()
	{
		$this->Curl();
	}

	public function Curl()
	{

	}

	public function init()
	{
		if(!function_exists('curl_init'))
		{
			$error_log = 'Curl_init not install!';
			$isexit    = 1;
			$this->B()->showerror->show_now_error($error_log , $isexit);
			return false;
		}
		else
		{
			$init = @curl_init();
			if($init)
			{
				$this->_ch = $init;
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public function setpushnow($sn_arr = array())
	{
		if(isset($sn_arr['refer']))
		{
			$this->refer = $sn_arr['refer'];
		}
		if(isset($sn_arr['ip']))
		{
			$this->ip = $sn_arr['ip'];
		}
		if(isset($sn_arr['useragent']))
		{
			$this->useragent = $sn_arr['useragent'];
		}
		if(isset($sn_arr['port']))
		{
			$this->port = $sn_arr['port'];
		}
		if(isset($sn_arr['isturnto']))
		{
			$this->isturnto = $sn_arr['isturnto'];
		}
		if(isset($sn_arr['isnobody']))
		{
			$this->isnobody = $sn_arr['isnobody'];
		}
		if(isset($sn_arr['isshowheader']))
		{
			$this->isshowheader = $sn_arr['isshowheader'];
		}
	}
	
	public function socket($url, $para = array(), $return = true)
	{
		return $this->_socket($url, $para, $return);
	}

	public function post($url , $para = array() , $headerarr=array() , &$httpinfo = 0)
	{
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
		return $this->_post($url,$postfield,$proxy,$headerarr,$httpinfo);
	}

	public function get($url , $headerarr = array() , &$httpinfo = '')
	{
		return $this->_curl_up($url,$headerarr,$httpinfo,'','',0);
	}

	private function _setUrl($url)
	{
		$this->init();
		$this->_url = $url;
		if(false !== strstr($this->_url, 'https://', true))
		{
			$this->_ssl = true;
		}
		return curl_setopt($this->_ch, CURLOPT_URL, $this->_url);
	}
	
	private function _socket($url, $para, $return)
	{
		$this->_setUrl($url);
		if (false === isset($para['header']))
		{
			$para['header'] = false;
		}
		else
		{
			$para['header'] = true;
		}
		curl_setopt($this->_ch, CURLOPT_HEADER, $para['header']);
		if (false === isset($para['location']))
		{
			$para['location'] = false;
		}
		else
		{
			$para['location'] = true;
		}
		curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, $para['location']);

		unset($para['location']);

		if (false === isset($para['cookieFile']))
		{
			$para['cookieFile'][0] = '';
			curl_setopt($this->_ch, CURLOPT_COOKIEFILE, $para['cookieFile'][0]);
			curl_setopt($this->_ch, CURLOPT_COOKIEJAR, $para['cookieFile'][0]);
		}
		if(true === $return)
		{
			curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		}
		
		if(true === $this->_ssl)
		{
			curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, true);
		}
		$result = curl_exec($this->_ch);
		curl_close($this->_ch);
		if (true === $return)
		{
			return $result;
		}
	}
	
	private function _post($url,$postfield,$proxy="",$headerarr=array(),&$httpinfo = 0)
	{
		return $this-> _curl_up($url,$headerarr,$httpinfo,$postfield,$proxy,1);
	}

	
	private function _curl_up($url,$headerarr=array(),&$httpinfo=0,$postfield = '',$proxy="",$ispost = 0)
	{
		$this->init();
		$proxy=trim($proxy);
		//$user_agent ="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
		if(!empty($proxy))
		{
			curl_setopt($this->_ch, CURLOPT_PROXY, $proxy);
		}
		curl_setopt($this->_ch, CURLOPT_URL, $url);
		curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($this->_ch, CURLOPT_TIMEOUT, 25);
		//curl_setopt($this->_ch, CURLOPT_MAXREDIRS,3);
		//curl_setopt($this->_ch, CURLOPT_FAILONERROR, 1);

		//curl_setopt($this->_ch,CURLOPT_HEADER,1);

		//refer
		if($this->refer <> '')
		{
			curl_setopt($this->_ch, CURLOPT_REFERER, $this->refer);
		}
		//ip
		if($this->ip <> '')
		{
			curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:' . $this->ip, 'CLIENT-IP:' . $this->ip));//IP
		}
		//useragent
		if($this->useragent <> '')
		{
			curl_setopt($this->_ch, CURLOPT_USERAGENT, $this->seragent);
		}
		if($this->port <> '')
		{
			curl_setopt($this->_ch, CURLOPT_PORT, $this->port);
		}
		if($this->isshowheader <> 0)
		{
			curl_setopt($this->_ch, CURLOPT_HEADER, 1);
		}
		if($this->isnobody <> 0)
		{
			curl_setopt($this->_ch, CURLOPT_NOBODY, 1);
		}
		if($ispost == 1)
		{
			curl_setopt($this->_ch, CURLOPT_POST, 1);
			curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postfield);
		}
		$header = array(
			'Accept-Language: zh-cn',
			'Connection: Keep-Alive',
			'Cache-Control: no-cache'
		);
		if(is_array($headerarr))
		{
			foreach($headerarr as $k => $v)
			{
				$header[] = $v;
			}
		}
		curl_setopt($this->_ch,CURLOPT_HTTPHEADER,$header);
		$document = curl_exec($this->_ch);
		if($document === false)
		{
			$httpinfo = curl_error($this->_ch);
			curl_close($this->_ch);
			return false;
		}
		else
		{
			$info=curl_getinfo($this->_ch);
			curl_close($this->_ch);
			if($info['http_code']=="405" || $info['http_code']=="0" || $info['http_code'] == "404")
			{
				$httpinfo = $info['http_code'];
				return false;
			}
			else
			{
				$httpinfo = 0;
				return $document;
			}
		}
	}
}

?>