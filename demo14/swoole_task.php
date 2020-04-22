<?php

$server = new Swoole\Server("0.0.0.0", 9501);
// 消息队列模式,生成密钥，会根据此密钥生成消息队列的存储，内核会自动区分该队列是由谁来发送的
$key = ftok(__FILE__,1);
$server->set(array(
    'worker_num'      => 2,
    // 设置并开启task进程
    'task_worker_num' => 2,
));

$server->on('Receive', function (Swoole\Server $server, $fd, $from_id, $data) {

    $r = "投递给task的任务\n";
//    var_dump($server->worker_id);
    // 这个是任务ID
    $task_id = $server->task($r);
    echo " 投递的taskID为：".$task_id."\n";
    $server->send($fd, "分发任务，任务id为$task_id\n");
});

// 一旦定义task进程此函数必须要写
// 此函数作用用来执行woker进程投递的任务
// $task_id 该参数为执行任务的task进程ID
// $from_id 为投递进来的的worker进程ID
$server->on('Task', function (Swoole\Server $server, $task_id, $from_id, $data) {
    echo "task进程收到消息，task_id为：".$task_id;
    echo " task进程id为：".$task_id."\n";
//    var_dump($server->worker_id);
    $server->sendMessage("传递给worker的信息\n",$from_id);
    // 执行完成时调用finish进行通知
    $server->finish($data);
});
// 一旦定义task进程此函数必须要写
$server->on('Finish', function (Swoole\Server $server, $task_id, $data) {
    echo "Task#$task_id finished, data_len=" . strlen($data) . PHP_EOL;
});

$server->on('PipeMessage', function (Swoole\Server $server, $task_id, $data) {
    var_dump("收到task进程发会的消息，task_id为：".$task_id."数据为：".$data);
});

$server->on('workerStart', function ($server, $worker_id) {
    global $argv;
    if ($worker_id >= $server->setting['worker_num']) {
        swoole_set_process_name("php {$argv[0]}: task_worker");
    } else {
        echo "worker进程ID\n";
        var_dump($worker_id);
        swoole_set_process_name("php {$argv[0]}: worker");
    }
});

$server->start();