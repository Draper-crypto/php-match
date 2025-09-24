<?php
declare (strict_types = 1);

namespace addons\user\library\token\driver;

use addons\user\library\token\Driver;
use think\facade\Db;

class MySql extends Driver
{
    protected $option = [
        'table'=>'user_token', // 数据表
        'expire'=>2592000 // 默认30天
    ];

    /**
     * @var Db
     */
    protected $model = null;

    public function __construct(array $option = [])
    {
        $config = $this->getConfig();
        $this->option = array_merge($this->option, $option, is_array($config)?$config:[]);
        $this->model = Db::name($this->option['table']);

        $time = time();
        $token_time = cache('token_time');
        if (!$token_time || $time-$token_time>86400) {  // 每天清理
            cache('token_time', $time);
            $this->model->where('expire_time','<', $time)->where('expire_time', '>', '0')->delete();
        }
    }

    /**
     * 获取token
     * @param string $token
     * @return array|mixed|\think\Model
     */
    public function get($token)
    {
        $info = $this->model->where(['token'=>$this->getEncryptedToken($token)])->find();
        if (!empty($info)) {
            if (!$info['expire_time'] || $info['expire_time']>time()) {
                return $info;
            } else {
                $this->delete($token);
                return [];
            }
        } else {
            return [];
        }
    }

    /**
     * 设置TOKEN
     * @param string $token token字符
     * @param int $userId 用户ID
     * @param int $expire 过期时间，单位秒，为空，0时，使用默认的过期时间
     * @return mixed|void
     */
    public function set($token, $userId, $expire = 0)
    {
        $expireTime = empty($expire) ? time()+$this->option['expire'] : time()+$expire;
        $token = $this->getEncryptedToken($token);
        $this->model->insert(['token'=>$token, 'user_id'=>$userId, 'create_time'=>time(), 'expire_time'=>$expireTime]);
    }

    /**
     * 删除token
     * @param $token
     * @return bool|mixed
     * @throws \think\db\exception\DbException
     */
    public function delete($token)
    {
        $this->model->where(['token'=>$this->getEncryptedToken($token)])->delete();
        return true;
    }

    /**
     * 验证token
     * @param $token
     * @param $userId
     * @return bool
     */
    public function has($token, $userId)
    {
        $info = $this->get($token);
        return $info && $info['user_id'] == $userId;
    }

    /**
     * 删除指定用户token
     * @param $userId
     * @return bool|mixed
     */
    public function clear($userId)
    {
        $this->model->where(['user_id'=>$userId])->delete();
        return true;
    }
}