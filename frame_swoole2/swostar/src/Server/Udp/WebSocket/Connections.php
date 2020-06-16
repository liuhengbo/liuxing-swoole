<?php


namespace SwoStar\Server\WebSocket;


class Connections
{
    /**
     * fd = [
     *  'path'=>'ddd'
     * ]
     * @var array
     */
    public static $connections = [];

    /**
     * 存储连接ID和路径
     * @param $fd
     * @param $path
     */
    public static function init($fd, $path)
    {
        self::$connections[$fd]['path'] = $path;
    }


    public static function get($fd)
    {
        return self::$connections[$fd];
    }

    public static  function del($fd)
    {
        unset(self::$connections[$fd]);
    }


}
