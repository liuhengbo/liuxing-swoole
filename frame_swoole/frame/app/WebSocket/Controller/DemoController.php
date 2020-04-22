<?php


namespace App\WebSocket\Controller;


use App\WebSocket\Interfaces\WebSocketInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

class DemoController implements WebSocketInterface
{
    public function message(SwooleWebSocketServer $server, Frame $frame)
    {

    }

    public function close(SwooleWebSocketServer $server, int $fd)
    {

    }

    public function open(SwooleWebSocketServer $server, SwooleHttpRequest $request)
    {
        dd("这是demo的open方法");
    }
}