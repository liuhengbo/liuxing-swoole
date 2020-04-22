# HTTP框架构建

## 课程回顾

* 基于swoole的(http与websocket服务)tcp,udp->swostar(laravel+swoft)
* 传统web不能满足的事情:直播,qq聊天,运用长链接
* 一个进程处理一个请求->多进程
* 网络IO模型->异步->早期fpm预派生子进程模型->reactor->swoole
* 请求访问服务器->php->Linux内核处理事情
* 单线程异步reactor模型,实现的用户空间异步,内核空间阻塞
* swoole结构:主进程,工作进程,worker工作进程,task异步工作进程
* worker_num配置设置的公式,一般为CPU核数的1-4倍
* worker进程交由task进程处理时传递方式有:socker,消息队列,消息队列争抢模式    使用消息队列模式时需要设置key值,key值可以用ftok函数生成
* swoole为什么取消PHP的预定义超全局变量,每次PHP请求时都会重新为超全局变量进行赋值,在swoole常驻内存中不会被改变,所以会出现问题

## HttpServer构建基础

### 基于swoole构建Http框架服务

* swoole框架swostar
* frame 完整框架,swostar框架核心源码
* Foundation应用程序,初始化和启动
* Server->服务->swosstar框架依托的服务->
* SwoStar框架基本目录搭建
