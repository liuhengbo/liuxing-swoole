<?php

namespace Hengbo\Io\AsyncModel;

use Swoole\Event;

class Worker{

    public $socket = null;
    public $onConnect= null;
    public $onReceive= null;

    public function __construct($socket_address)
    {
        $this->socket = stream_socket_server($socket_address);
    }

    /**
     * 事件监听
     */
    public function accpet()
    {
        // 注意事件会循环的触发，如果不进行移除监控时
        Event::add($this->socket,function ($socket){
            $client = stream_socket_accept($this->socket);

            if(is_callable($this->onConnect)){
                // 执行函数
                ($this->onConnect)($this,$client);
            }


            Event::add($client,function ($socket){
                $data = fread($socket,65535);

                if(empty($data) || !is_resource($socket)){
                    swoole_event_del($socket);
                    fclose($socket);
                    return ;
                }
                if(is_callable($this->onReceive)){
                    // 执行函数
                    ($this->onReceive)($this,$socket,$data);
                }
            });



        });
    }

    /**
     * 启动一个socket
     */
    public function start()
    {
        $this->accpet();
    }
}