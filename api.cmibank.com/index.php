<?php

$application_folder = 'application';

define('DS', '/');

define('ROOTPATH',dirname(__FILE__));

define('SYSLIBPATH',dirname(ROOTPATH).'/syscore');

define('SYSLIB', dirname(dirname(__FILE__)).'/');

$system_path = SYSLIBPATH;

define('BASEPATH', str_replace("\\", "/", $system_path . '/'));

// if (is_dir($application_folder))
// {
// 	define('APPPATH', $application_folder.'/');
// }
// else
// {
// 	if ( ! is_dir(BASEPATH.$application_folder.'/')) 
// 	{
// 		exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ". BASEPATH);
// 	}

// 	define('APPPATH', ROOTPATH. '/' .$application_folder.'/');
// }

define('APPPATH', ROOTPATH. '/' .$application_folder.'/');
require_once APPPATH.'config/define.php';
require_once BASEPATH.'core/CodeIgniter.php';
