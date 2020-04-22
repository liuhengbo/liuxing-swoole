<?php


namespace Hengbo\Io\Reactor\Swoole\Traits;


trait InotifyTraits
{
    /**
     * 设置监控的毁掉函数
     * @return \Closure
     */
    protected function watchEvent()
    {
        return function ($event) {
            $action = "file：";
            switch ($event['mask']) {
                case IN_CREATE:
                    $action = 'IN_CREATE';
                    break;
                case IN_DELETE:
                    $action = 'IN_DELETE';
                    break;
                case IN_MODIFY:
                    $action = 'IN_MODIFY';
                    break;
                case IN_MOVE:
                    $action = 'IN_MOVE';
                    break;
            }
//            var_dump('dddd');
            debug('已修改文件，工作进程重启中，修改操作：' . $action . "文件名称：" . $event['name']);
            $masterPid = file_get_contents($this->config['master_file_pids']);
            posix_kill($masterPid, SIGUSR1);
        };
    }
}