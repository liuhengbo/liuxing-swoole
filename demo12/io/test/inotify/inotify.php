<?php
// 初始化 inotify
$fd = inotify_init();

// 对某一个文件进行监听
// resource $fd 初始化的inotify资源
// string $pathname 要监听的文件，只支持文件不支持目录
// int $mask 常量  要监听的事件，如修改、删除等等， IN_MODIFY为修改,具体参考 https://php.net/manual/en/inotify.constants.php
// 如有多个文件，则获取文件目录，在获取文件目录下文件，进行循环监听
$watch_descriptor = inotify_add_watch($fd, __DIR__ . '/demo/index.php', IN_MODIFY);

// 读取发生改变的文件
// 是一个阻塞的
// swoole添加进入事件后，如果不进行关闭，自己会一直循环
swoole_event_add($fd, function ($fd) {
    $events = inotify_read($fd);
    var_dump($events);
});

