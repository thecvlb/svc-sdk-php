<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Email;

use CVLB\Svc\Api\Services\Notify\Notify;

class Email
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
     * @return SesService
     */
    public function ses(): SesService
    {
        return SesService::getInstance($this);
    }

    /**
     * @return SendPulseService
     */
    public function sendpulse(): SendPulseService
    {
        return SendPulseService::getInstance($this);
    }
}