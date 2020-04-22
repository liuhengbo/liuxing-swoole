<?php

$server = new Swoole\Server("0.0.0.0", 9501);
$key = ftok(__FILE__,1);
$server->set(array(
    'worker_num'      => 2,
    // 设置并开启task进程
    'task_worker_num' => 2,
));

$server->on('Receive', function (Swoole\Server $server, $fd, $from_id, $data) {

    $configArr = [
        'host'=>'127.0.0.1',
        'port'=>'3306',
        'user'=>'root',
        'passwd'=>'123456',
        'dbname'=>'demo',
    ];

    $mysql = new MMysql($configArr);

//插入
    $data = array(
        'sid'=>101,
        'aa'=>123456,
        'bbc'=>'aaaaaaaaaaaaaa',
    );

    $server->send($fd, "分发任务，任务id为$task_id\n");
});

// 一旦定义task进程此函数必须要写
// 此函数作用用来执行woker进程投递的任务
$server->on('Task', function (Swoole\Server $server, $task_id, $from_id, $data) {
    $server->finish($data);
});
// 一旦定义task进程此函数必须要写

$server->on('Finish', function (Swoole\Server $server, $task_id, $data) {
    echo "Task#$task_id finished, data_len=" . strlen($data) . PHP_EOL;
});

$server->start();