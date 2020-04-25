<?php

use Swoole\Server as SwooleServer;

class Swoole{

    const HOST = '0.0.0.0';
    const PORT = '9501';

    public $info = [];

    public function __construct()
    {
        $server = new SwooleServer(self::HOST,self::PORT);
        echo self::HOST.':'.self::PORT."\n";
        // 注册事件
        $server->on('receive',[$this,'onReceive']);

        $server->start();

    }

    public function onReceive(SwooleServer $server,$fd,$reactor_id,$data)
    {
        // 此处属性为主进程全局变量，所以会一直增加
        $this->info[count($this->info)] = time();
        var_dump($this->info);
        $server->send($fd,1);
    }

}

new Swoole();