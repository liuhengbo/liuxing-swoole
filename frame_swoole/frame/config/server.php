<?php

return [
    'Http'=>[
        'host'=>'127.0.0.1',
        'port'=>'9501',
        'tcpable' =>1 ,
        'rpc'=>[
            'host'=>'127.0.0.1',
            'port'=>'8501',
            'swoole'=>[
                'worker_num'=>1,
            ]
        ]
    ],


];