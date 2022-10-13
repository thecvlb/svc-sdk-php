<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Sms;

use CVLB\Svc\Api\Services\Notify\Notify;

class Sms
{
    /**
     * @var Notify
     */
    public Notify $notify;

    /**
     * @param Notify $notify
     */
    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    /**
     * @return TwilioService
     */
    public function twilio(): TwilioService
    {
        return TwilioService::getInstance($this);
    }
}