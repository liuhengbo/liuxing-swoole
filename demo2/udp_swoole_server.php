<?php

// 创建UPD服务器，不需连接直接发送
//创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
$serv = new swoole_server("0.0.0.0", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

//监听客户端发来的数据
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "给UDP客户端发送一个消息 ".$data);
    var_dump($clientInfo);
});

//启动服务器
$serv->start();
