# 
## 课程内容

0. 处理信号启动，子进程无法结束的问题与信息发送问题   swoole\Event:add
1. 代码热加载
2. 心跳检测
3. 回顾swoole结构
4. task进程介绍
5. 额外封装操作

### 进程问题展示

* 使用`kill -9 进程号`停止父进程结束后子进程依旧在运行  

```
// 信号执行流程
1. 安装信号sign
2. 分发信号dispatch
3. 调用kill-》pid信号-》运行 （用户空间给内核空间发信号，实际执行是在内核空间）   
```

* 另一个问题复现（孤儿进程产生--父进程已经被关闭，只留子进程在运行）

```
// 原因： 父进程被关闭，子进程交给了内核进行处理（进程ID为1的可以看做是内核）

1. 启动server进程
2. curl 请求server  父进程ID正常
3. 通过信号重启server工作进程
4. curl 请求server  父进程ID正常
5. ctrl+c关闭server主进程（此时只关闭了父进程子进程依旧在运行已关闭）
6. curl 请求server  父进程ID变为1（此时并没有父进程）

// 解决方案：创建一个信号SIGINT,对此信号进行监控，当父进程关闭时，同时关闭子进程
```
    
    
* 客户端curl请求时，一直进行等待?  

```
    原因是子进程创建后-》执行任务-》依旧会向后执行（之前使用的break，只是跳出循环，并没有结束子进程，将break改为exit即可）
    tips: 此处exit只会停止子进程执行，并不会终止脚本运行
```


### 代码热重启

* inotify 方式（inotify是PHP监控文件变化的扩展）

```
// inotify 扩展安装步骤（编译安装）

1. 解压 tar -zxvf 文件名
2. 进入文件夹 执行phpize
3. 执行 ./configure --with-php-config=/www/server/php/73/bin/php-config
4. 执行make
5. 执行make insstall(会输出安装目录)
6. 编辑php.ini文件 vi /www/server/php/73/etc/php.ini
7. extension=intofy

```

```
// 原理： 监控文件变化，发送信号，使swoole进行重启
// 实现思路：
1. 获取文件目录，把文件目录下的文件进行监听
2. 当文件发生改变时，重启worker进程

```

### 心跳检测

* 目的：客户端没有信号后，服务端主动断开链接

```
// 服务端：对于客户端在一定时间内进行检测
// 可以理解为：客户端发送了一个信息之后，多久没有联系-》定时器
// 使用swoole定时器 swoole_timer_tick
```

```
// 注意点：
1. 当资源已经被关闭时不应该再去执行定时器检测
2. 每次请求结束时，在一定的时间内检测客户端是否还在链接中，如果不在删除资源，删除事项
3. 防止请求时间间隔很短，创建多个定时器的情况发生，在请求进入之后，应进行判断是否已经有已存在的定时器，如果有应进行删除此定时器，并新建一个心的定时器，重新从心跳检测时间进行计算

```
    