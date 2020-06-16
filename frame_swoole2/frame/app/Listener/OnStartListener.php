<?php


namespace App\Listener;


use Swoole\Coroutine\Http\Client;
use SwoStar\Event\Listener;

class OnStartListener extends Listener
{

    protected $name = 'onStart';

    public function handle()
    {
        go(function () {
            $client = new Client('127.0.0.1', 9601);
            // 判断升级WebSocket是否成功
            if ($client->upgrade('/')) {

                $data = [
                    'host' => '127.0.0.1',
                    'port' => '9502',
                    'serverName' => 'im1',
                    'method' => 'register',
                ];
                debugEcho("向Route发送自己的信息");
                // 发送注册自己的服务器信息
                $client->push(json_encode($data,JSON_UNESCAPED_UNICODE));

                // 设置定时器像服务器发送心跳信息
                swoole_timer_tick(3000,function () use ($client){
                    $client->push('',WEBSOCKET_OPCODE_PING);
                });
//                $client->close();
            }

        });
    }
}