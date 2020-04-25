<?php

// http的路由

use SwoStar\Routes\Route;
//
//Route::get('index',function (){
//    return "这是http路由";
//});



Route::get('index','IndexController@index');

Route::get('index/dd','IndexController@dd');