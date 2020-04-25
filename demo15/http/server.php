<?php
$http = new Swoole\Http\Server("0.0.0.0", 9501);

$http->on('request', function (\Swoole\Http\Request $request, $response) {
    var_dump($request->rawContent());
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();