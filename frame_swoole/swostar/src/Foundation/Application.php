<?php

namespace SwoStar\Foundation;

use SwoStar\Container\Container;
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
        if(!empty($path)){
            $this->setBasePath($path);
        }
        $this->registerBaseBindings();
        $this->init();
        dd(self::SWOSTAR_WELCOME,'启动项目');
    }

    /**
     * 初始化
     */
    public function init()
    {
        $this->bind('route',Route::getInstance()->registerRoute());

//        dd(app('route')->getRoutes());

    }

    /**
     * 启动
     */
    public function run($arg){

        switch ($arg[1]){
            case 'http:start':
                // 启动http服务
                $server = new HttpServer($this);
                $server->start();
                break;
            case 'ws:start':
                // 启动webSocket服务
                $server = new WebSocketServer($this);
                $server->start();
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
        $this->basePath = rtrim($path,'\/');
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
            'index'       => (new \SwoStar\Index()),
            'httpRequest'       => (new \SwoStar\Message\Http\Request()),
            'config'       => (new \SwoStar\Config\Config()),
        ];
        foreach ($binds as $key => $value) {
            $this->bind($key, $value);
        }
    }



}