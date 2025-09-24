<?php

/*
 * This file is part of the overtrue/easy-sms.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace addons\basesms\library\EasySms;

use addons\basesms\library\EasySms\Contracts\MessageInterface;
use addons\basesms\library\EasySms\Contracts\PhoneNumberInterface;
use addons\basesms\library\EasySms\Exceptions\NoGatewayAvailableException;

/**
 * Class Messenger.
 */
class Messenger
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    /**
     * @var \addons\basesms\library\EasySms\EasySms
     */
    protected $easySms;

    /**
     * Messenger constructor.
     *
     * @param \addons\basesms\library\EasySms\EasySms $easySms
     */
    public function __construct(EasySms $easySms)
    {
        $this->easySms = $easySms;
    }

    /**
     * Send a message.
     *
     * @param \addons\basesms\library\EasySms\Contracts\PhoneNumberInterface $to
     * @param \addons\basesms\library\EasySms\Contracts\MessageInterface     $message
     * @param array                                            $gateways
     *
     * @return array
     *
     * @throws \addons\basesms\library\EasySms\Exceptions\NoGatewayAvailableException
     */
    public function send(PhoneNumberInterface $to, MessageInterface $message, array $gateways = [])
    {
        $results = [];
        $isSuccessful = false;

        foreach ($gateways as $gateway => $config) {
            try {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_SUCCESS,
                    'result' => $this->easySms->gateway($gateway)->send($to, $message, $config),
                ];
                $isSuccessful = true;

                break;
            } catch (\Exception $e) {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            } catch (\Throwable $e) {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            }
        }

        if (!$isSuccessful) {
            throw new NoGatewayAvailableException($results);
        }

        return $results;
    }
}
