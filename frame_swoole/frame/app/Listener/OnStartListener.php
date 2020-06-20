<?php


namespace App\Listener;


use Swoole\Coroutine\Http\Client;
use SwoStar\Console\Input;
use SwoStar\Event\Listener;
use SwoStar\Server\Server;

class OnStartListener extends Listener
{

    protected $name = 'onStart';

    /**
     * @param Server $server
     */
    public function handle($server)
    {
        $config = app('config');
        go(function () use ($server, $config) {
            $client = new Client($config->get('server.route_http.host'), $config->get('server.route_http.port'));

            Input::info("Route服务器地址:".$config->get('server.route_http.host').':'.$config->get('server.route_http.port'),"连接Route服务器");

            // 判断升级WebSocket是否成功
            if ($client->upgrade('/')) {
                $data = [
                    'host' => $server->getHost(),
                    'port' => $server->getPort(),
                    'serverName' => 'im1',
                    'method' => 'register',
                ];
                // 发送注册自己的服务器信息
                $client->push(json_encode($data,JSON_UNESCAPED_UNICODE));

                // 设置定时器像服务器发送心跳信息
                swoole_timer_tick(3000,function () use ($client){
                    $client->push('',WEBSOCKET_OPCODE_PING);
                });
//                $client->close();
            }else{
                logError("Route服务器连接失败,错误码为:".$client->errCode." 错误信息为:".socket_strerror($client->errCode));
            }


        });
    }
}