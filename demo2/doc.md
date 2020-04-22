# 02-基础概念与长连接

## 查看swoole版本

    php --ri swoole


## 同步（阻塞）

一群人去一个窗口买火车票（只能一个一个来），前一个人不买完票后一个人就无法进行买票

## 同步（非阻塞）

分解买票过程为：查票-》确认买票-》收钱

其中同步模式为一个售票员处理这些事情，

异步处理：

售票员1只进行查票操作，将确认买票收钱交给售票员1的助手，则售票员1则少进行了一些步骤，进而提高效率

## 安装swoole异步模块

1. 下载swoole异步模块，并解压
2. 进入解压目录执行`phpize`
3. 执行./configure --with-php-config={自己的php-config目录}（如果不知道可以用此命令搜索`find / -name php-config`）
4. 执行`make`
5. 执行`make install`
6. 将.so文件的路径加入php.ini（不知道的可以搜索`find / -name *.so`）

## 遇到的坑

解决php -v查看到版本于phpinfo()打印的版本不一致问题

https://blog.csdn.net/haif_city/article/details/81315372


## swoole 同步客户端

TCP短连接-》一次连接一次结果

同步时，客户端等几秒还没收到会报错（具体几秒不知道）

## swoole 异步客户端

TCP长连接

貌似连接成功发送的消息比较慢、慢与接收消息的事件


## 怎么确定保持连接

1. 轮询（资源占用大）、一般是服务器端每隔一段时间发送一次消息确定客户端还连接着
2. 心跳 客户端在一定时间内给服务端发送包，如果过了时间服务器没有接收到，则服务器认为客户端已死（包不能太大，此包称为心跳包），服务端会主动断开连接

## 可以参考方法的包

composer require eaglewu/swoole-ide-helper

## swoole 心跳包检测

### 心跳配置

服务器增加心跳配置

// heartbeat_check_interval 每多少秒检测一次
// heartbeat_idle_time 多少秒后没有心跳时服务端会主动断开连接

```php
$serv->set(array(
    'heartbeat_check_interval' => 5,
    'heartbeat_idle_time' => 10,
));
```

## 定时器

```php
//每隔2000ms触发一次
swoole_timer_tick(2000, function ($timer_id) {
    echo "tick-2000ms\n";
});

//3000ms后执行此函数
swoole_timer_after(3000, function () {
    echo "after 3000ms.\n";
});
```

* swoole_timer_tick函数就相当于setInterval，是持续触发的
* swoole_timer_after函数相当于setTimeout，仅在约定的时间触发一次
* swoole_timer_tick和swoole_timer_after函数会返回一个整数，表示定时器的ID
* 可以使用 swoole_timer_clear 清除此定时器，参数为定时器ID

注意：swoole定时器是异步的，如果在同步中使用


## TCP和UDP

TCP 先建立连接再发送数据

UDP 不建立连接直接发送

UDP使用场景： 如果公众号模板消息群发

创建UPD客户端

```php
// UDP 客户端

$client = new swoole_client(SWOOLE_SOCK_UDP);

// 给服务端发送数据

$client->sendto('127.0.0.1','9501','UPD客户端给服务器发的数据');

$data = $client->recv();

var_dump($data);

echo "ddd";
```

创建UPD服务端

```php
// 创建UPD服务器，不需连接直接发送
//创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
$serv = new swoole_server("0.0.0.0", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

//监听客户端发来的数据
$serv->on('Packet', function ($serv, $data, $clientInfo) {
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "给UDP客户端发送一个消息 ".$data);
    var_dump($clientInfo);
});

//启动服务器
$serv->start();
```
