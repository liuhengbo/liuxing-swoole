<?php

$client = stream_socket_client("tcp://127.0.0.1:9501");

$new = time();

// 第一次发送信息
echo "第一次发送的信息\n";
fwrite($client,"发送给服务端的消息1");
var_dump(fread($client,65535));

// 第二次
echo "第二次发送的信息\n";
sleep(1);
fwrite($client,"发送给服务端的消息2");
var_dump(fread($client,65535));


// 第三次
echo "第三次发送的信息\n";
sleep(4);
fwrite($client,"发送给服务端的消息3");
var_dump(fread($client,65535));

//echo time()-$new."\n";
//fclose($client);