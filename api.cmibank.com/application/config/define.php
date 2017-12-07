<?php

if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '127.0.0.1'){
    define('ENVIRONMENT', 'development');
}else if(isset($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] == '10.9.160.199'){
    define('ENVIRONMENT', 'development');
}else{
     

}
if(@$_SERVER['ENVIRONMENT'] == 'production'){
    define('ENVIRONMENT', 'production');
    define('DOMAIN', 'http://api.cmibank.com/');
    $dbw_passwd = 'add9a429f069d00b';
    define('TEST_IS_IDENTITY', true);
}elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    define('ENVIRONMENT', 'testing');
    define('DOMAIN', 'http://api.cmibank.vip/');
    define('TEST_IS_IDENTITY', true);
} else {
    define('ENVIRONMENT', 'development');
    define('DOMAIN', 'http://api.cmibank.dev/');
    define('TEST_IS_IDENTITY', true);
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

if(strstr(@$_SERVER['HTTP_USER_AGENT'], 'iPhone')){
    define('IS_ANDROID', false);
}else{
    define('IS_ANDROID', true);
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

define('EXT', '.php');

define('FCPATH', str_replace(SELF, '', __FILE__));

define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));

define('PF', 'pzapp');

define('NOW', time());

define('STATIC_DOMAIN', 'http://static1.cmibank.com/');

define('MOBILEVERIFY_LEN', 4);

define('USER_ACTION_PAY', 0);               //充值
define('USER_ACTION_PCASHOUT', 2);          //取现

define('USER_ACTION_PREPAYMENT', 4);        //定期还款
define('USER_ACTION_LREPAYMENT', 14);       //活期还款
define('USER_ACTION_PRODUCT', 1);           //购买定期
define('USER_ACTION_LONGPRODUCT', 11);      //购买活期
define('USER_ACTION_LONGTOBALANCE', 13);    //活期转余额

define('USER_ACTION_KLPRODUCT', 31);      //购买快乐宝
define('USER_ACTION_KLTOBALANCE', 33);      //快乐宝转余额

define('USER_ACTION_ACTIVITY', 5);          //活动赠送
define('USER_ACTION_INVITE', 6);            //活动赠送
define('USER_ACTION_BE_INVITE', 44);            //被邀请活动赠送
define('USER_ACTION_EXPMONEY', 7);          //体验金利息发放

define('USER_ACTION_PAY_FAIL', 10);          //充值失败
define('USER_ACTION_WITHDRAWFAILED', 20);    //取现失败
define('USER_ACTION_WITHDRAWBACK', 21);      //取现退回
define('USER_ACTION_WITHDRAWWASTE', 25);    //取现废弃

define('LONGPRODUCT_CID', 1);
define('LONGPRODUCT_PTID', 14);
define('NEW_LONGPRODUCT_PTID', 15);

define('KLPRODUCT_CID', 1);
define('KLPRODUCT_PTID', 14);

define('EXPMONEY_LOG_ADD', 1);  //获得
define('EXPMONEY_LOG_COST', 0); //消费
define('EXPMONEY_LOG_END', 2);  //到期

define('ACTIVITY_GIVE_MONEY', 1);
define('ACTIVITY_GIVE_MONEY_MONEY', 20);

define('MSM_PLAT', 'submail'); //submail  cpunc

//true开启  false关闭
define('INVITE', 'true');

define('RED_BAG', 'true');
define('LUCKY_BAG', 'true');

define('COUPON', 'true');

define('HDSJ', 'true');

define('COUPON_ACTIVITY_REGEDIT', 1);
define('COUPON_ACTIVITY_VALIDATE', 2);
define('COUPON_ACTIVITY_BUY', 3);
define('COUPON_ACTIVITY_FIRSTBUY', 4);
define('COUPON_ACTIVITY_DIRECT', 5);
define('COUPON_ACTIVITY_JIFENG', 6);

define('EXPMONEY_ACTIVITY_REGEDIT', 1);
define('EXPMONEY_ACTIVITY_JIFENG', 4);

define('JIFENG_BUY_PRODUCT', 1);
define('JIFENG_REGEDIT', 2);
define('JIFENG_BANGKA', 3);
define('JIFENG_FIRSTBUY', 4);
define('JIFENG_AWARD', 5);
define('JIFENG_QIANDAO', 6);
define('JIFENG_LEIJI_QIANDAO', 7);
define('JIFENG_DUIHUANG', 51);

define('NEW_USER_PTID', 11);

define('PAY_QUDAO', 'llpay');
define('PAY_TYPE', 'nobindcard');
define('PAY_PLAT', 'JYT');
define('WITHDRAW_PLAT', 'JYT');
define('YEE_AMOUNT_LIMIT', '2000');

define('LONGPRODUCT_LIMIT', true);              //是否开启活期限额
define('LONGPRODUCT_LIMIT_DEFAULT', 50000);     //活期默认额度

define('YEE_TG_PLATFORMNO', 10049999937);       
define('WITHDRAW_SXF', 2);                      //取现手续费

include(APPPATH.'config/redis_key.php');
