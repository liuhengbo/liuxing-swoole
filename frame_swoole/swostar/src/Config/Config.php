<?php


namespace SwoStar\Config;


class Config
{
    /**
     * @var string
     */
    protected $configPath;
    protected $items = [];

    public function __construct()
    {
        // 获取配置文件路径
        $this->configPath = app()->getBasePath() . '/config';
        // 读取配置
        $this->items = $this->phpParser();
    }

    /**
     * // yml
     * // json
     * // php
     */
    public function phpParser()
    {
        // 1. 找到文件
        $files = scandir($this->configPath);
        $data = null;
        // 2. 读取文件信息
        foreach ($files as $key => $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            // 读取文件名
            $filename = stristr($file, ".php", true);
            // 读取数据
            $data[$filename] = include $this->configPath . '/' . $file;

            return $data;
        }
    }

    /**
     * 获取配置
     * @param $keys
     * @return array|mixed|null
     */
    public function get($keys)
    {
        $data = $this->items;

        foreach (explode('.', $keys) as $key => $value) {
            $data = $data[$value];
        }
        return $data;

    }

}