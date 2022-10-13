<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Chat;

use CVLB\Svc\Api\Services\Notify\Notify;

class Chat
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
     * @return SlackService
     */
    public function slack(): SlackService
    {
        return SlackService::getInstance($this);
    }
}