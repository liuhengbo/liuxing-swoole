# 打造Swoole框架之HTTP服务完善

## swoole多端口监听

实际是另外开启了一个进程进行监听另外的端口

```
$port = $http->addListener('127.0.0.1', '8501', SWOOLE_SOCK_TCP);

$port->set([
    'worker_num' => 1,
]);
```