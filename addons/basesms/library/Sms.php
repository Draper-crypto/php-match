<?php
// +----------------------------------------------------------------------
// | HkCms
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 http://www.hkcms.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 广州恒企教育科技有限公司 <admin@hkcms.cn>
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace addons\basesms\library;

use addons\basesms\library\EasySms\EasySms;
use addons\basesms\library\EasySms\Strategies\OrderStrategy;
use think\facade\Db;

class Sms
{
    protected static $instance;

    /**
     * @var EasySms
     */
    protected $sms;

    /**
     * 配置信息
     * @var array
     */
    protected $options = [];

    /**
     * 最大验证次数
     * @var int
     */
    public $maxCheck = 10;

    /**
     * 最大过期时间
     * @var int
     */
    public $maxExpire = 1800;

    /**
     * 插件配置
     * @var array
     */
    protected $config = [];

    protected $error = '';

    /**
     * 单例模式
     * @param array $options
     * @return Sms
     */
    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    /**
     * Email constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        $path = runtime_path('sms');
        if (!is_dir($path)) {
            @mkdir($path);
        }
        $path = $path . date('Ymd') . '.log';

        $this->config = $config = get_addons_config('addon', 'basesms');
        $this->maxExpire = $this->config['max_expire'];

        $option = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    $config['driver'],
                ],
            ],

            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => $path,
                ],
                'aliyun' => [
                    'access_key_id' => $config['app_id'],
                    'access_key_secret' => $config['app_key'],
                    'sign_name' => $config['sign_name'],
                ],
                'aliyunrest' => [
                    'app_key' => $config['app_id'],
                    'app_secret_key' => $config['app_key'],
                    'sign_name' => $config['sign_name'],
                ],
                'qcloud' => [
                    'sdk_app_id' => $config['app_id'],
                    'app_key' => $config['app_key'],
                    'sign_name' => $config['sign_name'],
                ],
                'huyi' => [
                    'api_id' => $config['app_id'],
                    'api_key' => $config['app_key'],
                    'signature' => $config['sign_name'],
                ],
                'yunpian' => [
                    'api_key' => $config['app_key'],
                    'signature' => $config['sign_name'], // 内容中无签名时使用
                ],
                'submail' => [
                    'api_id' => $config['app_id'],
                    'api_key' => $config['app_key'],
                    'project' => $config['project'], // 默认 project，可在发送时 data 中指定
                ],
                'luosimao' => [
                    'api_key' => $config['app_key'],
                ],
                'juhe' => [
                    'app_key' => $config['app_key'],
                ]
            ]
        ];

        $this->options = array_merge($option, $options);
        $this->sms = new EasySms($this->options);
    }

    /**
     * 设置配置信息
     * @param $options
     * @return $this
     */
    public function setConfig($options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * 发送验证码
     * @param int $mobile
     * @param null $code
     * @param string $event
     * @return bool true-成功，false-失败
     */
    public function send($mobile, $code = null, $event = 'default')
    {
        $code = is_null($code) ? mt_rand(1000, 9999) : $code;

        Db::startTrans();
        try {
            Db::name('sms')->insert([
                'event' => $event,
                'mobile' => $mobile,
                'code' => $code,
                'ip' => request()->ip(),
                'create_time' => time(),
            ]);

            $this->sms->send($mobile, [
                'content' => $this->config['template_id']??"",
                'template' => $this->getTemplateId($event),
                'data' => [
                    'code' => $code
                ],
            ]);
            Db::commit();
        } catch (\Exception $exception) {
            Db::rollback();
            $this->error = $exception->getMessage();
            trace($exception->getMessage(), 'error');
            return false;
        }
        return true;
    }

    /**
     * 验证验证码
     * @param $mobile
     * @param $event
     * @param $code
     * @return bool
     */
    public function check($mobile, $event, $code)
    {
        $info = Db::name('sms')->where(['mobile'=>$mobile,'event'=>$event])->order('id','desc')->find();
        if (!$info) {
            return false;
        }

        $createTime = $info['create_time'];

        // 判断是否已经过期
        if ($createTime > (time()-$this->maxExpire) && $info['count'] <= $this->maxCheck) {
            if ($info['code']==$code) {
                return true;
            } else {
                $info['count'] = $info['count']+1;
                Db::name('sms')->where(['mobile'=>$mobile,'event'=>$event,'code'=>$info['code']])->save();
                return false;
            }
        } else {
            Db::name('sms')->where(['mobile'=>$mobile,'event'=>$event,'code'=>$info['code']])->delete();
            return false;
        }
    }

    /**
     * 获取模板ID
     * @param $event
     * @return mixed|string
     */
    public function getTemplateId($event)
    {
        $id = $this->config['template_id'];
        $id = explode("\r\n", $id);
        if (empty($id)) {
            return '';
        }

        $arr = [];
        foreach ($id as $key=>$value) {
            if (empty($value)) {
                continue;
            }
            $value = explode('|', $value);
            $arr[$value[0]] = $value[1];
        }
        return isset($arr[$event]) ? $arr[$event] : ($arr['default'] ?? '');
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}