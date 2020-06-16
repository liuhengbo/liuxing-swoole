<?php


namespace SwoCloud;

use Swoole\WebSocket\Frame;
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

    public $dispatcher = null;

    protected $imServerKey = 'im_server';

    /**
     * @return string
     */
    public function getImServerKey(): string
    {
        return $this->imServerKey;
    }

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


    public function onMessage(SwooleWebSocketServer $server,Frame $frame)
    {
        $data = json_decode($frame->data,true);

        $fd = $frame->fd;

        $this->getDispatcher()->{$data['method']}($this,$server,$fd,$data);

    }
    public function onClose(SwooleWebSocketServer $server,$fd)
    {
        dd('onClose');

    }
    public function onOpen(SwooleWebSocketServer $server,$request)
    {


    }


    public function onRequest(SwooleRequest $request,SwooleResponse $response)
    {
        debugEcho("有服务器接入进来");
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }

        // 进行用户登陆验证  Dispatch
        /**
         * [
         *      'method'=>,
         *      ''
         * ]
         */

        $this->getDispatcher()->{$request->post['method']}($this,$request,$response);

    }

    /**
     * 获取分发的对象
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        if(!$this->dispatcher){
            $this->dispatcher = new Dispatcher();
        }
        return $this->dispatcher;
    }

    /**
     * 获取 redis链接
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }


}