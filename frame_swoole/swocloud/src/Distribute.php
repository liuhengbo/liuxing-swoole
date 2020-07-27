<?php


namespace SwoCloud;

use Config\ServerConfig;
use Firebase\JWT\JWT;
use SwoCloud\Supper\Arithmetic;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use Redis;

class Distribute
{
    /**
     * 注册服务器
     * @param Route $route
     * @param SwooleWebSocketServer $server
     * @param $fd
     * @param array $data
     */
    public function register(Route $route,SwooleWebSocketServer $server,$fd,array $data){

        // 将IP和端口写到redis中

        $value = json_encode([
            'host'=>$data['host'],
            'port'=>$data['port'],
        ],JSON_UNESCAPED_UNICODE);
        $serverKey = $route->getServerKey();
        $redis = $route->getInstanceRedis();
        $redis->sadd($serverKey,$value);
        echo "服务器".$value."已注册,fd值为:".$fd."\n";
        // 增加定时器判断连接还是否存活
        $server->tick(3000,function ($timer_id) use ($data, $value, $serverKey, $fd, $server, $redis){
            if(!$server->exist($fd)){
                echo "Im-Server {$fd} 关闭,服务器为:{$data['host']}:{$data['port']},清空注册信息\n";
                // 服务器宕机,清除redis数据,清除定时器
                $redis->srem($serverKey,$value);
                $server->clearTimer($timer_id);
            }
        });
    }

    /**
     * @param Route $route
     * @param Request $request
     * @param Response $response
     */
    public function login(Route $route,Request $request,Response $response)
    {
        // 获取服务器
        $server = json_decode($this->getImServer($route),true);
        $url = $server['host'].':'.$server['port'];
        // $uid 本来是用数据哭获取,现在为了测试直接从参数里取  -> 数据库中验证
        $uid = $request->post['uid'];
        // 返回token
        $token = $this->getImToken($uid,$url);

        $result = [
            'token'=>$token,
            'url'=>$url,
            'uid'=>$uid,
        ];
        $response->header('Content-type','application/json');
        $response->end(json_encode($result,JSON_UNESCAPED_UNICODE));
    }

    /**
     * 返回要获取的服务器地址
     * @param Route $route
     * @return mixed
     */
    public function getImServer(Route $route)
    {
        // 获取redis中的所有服务期信息
        $list = $route->getInstanceRedis()->sMembers($route->getServerKey());
        // 根据算法取出需要的服务期
        return Arithmetic::{$route->getArithmetic()}($list);
    }

    /**
     * 获取token
     * @param $uid
     * @param $url
     * @return string
     */
    public function getImToken($uid,$url)
    {
        $route_jwt = ServerConfig::ROUTE_JWT;
        $key = $route_jwt['key'];
        $time = time();
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "http://example.com",
            "iat" => $time,
            "nbf" => $time,
            "exp" => $time + (60*60*24),
            "data" =>[
                'uid'=>$uid,
                'url'=>$url
            ]
        );

        $jwt = JWT::encode($payload, $key);
        return $jwt;

    }


}