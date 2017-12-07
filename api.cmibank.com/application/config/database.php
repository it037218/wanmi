<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(php_sapi_name() == 'cli'){
    if($_SERVER['argc'] == 4 && $_SERVER['argv'][3] == 'testing'){
        $dbr_host = '10.9.193.55';
        $dbr_user = 'root';
        $dbr_passwd = 'Aa123456';
        $dbw_host = '10.9.193.55';
        $dbw_user = 'root';
        $dbw_passwd = 'Aa123456';
    } else {
        $dbr_host = '10.66.250.15';
        $dbr_user = 'root';
        $dbr_passwd = 'add9a429f069d00b';
        
        $dbw_host = '10.66.250.15';
        $dbw_user = 'root';
        $dbw_passwd = 'add9a429f069d00b';
    }
}else{
    if(@$_SERVER['ENVIRONMENT'] == 'production'){
        $dbr_host = '10.66.250.15';
        $dbr_user = 'root';
        $dbr_passwd = 'add9a429f069d00b';
        
        $dbw_host = '10.66.250.15';
        $dbw_user = 'root';
        $dbw_passwd = 'add9a429f069d00b';
    }elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
        $dbr_host = '117.50.2.20';
        $dbr_user = 'root';
        $dbr_passwd = 'Aa123456';

        $dbw_host = '117.50.2.20';
        $dbw_user = 'root';
        $dbw_passwd = 'Aa123456';
    } else {
        $dbr_host = '117.50.2.20';
        $dbr_user = 'root';
        $dbr_passwd = 'Aa123456';
    
        $dbw_host = '117.50.2.20';
        $dbw_user = 'root';
        $dbw_passwd = 'Aa123456';
    }
}
$db['dbr']['hostname'] = $dbr_host;
$db['dbr']['username'] = $dbr_user;
$db['dbr']['password'] = $dbr_passwd;
$db['dbr']['database'] = 'cmibank';
$db['dbr']['dbdriver'] = 'mysql';
$db['dbr']['dbprefix'] = '';
$db['dbr']['pconnect'] = TRUE;
$db['dbr']['db_debug'] = TRUE;
$db['dbr']['cache_on'] = FALSE;
$db['dbr']['cachedir'] = '';
$db['dbr']['char_set'] = 'utf8';
$db['dbr']['dbcollat'] = 'utf8_general_ci';
$db['dbr']['swap_pre'] = '';
$db['dbr']['autoinit'] = TRUE;
$db['dbr']['stricton'] = FALSE;

$db['dbw']['hostname'] = $dbw_host;
$db['dbw']['username'] = $dbw_user;
$db['dbw']['password'] = $dbw_passwd;
$db['dbw']['database'] = 'cmibank';
$db['dbw']['dbdriver'] = 'mysql';
$db['dbw']['dbprefix'] = '';
$db['dbw']['pconnect'] = TRUE;
$db['dbw']['db_debug'] = TRUE;
$db['dbw']['cache_on'] = FALSE;
$db['dbw']['cachedir'] = '';
$db['dbw']['char_set'] = 'utf8';
$db['dbw']['dbcollat'] = 'utf8_general_ci';
$db['dbw']['swap_pre'] = '';
$db['dbw']['autoinit'] = TRUE;
$db['dbw']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */