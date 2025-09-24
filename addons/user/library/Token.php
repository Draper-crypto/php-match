<?php
declare (strict_types=1);

namespace addons\user\library;

use addons\user\library\token\Driver;

class Token
{
    protected $namespace = '\\addons\\user\\library\\token\\driver\\';

    /**
     * 驱动
     * @var array
     */
    protected $drivers = [];

    /**
     * 创建驱动
     * @param string $name 驱动名称
     * @return Driver
     */
    public function createDriver(string $name)
    {
        $class = false === strpos($name, '\\') ? $this->namespace . ucwords($name) : $name;
        return new $class();
    }

    /**
     * 获取驱动实例
     * @param null $name 不为空时获取插件配置
     * @return Driver
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (is_null($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to resolve NULL driver for [%s].',
                static::class
            ));
        }
        return $this->drivers[$name] = $this->drivers[$name] ?? $this->createDriver($name);
    }

    /**
     * 获取插件配置驱动
     * @return mixed
     */
    public function getDefaultDriver()
    {
        $config = get_addons_config('addon', 'user');
        return $config['driver'];
    }

    /**
     * 获取token信息
     * @param $token
     * @return mixed
     */
    public function get($token)
    {
        return $this->driver()->get($token);
    }

    /**
     * 设置toKen
     * @param $token
     * @param $userId
     * @param int $expire
     * @return mixed
     */
    public function set($token, $userId, $expire = 0)
    {
        return $this->driver()->set($token, $userId, $expire);
    }

    /**
     * 删除token
     * @param $token
     * @return mixed
     */
    public function delete($token)
    {
        return $this->driver()->delete($token);
    }

    /**
     * 判断token可用
     * @param $token
     * @param $userId
     * @return mixed
     */
    public function has($token, $userId)
    {
        return $this->driver()->has($token, $userId);
    }

    /**
     * 删除指定用户token
     * @param $userId
     * @return mixed
     */
    public function clear($userId)
    {
        return $this->driver()->clear($userId);
    }
}