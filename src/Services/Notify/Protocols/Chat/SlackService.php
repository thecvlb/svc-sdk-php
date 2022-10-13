<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Chat;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;

final class SlackService extends AbstractChatService
{
    /**
     * Name of service
     * This is used in the API path and much correspond
     * @var string
     */
    protected string $service = 'slack';

    /**
     * @var SlackService
     */
    protected static SlackService $obj;

    /**
     * Get instance of service
     * @param Chat $chat
     * @return SlackService
     */
    public static function getInstance(Chat $chat): SlackService
    {
        if(!isset(self::$obj)) {
            self::$obj = new self($chat);
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
            'message' => $message,
            'slack_channel' => $destination['slack_channel'],
            'verbose' => $this->getVerbose()
        ];
    }
}