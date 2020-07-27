<?php


namespace SwoStar\Event;


use SwoStar\Foundation\Application;

abstract class Listener
{
    /**
     * 事件标识
     * @var string
     */
    protected $name = 'listener';
    
    protected $app;

    public function __construct(Application $app)
    {
        $this->app =$app;
    }

    public abstract function handle($params);

    public function getName()
    {
        return $this->name;
    }

}