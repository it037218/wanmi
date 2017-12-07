<?php

// $config = array(
//    'reward_money' => 20,
//    'invite_reg_rewardmoney' => 10, //邀请进来的用户奖励
//    'transaction_scale' => 0.05,
//    'start_time' => '2015-08-26 11:00:00';
// );

if(@$_SERVER['ENVIRONMENT'] == 'production'){
    $buff_stime = '2017-11-11 00:00:00';
    $buff_etime = '2017-12-11 23:00:00';
}elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
    $buff_stime = '2017-10-19 00:00:00';
    $buff_etime = '2017-12-11 00:00:00';
}else{
    $buff_stime = '2017-10-19 00:00:00';
    $buff_etime = '2017-12-11 00:00:00';
}

$config = array(
    'buff_stime' => $buff_stime,
    'buff_etime' => $buff_etime,
    'buff_reward_money' => 5,    //当时间在buff_stime和buff_endtime之间的时候  reward_money无用,启用buff_reward_money
    'first_buy_reward' => 0,//首次购买奖励
    'reward_money' => 5,//邀请好友奖励
    'transaction_scale' => 0.05,
    'second_transaction_scale' => 0.03,
    'days' => 0,//15552000  180天  / 0
    'min_money' => 200,
    'min_yongjing' => 2,    //最少发放给用户的佣金数
    'be_invite_limit' => 100000,//若超出限制，邀请红包回到普通额度
    'invite_limit' => 1000000,//限制邀请人奖励数量
    'stage' => array(
        '1-2000' => array(
            'buff_reward_money' => 10,
        ),
        '2000-10000000' => array(
            'buff_reward_money' => 20,
        )
    ),
    'stage_2' => array(
        '1000-2000' => array(
            'buff_reward_money' => 10,
        ),
    	'2000-5000' => array(
    		'buff_reward_money' => 15,
    	),
    	'5000-10000' => array(
    		'buff_reward_money' => 25,
    	),
    	'10000-1000000' => array(
    		'buff_reward_money' => 50,
    	)
    ),
    'stage_3' => array(//邀请来源奖励
        '200-499' => array(
            'buff_reward_money' => 20,
            'first_buy_reward' => 10,
        ),
        '500-999' => array(
            'buff_reward_money' => 30,
            'first_buy_reward' => 20,
        ),
    	'1000-1999' => array(
            'buff_reward_money' => 40,
            'first_buy_reward' => 30,
    	),
        '2000-4999' => array(
            'buff_reward_money' => 60,
            'first_buy_reward' => 40,
    	),
    	'5000-9999' => array(
            'buff_reward_money' => 100,
            'first_buy_reward' => 60,
    	),
    	'10000-19999' => array(
            'buff_reward_money' => 150,
            'first_buy_reward' => 80,
    	),
    	'20000-1000000' => array(
            'buff_reward_money' => 200,
            'first_buy_reward' => 100,
    	)
    ),
    'stage_4' => array(//广告来源奖励
        '200-499' => array(
            'buff_reward_money' => 20,
            'first_buy_reward' => 20,
        ),
        '500-999' => array(
            'buff_reward_money' => 30,
            'first_buy_reward' => 30,
        ),
    	'1000-1999' => array(
            'buff_reward_money' => 50,
            'first_buy_reward' => 50,
    	),
        '2000-4999' => array(
            'buff_reward_money' => 70,
            'first_buy_reward' => 70,
    	),
    	'5000-9999' => array(
            'buff_reward_money' => 150,
            'first_buy_reward' => 150,
    	),
    	'10000-19999' => array(
            'buff_reward_money' => 200,
            'first_buy_reward' => 200,
    	),
    	'20000-1000000' => array(
            'buff_reward_money' => 300,
            'first_buy_reward' => 300,
    	)
    ),
    'stage_5' => array(//渠道
        '200-499' => array(
            'buff_reward_money' => 35,
            'first_buy_reward' => 10,
        ),
        '500-999' => array(
            'buff_reward_money' => 55,
            'first_buy_reward' => 20,
        ),
    	'1000-1999' => array(
            'buff_reward_money' => 65,
            'first_buy_reward' => 30,
    	),
        '2000-4999' => array(
            'buff_reward_money' => 75,
            'first_buy_reward' => 40,
    	),
    	'5000-9999' => array(
            'buff_reward_money' => 120,
            'first_buy_reward' => 60,
    	),
    	'10000-19999' => array(
            'buff_reward_money' => '5%',
            'first_buy_reward' => 80,
    	)
    ),
    'ad_master' => array(//广告媒体
        1147,1199,20006,20862
    ),
    'channel' => array(//渠道
        627,149,672,15379,20005,20147,20418,21467,21947,22188,22195,22196,22530,22620
    )
);