<?php


namespace SwoCloud;

use Swoole\WebSocket\Server as SwooleWebSocketServer;
use SwoStar\Console\Input;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
/**
 * WebSocket
 * Class Route
 * @package SwoCloud
 */
class Route extends Server
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
        dd('onMessage');
    }
    public function onClose(SwooleWebSocketServer $server,$fd)
    {
        dd('onClose');

    }
    public function onOpen(SwooleWebSocketServer $server,$request)
    {
        dd('onOpen');

    }


    public function onRequest(SwooleRequest $request,SwooleResponse $response)
    {
    }



}