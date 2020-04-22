<?php

require __DIR__.'/../../../../vendor/autoload.php';

use \Hengbo\Io\Reactor\Swoole\Mulit\Worker;

$host ="0.0.0.0:9501";

$worker =new Worker($host);
// 停止进程
$worker->stop();