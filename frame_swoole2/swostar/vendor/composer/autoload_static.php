<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6e8309a5baacdf1690c3f24611831a74
{
    public static $files = array (
        '4f7cdc70f27ab42f61459b8f59a6c796' => __DIR__ . '/../..' . '/src/Supper/Helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SwoStar\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SwoStar\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6e8309a5baacdf1690c3f24611831a74::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6e8309a5baacdf1690c3f24611831a74::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
