<?php

use SwoStar\Console\Input;

use SwoStar\Foundation\Application;

if (!function_exists('app')) {
    /**
     * @param  [type] $a [description]
     * @return Application
     */
    function app($a = null)
    {
        if (empty($a)) {
            return Application::getInstance();
        }
        return Application::getInstance()->make($a);
    }
}
if (!function_exists('dd')) {
    /**
     * @param  [type] $a [description]
     * @return Application
     */
    function dd($message, $description = null)
    {
        Input::info($message, $description);
    }
}
