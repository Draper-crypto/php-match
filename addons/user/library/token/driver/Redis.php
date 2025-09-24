<?php

namespace addons\user\library\token\driver;

use addons\user\library\token\Driver;
use DateTimeInterface;
use DateInterval;

class Redis extends Driver
{
    /** @var \Predis\Client|\Redis */
    protected $handler;

    /**
     * redis配置信息
     */
    protected $options = [
        'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
        'select'     => 0,
        'timeout'    => 0,
        'expire'     => 2592000,
        'persistent' => false,
        'token_prefix'  => 'token',
        'user_prefix'   => 'u',
        'serialize'  => [],
    ];

    public function __construct(array $options = [])
    {
        $config = $this->getConfig();
        $this->options = array_merge($this->options, $options, is_array($config)?$config:[]);

        if (extension_loaded('redis')) {
            $this->handler = new \Redis;

            if ($this->options['persistent']) {
                $this->handler->pconnect($this->options['host'], (int) $this->options['port'], (int) $this->options['timeout'], 'persistent_id_' . $this->options['select']);
            } else {
                $this->handler->connect($this->options['host'], (int) $this->options['port'], (int) $this->options['timeout']);
            }

            if ('' != $this->options['password']) {
                $this->handler->auth($this->options['password']);
            }
        } else {
            throw new \BadFunctionCallException('not support: redis');
        }

        if (0 != $this->options['select']) {
            $this->handler->select((int) $this->options['select']);
        }
    }

    /**
     * 获取实际的token缓存标识
     * @access public
     * @param string $name 缓存名
     * @return string
     */
    public function getCacheKey(string $name): string
    {
        return $this->options['token_prefix'] . $this->getEncryptedToken($name);
    }

    /**
     * 获取有效期
     * @access protected
     * @param integer|DateTimeInterface|DateInterval $expire 有效期
     * @return int
     */
    protected function getExpireTime($expire): int
    {
        if ($expire instanceof DateTimeInterface) {
            $expire = $expire->getTimestamp() - time();
        } elseif ($expire instanceof DateInterval) {
            $expire = \DateTime::createFromFormat('U', (string) time())
                    ->add($expire)
                    ->format('U') - time();
        }

        return (int) $expire;
    }

    /**
     * 获取toKen
     * @param string $token
     * @return array|mixed
     */
    public function get($token)
    {
        $key   = $this->getCacheKey($token);
        $value = $this->handler->get($key);

        if (false === $value || is_null($value)) {
            return [];
        }

        $expire = $this->handler->ttl($key);
        return ['token'=>$key, 'user_id'=>$value, 'expire_time'=>$expire+time()];
    }

    /**
     * 设置TOKEN
     * @param string $token
     * @param int $userId
     * @param int $expire
     * @return bool|mixed
     */
    public function set($token, $userId, $expire = 0)
    {
        if (empty($expire)) {
            $expire = $this->options['expire'];
        }

        $key    = $this->getCacheKey($token);
        $expire = $this->getExpireTime($expire);

        if ($expire) {
            $this->handler->setex($key, $expire, $userId);
        } else {
            $this->handler->set($key, $userId);
        }
        $this->handler->sAdd('user_prefix'.$userId, $key);
        return true;
    }

    /**
     * 删除token
     * @param $token
     * @return bool|mixed
     */
    public function delete($token)
    {
        $key    = $this->getCacheKey($token);
        $result = $this->handler->del($key);
        return $result > 0;
    }

    /**
     * 判断token
     * @param $token
     * @param $userId
     * @return bool|mixed
     */
    public function has($token, $userId)
    {
        $info = $this->get($token);
        return $info && $info['user_id'] == $userId;
    }

    /**
     * 删除指定用户的所有TOKEN
     * @param $userId
     * @return bool|mixed
     */
    public function clear($userId)
    {
        $keys = $this->handler->sMembers('user_prefix'.$userId);
        $this->handler->del('user_prefix'.$userId);
        $this->handler->del($keys);
        return true;
    }
}