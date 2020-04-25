<?php


namespace SwoStar\Server\WebSocket;


use SwoStar\Console\Input;
use SwoStar\Server\Http\HttpServer;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

class WebSocketServer extends HttpServer
{

    /**
     * @inheritDoc
     */
    protected function createServer()
    {

        $this->swooleServer = new SwooleWebSocketServer($this->host,$this->port);

        Input::info('WebServer访问: ws://127.0.0.1:'.$this->port);
    }

    public function initEvent()
    {
        $this->setEvent('sub',[
           'request'=>'onRequest',
           'message'=>'onMessage',
           'close'=>'onClose',
           'open'=>'onOpen',
        ]);
    }


    public function onMessage(SwooleWebSocketServer $server,$frame)
    {
        $path = Connections::get($frame->fd);

        $return = app('route')->setMethod('message')->setFlag('WebSocket')->match($path['path'],[$server,$frame]);

    }
    public function onClose(SwooleWebSocketServer $server,$fd)
    {
        $path = Connections::get($fd);
        $return = app('route')->setMethod('close')->setFlag('WebSocket')->match($path['path'],[$server,$fd]);
        Connections::del($fd);
    }
    public function onOpen(SwooleWebSocketServer $server,$request)
    {
        $return = app('route')->setMethod('open')->setFlag('WebSocket')->match($request->server['path_info'],[$server,$request]);

        Connections::init($request->fd,$request->server['path_info']);

    }
}