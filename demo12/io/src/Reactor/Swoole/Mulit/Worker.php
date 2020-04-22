<?php

namespace Hengbo\Io\Reactor\Swoole\Mulit;

use Hengbo\Io\Reactor\Swoole\Traits\InotifyTraits;
use Hengbo\Io\Reactor\Swoole\Traits\ServerTraits;
use Hengbo\Io\Util\Inotify;

class Worker
{

    public $socket_adress = null;

    public $workPidFile = __DIR__ . '/pid.txt';


    // 当前进程ID
    public $workerPids = [];

    // 要重启的进程子ID
    public $workerReloadPids = [];


    // 默认进程数量
    public $config = [
        'workerNum' => 4,
        'master_file_pids' => __DIR__ . '/masterPid.txt',
        'watch_file' => false,
        // 以秒作为单位
        'heartbeaat_check_interval' => 3,
    ];

    public $inotify = null;

    use ServerTraits,InotifyTraits;

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
//        debug("当前配置文件");
//        var_dump($this->config);
        // 启动之前，清空文件中的进程ID
        pidPut(null, $this->workPidFile);
        // 创建进程
        if ($this->config['watch_file']) {
            $this->inotify = new Inotify(basePath(), $this->watchEvent());
            $this->inotify->start();
//            $this->inotify->stop();
        }

        $this->fork();
        // 创建进程结束
        // 安装信号
        $this->monitorWorkersForLinux();
    }


    /**
     * 设置配置文件
     * @param $conf
     */
    public function set($conf)
    {
        foreach ($conf as $key => $value) {
            $this->config[$key] = $value;
        }
    }


    public function sendClient()
    {

        // 此$socket是客户端的
        return function ($socket) {

            // 判断是否已有定时器存在
            if($this->timer[(int)$socket]){
                // 如果存在定时器，代表用户在心跳检测范围内发送了多次请求
                // 解决：如果已存在则删除原定时器，并创建新的定时器，即重新计算心跳时间
                swoole_timer_clear($this->timer[(int)$socket]);
                debug("清空了定时器");
            }


            //从连接当中读取客户端的内容
            $buffer = fread($socket, 1024);
            //如果数据为空，或者为false,不是资源类型
            if (empty($buffer)) {
                if (feof($socket) || !is_resource($socket)) {
                    //触发关闭事件
                    swoole_event_del($socket);
                    fclose($socket);
                    return null;
                }
            }
            //正常读取到数据,触发消息接收事件,响应内容
            if (!empty($buffer) && is_callable($this->onReceive)) {
                ($this->onReceive)($this, $socket, $buffer);
            }

            // 客户端进来信息后触发心跳检测
            $this->heartbeaatCheck($socket);

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

    public function stop($is_kill_master_pid = true)
    {
        // 停止子进程
        // 获取到PID
        $workerPids = pidGet($this->workPidFile);
        foreach ($workerPids as $key => $value) {
            // 给linux传递一个信号进行杀死进程
            // 两种方式传递给linux信号
            // 1：kill 信号 进程号  2：程序中调用kill方法
            posix_kill($value, 9);
        }
        // 判断是否需要杀死父进程
        if ($is_kill_master_pid) {
            // 停止父进程
            $masterPid = file_get_contents($this->config['master_file_pids']);
//            var_dump($masterPid);
            posix_kill($masterPid, 9);
            $this->inotify->stop();
        }


    }



    /**
     * 启动时将此信号安装
     */
    public function monitorWorkersForLinux()
    {
        // 信号安装
        pcntl_signal(SIGUSR1, [$this, 'sigHandler'], false);
        // 对父进程关闭的信号监控安装
        pcntl_signal(SIGINT, [$this, 'sigHandler'], false);
        while (1) {
            // Calls signal handlers for pending signals.
            \pcntl_signal_dispatch();
            // Suspends execution of the current process until a child has exited, or until a signal is delivered
            // 此处死循环的原因是，有几个子进程就可以重启几个子进程
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
            case SIGINT:
                // 监控父进程停止时，停止子进程
                $this->stop();
                break;
        }
    }



}