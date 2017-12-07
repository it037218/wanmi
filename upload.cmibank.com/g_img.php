<?php

$file = $_POST['file'];
if($file == '')
{
	die(json_encode(array('error'=>1 , 'msg'=>"没有文件!")));
}
else
{
	$file_s = base64_decode(urldecode($file));
}
$path 	  = $_POST['path'];
$filename = $_POST['filename'];
//$filepath = '/data/web/image.xyzs.com/static/' . $path;
$filepath = dirname(__FILE__) . '/' . $path;
$filepath = str_replace("\\" , "/" , $filepath);
$re = createfolder($filepath);
if($re){
	if(is_file($filepath . "/" . $filename)){
		unlink($filepath . "/" . $filename);
	}
	$fp = @fopen($filepath . "/" . $filename , 'a');
	if($fp){
		@fwrite($fp , $file_s);
		@fclose($fp);
		die(json_encode(array('error'=>0 , 'msg'=>"文件上传成功")));
	}else{ 
		die(json_encode(array('error'=>3 , 'msg'=>"文件上传失败,请检查权限及路径是否正确！")));
	}
	
}else{ 
	die(json_encode(array('error'=>2 , 'msg'=>"文件上传失败,请检查权限及路径是否正确！")));
}

function createfolder($dir = '')
{
	if($dir == '')
	{
		return false;
	}
	$dir = $dir . '/';
	$dir = str_replace('/' . '/' , '/' , $dir);
	if($dir == "" || $dir == '/')
	{
		return 0;
	}
	else
	{
		$alldirarray = explode('/' , $dir);
		$count = count($alldirarray) - 1;
		$nodir = "";
		$createok = 1;
		for($i = 1 ; $i < $count ; $i++)
		{
			$nodir .=  '/' . $alldirarray[$i];
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
?>