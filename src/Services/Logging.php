<?php

namespace CVLB\Svc\Api\Services;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;

class Logging
{
    private Sdk $sdk;

    private string $base_uri = 'https://svc.logging.dev.prm-lfmd.com/api/v1/log';

    public function __construct(Sdk $sdk)
    {
        $this->sdk = $sdk;
    }

    public function log(string $message, int $level = 200): array
    {
        return ResponseMediator::getContent($this->sdk->getHttpClient()->post($this->base_uri . '/queue-msg', [], json_encode($this->setData($message, $level))));
    }

    private function setData(string $message, int $level): array
    {
        return [
            'message' => $message,
            'level' => $level,
            'context' => $this->getContext()
        ];
    }

    /**
     * Get meta data for logs
     * @return array
     */
    private function getContext(): array
    {
        return [
            "client_id" => $this->sdk->getAUth()->getClientId(),
            "app_name" => $_ENV['APP_NAME'] ?? 'UnknownApp',
            "app_env" => $_ENV['APP_ENV'] ??  'UnknownEnv',
            "source_ip" => $this->findInstanceIp(),
            "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
    }

    /**
     * Get an ip for this instance
     * @return string|null
     */
    private function findInstanceIp(): ?string
    {
        return $_SERVER['SERVER_ADDR'] ?? $this->getIpFromShell();
    }

    /**
     * Get ip from hostname
     * @return string|null
     */
    private function getIpFromShell(): ?string
    {
        $ips = explode(' ', shell_exec('hostname -I'));
        return $ips[0] ?? null;
    }
}