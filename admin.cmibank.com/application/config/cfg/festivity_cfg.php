<?php
//节日活动配置文件

//if(@$_SERVER['ENVIRONMENT'] == 'production'){
//
//}elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
//
//}else{
//
//}

$config = array(
    //11月28-12月11日活动
    'two_rule' => array(
        'one' => array(//活动期间所有用户总条件
            'three_product_tpid' => 100000,
            'six_product_tpid' => 60000,
            'year_product_tpid' => 30000,
        ),
        'two' => array(//获取期间个人条件
            'three_product_tpid' => 1000,
            'six_product_tpid' => 600,
            'year_product_tpid' => 300,
        )
    ),
    'product_tpid' => array('three_product_tpid' => '42','six_product_tpid' => '41', 'year_product_tpid' => '57'),
//    'fake_data' => array(
//        'three_product_tpid' => array(
//            '100000' => array('uid' => '100000','account' => '13841685874', 'product_tpid' => '42', 'money' => '5000'),
//        ),
//        'six_product_tpid' => array(
//            '100002' => array('uid' => '100002','account' => '13141684562', 'product_tpid' => '42', 'money' => '60000'),
//        ),
//        'year_product_tpid' => array(
//            '100004' => array('uid' => '100004','account' => '13641684980', 'product_tpid' => '42', 'money' => '5000'),
//        ),
//    ),
);
