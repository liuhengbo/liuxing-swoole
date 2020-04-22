<?php

$client = new swoole_client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);

//连接到服务器
if (!$client->connect('127.0.0.1', 9501, 0.5))
{
    die("连接服务器失败");
}
//向服务器发送数据
//$client->send("你好服务器hello world");
//if (!$client->send("你好服务器hello world"))
//{
//    die("向服务器发送消息失败");
//}

//$j = 1;

// 发送比较大的数据，测试分包
//$client->send(str_repeat("RD",1024*1024*1));

$str = "我是客户端";
$len = pack('N',strlen($str));
for ($i=0;$i<100;$i++){
    $send = $len.$str;
    $client->send($send);
}


// 从服务器接收数据
//$data = $client->recv();
//var_dump($data);
//if (!$data)
//{
//    die("recv failed.");
//}
//$i = 0;
//while (true){
//    echo $i++."\n";
//    // 每隔1s发送消息保证心跳不断开
//    $client->send("发送消息$i\n");
//    sleep(1);
//}

echo "浏览器访问";

//每隔2000ms触发一次  // 注意定时器时异步的
//swoole_timer_tick(1000, function ($timer_id) use(&$i,$client){
//    echo "$i++\n";
//    $client->send("发送消息$i\n");
//});

//关闭连接
$client->close();
