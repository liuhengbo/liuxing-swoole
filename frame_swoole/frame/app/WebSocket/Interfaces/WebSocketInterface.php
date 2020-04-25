<?php

namespace App\WebSocket\Interfaces;

use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

interface WebSocketInterface
{
    public function message(SwooleWebSocketServer $server,Frame $frame);

    public function close(SwooleWebSocketServer $server,int $fd);

    public function open(SwooleWebSocketServer $server,SwooleHttpRequest $request);
}