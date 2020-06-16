<?php


namespace SwoCloud;

use Swoole\Server as SwooleServer;
use Redis;

abstract class Server
{
    // 属性
    protected $swooleServer;

    /**
     * 注册的事件
     * @var array
     */
    protected $event = [
        // 这是所有服务均会注册的事件
        "server" => [
            "start" => "onStart",
            "managerStart" => "onManagerStart",
            "managerStop" => "onManagerStop",
            "shutdown" => "onShutdown",
            "workerStart" => "onWorkerStart",
            "workerStop" => "onWorkerStop",
            "workerError" => "onWorkerError",
        ],
        // 子类的服务
        "sub" => [],
        // 额外扩展的回调函数
        // 如 ontart等
        "ext" => []
    ];

    /**
     * 端口
     * @var string
     */
    protected $port = '9601';

    /**
     * @var string
     */
    protected $host = '0.0.0.0';

    protected $config = [
        'task_worker_num' => 0,
    ];
    /**
     * @var Redis
     */
    public $redis;

    /**
     * 设置事件
     * @return mixed
     */
    abstract protected function initEvent();

    /**
     * 定义创建服务方法,因为每个服务创建是不一样的
     * @return mixed
     */
    abstract protected function createServer();

    public function __construct()
    {


        // 注册事件

        // 创建服务
        $this->createServer();
        // 设置配置项
        $this->swooleServer->set($this->config);
        // 回调函数
        $this->initEvent();
        // 注册事件
        $this->registerSwooleEvent();

    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig($config)
    {
        $this->config = array_merge($this->config,$config);
        return $this;
    }


    /**
     * 注册swoole回调事件
     */
    protected function registerSwooleEvent()
    {
        foreach ($this->event as $key => $value) {
            foreach ($value as $event => $function) {
                $this->swooleServer->on($event, [$this, $function]);
            }
        }
    }

    /**
     * 设置回调函数
     * @param $type
     * @param $event
     * @return $this
     */
    protected function setEvent($type, $event)
    {
        // 不支持设置系统回调
        if ($type == 'server') {
            return $this;
        }
        $this->event[$type] = $event;
        return $this;

    }


    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return static
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return static
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->swooleEvent;
    }

    /**
     * 启动Swoole服务
     */
    public function start()
    {
        $this->swooleServer->start();
    }


    // 回调函数
    public function onStart(SwooleServer $server)
    {

    }
    public function onManagerStart(SwooleServer $server)
    {

    }
    public function onManagerStop(SwooleServer $server)
    {

    }
    public function onShutdown(SwooleServer $server)
    {

    }
    public function onWorkerStart(SwooleServer $server, int $worker_id)
    {

        // 注册每个进程下的redis连接
        $this->redis = new Redis();
        $this->redis->pconnect('127.0.0.1',6379);

    }
    public function onWorkerStop(SwooleServer $server, int $worker_id)
    {

    }
    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {

    }


}