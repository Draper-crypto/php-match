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
 * Class JuheGateway.
 *
 * @see https://www.juhe.cn/docs/api/id/54
 */
class JuheGateway extends Gateway
{
    use HasHttpRequest;

    const ENDPOINT_URL = 'http://v.juhe.cn/sms/send';

    const ENDPOINT_FORMAT = 'json';

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
        $params = [
            'mobile' => $to->getNumber(),
            'tpl_id' => $message->getTemplate($this),
            'tpl_value' => $this->formatTemplateVars($message->getData($this)),
            'dtype' => self::ENDPOINT_FORMAT,
            'key' => $config->get('app_key'),
        ];

        $result = $this->get(self::ENDPOINT_URL, $params);

        if ($result['error_code']) {
            throw new GatewayErrorException($result['reason'], $result['error_code'], $result);
        }

        return $result;
    }

    /**
     * @param array $vars
     *
     * @return string
     */
    protected function formatTemplateVars(array $vars)
    {
        $formatted = [];

        foreach ($vars as $key => $value) {
            $formatted[sprintf('#%s#', trim($key, '#'))] = $value;
        }

        return http_build_query($formatted);
    }
}
