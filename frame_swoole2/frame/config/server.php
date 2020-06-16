<?php

return [
    'Http'=>[
        'host'=>'127.0.0.1',
        'port'=>'9502',
        'tcpable' =>1 ,
        'rpc'=>[
            'host'=>'127.0.0.1',
            'port'=>'8502',
            'swoole'=>[
                'worker_num'=>1,
            ]
        ]
    ],


];