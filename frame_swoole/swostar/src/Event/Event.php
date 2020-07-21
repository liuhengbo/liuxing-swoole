<?php

namespace SwoStar\Event;

class Event
{

    /**
     * 事件
     * @var array
     */
    protected $events = [];

    /**
     * 注册事件
     * @param $event
     * @param $callback \Closure
     */
    public function register($event,$callback)
    {
        $event = strtolower($event);

        $this->events[$event] = ['callback'=>$callback];
    }

    /**
     * 触发事件
     * @param $event
     * @param array $params
     */
    public function trigger($event,$params = [])
    {
        $event = strtolower($event);

        if (isset($this->events[$event])){
            return ($this->events[$event]['callback'])(...$params);
        }
        dd('事件'.$event.'不存在');
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

}