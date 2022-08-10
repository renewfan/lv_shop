<?php


namespace App\Services;


class BaseService
{
    // 创建单例
    // 私有静态变量
    protected static $instance;
    // 防止其他地方、其他方式创建实例
    // 私有构造函数
    private function __construct()
    {
    }
    // 私有克隆方法
    private function __clone()
    {
    }
    /**
     * @return static
     * */
    // 公共静态获取单例方法
    // static 表示调用此方法的类
    public static function getInstance(){
        // 实例是否被创建
        if (static::$instance instanceof static) {
            // 已被创建--直接返回实例
            return static::$instance;
        }
        // 未被创建--创建实例
        static::$instance = new static();
        return static::$instance;
    }
}
