# 03-网络协议与tcp问题

## 协议分层

### 分几层

4、5、7

### 为什么需要协议

协议：通信语言

为什么需要协议：通信的基础，如果不设定协议，各种程序无法进行通信（例如：粤语和藏语无法通信）

### 为什么要分层

* mac地址不安全还不好区分

* 物理层（mac地址）-> 输出全靠吼（广播）

* 流程根据ID找到交换机再找到mac地址再找到端口然后找到相应的应用程序

## TCP三次握手和四次挥手通俗理解（短链接情况会进行四次挥手，长连接不用）

三次握手：客户端给服务端说我是客户端你能收到消息吗（确认自己能发，和服务端能收）
->服务端收到客户端的消息，再发送给服务端说，我收到了你能收到我发的消息吗（已确定客户端能发，自己能收，这一步确认自己能发，客户端能收）
->客户端收到服务端的消息，再发给服务端一条消息（客户端确认服务端能收能发，但此次服务端还不知道自己能发，所以只再发送一条消息给服务端）

四次挥手：

客户端->服务端——————我想断开连接

服务端->客户端——————我知道你要断开连接了，但我还有数据没有处理完，等我下我处理完

服务端->客户端——————我处理完了，关闭连接吧(服务端进入等待确认管理状态)

客户端->服务端——————我已关闭你也关闭吧

## TCP拆包、分包、组包

TCP传输单个数据很大时，会将数据分成等分的包进行发送（拆包、分包）

客户端接收到后会进行组包处理，再组成一个完整的数据

## TCP发送过程中出现的问题（粘包、抓包、丢包）

### 粘包（正常现象）

粘包现象

![](.doc_images/f1c31d4c.png)

短时间内发送大量很小的包，会粘再一起发送

优点：提高效率，会有一个缓存区，发送之前存入缓冲区，缓存区中数据达到一定量，在进行统一发送

#### 出现问题

1. 如出现以下错误，是因为使用同步客户端访问，同步客户端时短链接，而发送消息是异步的，造成发送不了

> 解决办法：可以修改同步客户端为长连接


```
WARNING	swProtocol_get_package_length: invalid package, remote_addr=unknown:0, length=-457334555, size=4
```


#### 解决办法

1. 发送的时候多发送一个分隔符，到服务端时在根据分隔符将粘住的包分开（1、自己手动定义；2、使用swoole的配置进行）

设置缓冲区内存尺寸 https://wiki.swoole.com/wiki/page/440.html

```
// swoole配置  EOF结束符协议
$server->set(array(
    'open_eof_split' => true,
    'package_eof' => "\r\n",
));
$client->set(array(
    'open_eof_split' => true,
    'package_eof' => "\r\n",
));
```

文档 https://wiki.swoole.com/wiki/page/484.html

2. 使用二进制分隔（原理php的pack函数）

> pack函数地址 https://php.golaravel.com/function.pack.html
> 1. 自定义使用pack函数进行处理
> 2. 使用swoole的处理方式（设置配置方式） 
> 文档  https://wiki.swoole.com/wiki/page/287.html
> 此配置配置过后无需在处理粘包的情况，官方推荐方式



```php
// 配置方式：

$server->set(array(
    'open_length_check' => true,
    'package_max_length' => 81920,
    'package_length_type' => 'N',
    // 数据从0开始
    'package_length_offset' => 0,
    // 是4是因为选择的处理类型是N 是4位
    'package_body_offset' => 4,
));
```

> 注意：具体package_length_type类型和package_body_offset字节数，参考文档：https://wiki.swoole.com/wiki/page/463.html
> 客户端向服务端发送时，服务端配置此配置处理粘包；服务端向客户端发送时客户端也需要配置此配置处理粘包(服务端也需要先进行pack)
> 注意使用此配置时原理：此配置时解客户端使用pack('N',strlen($str))后的数据，所以客户端传之前需要先进行pack数据，示例如下

```
$str = "我是客户端";
$len = pack('N',strlen($str));
for ($i=0;$i<100;$i++){
    $send = $len.$str;
    $client->send($send);
}
```

### TCP丢包

1. 重连机制
2. 数据校验-确认机制（会先发送此文件有多大，接收方接收完成后会验证是否接收了这么多文件）

## 作业

聊天室

## swoole 服务配置

nginx转发到swoole

nginx反向代理

例如：（将所有请求转发到9501端口进行处理）

```
location /{
    proxy_pass http://121.43.43.200:9501
}
```

## 地址

swoole.tingt.top  转发到swoole测试目录

proxy_swoole.tingt.top 转发到swoole代理端口



## 网络IO模型

* 进程、线程、多进程、协程、父进程、子进程

### 进程

系统中所运行的程序、如：PHP运行的程序

### php 创建进程

使用pcntl扩展的pcntl_fock()方法创建一个子进程

### Linux查看进程

    pstree -ap | grep pcntl.php

### 线程

进程-》多个线程-》就是任务

运行状态：

    初始化-》可运行-》运行中-》结束、销毁
    线程切换的时候是阻塞状态
    
涉及到的点：

    1. 涉及到锁
    2. 涉及到线程阻塞和运行状态的切换
    3. 上下文切换
    4. 线程有资源的消耗

线程-》被程序内核控制

协程-》可以被代码控制
    

### 协程

进程-》线程-》协程
