<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Email;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;

final class SendPulseService extends AbstractEmailService
{
    /**
     * Name of service
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $service = 'sendpulse';

    /**
     * @var SendPulseService
     */
    protected static SendPulseService $obj;

    /**
     * Get instance of service
     * @param Email $email
     * @return SendPulseService
     */
    public static function getInstance(Email $email): SendPulseService
    {
        if(!isset(self::$obj)) {
            self::$obj = new self($email);
        }
        return self::$obj;
    }

    /**
     * @param array $message
     * @param array $destination
     * @return array
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
                    $body
                )
            );
        }
        catch (\Exception $e) {
            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param array $message
     * @param array $destination
     * @return array
     */
    public function setBody(array $message, array $destination): array
    {
        return [
            'from_name' => $destination['from_name'],
            'from_address' => $destination['from_address'],
            'to_address' => $destination['to_address'],
            'email_subject' => $message['email_subject'],
            'email_base64_html_message' => base64_encode($message['email_html_message']),
            'email_text_message' => $message['email_text_message'],
            'verbose' => $this->getVerbose()
        ];
    }
}