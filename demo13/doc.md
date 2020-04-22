# task进程

## 课程任务

1. swoole结构回顾
2. task进程介绍
3. task初体验
4. task-ipc-mode的消息队列通信模式
5. task问题
6. task任务切分

### swoole结构回顾

* swoole的四层结构

```
// 主进程-》管理进程-》工作进程-》task进程（异步工作进程）
// 主进程：分为主线程和reactor线程组
// 主线程：主要处理连接，信号处理
// reactor线程组：分发请求
```

* swoole的两种模式

默认为SWOOLE_PROCESS模式

SWOOLE_BASE 这种模式就是传统的异步非阻塞，
当有 TCP 连接请求进来的时候，所有的 Worker 进程去争抢这一个连接，并最终会有一个 worker 进程成功直接和客户端建立 TCP 连接，之后这个连接的所有数据收发直接和这个 worker 通讯，不经过主进程的 Reactor 线程转发。

SWOOLE_PROCESS 模式的 Server 所有客户端的 TCP 连接都是和主进程建立的，内部实现比较复杂，用了大量的进程间通信、进程管理机制。适合业务逻辑非常复杂的场景。Swoole 提供了完善的进程管理、内存保护机制。 在业务逻辑非常复杂的情况下，也可以长期稳定运行。

Swoole 在 Reactor 线程中提供了 Buffer 的功能，可以应对大量慢速连接和逐字节的恶意客户端。

### task进程介绍

* task进程处理耗时任务，有worker进程投递任务进来
* worker进程投递给task进程，task进程执行完毕后通知worker进程
* task只是处理业务-》与消息队列的区别
* task进程开启

```
$server->set(array(
    'worker_num'      => 2,
    // 设置并开启task进程
    'task_worker_num' => 4,
));
// 注意：一旦task进程开启，必须要写task进程的两个事件
onTask和Finish
```

* task进程使用场景

```php
// 1. 大数据的操作（修改）-》处理耗时任务
// 2. 
// 3. 短信群发
```

* 如果给task进程未执行完，worker进程不会阻塞，task进程会阻塞
* task的task_ipc_mode模式

```
// 设置task和worker进程的通信方式
// 默认为1 socket通信方式
// task_ipc_mode
// 消息队列模式-》worker投递到消息队列，task监控消息队列
消息队列模式的意义：
1. 当主程序挂了,服务器停止等，可以保证任务不会丢失
2. 投递较大的任务时，程序断掉了
3. worker投递的任务超过了8k，如果没有及时的处理，会生成一个临时的缓存文件 目录 /tmp/
4. socket无法处理这些问题
消息队列-》redis，mysql：存储任务信息（临时缓存存储）
内核会自动区分该队列是有谁发送的

// 使用消息队列模式时需要指定mssage_queue_key
// 指定mssage_queue_key的值需要ftok函数生成
// 注意：消息队列使用的时linux内核的内存队列


// 三种方式

1	使用 Unix Socket 通信【默认模式】
2	使用 sysvmsg 消息队列通信
3	使用 sysvmsg 消息队列通信，并设置为争抢模式

```

### task_worker_num 值的设置

```
计算方法

单个 task 的处理耗时，如 100ms，那一个进程 1 秒就可以处理 1/0.1=10 个 task
task 投递的速度，如每秒产生 2000 个 task
2000/10=200，需要设置 task_worker_num => 200，启用 200 个 Task 进程
```

如果一瞬间投递大量任务给task会造成worker进程阻塞

task_max_request 可以设置最大任务进程数

```php
// server->taskwait  与 server->task方法相同往task进程中投递任务，不过此方法是同步等待的

```

### task任务切分

* 设置PHP内核大小进行测试(php.ini)  memory_limit=2048M
* 可以通过for循环分配给不同的task进程进行执行






















