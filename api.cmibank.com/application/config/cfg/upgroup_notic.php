<?php
if(@$_SERVER['ENVIRONMENT'] == 'production'){
    $qj_version = '1.0.0';
    $new_version = '1.0.0';
    $type = '0';
    $force_use_time = '';
}elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $qj_version = '1.0.0';
    $new_version = '1.0.0';
    $type = '0';
    $force_use_time = '';
}else{
    $qj_version = '1.0.5';
    $new_version = '1.0.0';
    $type = '0';
    $force_use_time = '';
}
$config = array(
    'title' => 'AppStore 最新版本上线公告',
    'content' => '
    亲爱的用户，您好！最新版本（1.0.1）已于2016年5月27日在AppStore正式上线。本次版本更新内容：
    一、优化产品使用界面；
    二、优化产品功能结构，提高用户体验；
    三、新增第三方宝付支付托管，用户资金更安全。如有疑问欢迎致电咨询：400-080-5611。',
    'qj_version' => $qj_version,
    'new_version' => $new_version,
    'type' => $type,//是否强制更新，0:否，1：是。
    'force_use_time' => $force_use_time, //最后使用时间
    'button_name' => '立即更新',
    //'url' => 'https://itunes.apple.com/cn/app/易米融/id1291154070',
);
