<?php


namespace SwoStar\Event;


abstract class Listener
{
    /**
     * 事件标识
     * @var string
     */
    protected $name = 'listener';

    public abstract function handle();

    public function getName()
    {
        return $this->name;
    }

}