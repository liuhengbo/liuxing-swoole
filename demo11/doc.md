# 子进程创建需要加exit

    需要合理分配进程数

# ab并发测试

    ab -n 请求数 -c 并发数 -k 请求地址
    
# 热加载的现象

    
# 查看信号

     1) SIGHUP	 2) SIGINT	 3) SIGQUIT	 4) SIGILL	 5) SIGTRAP
     6) SIGABRT	 7) SIGBUS	 8) SIGFPE	 9) SIGKILL	10) SIGUSR1
    11) SIGSEGV	12) SIGUSR2	13) SIGPIPE	14) SIGALRM	15) SIGTERM
    16) SIGSTKFLT	17) SIGCHLD	18) SIGCONT	19) SIGSTOP	20) SIGTSTP
    21) SIGTTIN	22) SIGTTOU	23) SIGURG	24) SIGXCPU	25) SIGXFSZ
    26) SIGVTALRM	27) SIGPROF	28) SIGWINCH	29) SIGIO	30) SIGPWR
    31) SIGSYS	34) SIGRTMIN	35) SIGRTMIN+1	36) SIGRTMIN+2	37) SIGRTMIN+3
    38) SIGRTMIN+4	39) SIGRTMIN+5	40) SIGRTMIN+6	41) SIGRTMIN+7	42) SIGRTMIN+8
    43) SIGRTMIN+9	44) SIGRTMIN+10	45) SIGRTMIN+11	46) SIGRTMIN+12	47) SIGRTMIN+13
    48) SIGRTMIN+14	49) SIGRTMIN+15	50) SIGRTMAX-14	51) SIGRTMAX-13	52) SIGRTMAX-12
    53) SIGRTMAX-11	54) SIGRTMAX-10	55) SIGRTMAX-9	56) SIGRTMAX-8	57) SIGRTMAX-7
    58) SIGRTMAX-6	59) SIGRTMAX-5	60) SIGRTMAX-4	61) SIGRTMAX-3	62) SIGRTMAX-2
    63) SIGRTMAX-1	64) SIGRTMAX	

    
    // 查看所有信号类型
    kill -l
    // 查看端口
    netstat -apn | grep 9000
    // 发送信号 （上一步获得的进程号） 使swoole重启（只有include引入的才可以）
    kill -10 进程号
    // 开发过程中可以根据此特性实现热加载
    
 # 监听文件变化扩展
 
    inotify监听文件变化
    
# reactor模型

    reactor模型是异步IO的一种经典实现
    
    
# 杀掉进程

    实现方式：创建进程的时候记录PID到文件中，重启时获取这些PID然后Kill掉
    
    
    
# 两种方式传递信号

    kill -信号 进程号
    
    posix_kill(进程号，信号)

#  信号接入重启

    
    
    
## 重启方式

* 实现思路：创建子进程的时候记录进程ID，执行PHP脚本关闭时，取出创建的子进程，传递信号给Linux进行关闭
* 记录方式有：文本、内存；laravel-s即有记录进程ID的文件，执行php artisan时进行取出传递信号
* 问题： 使用PHP函数进行停止后父进程被干掉了，解决：pcntl_wait将子进程回收了，没有阻塞了，父进程执行完，所以会被干掉
* 重启思路：停止-》启动
* 两种方式：信号，kill命令
* 信号方式：平滑启动，停止一个，启动一个(此中方式需注意，创建子进程后使用break只能跳出当前循环，如果外层还有循环，那么子进程依旧会执行外层的循环，尽量使用exit，可以避免子进程继续往后执行)


