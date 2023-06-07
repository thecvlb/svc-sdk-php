<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Sms;

use CVLB\Svc\Api\Services\Notify\Protocols\AbstractProtocol;

abstract class AbstractSmsService extends AbstractProtocol
{
    /**
     * Name of protocol
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $protocol = 'sms';

    /**
     * @param Sms $sms
     */
    protected function __construct(Sms $sms)
    {
        $this->notify = $sms->notify;
        $this->setUri();
        $this->setHeader('svc-notify-requesting-service', $this->notify->sdk->app_name);
    }

    /**
     * @param Sms $sms
     * @return object
     */
    abstract static function getInstance(Sms $sms): object;

    /**
     * Send message
     * Message and destination structure are specific to service
     * @param array{text: string} $message
     * @param array{to: string, from: string} $destination
     * @return array{success: bool, code: int, message: string}
     */
    abstract public function send(array $message, array $destination): array;
}
