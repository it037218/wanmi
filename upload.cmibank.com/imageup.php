<?php 
ini_set("memory_limit", "256M");
require(dirname(__FILE__) .'/uploadimage.php');
require(dirname(__FILE__) .'/rarImag.php');
require(dirname(__FILE__) .'/water.class.php');
file_put_contents('server.log', json_encode($_SERVER));
if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
    define('DO_MAIN','http://upload.cmibank.vip');
}else if($_SERVER['SERVER_ADDR'] == '10.9.193.55'){
    define('DO_MAIN','http://upload.cmibank.vip');
}else if($_SERVER['SERVER_ADDR'] == '127.0.53.53'){
	define('DO_MAIN','http://upload.cmibank.dev');
}else{
    define('DO_MAIN','http://upload1.cmibank.com');
}


class Imageup
{
	 public $data;
	 public $status;
	 public $statusText;
	
	//析构函数
	public function __construct()
	{
	}
	
	private function _createfolder($dir)
	{
		$dir = $dir . "/";
		$dir = str_replace("//" , "/" , $dir);
		if($dir == "" || $dir == "/")
		{
			return 0;
		}
		else
		{
			$alldirarray = explode("/" , $dir);
			$count = count($alldirarray) - 1;
			$nodir = "";
			$createok = 1;
			for($i = 1 ; $i < $count ; $i++)
			{
				$nodir .=  "/" . $alldirarray[$i];
				if(!is_dir($nodir))
				{
					$p = @mkdir($nodir);
					if(!$p)
					{
						$createok = 0;
					}
				}
			} 
			return $createok;
		}
	}

	
	//开始上传
	public function generateHtml()
	{
		$recode      = 0;
		$basepath    = str_replace('//' , '/' , $_SERVER['DOCUMENT_ROOT'] . '/');
		$uid         = intval(isset($_POST['uid'])?$_POST['uid']:'');
		$imagepost   = trim(isset($_POST['imagepost'])?$_POST['imagepost']:'');
		$w_h_arr_str = trim(isset($_POST['jsonwh'])?$_POST['jsonwh']:'');
		$folder_name = trim(isset($_POST['folder'])?$_POST['folder']:'upload'); // app 资源用 app ， 资讯资源用 image
		$is_app_logo = (int)trim(isset($_POST['is_app_logo'])?$_POST['is_app_logo']:'0'); //普通 upload 图片  1：app logo  2 ： iphone 详情图片 3 ： ipad 详情图片
		$imagenums   = (int)trim(isset($_POST['imagenums'])?$_POST['imagenums']:'0');
		$is_news_img = (int)trim(isset($_POST['is_news_img'])?$_POST['is_news_img']:'0');
		$is_pic = (int)trim(isset($_POST['is_pic'])?$_POST['is_pic']:'0');
		$title = trim(isset($_POST['title'])?$_POST['title']:'内容详情');
	
		if($w_h_arr_str)
		{
			$w_h_arr = json_decode($w_h_arr_str , true);
			if(!is_array($w_h_arr))
			{
				$w_h_arr = array();
			}
				
		}
		else
		{
			$w_h_arr = array();
		}
		if($imagepost == '')
		{
			$recode = 11002;  //图片post不存在
		}
		else
		{
			$imagefile = base64_decode($imagepost);
			$t1        = substr(md5($uid) , 0 , 2);
			$t2        = substr(md5($uid) , 2 , 2);
				
			//将资源写成临时文件并判断资源类型
			$filename  = time() . rand(10000 , 99999);
			$uidpath   = $folder_name."/" . $t1 . "/" . $t2 . "/" . $uid . "/" . date("Ymd") . "/";
			$imagepath = $basepath . $uidpath;
			$tmppath   = $basepath . "tmp/";
			$this->_createfolder($imagepath);  //创建目录
			$this->_createfolder($tmppath);    //创建目录
			$tmpfile   = $tmppath . $filename;
			$htmlfile   = $imagepath . $filename.".html";
			$fp = @fopen($tmpfile , 'w');
			if($fp)
			{
				@fwrite($fp , $imagefile);
				@fclose($fp);
				//开始读文件类别资源
				$resourcearr = $this->getresource($tmpfile);
				if(isset($resourcearr['r']) && $resourcearr['r'])
				{
					//根据uid上传图片
					$imagefile = $imagepath . $filename . $resourcearr['b'];
						
					$md5key    = hexdec(substr(md5($filename) , 0 , 1)) % 4;
					switch($md5key)
					{
						case 0:
							$weburl = DO_MAIN;
							break;
						case 1:
							$weburl = DO_MAIN;
							break;
						case 2:
							$weburl = DO_MAIN;
							break;
						default:
							$weburl = DO_MAIN;
					}
					$up = new Uploadimage;
						
					$up->setpath($basepath . $uidpath);     //设置服务端路径
					$up->setbaseurl($weburl);               //设置web基础路径
					$up->setfilename($filename);            //设置文件名
					$up->setimagewh($w_h_arr);              //设置文件宽高
					$up->copy_img($tmpfile);
					if($is_pic==1){
						rename($imagepath.$filename."_1".$resourcearr['b'],$imagepath.$filename."_0_s".$resourcearr['b']);
						rename($imagepath.$filename."_2".$resourcearr['b'],$imagepath.$filename."_0_b".$resourcearr['b']);
					}
					$error = $up->geterror();
	
					//显示本次上传后的所有图片路径,返回数组
					$picarr = $up->showpic();
					$linkfile   = dirname($picarr['url'][0])."/" . $filename.".html";
					
					$myfile = fopen("upload_file_template.txt", "r") or die("Unable to open file!");
					$filecontent =  fread($myfile,filesize("upload_file_template.txt"));
					fclose($myfile);
					
					$filecontent = str_replace("*title*",$title,$filecontent);
					$filecontent = str_replace("*imagePath*",$picarr['url'][0],$filecontent);
					
					$this->create_html_file($htmlfile,$filecontent);
					//如果是新闻详情图片，并且详情图片宽和高大于水印图片宽高，此时，为详情图片加水印
					if($is_news_img == 1){
						$img = @getimagesize($imagepath . $filename . "_0" . $resourcearr['b']);
						$water_img = @getimagesize("http://pic.wcdog.cn/news/water/logo.png");
						if($img[0]>$water_img[0] && $img[1]>$water_img[1] && $img['mime'] != "image/gif"){
							$water = new water();
							$water->waterInfo($imagepath . $filename . "_0" . $resourcearr['b'],"http://pic.wcdog.cn/news/water/logo.png",9,"");
						}
					}
					$recode = 1;
				}
				else
				{
					$recode = 11003;  //非图片资源
				}
			}
			else
			{
				$recode = 11001;  //文件创建失败
			}
		}
	
		if($recode == 1)
		{
			$r['error']     = 0;
			$r['reason']    = 0;
			$r['picarr']    = $linkfile;
			@unlink($tmpfile);
		}
		else
		{
			$r['error']     = 1;
			$r['reason']    = $recode;
			$r['picarr']    = '';
		}
		$json = json_encode($r);
		echo $json;
		exit;
	}
	
	public function create_html_file($filePath,$content){
		$fp = fopen($filePath, 'a');
		$isNewFile = !file_exists($filePath);
		if (flock($fp, LOCK_EX)) {
			if ($isNewFile) {
				chmod($filePath, 0666);
			}
			fwrite($fp, $content . "\n");
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
	
	private function fuiounotify_log($msg){
		if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
			$logFile = './upload_log.'.date("Y-m-d");
		}else{
			$logFile = '/tmp/upload_log.'.date("Y-m-d");
		}
		$fp = fopen($logFile, 'a');
		$isNewFile = !file_exists($logFile);
		if (flock($fp, LOCK_EX)) {
			if ($isNewFile) {
				chmod($logFile, 0666);
			}
			fwrite($fp, $msg . "\n");
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
	
	
	//开始上传
	public function post()
	{
		$recode      = 0;
		$basepath    = str_replace('//' , '/' , $_SERVER['DOCUMENT_ROOT'] . '/');
		$uid         = intval(isset($_POST['uid'])?$_POST['uid']:'');
		$imagepost   = trim(isset($_POST['imagepost'])?$_POST['imagepost']:'');
		$w_h_arr_str = trim(isset($_POST['jsonwh'])?$_POST['jsonwh']:'');
		$folder_name = trim(isset($_POST['folder'])?$_POST['folder']:'upload'); // app 资源用 app ， 资讯资源用 image
		$is_app_logo = (int)trim(isset($_POST['is_app_logo'])?$_POST['is_app_logo']:'0'); //普通 upload 图片  1：app logo  2 ： iphone 详情图片 3 ： ipad 详情图片
		$imagenums   = (int)trim(isset($_POST['imagenums'])?$_POST['imagenums']:'0');
                $is_news_img = (int)trim(isset($_POST['is_news_img'])?$_POST['is_news_img']:'0'); 
                $is_pic = (int)trim(isset($_POST['is_pic'])?$_POST['is_pic']:'0'); 
                
		if($w_h_arr_str)
		{
			$w_h_arr = json_decode($w_h_arr_str , true);
			if(!is_array($w_h_arr))
			{
				$w_h_arr = array();
			}
			
		}
		else
		{
			$w_h_arr = array();
		}
		if($imagepost == '')
		{
			$recode = 11002;  //图片post不存在
		}
		else
		{
			$imagefile = base64_decode($imagepost);
			$t1        = substr(md5($uid) , 0 , 2);
			$t2        = substr(md5($uid) , 2 , 2);
			
			//将资源写成临时文件并判断资源类型
			$filename  = time() . rand(10000 , 99999);
			$uidpath   = $folder_name."/" . $t1 . "/" . $t2 . "/" . $uid . "/" . date("Ymd") . "/";
			$imagepath = $basepath . $uidpath;
			$tmppath   = $basepath . "tmp/";
			$this->_createfolder($imagepath);  //创建目录
			$this->_createfolder($tmppath);    //创建目录
			$tmpfile   = $tmppath . $filename;
			$fp = @fopen($tmpfile , 'w');
			if($fp)
			{
				@fwrite($fp , $imagefile);
				@fclose($fp);
				//开始读文件类别资源
				$resourcearr = $this->getresource($tmpfile);
				if(isset($resourcearr['r']) && $resourcearr['r'])
				{
					//根据uid上传图片
					$imagefile = $imagepath . $filename . $resourcearr['b'];
					
					$md5key    = hexdec(substr(md5($filename) , 0 , 1)) % 4;
					switch($md5key)
					{
						case 0:
							$weburl = DO_MAIN;
							break;
						case 1:
							$weburl = DO_MAIN;
							break;
						case 2:
							$weburl = DO_MAIN;
							break;
						default:
							$weburl = DO_MAIN;
					}						
					$up = new Uploadimage;
					
					$up->setpath($basepath . $uidpath);     //设置服务端路径
					$up->setbaseurl($weburl);               //设置web基础路径
					$up->setfilename($filename);            //设置文件名
					$up->setimagewh($w_h_arr);              //设置文件宽高
					$up->copy_img($tmpfile);
                                            if($is_pic==1){
                                                rename($imagepath.$filename."_1".$resourcearr['b'],$imagepath.$filename."_0_s".$resourcearr['b']);
                                                rename($imagepath.$filename."_2".$resourcearr['b'],$imagepath.$filename."_0_b".$resourcearr['b']);
                                            }
					$error = $up->geterror();
										
					//显示本次上传后的所有图片路径,返回数组
					$picarr = $up->showpic();
                                            //如果是新闻详情图片，并且详情图片宽和高大于水印图片宽高，此时，为详情图片加水印
                                            if($is_news_img == 1){
                                                $img = @getimagesize($imagepath . $filename . "_0" . $resourcearr['b']);
                                                $water_img = @getimagesize("http://pic.wcdog.cn/news/water/logo.png");
                                                if($img[0]>$water_img[0] && $img[1]>$water_img[1] && $img['mime'] != "image/gif"){
                                                    $water = new water();
                                                    $water->waterInfo($imagepath . $filename . "_0" . $resourcearr['b'],"http://pic.wcdog.cn/news/water/logo.png",9,"");
                                                }
                                            }
					$recode = 1;
				}
				else
				{
					$recode = 11003;  //非图片资源
				}
			}
			else
			{
				$recode = 11001;  //文件创建失败
			}
		}
		
		if($recode == 1)
		{
			$r['error']     = 0;
			$r['reason']    = 0;
			$r['picarr']    = $picarr;
			@unlink($tmpfile);
		}
		else
		{
			$r['error']     = 1;
			$r['reason']    = $recode;
			$r['picarr']    = array();
		}
		$json = json_encode($r);
		echo $json;
		exit;
	}
	
	//读取并分配图片资源
	public function getresource($path = '')
	{
		$new_r = array();
		if(!empty($path) && file_exists($path))
		{
			$info   = @getimagesize($path);
			$width  = $info[0];
			$height = $info[1];
			$type   = $info[2];
			switch($type)
			{
				case 1:$im = imagecreatefromgif($path);$btype='.gif';break; 
				case 2:$im = imagecreatefromjpeg($path);$btype='.jpg';break; 
				case 3:$im = imagecreatefrompng($path);$btype='.png';break;
				case 6:$im = imagecreatefromwbmp($path);$btype='.bmp';break;
				default:$im= false;$btype='';$width=0;$height=0;
			}
			$new_r['w']  = $width;
			$new_r['h']  = $height;
			$new_r['t']  = $type;
			$new_r['r']  = $im;
			$new_r['b']  = $btype;
		}
		else
		{
			$new_r['w']  = 0;
			$new_r['h']  = 0;
			$new_r['t']  = 0;
			$new_r['r']  = false;
			$new_r['b']  = '';
		}
		return $new_r;
	}
	
	//一个简单的图片上传测试
	public function uppic()
	{
		$uid = intval(isset($_GET['uid'])?$_GET['uid']:'0');
		if($uid == 0)
		{
			$uid = mt_rand(1000 , 9999);
		}
		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>图片上传测试</title>
		</head>
		<body>
		<form name="fr1" action="/imageup.php?method=uppicpost&uid=<?php echo($uid);?>" method="post"  enctype="multipart/form-data">
			<input type="file" name="f" /><input type="submit" value="上传">
		</form>
		</body>
		</html>
		<?php
	}
	
	//一个简单的图片上传post部分
	public function javauppicpost()
	{
		$basepath      = str_replace('//' , '/' , $_SERVER['DOCUMENT_ROOT'] . '/');
		$baseurl       = DO_MAIN;
		$formfilename  = 'file';
		//将资源写成临时文件并判断资源类型
		$filename      = time() . rand(10000 , 99999);
		$uidpath       = "/upload/" . date("Ymd") . "/";
		$upladfilename = $basepath . $uidpath . $filename;
		$this->_createfolder($basepath . $uidpath);
		$reurl         = $baseurl . $uidpath . $filename;
		if($_FILES[$formfilename]['size'] > 41943040)  //40M
		{
			echo($this->rehead());
			echo('尺寸超出40M！');
			exit;
		}
		else
		{
			$oldfilename = $_FILES[$formfilename]['tmp_name'];
			$re_r = $this->getresource($oldfilename);
			if($re_r['r'])
			{
				@copy($oldfilename , $upladfilename . $re_r['b']);
				$response = array('error'=> 0, 'msg'=>'上传成功','path'=>$reurl.$re_r['b']);
				echo json_encode($response);
				exit;
			}
			else
			{
				echo($this->rehead());
				echo('图片不正确，请重试！');
				exit;
			}
		}
	}
	
	//一个简单的图片上传post部分
	public function uppicpost()
	{
		$uid = intval(isset($_GET['uid'])?$_GET['uid']:'0');
		$basepath      = str_replace('//' , '/' , $_SERVER['DOCUMENT_ROOT'] . '/');
		$baseurl       = DO_MAIN;
		$formfilename  = 'f';
		$t1            = substr(md5($uid) , 0 , 2);
		$t2            = substr(md5($uid) , 2 , 2);
		//将资源写成临时文件并判断资源类型
		$filename      = time() . rand(10000 , 99999);
		$uidpath       = "upload/" . $t1 . "/" . $t2 . "/" . $uid . "/" . date("Ymd") . "/";
		$upladfilename = $basepath . $uidpath . $filename;
		$this->_createfolder($basepath . $uidpath);
		$reurl         = $baseurl . $uidpath . $filename;
		if($_FILES[$formfilename]['size'] > 41943040)  //40M
		{
			echo($this->rehead());
			echo('尺寸超出40M！');
			exit;
		}
		else
		{
			$oldfilename = $_FILES[$formfilename]['tmp_name'];
			$re_r = $this->getresource($oldfilename);
			if($re_r['r'])
			{
				@copy($oldfilename , $upladfilename . $re_r['b']);
				echo($this->rehead());
				echo('图片上传成功，<a href="/imageup.php?method=uppic&uid=' . $uid . '">返回</a>&nbsp;&nbsp;<a href="/imageup.php?method=viewlist&uid=' . $uid . '" target="_blank">查看</a>');
				exit;
			}
			else
			{
				echo($this->rehead());
				echo('图片不正确，请重试！');
				exit;
			}
		}
	}
	
	//图片文件读取器
	public function viewlist()
	{
		$uid = intval(isset($_GET['uid'])?$_GET['uid']:'0');
		$basepath      = str_replace('//' , '/' , $_SERVER['DOCUMENT_ROOT'] . '/');
		$baseurl       = DO_MAIN.'/';
		$t1            = substr(md5($uid) , 0 , 2);
		$t2            = substr(md5($uid) , 2 , 2);
		$uidpath       = "upload/" . $t1 . "/" . $t2 . "/" . $uid . "/";
		$f = $this->upreadfile($basepath . $uidpath);
		if(is_array($f))
		{
			foreach($f as $k => $v)
			{
				echo('<a href="' . str_replace($basepath , $baseurl , $v) . '" target="_blank"><img src="' . str_replace($basepath , $baseurl , $v) . '" border="0" width="60" />' . basename($v) . '</a><br >');
			}
		}else{
			echo ('folder');
		}
	}
	
	private function upreadfile($filepath = '')
	{
		$filenamearr = array();
		if(file_exists($filepath)){
			$d = dir($filepath);
			while(false !== ($entry = $d->read())) 
			{
			  if($entry != '.' && $entry != '..')
			  {
			  	if(is_dir($filepath . $entry))
			  	{
			  		$dn = dir($filepath . $entry);
			  		while(false !== ($fn = $dn->read())) 
						{
							$filename = $filepath . $entry . '/' . $fn;
							if(!is_dir($filename))
							{
								$t_file_time = filemtime($filename);
								$filenamearr[$t_file_time] = $filename;
							}
						}
			  	}
			  }
			}
			$d->close();
			krsort($filenamearr);
			return $filenamearr;	
		}
	}
	
	//头部
	private function rehead()
	{
		$r = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		return $r;
	}
}

$imageup = new Imageup();
$method  = isset($_REQUEST['method']) ? $_REQUEST['method'] : 'uppic';
if($method){
	$imageup->$method();	
}else{
	die('method params not null!');
}
?>