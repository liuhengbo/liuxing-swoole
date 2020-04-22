<?php

namespace Hengbo\Io\Reactor\Swoole\Mulit;

use Swoole\Event;

class Worker
{

    public $socket_adress = null;

    public $workPidFile = __DIR__ . '/pid.txt';
    // socket
    public $socket = null;

    // socket连接事件
    public $onConnect = null;

    // socker接收消息事件
    public $onReceive = null;

    // 当前进程ID
    public $workerPids = [];

    // 要重启的进程ID
    public $workerReloadPids = [];

    // 默认进程数量
    public $config = [
        'workerNum' => 4,
    ];


    public function __construct($socket_adress)
    {
        $this->socket_adress = $socket_adress;
    }

    /**
     * 启动
     */
    public function start()
    {
        debug("开始访问》》" . $this->socket_adress);
        // 启动之前，清空文件中的进程ID
        pidPut(null, $this->workPidFile);
        // 创建进程
        $this->fork();
        // 创建进程结束
        // 安装信号
        $this->monitorWorkersForLinux();
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
                debug("新创建的子进程ID为：".$son11);
                $this->workerPids[] = $son11;
                debug("创建后的进程为：");
                var_dump($this->workerPids);
            } else if ($son11 < 0) {
                // 进程创建失败的时候
            } else {
                // 子进程空间
                // debug(posix_getpid()); // 阻塞
                $this->accept();
                // 处理接收请求
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

    public function sendClient()
    {
        // 此$socket是客户端的
        return function ($socket) {
            //从连接当中读取客户端的内容
            $buffer = fread($socket, 1024);
            //如果数据为空，或者为false,不是资源类型
            if (empty($buffer)) {
                if (feof($socket) || !is_resource($socket)) {
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if (!empty($buffer) && is_callable($this->onReceive)) {
                ($this->onReceive)($this, $socket, $buffer);
            }
        };
    }

    public function initServer()
    {
        // 并不会起到太大的影响
        // 这里是参考与workerman中的写法
        $opts = [
            'socket' => [
                // 设置等待资源的个数
                'backlog' => '102400',
            ],
        ];

        $context = stream_context_create($opts);

        // 设置端口可以重复监听
        \stream_context_set_option($context, 'socket', 'so_reuseport', 1);

        // 传递一个资源的文本 context
        return $this->socket = stream_socket_server($this->socket_adress, $errno, $errstr, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $context);

    }

    public function stop()
    {
        // 停止
        // 获取到PID
        $workerPids = pidGet($this->workPidFile);
        foreach ($workerPids as $key => $value) {
            // 给linux传递一个信号进行杀死进程
            // 两种方式传递给linux信号
            // 1：kill 信号 进程号  2：程序中调用kill方法
            posix_kill($value, 9);
        }
    }

    /**
     * 通过kill -9 方式进行重启
     */
    public function reloadCli()
    {
        // 重启
        // 停止。
        $this->stop();
        // 启动
        $this->start();
    }

    /**
     * 通过信号的方式进行重启
     */
    public function reloadSig()
    {
        // 平滑重启，停止一个，启动一个
        // 获取到PID
        $this->workerReloadPids =  $this->workerPids;
        foreach ($this->workerReloadPids as $key => $value) {
            if (!in_array(posix_getpid(), $this->workerPids)) {
                // 杀死进程
                posix_kill($value, 9);
                debug("停止了进程" . $value);
                debug("当前进程ID" . posix_getpid());
                // 移除已停止的进程
                unset($this->workerReloadPids[$key]);
                unset($this->workerPids[$key]);
                debug("当前还需要重启的进程有");
                var_dump($this->workerReloadPids);
                // 启动进程
//                debug("当前现有的进程有");
//                var_dump($this->workerPids);

                $this->fork(1);
            }
        }
    }

    /**
     * 启动时将此信号安装
     */
    public function monitorWorkersForLinux()
    {
        // 信号安装
        pcntl_signal(SIGUSR1, [$this, 'sigHandler'], false);
        while (1) {
            // Calls signal handlers for pending signals.
            \pcntl_signal_dispatch();
            // Suspends execution of the current process until a child has exited, or until a signal is delivered
            \pcntl_wait($status);
            // Calls signal handlers for pending signals again.
            \pcntl_signal_dispatch();
        }
    }

    /**
     * 信号处理
     * @param $sig
     */
    public function sigHandler($sig)
    {
        switch ($sig) {
            case SIGUSR1:
                // 重启信号
                $this->reloadSig();
                break;
            case SIGKILL:
                // 停止信号
                $this->stop();
                break;
        }
    }

}