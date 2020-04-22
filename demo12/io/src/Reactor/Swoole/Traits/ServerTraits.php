<?php


namespace Hengbo\Io\Reactor\Swoole\Traits;


use Swoole\Event;

trait ServerTraits
{
    // socket
    public $socket = null;

    // socket连接事件
    public $onConnect = null;

    // socker接收消息事件
    public $onReceive = null;
    // 存储已连接的客户端
    public $clients = [];
    // 存储已创建的定时器
    public $timer = [];

    /**
     * 通过kill -9 方式进行重启
     */
    public function reloadCli()
    {
        // 重启之前需要将对象的回调函数加入，否则发送消息会有问题
        // TODO：
        // 重启
        // 停止。
        $this->stop(false);
        // 启动
        $this->start();
    }

    /**
     * 创建进程
     * @param null $workerNum
     */
    public function fork($workerNum = null)
    {
        // 获取进程数量
        $workerNum = empty($workerNum) ? $this->config['workerNum'] : $workerNum;

        // 循环创建进程
        for ($i = 0; $i < $workerNum; $i++) {
            $son11 = pcntl_fork();
            if ($son11 > 0) {
                // 父进程空间,记录创建的子进程ID
                pidPut($son11, $this->workPidFile);
                // 记路父进程ID
                file_put_contents($this->config['master_file_pids'], posix_getpid());
//                var_dump(posix_getpid());
//                debug("新创建的子进程ID为：" . $son11);
                $this->workerPids[] = $son11;
//                debug("创建后的进程为：");
//                var_dump($this->workerPids);
            } else if ($son11 < 0) {
                // 进程创建失败的时候
            } else {
                // 子进程空间
                // debug(posix_getpid()); // 阻塞
                // 处理任务
                $this->accept();
                // 注意此处break只是跳出当前循环，子进程还会往后执行，可以改为exit；
//                break;
                exit;
            }
        }

        // 等待子进程返回
        // 因之后需要安装信号，可将回收子进程部分入安装信号后
        /*for($i=0;$i<$this->config['workerNum'];$i++){
            $status = 0;
            $son = pcntl_wait($status);
            debug("回收子进程".$son);
        }*/

    }


    /**
     * 需要处理事情
     */
    public function accept()
    {
        Event::add($this->initServer(), $this->createSocket());
    }

    // 创建socket
    public function createSocket()
    {
        return function ($socket) {
            // debug(posix_getpid());
            // $client 是不是资源 socket
            $client = stream_socket_accept($this->socket);
            // is_callable判断一个参数是不是闭包
            if (is_callable($this->onConnect)) {
                // 执行函数
                ($this->onConnect)($this, $client);
            }
            // 默认就是循环操作
            // 如果有读事件，则执行回调
            Event::add($client, $this->sendClient());
        };
    }

    /**
     * 通过信号的方式进行重启
     */
    public function reloadSig()
    {
        // 平滑重启，停止一个，启动一个
        // 获取到PID
        $this->workerReloadPids = $this->workerPids;
        foreach ($this->workerReloadPids as $key => $value) {
            if (!in_array(posix_getpid(), $this->workerPids)) {
                // 杀死进程
                posix_kill($value, 9);
//                debug("停止了进程" . $value);
//                debug("当前进程ID" . posix_getpid());
                // 移除已停止的进程
                unset($this->workerReloadPids[$key]);
                unset($this->workerPids[$key]);
//                debug("当前还需要重启的进程有");
//                var_dump($this->workerReloadPids);
                // 启动进程
//                debug("当前现有的进程有");
//                var_dump($this->workerPids);

                $this->fork(1);
            }
        }
    }

    /**
     * 心跳检测  默认不开启
     */
    public function heartbeaatCheck($socket)
    {

        // 记录客户端请求进入的时间
        $this->clients[(int)$socket] = time();

        $time = $this->config['heartbeaat_check_interval'];

        // 客户端发送消息后多久在进行检测判断是否断开
        if (!empty($time)) {
            $timer = swoole_timer_after($time * 1000, function () use ($socket, $time) {
                if (time() - $this->clients[(int)$socket] >= $time) {
                    // 客户端超时
                    // 删除swoole事件
                    swoole_event_del($socket);
                    // 断开链接
                    unset($this->clients[(int)$socket]);
                    fclose($socket);
                    debug("客户端超时,断开链接" . (int)$socket);
                }
                debug("执行了定时器");

            });
            // 已创建的定时器加入属性
            $this->timer[(int)$socket] = $timer;

        }

    }

}