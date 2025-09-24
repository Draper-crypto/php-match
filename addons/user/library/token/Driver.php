<?php
declare (strict_types = 1);

namespace addons\user\library\token;

abstract class Driver
{
    /**
     * 驱动句柄
     * @var object
     */
    protected $handler = null;

    /**
     * 获取token
     * @param string $token
     * @return mixed
     */
    public abstract function get($token);

    /**
     * 设置token
     * @param string $token
     * @param int $userId 用户ID
     * @param int $expire 到期时间，0-永久
     * @return mixed
     */
    public abstract function set($token, $userId, $expire=0);

    /**
     * 删除token
     * @param $token
     * @return mixed
     */
    public abstract function delete($token);

    /**
     * 判断token可用
     * @param $token
     * @param $userId
     * @return mixed
     */
    public abstract function has($token, $userId);

    /**
     * 删除指定用户token
     * @param $userId
     * @return mixed
     */
    public abstract function clear($userId);

    /**
     * 获取加密token
     * @param string $token
     * @return string
     */
    public function getEncryptedToken($token)
    {
        return hash_hmac('haval128,3', $token, 'eQQ5RxJSYWdjr/MFU5Jndw==');
    }

    /**
     * 获取配置信息
     * @return array|mixed|null
     */
    public function getConfig()
    {
        return get_addons_config('addon','user');
    }
}