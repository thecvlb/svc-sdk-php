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
     * @param array<mixed> $message
     * @param array{slack_channel: string} $destination
     * @return array{success: bool, code: int, message: string}
     */
    abstract public function send(array $message, array $destination): array;
}
