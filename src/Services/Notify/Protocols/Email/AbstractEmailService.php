<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Email;

use CVLB\Svc\Api\Services\Notify\Protocols\AbstractProtocol;

abstract class AbstractEmailService extends AbstractProtocol
{
    /**
     * Name of protocol
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $protocol = 'email';

    /**
     * Default version
     * @var string
     */
    protected string $version = '1';

    /**
     * @param Email $email
     */
    protected function __construct(Email $email)
    {
        $this->notify = $email->notify;
        $this->setUri();
        $this->setHeader('svc-notify-requesting-service', $this->notify->sdk->app_name);
    }

    /**
     * @param Email $email
     * @return object
     */
    abstract static function getInstance(Email $email): object;

    /**
     * @param array $message
     * @param array $destination
     * @return array
     */
    abstract public function send(array $message, array $destination): array;
}
