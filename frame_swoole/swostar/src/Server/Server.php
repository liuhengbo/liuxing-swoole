<?php


namespace SwoStar\Server;

use Swoole\Server as SwooleServer;
use SwoStar\Foundation\Application;
use SwoStar\Rpc\Rpc;
use SwoStar\Supper\Inotify;

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

    protected $config = [
        'task_worker_num' => 0,
    ];

    protected $pidFile = '/runtime/swostar.pid';


    /**
     * 是否监听文件热加载
     * @var bool
     */
    public $watchFile = false;

    /**
     * 热加载对象
     * @var null
     */
    public $inotify = null;

    /**
     * 端口
     * @var string
     */
    protected $port = '9501';

    /**
     * @var string
     */
    protected $host = '0.0.0.0';

    protected $app = null;

    /**
     * 用于记录pid的信息
     * @var array
     */
    protected $pidMap = [
        'masterPid'  => 0,
        'managerPid' => 0,
        'workerPids' => [],
        'taskPids'   => []
    ];

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

    public function __construct(Application $app)
    {
        $this->app = $app;
        // 初始化配置
        $this->initSetting();
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
     * 初始化配置
     */
    public function initSetting()
    {
        $config = app('config');
        $this->port = $config->get('server.http.port');
        $this->host = $config->get('server.http.host');
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
        $config = app('config');
        // 判断是否开启rpc
        if($config->get('server.http.tcpable')){
            new Rpc($this->swooleServer,$config->get('server.http.rpc'));
        }
        $this->swooleServer->start();
    }

    /**
     * 热重启事件
     * @return \Closure
     */
    protected function watchEvent()
    {
        return function($event){
            $this->swooleServer->reload();
        };
    }

    // 回调函数
    public function onStart(SwooleServer $server)
    {
        $this->pidMap['masterPid'] = $server->master_pid;
        $this->pidMap['managerPid'] = $server->manager_pid;

        // 保存Pid到文件
        $pidStr = sprintf('%s,%s',$server->master_pid,$server->manager_pid);
        file_put_contents(app()->getBasePath().$this->pidFile,$pidStr);

        app('event')->trigger('onStart',[$this]);


        //是否启动热重启
        if($this->watchFile){
            $this->inotify = new Inotify($this->app->getBasePath(),$this->watchEvent());
            $this->inotify->start();
        }


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
        $this->pidMap['workerPids'] = [
            'id'  => $worker_id,
            'pid' => $server->worker_id
        ];
    }
    public function onWorkerStop(SwooleServer $server, int $worker_id)
    {

    }
    public function onWorkerError(SwooleServer $server, int $workerId, int $workerPid, int $exitCode, int $signal)
    {

    }

    public function setWatchFile($status)
    {
        $this->watchFile = $status;
    }


}