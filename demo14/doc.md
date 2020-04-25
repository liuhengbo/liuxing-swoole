# 进程间通信

## task进程回顾

* task进程超过8k后会放到缓存区

## 进程间通信（IPC）

### 进程间通信方式

* socket（通过资源发送）（常见）
* 消息队列（常见）
* 信号（常见）
* 共享内存
* 管道
* ...

1.消息队列

    a. 用户空间（生产者）
    b. 系统内核（消费者）
    c. 暂存区
    
    ipcs -q（查看Linux的消息队列）
    
    swoole中也有提供task使用消息队列的方式、
    
    实现原声PHP与swoole通过消息队列进行交互
    
    1. PHP如何操作Linux内核的消息队列？
        msg_get_queue()   创建一个消息队列
        基于sysvmsg扩展和sysvshm扩展
        实现思路，父进程像子进程发送信息
        msg_receive接受消息会阻塞
        注意：$key = ftok(__DIR__,'u');生成的key值一致并且msgType参数类型一致才可以才可以接收到
        msgType参数为0时可以接受到任何类型的值传入
        
        msg_send参数     （发送）
            ：bool msg_send ( resource $queue , int $msgtype , mixed $message [, bool $serialize = true [, bool $blocking = true [, int &$errorcode ]]] )
            : $queue指定发送数据的队列
            ： $msgType 0和指定类型，0为接收所有消息队列，其他值为指定类型  类型相当于IP地址
            ：$message 发送的数据
            
        msg_receive参数 （接收）
            ：bool msg_receive ( resource $queue , int $desiredmsgtype , int &$msgtype , int $maxsize , mixed &$message [, bool $unserialize = true [, int $flags = 0 [, int &$errorcode ]]] )
            ：$queue 资源
            ：$desiredmsgtype 0和任意类型
            ：$msgtype 

    2. PHP可以通过消息队列调用swoole的task进程
        但会没有相应的workerID，会发出警告
        
    3. 如果有多个receive进程，是否会出现多个receive进程同时收到msg_send发送的消息队列
        只有一个会接受到
### worker实现task进程

    worker-》task进程-》task进程有很多个
    实现思路：

### task消息队列（作用）

    1. task会不会阻塞
        task阻塞的是子进程，不是父进程
    2. 发送的时候是随机向一个task中发送，那可以去报接收成功吗
        
    3. 队列会清空吗

### swoole编程中注意事项

    1. task并不能替代消息队列，消息队列处理任务的时候-》延迟任务，任务优先级，任务的存储
        对于task来说更多的只是业务的处理，是对worker的补充
    2. server->sendMessage()函数   可以向任意一个worker和task进程发送信息  server->PipeMessage  接收消息
    3. 注意：workerStart事件在worker进程和task进程启动时都会进行触发

