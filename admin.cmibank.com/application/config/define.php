<?php



if(php_sapi_name() == 'cli'){
        define('ENVIRONMENT', 'production');
        define('OP_DOMAIN', 'http://admin.cmibank.com/');
        define('STATIC_DOMAIN', 'http://static1.cmibank.com/');
        define('UPLOAD_IMAGE', "http://upload1.cmibank.com/imageup.php");
}else{
    if(@$_SERVER['ENVIRONMENT'] == 'production'){
        define('ENVIRONMENT', 'production');
        define('OP_DOMAIN', 'http://admin.cmibank.com/');
        define('STATIC_DOMAIN', 'http://static1.cmibank.com/');
        define('UPLOAD_IMAGE', "http://upload.cmibank.com/imageup.php");
    }elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
        define('ENVIRONMENT', 'production');
        define('OP_DOMAIN', 'http://admin.cmibank.vip/');
        define('STATIC_DOMAIN', 'http://static.cmibank.vip/');
        define('UPLOAD_IMAGE', "http://upload.cmibank.vip/imageup.php");
    } else {
        define('ENVIRONMENT', 'development');
        define('OP_DOMAIN', 'http://admin.cmibank.dev/index.php');
        define('STATIC_DOMAIN', 'http://static.cmibank.dev');
        define('UPLOAD_IMAGE', "http://upload.cmibank.dev/imageup.php");
    }
}

if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
            error_reporting(E_ALL);
            break;
        case 'testing':
        case 'production':
            error_reporting(0);
            break;
        default:
            exit('The application environment is not set correctly.');
    }
}


if (defined('STDIN'))
{
    chdir(dirname(__FILE__));
}

if (realpath($system_path) !== FALSE)
{
    $system_path = realpath($system_path).'/';
}


$system_path = rtrim($system_path, '/').'/';

if ( ! is_dir($system_path))
{
    exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

define('EXT', '.php');


define('FCPATH', str_replace(SELF, '', __FILE__));

define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


define('USER_ACTION_PAY', 0);           //充值
define('USER_ACTION_PREPAYMENT', 4);     //定期还款
define('USER_ACTION_LREPAYMENT', 14);     //活期还款
define('USER_ACTION_PRODUCT', 1);       //购买定期
define('USER_ACTION_LONGPRODUCT', 11);  //购买活期
define('USER_ACTION_PCASHOUT', 2);      //取现
define('USER_ACTION_LONGTOBALANCE', 13);      //活期转余额
define('USER_ACTION_ACTIVITY', 5);          //活动赠送
define('USER_ACTION_INVITE', 6);          //邀请奖励
define('USER_ACTION_EXPMONEY', 7);          //体验金利息发放

define('USER_ACTION_PAY_FAIL', 10);          //充值失败
define('USER_ACTION_WITHDRAWFAILED', 20);    //取现失败
define('USER_ACTION_WITHDRAWBACK', 21);      //取现退回

define('LONGPRODUCT_CID', 1);
define('LONGPRODUCT_PTID', 14);
define('NEW_LONGPRODUCT_PTID', 15);

define('NOW', time());

define('COUPON_ACTIVITY_REGEDIT', 1);
define('COUPON_ACTIVITY_VALIDATE', 2);
define('COUPON_ACTIVITY_BUY', 3);
define('COUPON_ACTIVITY_FIRSTBUY', 4);
define('COUPON_ACTIVITY_DIRECT', 5);
define('COUPON_ACTIVITY_JIFENG', 6);

define('EXPMONEY_ACTIVITY_REGEDIT', 1);

include(APPPATH.'config/redis_key.php');

