<?php
if(@$_SERVER['ENVIRONMENT'] == 'production'){
    $qj_version = '1.0.0';
    $new_version = '1.0.3';
    $type = '0';
    $force_use_time = '2017-11-31 18:00:00';
    $host = 'https://api.cmibank.com';
}elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $qj_version = '1.0.0';
    $new_version = '1.0.2';
    $type = '0';
    $force_use_time = '2017-10-31 18:00:00';
    $host = 'http://api.cmibank.vip';
}else{
    $qj_version = '1.0.0';
    $new_version = '1.0.0';
    $type = '0';
    $force_use_time = '';
    $host = 'http://api.cmibank.dev';
}
$config = array(
    'title' => '新版本上线啦',
    'content' => '版本升级，请速安装。',
    'qj_version' => $qj_version,
    'new_version' => $new_version,
    'type' => $type,//是否强制更新，0:否，1：是。
    'force_use_time' => $force_use_time, //最后使用时间
    'button_name' => '立即更新',
    'url' => $host.'/system/getdownload',
    'md5'=>'A63A3F1CA885249E8C91CE7FC0149168'
);