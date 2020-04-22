# IM　构建及Route构建及server检测

## Route层需要的服务

1. 检测Im-Server的存活状态
2. 支持权限认证
3. 根据服务器的状态,按照一定的算法,计算出该客户端连接到哪台Im-Server,返回给客户端,客户端再去连接到对应的服务器,保存客户端与Im-Server的路由关系
4. 如果Im-Server服务器挂了,会自动将该Server从Redis中删除
5. Im-Server上线后链接到Route,自动报告自己的运行状态,并写入的redis(ip+端口)
6. 可以接受来自PHP代码,C++程序,Java程序的消息请求,转发给用户所在的Im-Server
7. 缓存服务器地址,多次查询Redis

## Route层

swostar -> springboot
swocloud -> springcloud

## 心跳和轮训的区别

心跳是检测一定时间内客户端有没有请求进入,轮训是客户端不断请求服务端

## 思路

Im-Server中onStar时像Route服务注册s



