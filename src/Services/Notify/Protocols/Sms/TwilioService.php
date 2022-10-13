<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Sms;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;

final class TwilioService extends AbstractSmsService
{
    /**
     * Name of service
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $service = 'twilio';

    /**
     * @var TwilioService
     */
    protected static TwilioService $obj;

    /**
     * Get instance of service
     * @param Sms $sms
     * @return TwilioService
     */
    public static function getInstance(Sms $sms): TwilioService
    {
        if(!isset(self::$obj)) {
            self::$obj = new self($sms);
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
            'message' => $message['text'],
            'from' => $destination['from'],
            'to' => $destination['to'],
            'verbose' => $this->getVerbose()
        ];
    }
}