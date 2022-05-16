<?php

namespace CVLB\Svc\Api\Services;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;

class Logging
{
    /**
     * @var Sdk
     */
    private $sdk;

    /**
     * @var string
     */
    private static $base_uri;

    /**
     * @var string
     */
    private $api_uri;

    /**
     * @var array
     */
    private static $logging_endpoints = [
        'local' =>          'https://svc.logging.dev.prm-lfmd.com',
        'development' =>    'https://svc.logging.dev.prm-lfmd.com',
        'staging' =>        'https://svc.logging.stage.prm-lfmd.com',
        'production' =>     'https://svc.logging.prm-lfmd.com',
    ];

    /**
     * @var string
     */
    public static $version = '1';

    /**
     * @param Sdk $sdk
     */
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        self::$base_uri = self::$logging_endpoints[$_ENV['APP_ENV']] ?? self::$logging_endpoints['development'];
        $this->api_uri = self::$base_uri . '/api/v' . self::$version;
    }

    /**
     * @param string $message
     * @param array $context
     * @param int $level
     * @return array
     */
    public function put(string $message, array $context = [], int $level = 200): array
    {
        try {
            return ResponseMediator::getContent($this->sdk->getHttpClient()->post($this->api_uri . '/log/queue-msg', [], json_encode($this->setData($message, $context, $level))));
        }
        catch (\Exception $e) {
            // Log request failed, log this error in server logs
            error_log('Failed to log to service. ' . $e->getMessage());

            // Return some context for the response
            return ['success' => false, 'code' => 500, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param string $message
     * @param array $context
     * @param int $level
     * @return array
     */
    private function setData(string $message, array $context, int $level): array
    {
        return [
            'message' => $message,
            'level' => $level,
            'context' => $this->getContext($context)
        ];
    }

    /**
     * Get merge meta data for logs
     * @param array $context
     * @return array
     */
    private function getContext(array $context): array
    {
        return array_merge(
            $context,
            [
                "client_id" => $this->sdk->getAUth()->getClientId(),
                "app_name" => $_ENV['APP_NAME'] ?? 'UnknownApp',
                "app_env" => $_ENV['APP_ENV'] ?? 'UnknownEnv',
                "source_ip" => $this->findInstanceIp(),
                "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        );
    }

    /**
     * Get an ip for this instance
     * @return string|null
     */
    private function findInstanceIp(): ?string
    {
        return $_SERVER['SERVER_ADDR'] ?? $this->getHostname();
    }

    /**
     * Get ip from hostname
     * @return string|null
     */
    private function getHostname(): ?string
    {
        $ips = explode(' ', shell_exec('hostname'));
        return str_replace(' ', '_', $ips[0]) ?? null;
    }
}