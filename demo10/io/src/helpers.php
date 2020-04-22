<?php
function debug($data, $flag = false)
{
    if ($flag) {
        var_dump($data);
    } else {
        echo "==== >>>> : ".$data." \n";
    }
}
// 发送信息
function send($client, $data, $flag = true)
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
