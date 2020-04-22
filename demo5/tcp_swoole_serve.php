<?php

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new Swoole\Server("0.0.0.0", 9501);

// swoole主进程启动事件
$serv->on('Start', function () {
    // 修改进程名
    // 此处修改的是主进程名称
    // https://wiki.swoole.com/wiki/page/p-server.html
    // 可参考根据图（各进程触发事件）修改其他进程的名称
    // 修改后使用  pstree -a | grep swoole 查不到树形结构了
    swoole_set_process_name("进程名");
    echo "swoole启动";
});

//监听连接进入事件
$serv->on('Connect', function ($serv, $fd) {
    echo "连接进入."."客户端唯一标识为{$fd}\n";
});

//监听数据接收事件
$serv->on('Receive', function ($serv, $fd, $from_id, $data) {
    echo "接受到消息，客户端唯一标识为$fd\n";
    echo $data . "\n";

});
//监听连接关闭事件
$serv->on('Close', function ($serv, $fd) {
    echo "连接关闭，客户端唯一标识为$fd.\n";
});

//启动服务器
$serv->start();
