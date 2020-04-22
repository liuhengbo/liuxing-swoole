<?php

// 1. 创建swoole 默认创建的是一个同步的阻塞tcp服务
$host = "0.0.0.0"; // 0.0.0.0 代表接听所有
// 创建Server对象，监听 127.0.0.1:9501端口
// 默认是tcp
$serv = new Swoole\Server($host, 9501);
// 2. 注册事件
$serv->on('Start', function ($serv) use($host){
    echo "启动swoole 监听的信息tcp:$host:9501\n";
});
//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "Client: Connect.\n";
});
//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: " . $data);
});
//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
// 3. 启动服务器
// 阻塞
$serv->start(); // 阻塞与非阻塞
