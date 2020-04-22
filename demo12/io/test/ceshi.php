<?php

require __DIR__.'/../vendor/autoload.php';

use Hengbo\Io\Ceshi\Ceshi;

// composer 类测试
(new Ceshi())->start();

// 帮助函数测试

\ceshi();