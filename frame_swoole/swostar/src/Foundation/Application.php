<?php

namespace SwoStar\Foundation;

use SwoStar\Container\Container;
use SwoStar\Event\Event;
use SwoStar\Routes\Route;
use SwoStar\Server\Http\HttpServer;
use SwoStar\Server\WebSocket\WebSocketServer;

class Application extends Container
{
    protected const SWOSTAR_WELCOME = "
      _____                     _____     ___
     /  __/             ____   /  __/  __/  /__   ___ __    __  __
     \__ \  | | /| / / / __ \  \__ \  /_   ___/  /  _`  |  |  \/ /
     __/ /  | |/ |/ / / /_/ /  __/ /   /  /_    |  (_|  |  |   _/
    /___/   |__/\__/  \____/  /___/    \___/     \___/\_|  |__|
    ";

    public $basePath = null;

    public function __construct($path = null)
    {

        // 设置项目总路径
        if (!empty($path)) {
            $this->setBasePath($path);
        }
        $this->registerBaseBindings();
        $this->init();
        echoTips(self::SWOSTAR_WELCOME);
    }

    /**
     * 初始化
     */
    public function init()
    {
        $this->bind('route', Route::getInstance()->registerRoute());
        $this->bind('event', $this->registerEvent());

    }

    /**
     * 注册事件
     * @return Event
     */
    public function registerEvent()
    {
        $event = new Event();

        // 1. 找到文件
        $files = scandir($this->getBasePath(). '/app/Listener/');
        // 2. 读取文件信息
        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            // 读取文件名
            $filename = stristr($file, ".php", true);

            $class = 'App\\Listener\\' . $filename;
            if (class_exists($class)) {
                $listener = new $class();
                // 注册事件
                $event->register($listener->getName(), [$listener, 'handle']);
            }
        }

        return $event;


    }

    /**
     * 启动
     */
    public function run($arg)
    {

        switch ($arg[1]) {
            case 'http:start':
                // 启动http服务
                $server = new HttpServer($this);
                break;
            case 'ws:start':
                // 启动webSocket服务
                $server = new WebSocketServer($this);
                break;
            default:
                $server = new HttpServer($this);

        }
        $server->start();
    }

    /**
     * 设置项目基本路径
     * @param $path
     */
    public function setBasePath($path)
    {
        $this->basePath = rtrim($path, '\/');
    }

    /**
     * 获取项目基本路径
     * @return null
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    public function registerBaseBindings()
    {
        self::setInstance($this);
        $binds = [
            // 标识  ， 对象
            'index' => (new \SwoStar\Index()),
            'httpRequest' => (new \SwoStar\Message\Http\Request()),
            'config' => (new \SwoStar\Config\Config()),
        ];
        foreach ($binds as $key => $value) {
            $this->bind($key, $value);
        }
    }


}