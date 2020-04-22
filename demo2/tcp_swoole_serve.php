<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("0.0.0.0", 9501);

// 心跳检测
// heartbeat_check_interval 每多少秒检测一次
// heartbeat_idle_time 多少秒后没有心跳时服务端会主动断开连接
$serv->set(array(
    'heartbeat_check_interval' => 5,
    'heartbeat_idle_time' => 10,
));

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "连接进入."."客户端唯一标识为{$fd}\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    echo "接受到消息，客户端唯一标识为$fd\n";
    $serv->send($fd, "间隔了5s给客户端回一个消息: ".$data);
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "连接关闭，客户端唯一标识为$fd.\n";
});

//启动服务器
$serv->start();
