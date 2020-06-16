<?php

namespace SwoCloud\Supper;

class Arithmetic
{
    public static $currentIndex = 0;

    /**
     * 轮训算法
     * @param array $list
     * @return mixed
     */
    public static function round(array $list)
    {
        $max_index = count($list) - 1;

        if(self::$currentIndex >= $max_index){
            self::$currentIndex = 0;
        }else{
            self::$currentIndex++;
        }

        return $list[self::$currentIndex];

    }
}