# 打造swoole框架路由响应到请求

## 课程回顾

1. swostar 项目结构构建
2. 启动HttpServer


## 引入IOC


## HTTP 请求

* public/index.php->application::init()初始化->根据解析url找到控制器(1.直接根据命名空间;2.根据route)->执行操作->根据不同响应响应内容->返回结构

* 路由本质也是一个数组,key->自定义的表示,value->