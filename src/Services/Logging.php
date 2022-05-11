<?php

namespace CVLB\Svc\Api\Services;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;
use Http\Client\Exception;

class Logging
{
    /**
     * @var Sdk
     */
    private $sdk;

    /**
     * @var string
     */
    private $base_uri;

    /**
     * @var array
     */
    private $logging_endpoints = [
        'local' =>          'https://svc.logging.dev.prm-lfmd.com/api/v1/log',
        'development' =>    'https://svc.logging.dev.prm-lfmd.com/api/v1/log',
        'staging' =>        'https://svc.logging.stage.prm-lfmd.com/api/v1/log',
        'production' =>     'https://svc.logging.prm-lfmd.com/api/v1/log',
    ];

    /**
     * @param Sdk $sdk
     */
    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
        $this->base_uri = $this->logging_endpoints[$_ENV['APP_ENV']] ?? $this->logging_endpoints['development'];
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
            return ResponseMediator::getContent($this->sdk->getHttpClient()->post($this->base_uri . '/queue-msg', [], json_encode($this->setData($message, $context, $level))));
        } catch (Exception $e) {
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
                "app_env" => $_ENV['APP_ENV'] ??  'UnknownEnv',
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