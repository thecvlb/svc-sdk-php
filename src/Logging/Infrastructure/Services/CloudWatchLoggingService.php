<?php

namespace Svc\Logging\Infrastructure\Services;

use Svc\Auth\Providers\AuthServiceProvider;
use Svc\Logging\Contracts\LogService;
use Svc\Logging\Domain\Exceptions\LogException;

/**
 * Uses SVC Logging 
 * @see https://documenter.getpostman.com/view/16680838/UVeGpQY7
 */
class CloudWatchLoggingService implements LogService
{
    /**
     * @var AuthServiceProvider 
     */
    private AuthServiceProvider $auth;

    /**
     * @var array
     */
    private array $api_endpoints = [
        'development' =>    'https://svc.logging.dev.prm-lfmd.com/api/v1/log',
        'staging' =>        'https://svc.logging.staging.prm-lfmd.com/api/v1/log',
        'production' =>     'https://svc.logging.prm-lfmd.com/api/v1/log',
    ];

    /**
     * @var string 
     */
    private string $endpoint;

    /**
     * @param AuthServiceProvider $authServiceProvider
     */
    public function __construct(AuthServiceProvider $authServiceProvider) 
    {
        $this->auth = $authServiceProvider;
        $this->endpoint = $this->api_endpoints[$_ENV['APP_ENV']] ?? $this->api_endpoints['development'];
    }

    /**

     * @param string $message
     * @param int $level
     * @return string
     * @throws LogException
     */
    public function log(string $message, int $level = \Monolog\Logger::INFO): string
    {
        return $this->doCurl($this->endpoint . '/queue-msg', $this->setData($message, $level));
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
            "client_id" => $this->auth->getClientId(), 
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

    /**
     * @param string $url
     * @param array $data
     * @return string
     * @throws LogException
     */
    private function doCurl(string $url, array $data): string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->auth->getAccessToken()
            ),
        ));

        $response = curl_exec($curl);

        // Check for errors
        $error = curl_errno($curl) ? curl_error($curl) : null;
        
        curl_close($curl);
        
        // If error, throw exception
        if ($error)
            throw new LogException('Request Error:' . $error);
        
        // If curl response is false, throw exception
        if ($response === false)
            throw new LogException('Request Error: Unknown');

        return $response;
    }
}