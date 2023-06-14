<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Email;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;

final class SesService extends AbstractEmailService
{
    /**
     * Name of service
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $service = 'ses';

    /**
     * @var SesService
     */
    protected static SesService $obj;

    /**
     * Get instance of service
     * @param Email $email
     * @return SesService
     */
    public static function getInstance(Email $email): SesService
    {
        if(!isset(self::$obj)) {
            self::$obj = new self($email);
        }
        return self::$obj;
    }

    /**
     * @param array{email_subject: string, email_html_message: string, email_text_message: string} $message
     * @param array{from_name: string, from_address: string, to_address: string, cc?: array<array{to: string, address: string}>, bcc?: array<array{to: string, address: string}>} $destination
     * @return array<mixed>
     * @throws \Http\Client\Exception
     */
    public function send(array $message, array $destination): array
    {
        try {
            $headers = $this->getHeaders();
            $body = json_encode($this->setBody($message, $destination));

            return ResponseMediator::getContent(
                $this->notify->sdk->getHttpClient()->post(
                    $this->api_uri,
                    $headers,
                    $body?:''
                )
            );
        }
        catch (\Exception $e) {
            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param array{email_subject: string, email_html_message: string, email_text_message: string} $message
     * @param array{from_name: string, from_address: string, to_name?: string, to_address: string, cc?: array<array{to: string, address: string}>, bcc?: array<array{to: string, address: string}>} $destination
     * @return array{from_name: string, from_address: string, to_name: string, to_address: string, cc: array<mixed>, bcc: array<mixed>, email_subject: string, email_base64_html_message: string, email_text_message: string, verbose: bool}
     */
    public function setBody(array $message, array $destination): array
    {
        return [
            'from_name' => $destination['from_name'],
            'from_address' => $destination['from_address'],
            'to_name' => $destination['to_name'] ?? '',
            'to_address' => $destination['to_address'],
            'cc' => $destination['cc'] ?? [],
            'bcc' => $destination['bcc'] ?? [],
            'email_subject' => $message['email_subject'],
            'email_base64_html_message' => base64_encode($message['email_html_message']),
            'email_text_message' => $message['email_text_message'],
            'verbose' => $this->getVerbose()
        ];
    }
}