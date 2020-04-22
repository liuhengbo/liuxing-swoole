<?php

$http = new Swoole\Http\Server("0.0.0.0", 9501);


define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);


$http->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use($kernel){

    // 将swoole的超全局变量取出放到PHP中
    $_SERVER = [];
    if (isset($request->server)) {
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    // 这个一定要写不然会报错
    $_SERVER['argv'] = [];
    if (isset($request->header)) {
        foreach ($request->server as $k => $v) {
            $_SERVER[strtoupper($k)] = $v;
        }
    }
    $_GET = [];
    if (isset($request->get)) {
        foreach ($request->get as $k => $v) {
            if($k == 's'){
                $_GET[$k] = $v;
            }else{
                $_GET[strtoupper($k)] = $v;
            }
        }
    }
    $_POST =[];
    if (isset($request->post)) {
        foreach ($request->post as $k => $v) {
            $_POST[strtoupper($k)] = $v;
        }
    }


    $laravel_response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    // 设置编码正确
    $response->header('Content-Type','text/html;charset=utf-8');
    $response->end($laravel_response->getContent());

    $kernel->terminate($request, $laravel_response);

});

$http->start();
