<?php
$config = array(
    1 => array(         //首投
        'starttime' => '2016-07-26 00:00:01',
        'endtime' => '2017-09-31 24:00:00',
    ),
    2 => array(//积分
        'starttime' => '2016-04-11 09:00:00',
        'endtime' => '2018-05-01 00:00:00',
        'rate' => array(47 => 1,48 => 1,49 => 1, 45 => 1, 41 => 1, 46 => 1, 42 => 2, 40 => 3, 44 => 3, 43 => 6) //类型id=>积分倍数
    ),
    3 => array(//体验金
        'starttime' => '2016-01-15 18:00:00',
        'endtime' => '2016-05-01 18:00:00',
        'expmoney' => 1288
    )
);