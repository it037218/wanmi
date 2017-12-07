<?php
if (php_sapi_name() == 'cli') {
    if ($_SERVER['argc'] == 4 && $_SERVER['argv'][3] == 'testing') {
        $config = array(
            'longmoneyToBalance' => 3,
            'klmoneyToBalance' => 3,
            'withDraw' => 3,
            'free_withDraw' => 10,
            'withDrawVersion' => 1,
            'pay' => 10,
            't' => NOW,
            'mt'=>NOW,
        );
    } else {
        $config = array(
            'longmoneyToBalance' => 3,
            'klmoneyToBalance' => 3,
            'withDraw' => 3,
            'free_withDraw' => 10,
            'withDrawVersion' => 1,
            'pay' => 10,
            't' => NOW,
            'mt'=>NOW,
        );
    }
} else {
    if (@$_SERVER['ENVIRONMENT'] == 'production') {
        $config = array(
            'longmoneyToBalance' => 3,
            'klmoneyToBalance' => 3,
            'withDraw' => 3,
            'free_withDraw' => 10,
            'withDrawVersion' => 1,
            'pay' => 10,
            't' => NOW,
            'mt'=>NOW,
        );
    } elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
        $config = array(
            'longmoneyToBalance' => 3,
            'klmoneyToBalance' => 3,
            'withDraw' => 3,
            'free_withDraw' => 10,
            'withDrawVersion' => 1,
            'pay' => 10,
            't' => NOW,
            'mt'=>NOW,
        );
    } else {
        $config = array(
            'longmoneyToBalance' => 3,
            'klmoneyToBalance' => 3,
            'withDraw' => 3,
            'free_withDraw' => 10,
            'withDrawVersion' => 1,
            'pay' => 10,
            't' => NOW,
            'mt'=>NOW,
        );
    }
}
?>