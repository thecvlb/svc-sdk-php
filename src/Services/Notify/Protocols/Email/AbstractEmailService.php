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
     * @param array{email_subject: string, email_html_message: string, email_text_message: string} $message
     * @param array{from_name: string, from_address: string, to_name?: string, to_address: string, bcc?: array<string>} $destination
     * @return array{success: bool, code: int, message: string}
     */
    abstract public function send(array $message, array $destination): array;
}
