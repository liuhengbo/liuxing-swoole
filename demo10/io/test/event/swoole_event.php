<?php
$fp = stream_socket_client("tcp://127.0.0.1:9501", $errno, $errstr, 30);
fwrite($fp,"写入信息");

Swoole\Event::add($fp, function($fp) {
    $resp = fread($fp, 8192);
    var_dump($resp);
    //socket处理完成后，从epoll事件中移除socket
    swoole_event_del($fp);
    fclose($fp);
});
echo "Finish\n";  //Swoole\Event::add 不会阻塞进程，这行代码会顺序执行