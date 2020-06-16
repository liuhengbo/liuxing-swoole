<?php


namespace SwoCloud;


use Swoole\Http\Request;
use Swoole\Http\Response;
use \Swoole\WebSocket\Server;

class Dispatcher
{
    public function register(Route $route, Server $server, $fd, array $data)
    {
        // 将服务器信息写入redis

        $redis = $route->getRedis();

        $value = json_encode(
            [
                'host'=>$data['host'],
                'port'=>$data['port'],
            ],
            JSON_UNESCAPED_UNICODE
        );
        debugEcho("注册了服务器".$value);
        $redis->sAdd($route->getImServerKey(),$value);

        $server->tick(3000,function ($time_id) use ($value, $route, $redis, $fd, $server) {
            if($server->exist($fd)){
                // 服务器凉了  -> 清除注册中心的地址
                $redis->sRem($route->getImServerKey(),$value);
                $server->clearTimer($time_id);
                debugEcho("服务器凉了，已清除注册中心的地址");
            }
        });


    }
}