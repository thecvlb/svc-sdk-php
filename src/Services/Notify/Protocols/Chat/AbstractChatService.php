<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Chat;

use CVLB\Svc\Api\Services\Notify\Protocols\AbstractProtocol;

abstract class AbstractChatService extends AbstractProtocol
{
    /**
     * Name of protocol
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $protocol = 'chat';

    /**
     * Default version
     * @var string
     */
    protected string $version = '1';

    /**
     * @param Chat $chat
     */
    protected function __construct(Chat $chat)
    {
        $this->notify = $chat->notify;
        $this->setUri();
        $this->setHeader('svc-notify-requesting-service', $this->notify->sdk->app_name);
    }

    /**
     * @param Chat $chat
     * @return object
     */
    abstract static function getInstance(Chat $chat): object;

    /**
     * Send message
     * Message and destination structure are specific to service
     * @param array $message
     * @param array $destination
     * @return array
     */
    abstract public function send(array $message, array $destination): array;
}
