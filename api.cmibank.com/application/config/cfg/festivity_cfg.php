<?php
//节日活动配置文件

if(@$_SERVER['ENVIRONMENT'] == 'production'){
    $buff_stime = '2017-11-11 00:00:00';
    $buff_etime = '2017-12-11 23:00:00';
    
    $start_time = '2017-11-28 09:30:00';
    $mid_time = '2017-12-04 23:59:59';
    $end_time = '2017-12-11 23:00:00';
    //复投活动
    $activety_fu_stime = '2017-11-28 00:00:00';
    $activety_fu_etime = '2017-12-11 23:00:00';
    //双十二
    $twelve_start_time = '2017-12-12 09:30:00';
    $twelve_end_time = '2017-12-20 23:00:00';
}elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $buff_stime = '2017-10-19 00:00:00';
    $buff_etime = '2017-11-11 23:00:00';
    
    $start_time = '2017-01-28 09:30:00';
    $mid_time = '2017-11-05 00:00:00';
    $end_time = '2017-11-11 24:00:00';

    //复投活动
    $activety_fu_stime = '2017-10-23 00:00:00';
    $activety_fu_etime = '2017-11-11 23:00:00';
    //双十二
    $twelve_start_time = '2017-01-10 00:00:00';
    $twelve_end_time = '2017-12-10 00:00:00';
}else{
    $buff_stime = '2017-10-19 00:00:00';
    $buff_etime = '2017-12-11 23:00:00';
    
    $start_time = '2017-01-28 09:30:00';
    $mid_time = '2017-11-26 19:49:48';
    $end_time = '2017-12-11 24:00:00';
    
    //复投活动
    $activety_fu_stime = '2017-11-22 00:00:00';
    $activety_fu_etime = '2017-12-11 23:00:00';
    //双十二
    $twelve_start_time = '2017-01-10 00:00:00';
    $twelve_end_time = '2017-12-10 00:00:00';
}

$config = array(
    'buff_stime' => $buff_stime,
    'buff_etime' => $buff_etime,
    'fest_rule' => array(
        '200-499' => array(
            'first_buy_reward' => 20,
        ),
        '500-999' => array(
            'first_buy_reward' => 30,
        ),
        '1000-1999' => array(
            'first_buy_reward' => 50,
        ),
        '2000-4999' => array(
            'first_buy_reward' => 70,
        ),
        '5000-9999' => array(
            'first_buy_reward' => 150,
        ),
        '10000-19999' => array(
            'first_buy_reward' => 200,
        ),
        '20000-1000000' => array(
            'first_buy_reward' => 300,
        )
    ),
    'remove' => array('invite','dayonghu','sinahls','163hls','anmo','beizhen','lezhuan','ledouwan','shitoucun'),
    
    //复投活动
    'activety_fu_stime' => $activety_fu_stime,
    'activety_fu_etime' => $activety_fu_etime,
    'activety_fu_rule' => array(
        'chanpin-40' => array(
            'condition' => array(
                '50' => '>= 3000',
                '40-56-42-41-57-43-52' => '>= 2000',
            ),
            'rule' => array(
                '2000-3999' =>array(
                    'buy_reward' => '100',
                ),
                '4000-9999' => array(
                    'buy_reward' => '200',
                ),
                '10000-9999999' => array(
                    'buy_reward' => '300',
                )
            )
        ),
        'chanpin-42' => array(
            'condition' => array(
                '50' => '>= 3000',
                '40-56-42-41-57-43' => '>= 2000',
            ),
            'rule' => array(
                '2000-3999' =>array(
                    'buy_reward' => '218',
                ),
                '4000-9999' => array(
                    'buy_reward' => '318',
                ),
                '10000-9999999' => array(
                    'buy_reward' => '418',
                )
            )
        ),
        'chanpin-41' => array(
            'condition' => array(
                '50' => '>= 3000',
                '40-56-42-41-57-43' => '>= 2000',
            ),
            'rule' => array(
                '2000-3999' =>array(
                    'buy_reward' => '318',
                ),
                '4000-9999' => array(
                    'buy_reward' => '418',
                ),
                '10000-9999999' => array(
                    'buy_reward' => '518',
                )
            )
        ),
        'chanpin-57' => array(
            'condition' => array(
                '50' => '>= 3000',
                '40-56-42-41-57-43' => '>= 2000',
            ),
            'rule' => array(
                '2000-3999' =>array(
                    'buy_reward' => '418',
                ),
                '4000-9999' => array(
                    'buy_reward' => '518',
                ),
                '10000-9999999' => array(
                    'buy_reward' => '618',
                )
            )
        )
    ),
    //11月28-12月11日活动
    'start_time' => $start_time,
    'mid_time' => $mid_time,
    'end_time' => $end_time,
//    'clear_time' => $clear_time,
    'two_rule' => array(
        'three' => 100000,
        'six' => 60000,
        'year' => 30000,
    ),
    'product_tpid' => array('three_product_tpid' => '42','six_product_tpid' => '41', 'year_product_tpid' => '57'),
    'fake_data' => array(
        'three_product_tpid' => array(
            '100000' => array('uid' => '100000','account' => '13841685874', 'product_tpid' => '42', 'money' => '5000'),
            '10000222' => array('uid' => '100000','account' => '13841685874', 'product_tpid' => '42', 'money' => '500000'),
        ),
        'six_product_tpid' => array(
            '100002' => array('uid' => '100002','account' => '13141684562', 'product_tpid' => '42', 'money' => '60000'),
        ),
        'year_product_tpid' => array(
            '100004' => array('uid' => '100004','account' => '13641684980', 'product_tpid' => '42', 'money' => '5000'),
        ),
    ),
    //12月12日活动
    'twelve_start_time' => $twelve_start_time,
    'twelve_end_time' => $twelve_end_time,
    'twelve_single_condition' => array(
        'product-40' => array(
            '2000-3999' =>array(
                'buy_reward' => '118',
            ),
            '4000-9999' => array(
                'buy_reward' => '218',
            ),
            '10000-49999' => array(
                'buy_reward' => '588',
            ),
            '50000-99999' => array(
                'buy_reward' => '688',
            ),
            '100000-100000000' => array(
                'buy_reward' => '888',
            )
        ),
        'product-42' => array(
            '2000-3999' =>array(
                'buy_reward' => '288',
            ),
            '4000-9999' => array(
                'buy_reward' => '388',
            ),
            '10000-49999' => array(
                'buy_reward' => '788',
            ),
            '50000-99999' => array(
                'buy_reward' => '888',
            ),
            '100000-100000000' => array(
                'buy_reward' => '1088',
            )
        ),
        'product-41' => array(
            '2000-3999' =>array(
                'buy_reward' => '388',
            ),
            '4000-9999' => array(
                'buy_reward' => '688',
            ),
            '10000-49999' => array(
                'buy_reward' => '888',
            ),
            '50000-99999' => array(
                'buy_reward' => '1088',
            ),
            '100000-100000000' => array(
                'buy_reward' => '1288',
            )
        )
    ),
    'twelve_accumulative_condition' => array(
        'product-50' => array(
            '4000-9999' =>array(
                'buy_reward' => '118',
            ),
            '10000-49999' => array(
                'buy_reward' => '188',
            ),
            '50000-100000' => array(
                'buy_reward' => '218',
            ),
            '100000-100000000' => array(
                'buy_reward' => '318',
            )
        ),
        'product-40' => array(
            '4000-9999' =>array(
                'buy_reward' => '188',
            ),
            '10000-49999' => array(
                'buy_reward' => '288',
            ),
            '50000-100000' => array(
                'buy_reward' => '388',
            ),
            '100000-100000000' => array(
                'buy_reward' => '488',
            )
        ),
        'product-42' => array(
            '4000-9999' =>array(
                'buy_reward' => '288',
            ),
            '10000-49999' => array(
                'buy_reward' => '388',
            ),
            '50000-100000' => array(
                'buy_reward' => '488',
            ),
            '100000-100000000' => array(
                'buy_reward' => '588',
            )
        ),
        'product-41' => array(
            '4000-9999' =>array(
                'buy_reward' => '388',
            ),
            '10000-49999' => array(
                'buy_reward' => '488',
            ),
            '50000-100000' => array(
                'buy_reward' => '588',
            ),
            '100000-100000000' => array(
                'buy_reward' => '688',
            )
        ),
        'product-57' => array(
            '4000-9999' =>array(
                'buy_reward' => '488',
            ),
            '10000-49999' => array(
                'buy_reward' => '588',
            ),
            '50000-100000' => array(
                'buy_reward' => '688',
            ),
            '100000-100000000' => array(
                'buy_reward' => '788',
            )
        )
    ),
    'rank_product_ptid' => array('easy_product_tpid'=>'50','one_product_tpid'=>'40','three_product_tpid'=>'42','six_product_tpid'=>'41'),
    'double_twelve_rank_award_money' => '12',
);
