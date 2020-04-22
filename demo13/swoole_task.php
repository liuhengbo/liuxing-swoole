<?php

$server = new Swoole\Server("0.0.0.0", 9501);
// 消息队列模式,生成密钥，会根据此密钥生成消息队列的存储，内核会自动区分该队列是由谁来发送的
$key = ftok(__FILE__,1);
$server->set(array(
    'worker_num'      => 2,
    // 设置并开启task进程
    'task_worker_num' => 2,
    // 默认为1
    'task_ipc_mode' => 2,
    'mssage_queue_key'=>$key,
    // 处理粘包问题
    'open_length_check'=>true,
    'package_max_length'=>1024*1024*3,
    'package_length_type'=>'N',
    'package_length_offset'=>0,
    'package_body_offset'=>4,
));

$server->on('Receive', function (Swoole\Server $server, $fd, $from_id, $data) {
//    echo "接收数据" . $data . "\n";
//    $data    = trim($data);
//    $server->task($data, -1, function (Swoole\Server $server, $task_id, $data) {
//        echo "Task Callback: ";
//        var_dump($task_id, $data);
//    });

    $t = time();

    $r = str_repeat('a',10*1024*1024);

    // $dst_worker_id不传时会先投递给空闲的task进程

    $task_id = $server->task($r);
    echo "测试阻塞\n";
    $server->send($fd, "分发任务，任务id为$task_id\n");
});

// 一旦定义task进程此函数必须要写
// 此函数作用用来执行woker进程投递的任务
$server->on('Task', function (Swoole\Server $server, $task_id, $from_id, $data) {
    var_dump(posix_getpid());
    sleep(4);
    // 执行完成时调用finish进行通知
    $server->finish($data);
});
// 一旦定义task进程此函数必须要写

$server->on('Finish', function (Swoole\Server $server, $task_id, $data) {
    echo "Task#$task_id finished, data_len=" . strlen($data) . PHP_EOL;
});

// 创建task和worker进程的时候执行此函数
//$server->on('workerStart', function ($server, $worker_id) {
//    global $argv;
//    if ($worker_id >= $server->setting['worker_num']) {
//        swoole_set_process_name("php {$argv[0]}: task_worker");
//    } else {
//        swoole_set_process_name("php {$argv[0]}: worker");
//    }
//});

$server->start();