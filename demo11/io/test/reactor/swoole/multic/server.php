<?php

require __DIR__.'/../../../../vendor/autoload.php';

use \Hengbo\Io\Reactor\Swoole\Mulit\Worker;

$host ="0.0.0.0:9501";

$worker =new Worker($host);

// 消息进入服务端执行
$worker->onReceive = function ($worker,$client,$data){
    // 引入一个文件
//    include 'echo.php';
    //
    echo "收到了一个消息\n";
    send($client,"你好客户端\n");
};

$worker->start();