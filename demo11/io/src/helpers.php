<?php

function ceshi(){
    echo "帮组函数测试\n";
}

// 调试函数
function debug($data, $flag = false)
{
    if ($flag) {
        var_dump($data);
    } else {
        echo "==== >>>> : ".$data." \n";
    }
}

/**
 * 记录PID到文件
 * @param $data
 * @param $path
 */
function pidPut($data,$path){
    empty($data) ? file_put_contents($path,null) : file_put_contents($path,$data.'|',8);
}

/**
 * 从文件中获取PID
 * @param $path
 * @return array
 */
function pidGet($path){
    $string =file_get_contents($path);
    return explode("|",substr($string,0,strlen($string)-1));
}


// 发送信息
function send($client, $data, $flag = false)
{
    if ($flag) {
        fwrite($client, $data);
    } else {
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: ".strlen($data)."\r\n\r\n";
        $response .= $data;
        fwrite($client, $response);
    }
}