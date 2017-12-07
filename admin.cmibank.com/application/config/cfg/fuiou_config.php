<?php
if (php_sapi_name() == 'cli') {
    if ($_SERVER['argc'] == 4 && $_SERVER['argv'][3] == 'testing') {
        $config = array(
            'ver' => '1.00',
            'merchant_id' => '0002900F0280321',
            'merchant_key' => 'fx45crzkmo8akn24plwvrv8ywd10zjuy',
            'withdraw_merchant_id' => '0002900F0345178',
            'withdraw_merchant_key' => '123456',
            'withdraw_url' => 'https://fht-test.fuiou.com/fuMer/req.do',
            'pay_notify_url' => 'http://api.cmibank.vip/fuioupay_back/payNotify' 
        );
    } else {
        $config = array(
            'ver' => '1.00',
            'merchant_id' => '0002900F0504406',
            'merchant_key' => '5f0bx65hzecaduuib7ckfidy17ck4fa2',
            'withdraw_merchant_id' => '0002900F0504406',
            'withdraw_merchant_key' => 'ljg6f5454dkuzfcl70imnmtnaaebc65u',
            'withdraw_url' => 'https://fht.fuiou.com/req.do',
            'pay_notify_url' => 'http://api.cmibank.com/fuioupay_back/payNotify' 
        );
    }
} else {
    if (@$_SERVER['ENVIRONMENT'] == 'production') {
        $config = array(
            'ver' => '1.00',
            'merchant_id' => '0002900F0504406',
            'merchant_key' => '5f0bx65hzecaduuib7ckfidy17ck4fa2',
            'withdraw_merchant_id' => '0002900F0504406',
            'withdraw_merchant_key' => 'ljg6f5454dkuzfcl70imnmtnaaebc65u',
            'withdraw_url' => 'https://fht.fuiou.com/req.do',
            'pay_notify_url' => 'http://api.cmibank.com/fuioupay_back/payNotify' 
        );
    } elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
        $config = array(
            'ver' => '1.00',
            'merchant_id' => '0002900F0280321',
            'merchant_key' => 'fx45crzkmo8akn24plwvrv8ywd10zjuy',
            'withdraw_merchant_id' => '0002900F0345178',
            'withdraw_merchant_key' => '123456',
            'withdraw_url' => 'https://fht-test.fuiou.com/fuMer/req.do',
            'pay_notify_url' => 'http://api.cmibank.vip/fuioupay_back/payNotify' 
        );
    } else {
        $config = array(
            'ver' => '1.00',
            'merchant_id' => '0002900F0280321', //充值环节用
            'merchant_key' => 'fx45crzkmo8akn24plwvrv8ywd10zjuy',
            'withdraw_merchant_id' => '0002900F0345178',//代付系统提现环节商户号
            'withdraw_merchant_key' => '123456',
            'withdraw_url' => 'https://fht-test.fuiou.com/fuMer/req.do',
            'pay_notify_url' => 'http://api.cmibank.vip/fuioupay_back/payNotify' 
        );
    }
}
?>