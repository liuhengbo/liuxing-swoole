# HTTP服务

## server的4层生命周期

* 变量、对象、资源、require/include的文件统称为对象
* 程序全局期
    
    
    共享空间
    $serv->start();之前创建好的对象，成为程序全局期，程序全局对象在worker进程之间是共享的
    信号重启后不会改变程序全局变量，信号重启重启的只是worker进程
    
    
* 进程全局期

* 会话期


    是onConnect或onReceive事件后创建，客户端进入会常驻内存，直到客户端离开onClose事件后销毁
    此处在不同事件中访问需要进行globlal修饰
    可以在不同链接进入后，改变此值进行测试，不同链接进入并不会改变值
    

* 请求期

    
    onReceive事件中，请求在此事件进行处理
    
    
## server的内存管理机制



## swoole常驻内存

    1. 变量的回收
        主动回收变量
    2. 主进程全局变量注意回收
    
    
    
## HttpServer介绍与体验

* 与php-fpm的区别：swoole的HttpServer为常驻内存
* HttpServer继承与Swoole/Server，可以使用Swoole/Server中的方法，主要添加了一层Http协议，request，response
* PHP使用Swoole/Server，静态文件依旧使用nginx
* nginx+swoole的配置

    server {
        root /data/wwwroot/;
        server_name local.swoole.com;
    
        location / {
            proxy_http_version 1.1;
            proxy_set_header Connection "keep-alive";
            proxy_set_header X-Real-IP $remote_addr;
            if (!-e $request_filename) {
                 proxy_pass http://127.0.0.1:9501;
            }
        }
    }


## HttpServer的请求和响应

* swoole回收了PHP的超全局变量


## swoole加速框架laravel,tp

### swoole加速框架原理

    传统php-fpm 是一次性加载,用完销毁
    加载php.ini->解析php文件->放到内存->干掉
    swoole是一直在内存中
    加速框架的类型ioc,本质是将ioc容器放到swoole全局变量(程序全局变量)中常驻内存,并不会每次请求都会重新解析,加载
    ioc框架运行的生命周期:请求进入->初始化->将框架核心放到容器内->从容器中取出对象处理请求

    注意:swoole会回收PHP的超全局变量,而Laravel的路由主要依靠超全局变量,
        解决方案:将全局变量中的内容从swoole获取到,在赋值进laravel的超全局变量
