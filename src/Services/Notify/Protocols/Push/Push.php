<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Push;

use CVLB\Svc\Api\Services\Notify\Notify;

class Push
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
}