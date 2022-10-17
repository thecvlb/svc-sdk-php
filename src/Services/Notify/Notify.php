<?php

namespace CVLB\Svc\Api\Services\Notify;

use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Notify\Protocols\Chat\Chat;
use CVLB\Svc\Api\Services\Notify\Protocols\Email\Email;
use CVLB\Svc\Api\Services\Notify\Protocols\Push\Push;
use CVLB\Svc\Api\Services\Notify\Protocols\Sms\Sms;

class Notify
{
    /**
     * @var Sdk
     */
    public Sdk $sdk;

    /**
     * @var string
     */
    public static string $base_uri;

    /**
     * @var array
     */
    private static array $notify_endpoints = [
        'local' =>          'https://x5g8crw84f.execute-api.us-west-2.amazonaws.com',
        'development' =>    'https://x5g8crw84f.execute-api.us-west-2.amazonaws.com',
        'stage' =>          'https://svc.notify.stage.prm-lfmd.com',
        'production' =>     'https://svc.notify.prm-lfmd.com',
    ];

    /**
     * @param Sdk $sdk
     */
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        self::$base_uri = self::$notify_endpoints[$_ENV['APP_ENV']] ?? self::$notify_endpoints['development'];
    }

    /**
     * @return Chat
     */
    public function chat(): Chat
    {
        return new Chat($this);
    }

    /**
     * @return Email
     */
    public function email(): Email
    {
        return new Email($this);
    }

    /**
     * @return Sms
     */
    public function sms(): Sms
    {
        return new Sms($this);
    }

    /**
     * @return Push
     */
    public function push(): Push
    {
        return new Push($this);
    }
}
