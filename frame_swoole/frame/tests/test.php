<?php

require __DIR__.'/../vendor/autoload.php';

use App\Http\IndexController;
use \App\App;

echo (new IndexController())->index();
echo (new App())->index();


echo app('index')->index();