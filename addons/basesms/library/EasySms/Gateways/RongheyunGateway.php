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
 * @see https://doc.zthysms.com/web/#/1?page_id=13
 */
class RongheyunGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'https://api.mix2.zthysms.com/v2/sendSmsTp';

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
        $tKey = time();
        $password = md5(md5($config->get('password')) . $tKey);
        $params = [
            'username' => $config->get('username', ''),
            'password' => $password,
            'tKey' => $tKey,
            'signature' => $config->get('signature', ''),
            'tpId' => $message->getTemplate($this),
            'ext' => '',
            'extend' => '',
            'records' => [
                'mobile' => $to->getNumber(),
                'tpContent' => $message->getData($this),
            ],
        ];

        $result = $this->postJson(
            self::ENDPOINT_URL,
            $params,
            ['Content-Type' => 'application/json; charset="UTF-8"']
        );
        if (200 != $result['code']) {
            throw new GatewayErrorException($result['msg'], $result['code'], $result);
        }

        return $result;
    }
}
