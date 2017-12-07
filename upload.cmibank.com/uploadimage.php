<?php
/**
 * 文件名：Uploadimage.php
 * 作者：liguoxi
 * 时间：2011-07-06
 * 功能：图片上传
 * 特点：全新的上传，先预上传，之后再上传保存，可保留多张缩图
 */
//需要curl支持
//error_reporting(0);
ini_set("memory_limit", "256M");
$include_up_curl_path   = dirname(__FILE__) . "/Curl.php";
if(is_file($include_up_curl_path))
{
	include_once($include_up_curl_path);
}
else
{
	echo('No Curl！');
	exit;
}
class Uploadimage
{
	private $formname     = '';
	private $maxsize      = 10240000;  //图片尺寸
	private $iszoom       = 0;         //是否等比缩放
	private $ismiddlecut  = 1;         //是否中间切割
	private $waterpath    = array();   //水印路径(数组，对应各图片的水印)
	private $wharr        = array();   //上传的图片的尺寸数组，如为空或00，则保留原图
	private $error        = 0;
	private $quality      = 90;        //清晰度
	private $baseurl      = '/';
	private $newpicpath   = '';        //设置的文件路径，注：外部计算后传入
	private $filename     = '';        //设置文件名
	private $newpicname   = array();        //新生成的文件地址
	private $uid          = 0;
	private $picarr       = array();   //这个是上传后返回的值，如果在image端的话，这个值为空数组
	private $imageServer  = 'http://upload.cmibank.com/imageup.php';

	//析构函数
	public function __construct()
	{
		$this->Uploadimage();
	}

	public function Uploadimage()
	{
		$wh[0]['w'] = 0;
		$wh[0]['h'] = 0;
		$this->wharr = $wh;
		$this->setpath();
		$this->setfilename();
	}

	//设置uid，用于存储
	public function setuid($uid = 0)
	{
		$this->uid = $uid;
	}

	//设置基础url
	public function setbaseurl($baseurl = '')
	{
		if($baseurl == '')
		{
			$this->baseurl = '/';
		}
		else
		{
			$this->baseurl = $baseurl;
		}
	}

	//设置form表单的名称
	public function setformname($formname = '')
	{
		$this->formname = $formname;
	}

	//设置尺寸
	public function setsize($maxsize = 10240000)
	{
		$this->maxsize = $maxsize;
	}

	//是否等比缩放
	public function setzoom($iszoom = 0)
	{
		$this->iszoom = $iszoom;
	}

	//是否中间切割
	public function setcut($ismiddlecut = 0)
	{
		$this->ismiddlecut = $ismiddlecut;
	}

	//设置水印路径
	public function setwater($waterpath = array())
	{
		//$this->waterpath = $waterpath;  //不增加水印
	}

	//设置图片的宽高(数组)
	public function setimagewh($wharr = array())
	{
		if(is_array($wharr) && count($wharr) > 0)
		{
			$wh[0]['w'] = 0;
			$wh[0]['h'] = 0;
			$i = 1;
			foreach($wharr as $k => $v)
			{
				if(isset($v['w']) && isset($v['h']))
				{
					if(intval($v['w']) > 0 && intval($v['h']) > 0)
					{
						$wh[$i]['w'] = $v['w'];
						$wh[$i]['h'] = $v['h'];
						$i++;
					}
				}
			}
			$this->wharr = $wh;
		}
		else
		{
			$wh[0]['w'] = 0;
			$wh[0]['h'] = 0;
			$this->wharr = $wh;
		}
	}

	//设置清晰度
	public function setquality($quality = 100)
	{
		$this->quality = $quality;
	}

	//设置图片的路径
	public function setpath($newpicpath = '')
	{
		if($newpicpath == '')
		{
			$newpicpath = $_SERVER['DOCUMENT_ROOT'] . '/upload/0/0/';
		}
		$this->newpicpath = $newpicpath;
	}

	//设置图片的名称
	public function setfilename($filename = '')
	{
		if($filename == '')
		{
			$filename = time() . rand(1000,9999);
		}
		$this->filename = $filename;
	}

	//预上传开始
	public function upfirst(&$nowsession = '')
	{
		if($this->formname <> '')
		{
			if($_FILES[$this->formname]['size'] > $this->maxsize)
			{
				$this->seterror(1005);       //尺寸超出
				return false;
			}
			else
			{
				$tmp_file   = $_FILES[$this->formname]['tmp_name'];
				$r_array = $this->getresource($tmp_file);  //取出资源数组
				if($this->error == 1001 || $this->error == 1002)
				{
					return false;  //资源取出错误
				}
				else
				{
					//copy至临时文件夹，该文件夹定期清除多余值
					$folder_tmp = $_SERVER['DOCUMENT_ROOT'] . '/tmp/';
					$this->createfolder($folder_tmp);  //创建目录
					$file_tmp   = $folder_tmp . md5($tmp_file);

					@copy($tmp_file , $file_tmp);

					$up_session = $file_tmp;
					if(!isset($_SESSION['up']))
					{
						$nk = 0;
						$_SESSION['up'][$nk] = $up_session;
					}
					else
					{
						if(!in_array($up_session , $_SESSION['up']))
						{
							//$nk = count($_SESSION['up']);
							$_SESSION['up'][] = $up_session;
						}

						//取出nk
						krsort($_SESSION['up']);
						$i = 0;
						$nk = 0;
						if(is_array($_SESSION['up']) && count($_SESSION['up']) > 0)
						{
							foreach($_SESSION['up'] as $kst => $vst)
							{
								if($i == 0)
								{
									$nk = $kst;
								}
								$i++;
							}
						}

						//$nk = 0;
					}
					$nowsession = $nk;

					return true;
				}
			}
		}
		else
		{
			$this->seterror(1004);   //formname为空
			return false;
		}
	}

	//取出预上传的资源(资源下标，取出的图片的宽高值的下标)
	public function getsessionimg($nk = '' , $readnum = 0)
	{
		//@file_put_contents("log_b.txt" , 's2:' . print_r($_SESSION , true));
		$tmp_up = $_SESSION['up'][$nk]; //临时文件的名称
		$g_im = $this->getresource($tmp_up);
		if($nk === '')
		{
			$this->seterror(1011);   //资源错误
		}
		else if(!isset($_SESSION['up']))
		{
			$this->seterror(1012);   //资源错误
		}
		else if(!isset($_SESSION['up'][$nk]))
		{
			$this->seterror(1013);   //资源不正确
		}
		else
		{
			$tmp_up = $_SESSION['up'][$nk]; //临时文件的名称
			$g_im = $this->getresource($tmp_up);
			if($this->error == 1001 || $this->error == 1002)
			{
				unset($_SESSION['up'][$nk]);
				/*
				//后面的向前移动
				for($i = $nk ; $i < count($_SESSION['up']) ; $i++)
				{
				if(isset($_SESSION['up'][($i+1)]))
				{
				$_SESSION['up'][$i] = $_SESSION['up'][($i+1)];
				}
				}
				*/
				unset($_SESSION['up'][count($_SESSION['up'])]);
				$this->seterror(1021);   //资源取出错误
			}
			else
			{
				$new_w_h_arr = $this->getnew_w_h($g_im['w'] , $g_im['h'] , $readnum);
				//开始裁切图片
				$this->cut_img($g_im , $new_w_h_arr , $readnum , 0);
				$this->seterror(0);
			}
		}
		//结束后注销资源
		if(isset($g_im['r']))
		{
			imagedestroy($g_im['r']);
		}
		if($this->error == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//正式上传
	public function upnow($nk)
	{
		if(isset($_SESSION['up'])){
			$tmp_up = $_SESSION['up'][$nk]; //临时文件的名称
			$g_im = $this->getresource($tmp_up);
			if($nk === '')
			{
				$this->seterror(1011);   //资源错误
			}
			else if(!isset($_SESSION['up']))
			{
				$this->seterror(1012);   //资源错误
			}
			else if(!isset($_SESSION['up'][$nk]))
			{
				$this->seterror(1013);   //资源不正确
			}
			else
			{
				if($this->error == 1001 || $this->error == 1002)
				{
					unset($_SESSION['up'][$nk]);
					unset($_SESSION['up'][count($_SESSION['up'])]);
					$this->seterror(1021);  //资源取出错误
				}
				else
				{
					/*
					//11-29更改，直接作用于image上
					$this->wharr[0]['w'] = $g_im['w'];
					$this->wharr[0]['h'] = $g_im['h'];
					foreach($this->wharr as $k => $v)
					{
					if($k <> 0)
					{
					$new_w_h_arr = $this->getnew_w_h($g_im['w'] , $g_im['h'] , $k);
					//开始裁切图片
					$this->cut_img($g_im , $new_w_h_arr , $k , 1);
					}
					}
					$this->seterror(0);
					*/
					$jsonwh = json_encode($this->wharr);
					if($this->uid == 0)
					{
						$uid    = intval($_SESSION['userinfo']['uid']);
					}
					else
					{
						$uid    = $this->uid;
					}
					$imagepost = base64_encode(file_get_contents($tmp_up));
					$para = array(
					'uid'       => $uid,
					'imagepost' => $imagepost,
					'jsonwh'    => $jsonwh,
					);
					$curlobj = new Curl;
					$r = $curlobj -> post($this->imageServer , $para);
					$re_arr = json_decode($r , true);
					if($re_arr['error'] == 0 && $re_arr['reason'] == 0)
					{
						$this->picarr = $re_arr['picarr'];
						$this->seterror(0);
					}
					else
					{
						$this->seterror(10022);  //image远端上传失败了
					}
				}
			}
			//结束后注销资源
			if(isset($g_im['r']))
			{
				imagedestroy($g_im['r']);
			}
			if($this->error == 0)
			{
				return true;
			}
			else
			{
				return false;
			}
			/*
			//11-29更改，直接作用于image上
			//保留原图
			if($this->error == 0)
			{

			//将原图进行一次压缩处理(90%清析度)
			$type = $g_im['t'];   //图片类型1-gif;2-jpg;3-png;6-bmp
			$this->newpicname[0] = $this->newpicpath . $this->filename . '_0' . $g_im['b'];
			$this->createfolder($this->newpicpath);
			//
			if($type <> 1)
			{
			$imnewcreate = imagecreatetruecolor($g_im['w'], $g_im['h']);
			imagealphablending($imnewcreate, true);
			imagecopyresampled($imnewcreate, $g_im['r'], 0, 0, 0, 0, $g_im['w'], $g_im['h'] , $g_im['w'], $g_im['h']);
			//输出图像
			switch($type)
			{
			//case 1:imagegif($imnewcreate , $this->newpicname[0], $this->quality);break;
			case 2:imagejpeg($imnewcreate, $this->newpicname[0], $this->quality);break;
			case 3:imagepng($imnewcreate, $this->newpicname[0], intval($this->quality/10));break;
			case 6:imagebmp($imnewcreate, $this->newpicname[0], $this->quality);break;
			default:return false;
			}
			imagedestroy($imnewcreate);
			}
			else  //gif图直接copy
			{
			//$imnewcreate = imagecreate($g_im['w'], $g_im['h']);
			//imagealphablending($imnewcreate, true);
			//imagecopyresized($imnewcreate, $g_im['r'], 0, 0, 0, 0, $bigwidth, $bigheight , $g_im['w'], $g_im['h']);
			@copy($_SESSION['up'][$nk] , $this->newpicname[0]);
			}

			$this->newpicname['old'] = $this->newpicpath . $this->filename . '_old' . $g_im['b'];
			$this->createfolder($this->newpicpath);
			@copy($_SESSION['up'][$nk] , $this->newpicname['old']);

			unlink($_SESSION['up'][$nk]);
			unset($_SESSION['up'][$nk]);

			//结束后注销资源
			if(isset($g_im['r']))
			{
			imagedestroy($g_im['r']);
			}
			return true;
			}
			else
			{
			//结束后注销资源
			if(isset($g_im['r']))
			{
			imagedestroy($g_im['r']);
			}
			return false;
			}
			*/
		}else{
			return false;
		}
	}

	//直接上传图片（给一个图片的地址进行上传操作）
	public function copy_img($tmp_up = '')
	{
		if($tmp_up == '' || !file_exists($tmp_up))
		{
			$this->seterror(1021);  //资源取出错误
			return false;
		}
		else
		{
			$g_im = $this->getresource($tmp_up);
			if($this->error == 1001 || $this->error == 1002)
			{
				$this->seterror(1021);  //资源取出错误
				return false;
			}
			else
			{
			    
				$this->wharr[0]['w'] = $g_im['w'];
				$this->wharr[0]['h'] = $g_im['h'];
				foreach($this->wharr as $k => $v)
				{
					if($k <> 0)
					{
						$new_w_h_arr = $this->getnew_w_h($g_im['w'] , $g_im['h'] , $k);
						//print_r($new_w_h_arr);
						//开始裁切图片
						$this->cut_img($g_im , $new_w_h_arr , $k , 1);
					}
				}
				$this->seterror(0);
			}
			//保留原图
			if($this->error == 0)
			{
				//将原图进行一次压缩处理(90%清析度)
				$type = $g_im['t'];   //图片类型1-gif;2-jpg;3-png;6-bmp
				$this->newpicname[0] = $this->newpicpath . $this->filename . '_0' . $g_im['b'];
				$this->createfolder($this->newpicpath);
				//
// 				if($type <> 1)
// 				{
// 					$imnewcreate = imagecreatetruecolor($g_im['w'], $g_im['h']);
// 					imagealphablending($imnewcreate, true);
// 					imagecopyresampled($imnewcreate, $g_im['r'], 0, 0, 0, 0, $g_im['w'], $g_im['h'] , $g_im['w'], $g_im['h']);
// 					//输出图像
// 					switch($type)
// 					{
// 						//case 1:imagegif($imnewcreate , $this->newpicname[0], $this->quality);break;
// 						case 2:imagejpeg($imnewcreate, $this->newpicname[0], $this->quality);break;
// 						case 3:imagepng($imnewcreate, $this->newpicname[0], intval($this->quality/10));break;
// 						case 6:imagebmp($imnewcreate, $this->newpicname[0], $this->quality);break;
// 						default:return false;
// 					}
// 					imagedestroy($imnewcreate);
// 				}
// 				else  //gif图直接copy
// 				{
					//$imnewcreate = imagecreate($g_im['w'], $g_im['h']);
					//imagealphablending($imnewcreate, true);
					//imagecopyresized($imnewcreate, $g_im['r'], 0, 0, 0, 0, $bigwidth, $bigheight , $g_im['w'], $g_im['h']);
					@copy($tmp_up , $this->newpicname[0]);
				//}

//				$this->newpicname['old'] = $this->newpicpath . $this->filename . '_old' . $g_im['b'];
//				$this->createfolder($this->newpicpath);
//				@copy($tmp_up , $this->newpicname['old']);

				//结束后注销资源
				if(isset($g_im['r']))
				{
					imagedestroy($g_im['r']);
				}
				return true;
			}
			else
			{
				//结束后注销资源
				if(isset($g_im['r']))
				{
					imagedestroy($g_im['r']);
				}
				return false;
			}
		}
	}


	//裁切图片
	private function cut_img($g_im , $n_arr , $readnum , $out = 0)
	{
		$bigwidth  = min($n_arr['w'] , $g_im['w']);
		$bigheight = min($n_arr['h'] , $g_im['h']);
		$type = $g_im['t'];   //图片类型1-gif;2-jpg;3-png;6-bmp

		//开始拉伸
		if(function_exists("imagecopyresampled"))
		{
			if($type <> 1)
			{
				$imnewcreate = imagecreatetruecolor($bigwidth, $bigheight);
				imagealphablending($imnewcreate, true);
				$isok = imagecopyresampled($imnewcreate, $g_im['r'], 0, 0, 0, 0, $bigwidth, $bigheight , $g_im['w'], $g_im['h']);
			}
			else
			{
				$imnewcreate = imagecreate($bigwidth, $bigheight);
				imagealphablending($imnewcreate, true);
				$isok = imagecopyresized($imnewcreate, $g_im['r'], 0, 0, 0, 0, $bigwidth, $bigheight , $g_im['w'], $g_im['h']);
			}
		}
		else
		{
			$imnewcreate = imagecreate($bigwidth, $bigheight);
			imagealphablending($imnewcreate, true);
			$isok = imagecopyresized($imnewcreate, $g_im['r'], 0, 0, 0, 0, $bigwidth, $bigheight , $g_im['w'], $g_im['h']);
		}

		//开始中间切割
		if($this->ismiddlecut == 1)
		{

			$cutwidth  = $n_arr['px'];
			$cutheight = $n_arr['py'];

			if($cutwidth > 0 || $cutheight > 0)
			{
				$mw = $this->wharr[$readnum]['w'];
				$mh = $this->wharr[$readnum]['h'];
				if(function_exists("imagecopyresampled"))
				{
					if($type <> 1)
					{
						$imnewcreate_c = imagecreatetruecolor($mw, $mh);
						imagealphablending($imnewcreate_c, true);
						$isok = imagecopyresampled($imnewcreate_c, $imnewcreate, 0, 0, $cutwidth, $cutheight , $mw, $mh , $mw, $mh);
					}
					else
					{
						$imnewcreate_c = imagecreate($mw, $mh);
						imagealphablending($imnewcreate_c, true);
						$isok = imagecopyresized($imnewcreate_c, $imnewcreate, 0, 0, $cutwidth, $cutheight , $mw, $mh , $mw, $mh);
					}
				}
				else
				{
					$imnewcreate_c = imagecreate($mw, $mh);
					imagealphablending($imnewcreate_c, true);
					$isok = imagecopyresized($imnewcreate_c, $imnewcreate, 0, 0, $cutwidth, $cutheight , $mw, $mh , $mw, $mh);
				}
				$imnewcreate = $imnewcreate_c;
			}
		}

		//增加覆层
		if(!empty($this->waterpath[$readnum]) && file_exists($this->waterpath[$readnum]))
		{
			//判断是否是gif，适时更换logo图
			if($type == 1)
			{
				$this->waterpath[$readnum] = str_replace('.png' , '.gif' , $this->waterpath[$readnum]);
			}
			$new_r_array = $this->getresource($this->waterpath[$readnum] , 1);
			if($new_r_array['r'])
			{
				$r_copy = imagecopy($imnewcreate, $new_r_array['r'] , 0 , 0 , 0 , 0 , $mw, $mh);//拷贝水印到目标文件
			}
		}


		//输出图像
		if($out == 1)
		{
			$this->newpicname[$readnum] = $this->newpicpath . $this->filename . '_' . $readnum . '' . $g_im['b'];
			$this->createfolder($this->newpicpath);
			//输出图像
			$this->newpicname[$readnum] = str_replace('//' , '/' , $this->newpicname[$readnum]);
			//echo $type . "|" . $this->newpicname[$readnum] . "<br />";
			switch($type)
			{
				case 1:$s=imagegif($imnewcreate , $this->newpicname[$readnum], $this->quality);break;
				case 2:$s=imagejpeg($imnewcreate, $this->newpicname[$readnum], $this->quality);break;
				case 3:$s=imagepng($imnewcreate, $this->newpicname[$readnum], intval($this->quality/10));break;
				case 6:$s=imagebmp($imnewcreate, $this->newpicname[$readnum], $this->quality);break;
				default:return false;
			}
			//echo $readnum . "|" . $this->newpicname[$readnum] . "|";
			//var_dump($s);
			//echo "<br />";
			//注销资源
			if(isset($imnewcreate))
			{
				imagedestroy($imnewcreate);
			}
			if(!empty($this->waterpath[$readnum]) && file_exists($this->waterpath[$readnum]))
			{
				if(isset($new_r_array['r']))
				{
					imagedestroy($new_r_array['r']);
				}
			}
		}
		else  //不存储文件，仅对外输出
		{
			switch($type)
			{
				case 1:Header("Content-type: image/gif"); imagegif($imnewcreate,'', $this->quality);break;
				case 2:Header("Content-type: image/jpeg"); imagejpeg($imnewcreate,'', $this->quality);break;
				case 3:Header("Content-type: image/png"); imagepng($imnewcreate,'', intval($this->quality/10));break;
				case 6:Header("Content-type: image/bmp"); imagebmp($imnewcreate,'', $this->quality);break;
				default:return false;
			}
			//注销资源
			if(isset($imnewcreate))
			{
				imagedestroy($imnewcreate);
			}
			if(!empty($this->waterpath[$readnum]) && file_exists($this->waterpath[$readnum]))
			{
				if(isset($new_r_array['r']))
				{
					imagedestroy($new_r_array['r']);
				}
			}
		}

	}

	//取消预上传
	public function delupfirst($k = '')
	{
		unset($_SESSION['up'][$k]);
	}

	//取消全部
	public function delupall()
	{
		unset($_SESSION['up']);
	}


	//设置错误
	private function seterror($error)
	{
		$this->error = $error;
	}

	//取出错误值
	public function geterror()
	{
		return $this->error;
	}

	//取出本次上传的所有图片的值
	public function showpic()
	{
		if(is_array($this->picarr) && count($this->picarr) > 0)
		{
			$rpath = $this->picarr;
		}
		else
		{
			$rpath['server'] = $this->newpicname;
			if(is_array($this->newpicname) && count($this->newpicname) > 0)
			{
				foreach($this->newpicname as $k => $v)
				{
					$rpath['url'][$k] = str_replace($_SERVER['DOCUMENT_ROOT'] , $this->baseurl , $v);
					$rpath['md5'][$k] = md5(@file_get_contents($v));
				}
			}
		}
		return $rpath;
	}

	//取得资源(opentype=1为系统内资源打开，可以不管类型)
	private function getresource($path = '' , $opentype = 1)
	{
		$new_r = array();
		if(!empty($path) && file_exists($path))
		{
			$info   = @getimagesize($path);
			$width  = $info[0];
			$height = $info[1];
			$type   = $info[2];
			if($opentype == 0)
			{
				switch($type)
				{
					case 1:$im = imagecreatefromgif($path);$btype='.gif';break;
					case 2:$im = imagecreatefromjpeg($path);$btype='.jpg';break;
					//注:png/bmp需求版本支持,暂不开放
					case 3:$this->error = 1001;$im= false;$btype='';break;   //不支持
					case 6:$this->error = 1001;$im= false;$btype='';break;   //不支持
					//case 3:$im = imagecreatefrompng($path);$btype='.png';break;
					//case 6:$im = imagecreatefromwbmp($path);$btype='.bmp';break;
					default:$this->error = 1001;$im= false;$btype='';
				}
			}
			else
			{
				switch($type)
				{
					case 1:$im = imagecreatefromgif($path);$btype='.gif';break;
					case 2:$im = imagecreatefromjpeg($path);$btype='.jpg';break;
					case 3:$im = imagecreatefrompng($path);$btype='.png';break;
					case 6:$im = imagecreatefromwbmp($path);$btype='.bmp';break;
					default:$this->error = 1001;$im= false;$btype='';
				}
			}
			$new_r['w']  = $width;
			$new_r['h']  = $height;
			$new_r['t']  = $type;
			$new_r['r']  = $im;
			$new_r['b']  = $btype;
			$s = implode('|' , $new_r);
		}
		else
		{
			$new_r['w']  = 0;
			$new_r['h']  = 0;
			$new_r['t']  = 0;
			$new_r['r']  = false;
			$new_r['b']  = '';
			$this->error = 1002;  //无文件
		}
		return $new_r;
	}

	//取得新的宽与高(第三项值为取得的哪一个数组的宽高)
	private function getnew_w_h($oldwidth = 0 , $oldheight = 0 , $setwhkey = 0)
	{
		//print_r($this->wharr);
		if($setwhkey == 0 || (!isset($this->wharr[$setwhkey]['h'])) || (!isset($this->wharr[$setwhkey]['w'])))
		{
			$newwidth  = $oldwidth;
			$newheight = $oldheight;
			if($oldheight == 0)
			{
				$w_h_b     = 1;
			}
			else
			{
				$w_h_b = round($oldwidth / $oldheight , 2);
			}
		}
		else
		{
			$oldwidth  = max($oldwidth , 0);
			$oldheight = max($oldheight , 0);
			if($oldwidth == 0 || $oldheight == 0)
			{
				$newwidth  = 0;
				$newheight = 0;
				$w_h_b     = 1;
			}
			else
			{
				$maxwidth  = $this->wharr[$setwhkey]['w'];
				$maxheight = $this->wharr[$setwhkey]['h'];
				$w_h_b = round($oldwidth / $oldheight , 2);

				if($this->ismiddlecut == 0)
				{
					$ischange = 0;
					if($oldwidth > $maxwidth)
					{
						$newwidth  = $maxwidth;
						$newheight = intval($newwidth / $w_h_b);
						$ischange  = 1;
					}
					if($newheight > $maxheight)
					{
						$newheight = $maxheight;
						$newwidth  = $newheight * $w_h_b;
						$ischange  = 1;
					}
					if($ischange  == 0)
					{
						$newwidth  = $maxwidth;
						$newheight = $maxheight;
					}
				}
				else
				{
					$ischange = 0;
					if($w_h_b > 1)
					{
						//调高
						if($oldheight > $maxheight)
						{
							$newheight = $maxheight;
							$newwidth  = intval($newheight * $w_h_b);
							$ischange  = 1;
						}
						else
						{
							$newheight = $oldheight;
							$newwidth  = $newheight * $w_h_b;
							$ischange  = 1;
						}
					}
					else
					{
						if($oldwidth > $maxwidth)
						{
							$newwidth  = $maxwidth;
							$newheight = intval($newwidth / $w_h_b);
							$ischange  = 1;
						}
						else
						{
							$newwidth  = $oldwidth;
							$newheight = intval($newwidth / $w_h_b);
							$ischange  = 1;
						}
					}
					if($ischange  == 0)
					{
						$newwidth  = $maxwidth;
						$newheight = $maxheight;
					}
				}

			}
		}
		$new['w'] = intval($newwidth);
		$new['h'] = intval($newheight);
		//设置偏移量
		//等比缩放
		if($this->iszoom == 1)
		{
			$new['px'] = 0;
			$new['py'] = 0;
		}
		else
		{
			if($new['w'] > $maxwidth)
			{
				$new['px'] = intval(($new['w'] - $maxwidth)/2);
			}
			else
			{
				//$new['px'] = 0;
				$new['px'] = intval(($new['w'] - $maxwidth)/2);
			}
			if($new['h'] > $maxheight)
			{
				$new['py'] = intval(($new['h'] - $maxheight)/2);
			}
			else
			{
				//$new['py'] = 0;
				$new['py'] = intval(($new['h'] - $maxheight)/2);
			}
		}
		$new['b'] = $w_h_b;  //宽高比
		return $new;
	}

	public function createfolder($dir)
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
}

/*
例：
$up = new Uploadimage;
$formname = 'file';
//设置宽高（注：下标从1开始）
$w_h_arr[1]['w'] = 82;
$w_h_arr[1]['h'] = 82;
//$w_h_arr[2]['w'] = 146;
//$w_h_arr[2]['h'] = 146;
$up->setimagewh($w_h_arr);
//正式上传
$up->upfirst($sid);  //返回本次上传的$sid，注：多次上传时会在$_SESSION中留下值
//显示本次上传的图片
$up->getsessionimg($sid , 1);  //1-表示用第一个格式（812*82显示），返回一张图片（可用ajax调用一个PHP，如a.php?sid=0,然后调用该类来显示成图片，前端使用<img src="a.php?sid=0" />）
//正式上传本次图片
$up->upnow($sid);
//给一个路径进行上传
$up->copy_img($tmp_up = '');
//显示上传的错误值
$error = $up->geterror();
//显示本次上传后的所有图片路径,返回数组
$picarr = $up->showpic();
//错误码
1001 / 1002 /1011 /1012 /1013 -图片资源错误
1004 - form空
1005 - 尺寸超出
*/