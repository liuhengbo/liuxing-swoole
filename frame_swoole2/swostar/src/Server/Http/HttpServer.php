<?php


namespace SwoStar\Server\Http;


use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use SwoStar\Console\Input;
use SwoStar\Message\Http\Request;
use SwoStar\Server\Server;
use Swoole\Http\Server as SwooleHttpServer;

class HttpServer extends Server
{

    /**
     * 让子类可以扩展事件
     * @inheritDoc
     */
    protected function initEvent()
    {

        $this->setEvent('sub',[
            'request'=>'onRequest'
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function createServer()
    {

        $this->swooleServer = new SwooleHttpServer($this->host,$this->port);

        Input::info('HttpServer访问: http://127.0.0.1:'.$this->port);
    }

    public function onRequest(SwooleRequest $request,SwooleResponse $response)
    {
        // 解决chrome请求两次问题
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }

        $httpRequest = Request::init($request);

        dd($httpRequest->getMethod(),'请求方法');
        dd($httpRequest->getUriPath(),'请求路径');

        $return = app('route')->setFlag('Http')->setMethod($httpRequest->getMethod())->match($httpRequest->getUriPath());

        $response->end($return);
    }



}