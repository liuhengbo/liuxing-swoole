<?php


namespace App\Listener;


use Firebase\JWT\JWT;
use Swoole\Http\Request;
use Swoole\Http\Response;
use SwoStar\Event\Listener;
use SwoStar\Server\WebSocket\WebSocketServer;

class HandshakeListener extends Listener
{
    protected $name = 'ws.handshake';

    public function handle($webSocketServer = null,Request $request =null, Response $response = null)
    {
        $token = $request->header['sec-websocket-protocol'];
        if(empty($token)){
            // 不存在token
            $response->end();
            return false;
        }

        // jwt认证
        $this->check($token);

        $this->handShake($request,$response);
    }


    /**
     * jwt 认证
     * @param $token
     */
    public function check($token)
    {
        // 1.认证
        $route_jwt = app('config')->get('server.route_jwt');

        $key = $route_jwt['key'];
        $allowed_algs = $route_jwt['allowed_algs'];
        $res = JWT::decode($token,$key,$allowed_algs);
        var_dump($res);
        // 2. 存储信息到redis中
    }

    /**
     * 握手
     * @param $request
     * @param $response
     * @return bool
     */
    public function handShake($request,$response)
    {
        // websocket握手连接算法验证
        $secWebSocketKey = $request->header['sec-websocket-key'];
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        echo $request->header['sec-websocket-key'];
        $key = base64_encode(
            sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            )
        );

        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];

        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (isset($request->header['sec-websocket-protocol'])) {
            $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
        }

        foreach ($headers as $key => $val) {
            $response->header($key, $val);
        }

        $response->status(101);
        $response->end();
    }
}