<?php

// UDP 客户端

$client = new swoole_client(SWOOLE_SOCK_UDP);

// 给服务端发送数据

$client->sendto('127.0.0.1','9501','UPD客户端给服务器发的数据');

$data = $client->recv();

var_dump($data);

echo "ddd";
