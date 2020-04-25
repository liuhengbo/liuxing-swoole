<?php


namespace App\WebSocket\Controller;


use App\WebSocket\Interfaces\WebSocketInterface;
use Swoole\Http\Request as SwooleHttpRequest;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

class IndexController implements WebSocketInterface
{

    public function message(SwooleWebSocketServer $server,Frame $frame)
    {
        $server->push($frame->fd,"这是websocketServer服务器,你发送的信息为:".$frame->data);
    }

    public function close(SwooleWebSocketServer $server,int $fd)
    {
        var_dump($fd);
        dd("已删除连接",'删除连接');
    }

    public function open(SwooleWebSocketServer $server,SwooleHttpRequest $request)
    {
        dd('IndexController->onOpen');
    }
}