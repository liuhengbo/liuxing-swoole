<?php
use Swoole\Async\Client;
// 异步客户端
$client = new Client(SWOOLE_SOCK_TCP);

$client->on("connect", function(Client $cli) {
    $cli->send("给服务器发个连接成功的消息 / HTTP/1.1\r\n\r\n");
});
$client->on("receive", function(Client $cli, $data){
    echo "服务器给我回了一个消息: $data\n";
//    $cli->send("我给服务器端回一个消息"."\n");
//    sleep(1);
});
$client->on("error", function(Client $cli){
    echo "连接服务器失败调用的函数\n";
});
$client->on("close", function(Client $cli){
    echo "连接关闭调用的函数\n";
});

$client->connect('127.0.0.1', 9501);

//每隔2000ms触发一次  // 注意定时器时异步的
swoole_timer_tick(1000, function ($timer_id) use(&$i,$client){
    echo "$i++\n";
    $client->send("发送消息$i\n");
});
