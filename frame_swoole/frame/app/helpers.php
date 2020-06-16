<?php

/**
 * 输出调试信息
 * @param $message
 */
function debugEcho($message){
    $env = 'local';
    if($env == 'local'){
        echo $message . "\n";
    }
}