<?php

/*
 * This file is part of the overtrue/easy-sms.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace addons\basesms\library\EasySms\Gateways;

use addons\basesms\library\EasySms\Contracts\MessageInterface;
use addons\basesms\library\EasySms\Contracts\PhoneNumberInterface;
use addons\basesms\library\EasySms\Exceptions\GatewayErrorException;
use addons\basesms\library\EasySms\Support\Config;
use addons\basesms\library\EasySms\Traits\HasHttpRequest;

/**
 * Class RongheyunGateway.
 *
 * @see https://zzyun.com/
 */
class ZzyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://zzyun.com/api/sms/sendByTplCode';

    /**
     * @param \addons\basesms\library\EasySms\Contracts\PhoneNumberInterface $to
     * @param \addons\basesms\library\EasySms\Contracts\MessageInterface     $message
     * @param \addons\basesms\library\EasySms\Support\Config                 $config
     *
     * @return array
     *
     * @throws \addons\basesms\library\EasySms\Exceptions\GatewayErrorException ;
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, Config $config)
    {
        $time = time();
        $user_id = $config->get('user_id');
        $token = md5($time . $user_id . $config->get('secret'));
        $params = [
            'user_id' => $user_id,
            'time' => $time,
            'token' => $token,
            'mobiles' => $to->getNumber(),// 手机号码，多个英文逗号隔开
            'tpl_code' => $message->getTemplate($this),
            'tpl_params' => $message->getData($this),
            'sign_name' => $config->get('sign_name'),
        ];

        $result = $this->post(self::ENDPOINT_URL, $params);

        if ('Success' != $result['Code']) {
            throw new GatewayErrorException($result['Message'], $result['Code'], $result);
        }

        return $result;
    }
}
