<?php

$http = new Swoole\Http\Server("127.0.0.1", 9501);


$http->on('request', function ($request, $response) {
    $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
});


$port = $http->addListener('127.0.0.1', '8501', SWOOLE_SOCK_TCP);

$port->set([
    'worker_num' => 1,
]);

$port->on('receive', function ($serv, $fd, $from_id, $data) {
    echo "超管查房\n";
    // ting
});
echo "http://127.0.0.1:9501";

$http->start();