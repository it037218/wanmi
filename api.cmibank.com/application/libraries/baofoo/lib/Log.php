<?php

class Log
{
	public static function LogWirte($Astring)
	{

		if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
		    $file = './baofoolog' . date("Y-m-d") . '.log';
		}else{
		    $file = '/usr/logs/baofoolog' . date("Y-m-d") . '.log';
		}
		$LogTime = date('Y-m-d H:i:s',time());
		if(!file_exists($file))
		{
			$logfile = fopen($file, "w") or die("Unable to open file!");
			fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
			fclose($logfile);
		}else{		
			$logfile = fopen($file, "a") or die("Unable to open file!");
			fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
			fclose($logfile);
		}			
	}
	
	
	
	public static function returnLogWirte($Astring)
	{
	
	    if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
	        $file = './baofoolog_notify_' . date("Y-m-d") . '.log';
	    }else{
	        $file = '/usr/logs/baofoolog_notify_' . date("Y-m-d") . '.log';
	    }
	    $LogTime = date('Y-m-d H:i:s',time());
	    if(!file_exists($file))
	    {
	        $logfile = fopen($file, "w") or die("Unable to open file!");
	        fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
	        fclose($logfile);
	    }else{
	        $logfile = fopen($file, "a") or die("Unable to open file!");
	        fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
	        fclose($logfile);
	    }
	}
	
	public static function withdrawLogWirte($Astring)
	{
	
		if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
			$file = './baofoolog_withdraw_' . date("Y-m-d") . '.log';
		}else{
			$file = '/usr/logs/baofoolog_withdraw_' . date("Y-m-d") . '.log';
		}
		$LogTime = date('Y-m-d H:i:s',time());
		if(!file_exists($file))
		{
			$logfile = fopen($file, "w") or die("Unable to open file!");
			fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
			fclose($logfile);
		}else{
			$logfile = fopen($file, "a") or die("Unable to open file!");
			fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
			fclose($logfile);
		}
	}
	
	public static function queryLogWirte($Astring)
	{
	
		if(isset($_SERVER['SystemRoot']) && $_SERVER['SystemRoot'] == 'C:\Windows'){
			$file = './baofoolog_query_' . date("Y-m-d") . '.log';
		}else{
			$file = '/usr/logs/baofoolog_query_' . date("Y-m-d") . '.log';
		}
		$LogTime = date('Y-m-d H:i:s',time());
		if(!file_exists($file))
		{
			$logfile = fopen($file, "w") or die("Unable to open file!");
			fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
			fclose($logfile);
		}else{
			$logfile = fopen($file, "a") or die("Unable to open file!");
			fwrite($logfile, "[$LogTime]:".$Astring."\r\n");
			fclose($logfile);
		}
	}
}

?>