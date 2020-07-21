<?php

return [
    // 本机服务器地址
    'http'=>[
        'host'=>'127.0.0.1',
        'port'=>'9501',
        'tcpable' =>1 ,
        'rpc'=>[
            // 暴露出的服务器监听地址
            'host'=>'127.0.0.1',
            'port'=>'9502',
            'swoole'=>[
                'worker_num'=>1,
            ]
        ]
    ],
    // route 层
    'route_http'=>[
        'host'=>'127.0.0.1',
        'port'=>'9601',
    ],
    "ws"=>[
        'is_handshake' => false
    ],
    "route_jwt"=>[
        'key'=>'swocloud',
        'allowed_algs'=>[
            'HS256'
        ],
    ]

];