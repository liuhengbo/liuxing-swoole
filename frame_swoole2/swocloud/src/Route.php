<?php


namespace SwoCloud;

use Swoole\Server as SwooleServer;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleWebSocketServer;
use SwoStar\Console\Input;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Redis;
/**
 * WebSocket
 * Class Route
 * @package SwoCloud
 */
class Route extends Server
{

    protected $distribute = null;

    protected $serverKey = 'im-server';

    /**
     * 获取服务期的算法
     * @var string
     */
    protected $arithmetic = 'round';

    public $redis = null;
    public $redisHost = '127.0.0.1';
    public $redisPort = '6379';


    /**
     * @return string
     */
    public function getArithmetic(): string
    {
        return $this->arithmetic;
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

    public function onWorkerStart(SwooleServer $server, int $worker_id)
    {
        $this->redis = new Redis();
        $this->redis->pconnect($this->redisHost,$this->redisPort);
    }

    public function onMessage(SwooleWebSocketServer $server,Frame $frame)
    {
        $data = json_decode($frame->data,true);
        $fd = $frame->fd;

        $this->getDistribute()->{$data['method']}($this,$server,...[$fd,$data]);

    }
    public function onClose(SwooleWebSocketServer $server,$fd)
    {
        dd('onClose');

    }
    public function onOpen(SwooleWebSocketServer $server,$request)
    {
        dd('onOpen');

    }

    /**
     * @return null
     */
    public function getDistribute()
    {
        if(!$this->distribute){
            $this->distribute = new Distribute();
        }
        return $this->distribute;
    }

    /**
     * 获取redis对象
     * @return Redis
     */
    public function getInstanceRedis()
    {
        return $this->redis;
    }

    /**
     * @return string
     */
    public function getServerKey(): string
    {
        return $this->serverKey;
    }


    public function onRequest(SwooleRequest $request,SwooleResponse $response)
    {
        if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
            $response->end();
            return;
        }

        $response->header('Access-Control-Allow-Origin','*');
        $response->header('Access-Control-Allow-Methods','GET,POST');

        $this->getDistribute()->{$request->post['method']}($this, $request,$response);


    }



}