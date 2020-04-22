<?php

// 是建立连接
$client = stream_socket_client("tcp://127.0.0.1:9501");
// var_dump($client);
// 给socket通写信息
// 粗暴的方式去实现
while (true) {
    // echo "===》 准备发送信息 \n";
    fwrite($client, "hello world");
    // fread($client, 65535);

    echo "===》 信息发送成功 \n";
    var_dump(fread($client, 65535));
    sleep(2);
}
// 读取信息

// 关闭连接
// fclose($client);12
