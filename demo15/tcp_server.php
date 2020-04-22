<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("127.0.0.1", 9501);

$r = "程序全局对象\n";

//程序启动回调
// 此回调为子进程执行的
$serv->on('Start', function ($serv) {
    global $r;
    var_dump($r);
    echo "程序启动\n";
});


//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    global $r;
    $r = "修改了程序全局对象\n";
    echo "Client: Connect.\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {

    $serv->send($fd, "Server: ".$data);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    global $r;
    echo "修改后的程序全局对象变量\n";
    var_dump($r);
    echo "Client: Close.\n";
});

//
$serv->start();
