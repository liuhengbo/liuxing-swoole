<?php
require __DIR__.'/../../vendor/autoload.php';
use Hengbo\Io\AsyncModel\Worker;
$host = "tcp://0.0.0.0:9501";
$server = new Worker($host);
// echo 1;
// 收到消息时
$server->onReceive = function($socket, $client, $data){
     debug($data);
    // sleep(3);
    // echo "给连接发送信息\n";
    // 封装在src/Helper.php
    // $socket->send($client, "hello world client \n");
    // 给客户端发送回去
    send($client, "hello world client \n");
};
debug($host);
$server->start();
