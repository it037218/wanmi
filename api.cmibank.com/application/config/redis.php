<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(php_sapi_name() == 'cli'){
    if($_SERVER['argc'] == 4 && $_SERVER['argv'][3] == 'testing'){
        $redis_cfg_read = array(
            'default' => array(
                'socket_type' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 7480,
                'timeout' => 0,
            )
        );
        
        $redis_cfg_write = array(
            'default' => array(
                'socket_type' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 7480,
                'timeout' => 0,
            )
        );
    } else {
        $redis_cfg_read = array(
            'default' => array(
                'socket_type' => 'tcp',         //腾讯CVM 8G内存
                'host' => '10.135.140.21',
                'port' => 6379,
                'timeout' => 0,
            )
        );
        
        $redis_cfg_write = array(
            'default' => array(
                'socket_type' => 'tcp',         //腾讯CVM 8G内存
                'host' => '10.135.140.21',
                'port' => 6379,
                'timeout' => 0,
            )   
        );
    }
}else{
    if(@$_SERVER['ENVIRONMENT'] == 'production'){
        $redis_cfg_read = array(
            'default' => array(
                'socket_type' => 'tcp',         //腾讯CVM 8G内存
                'host' => '10.135.140.21',
                'port' => 6379,
                'timeout' => 0,
            )
        );
        
        $redis_cfg_write = array(
            'default' => array(
                'socket_type' => 'tcp',         //腾讯CVM 8G内存
                'host' => '10.135.140.21',
                'port' => 6379,
                'timeout' => 0,
            )   
        );
    }elseif (@$_SERVER['ENVIRONMENT'] == 'testing') {
        $redis_cfg_read = array(
            'default' => array(
                'socket_type' => 'tcp',
                'host' => '10.9.193.55',
                'port' => 7480,
                'timeout' => 0,
            )
        );
        
        $redis_cfg_write = array(
            'default' => array(
                'socket_type' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 7480,
                'timeout' => 0,
            )   
        );
    } else {
        $redis_cfg_read = array(
            'default' => array(
                'socket_type' => 'tcp',
                'host' => '117.50.2.20',
                'port' => 7480,
                'timeout' => 0,
            )
        );
        
        $redis_cfg_write = array(
            'default' => array(
                'socket_type' => 'tcp',
                'host' => '117.50.2.20',
                'port' => 7480,
                'timeout' => 0,
            )
        );
    }
}

$redis_app = array(
    'write' => array(
        'socket_type' => $redis_cfg_write['default']['socket_type'],
        'host'        => $redis_cfg_write['default']['host'],
        'port'        => $redis_cfg_write['default']['port'],
        'timeout'     => 3,
    ),  
    'read' => array(        //随机读取一台读服务器
        array(
            'socket_type' => $redis_cfg_read['default']['socket_type'],
            'host'        => $redis_cfg_read['default']['host'],
            'port'        => $redis_cfg_read['default']['port'],
            'timeout' => 3,
        ),
        array(
            'socket_type' => $redis_cfg_read['default']['socket_type'],
            'host'        => $redis_cfg_read['default']['host'],
            'port'        => $redis_cfg_read['default']['port'],
            'timeout' => 3,
        ),
    )
);

$redis_account = array(
    'write' => array(
        'socket_type' => $redis_cfg_write['default']['socket_type'],
        'host'        => $redis_cfg_write['default']['host'],
        'port'        => $redis_cfg_write['default']['port'],
        'timeout'     => 3,
    ),  
    'read' => array(        //随机读取一台读服务器
        array(
            'socket_type' => $redis_cfg_read['default']['socket_type'],
            'host'        => $redis_cfg_read['default']['host'],
            'port'        => $redis_cfg_read['default']['port'],
            'timeout' => 3,
        ),
        array(
            'socket_type' => $redis_cfg_read['default']['socket_type'],
            'host'        => $redis_cfg_read['default']['host'],
            'port'        => $redis_cfg_read['default']['port'],
            'timeout' => 3,
        ),
    )
);

$config = array(
    'redis_default'  => $redis_cfg_write,
    'redis_account'  => $redis_account,
    'redis_app'      => $redis_app,
);

