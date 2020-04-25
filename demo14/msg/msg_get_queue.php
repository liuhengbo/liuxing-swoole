<?php

//msg_get_queue() 创建一个消息队列
// msg_send（）发送消息
// msg_receive（）接受消息



// $key为资源
$key = ftok(__DIR__,'u');

echo "消息队列key值".$key."\n";
// 创建队列
$queue = msg_get_queue($key);

$r = 0;

// fock之前的变量为全局变量
$son = pcntl_fork();

if($son == 0){
    // 子进程
    $r = 2;
    msg_receive($queue,10,$msgType,1024,$message);
    echo "接受的父进程信息\n";
    var_dump($message);

}else{
    echo "像紫禁城发送消息";
    sleep(3);
    // 父进程
    $r = 3;
    // 像队列写入类型为10的数据
    msg_send($queue,10,$r);

}
