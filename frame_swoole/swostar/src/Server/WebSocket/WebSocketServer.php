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
        $event = [
            'request'=>'onRequest',
            'message'=>'onMessage',
            'close'=>'onClose',
            'open'=>'onOpen',
        ];
        // 判断是否自定义握手过程
        ($this->app->make('config')->get('server.ws.is_handshake')) ?: $event['handshake'] = 'onHandshake';

        $this->setEvent('sub',$event);



    }

    /**
     * 建立连接后进行握手。WebSocket 服务器会自动进行 handshake 握手的过程
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function onHandshake(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $this->app->make('event')->trigger('ws.handshake',[$this,$request,$response]);
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