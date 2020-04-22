<?php


class Http{

    protected $http;

    protected $r='变量';

    protected $config=[
      'worker_num'=>4
    ];

    public function __construct($ip,$port)
    {
        $this->http = new Swoole\Http\Server($ip,$port);
        $this->http->set($this->config);
        $this->http->on('request',[$this,'request']);
        echo "启动成功\n";

    }

    public function request($request, $response)
    {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->end("<h1>Hello Swoole. #".$this->r."</h1>");
    }

    public function start()
    {
        $this->http->start();

    }
    
}

$http = new Http('0.0.0.0','9501');

$http->start();
