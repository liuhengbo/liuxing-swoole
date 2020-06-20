<?php

/**
 * 输出调试信息
 * @param $message
 */
function echoDebug($message){
    $env = 'local';
    if($env == 'local'){
        echo $message . "\n";
    }
}

function  echoTips($message){
    print_r($message);
}

function logError($message){
    echo $message . "\n";
}

