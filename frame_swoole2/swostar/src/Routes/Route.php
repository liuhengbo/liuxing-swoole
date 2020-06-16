<?php


namespace SwoStar\Routes;


use SwoStar\Console\Input;

class Route
{
    //存储路由
    protected $routes = [];

    // 当前实例
    public static $instance = null;

    // 定义路由访问类型
    protected static $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    // 记录路由的文件地址
    protected $routeMap = [];

    // 记录请求方式
    protected $method = null;
    // 协议标识
    public $flag = null;

    public function __construct()
    {
        $this->routeMap = [
            'Http' => app()->getBasePath() . '/route/http.php',
            'WebSocket' => app()->getBasePath() . '/route/web_socket.php',
        ];
    }

    /**
     * 注册get方法
     * @param $uri
     * @param $action
     */
    public function get($uri, $action)
    {
        $this->addRoute(['GET'], $uri, $action);
    }

    public function any($uri, $action)
    {
        return $this->addRoute(self::$verbs, $uri, $action);
    }

    public function wsController($uri, $controller)
    {
        $actions =[
            'open',
            'message',
            'close',
        ];

        foreach ($actions as $action){
            $this->addRoute([$action],$uri,$controller.'@'.$action);
        }

    }

    /**
     * 注册post方法
     * @param $uri
     * @param $action
     */
    public function post($uri, $action)
    {
        $this->addRoute(['POST'], $uri, $action);
    }


    /**
     * 获取路由并存储
     * @param $methods
     * @param $uri
     * @param $action
     * @return $this
     */
    public function addRoute($methods, $uri, $action)
    {
        foreach ($methods as $method) {
            $this->routes[$this->flag][$method][$uri] = $action;
        }
        return $this;

    }

    /**
     * 根据请求校验路由
     * @param $path
     * @param array $params
     * @return mixed|string
     */
    public function match($path,$params = [])
    {
        // 根据请求类型获取路由
        $routes = $this->routes[$this->flag][$this->method];
        $action = null;
        foreach ($routes as $uri => $value){
            // 如果"/"不在第一位或者没有匹配到,则在开头加一个"/"
            $uri = ((strpos($uri,'/') !== 0) || (strpos($uri,'/')) === false  ) ? "/".$uri : $uri;
            if($uri === $path){
                $action = $value;
                break;
            }
        }

        if(!empty($action)){
            return $this->runAction($action,$params);
        }

        Input::info('没有找到方法');

        return '404';
    }

    /**
     * 运行路由对应控制器和方法
     * @param $action
     * @param array $params
     * @return mixed
     */
    private function runAction($action,$params = [])
    {
        if($action instanceof \Closure){
            // 闭包形式
            return $action(...$params);
        }else{
            // 控制器解析
            $namspace = '\App\\'.$this->flag.'\Controller\\';

            list($controller,$action) = explode('@',$action);
            // 暂时不支持传参
            $controller = $namspace . $controller;
            $class = new $controller();
            return $class->$action(...$params);

        }
    }


    /**
     * 注册路由
     */
    public function registerRoute()
    {
        foreach ($this->routeMap as $key=>$value) {
            $this->flag = $key;
            require_once $value;
        }
        return $this;
    }

    /**
     * 获取单利对象
     * @return $this|null
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            return new static();
        }

        return self::$instance;

    }

    /**
     * 设置请求方法
     * @param $method
     * @return Route
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * 设置请求标识
     * @param $flag
     * @return Route
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
        return $this;
    }

    /**
     * 获取注册的路由
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }


}