<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("0.0.0.0", 9501);

// 心跳检测
// heartbeat_check_interval 每多少秒检测一次
// heartbeat_idle_time 多少秒后没有心跳时服务端会主动断开连接
//$serv->set(array(
//    'heartbeat_check_interval' => 5,
//    'heartbeat_idle_time' => 10,
//));

// 粘包处理 EOF
$serv->set(array(
    'open_length_check' => true,
    'package_max_length' => 81920,
    'package_length_type' => 'N',
    // 数据从0开始
    'package_length_offset' => 0,
    // 是4是因为选择的处理类型是N 是4位
    'package_body_offset' => 4,
));

// 粘包处理 官方推荐





//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "连接进入."."客户端唯一标识为{$fd}\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    echo "接受到消息，客户端唯一标识为$fd\n";
    echo $data."\n";
//    echo $data . "\n";
    // 注意：给同步客户端会消息是，同步客户端需要有接收消息方法，否则会有错误爆出
    // 错误如下： NOTICE	swFactoryProcess_finish (ERRNO 1004): send 29 byte failed, because connection[fd=1] is closed
//    $serv->send($fd, "给客户端回一个消息: ");
});

//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "连接关闭，客户端唯一标识为$fd.\n";
});

//启动服务器
$serv->start();
