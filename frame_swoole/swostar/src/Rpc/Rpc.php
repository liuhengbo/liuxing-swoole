<?php


namespace SwoStar\Rpc;


use Swoole\Server;
use SwoStar\Console\Input;

class Rpc
{
    protected $port;
    protected $host;

    /**
     * Rpc constructor.
     * @param Server $server [swooleServer]
     * @param $config [rpc的配置]
     */
    public function __construct(Server $server,$config)
    {
        $this->host = $config['host'];
        $this->port = $config['port'];
        $listen = $server->listen($this->host,$this->port,SWOOLE_SOCK_TCP);
        $listen->set($config['swoole']);
        // 注册回调
        $this->registerEvent($listen);

        Input::info("tcp监听地址:".$this->host.':'.$this->port);
    }

    /**
     * 注册事件
     * @param $listen
     */
    private function registerEvent($listen){
        $listen->on('connect',[$this,'connect']);
        $listen->on('receive',[$this,'receive']);
        $listen->on('close',[$this,'close']);
    }

    public function receive(Server $serv, $fd, $from_id, $data)
    {
        $serv->send($fd,'Swoole'.$data);
        $serv->close($fd);
    }

    public function connect($serv, $fd)
    {
        dd("超管查房");
    }
    public function close(Server $serv, $fd)
    {
        echo "连接关闭\n";
    }

//    public function packet($serv, $data, $addr)
//    {
//
//    }


}