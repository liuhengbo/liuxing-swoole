<?php

$client = new Swoole\Client(SWOOLE_SOCK_TCP|SWOOLE_KEEP);
if (!$client->connect('127.0.0.1', 9501, -1)) {
    exit("connect failed. Error: {$client->errCode}\n");
}

for($i = 0;$i<100;$i++){
    $len=pack('N',strlen($i));
    $send = $len.$i;
    $client->send($send);
}

//$client->send("hello world\n");
echo $client->recv();
//$client->close();