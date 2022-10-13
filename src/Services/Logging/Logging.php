<?php

namespace CVLB\Svc\Api\Services\Logging;

use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;

class Logging
{
    /**
     * @var Sdk
     */
    private Sdk $sdk;

    /**
     * @var string
     */
    private static string $base_uri;

    /**
     * @var string
     */
    private string $api_uri;

    /**
     * @var array
     */
    private static array $logging_endpoints = [
        'local' =>          'https://svc.logging.dev.prm-lfmd.com',
        'development' =>    'https://svc.logging.dev.prm-lfmd.com',
        'stage' =>        'https://svc.logging.stage.prm-lfmd.com',
        'production' =>     'https://svc.logging.prm-lfmd.com',
    ];

    /**
     * @var string
     */
    public static string $version = '1';

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
     * @throws \Http\Client\Exception
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
            'context' => $this->getContext($context),
            'dd' => $this->getDataDogContext()
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
                "client_id" => $this->sdk->getAuth()->getClientId(),
                "app_name" => $_ENV['APP_NAME'] ?? 'UnknownApp',
                "app_env" => $_ENV['APP_ENV'] ?? 'UnknownEnv',
                "source_ip" => $this->findInstanceIp(),
                "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        );
    }

    /**
     * Get trace and span ids from DataDog context
     * Requires the DataDog agent to be installed on the server
     * Will be empty values otherwise
     *
     * If DataDog is no longer used, this method
     * and any calls to it should be removed
     *
     * @return array|string[][]
     */
    private function getDataDogContext(): array
    {
        /*
         * This function is required
         */
        $func = '\DDTrace\current_context';

        // Top level element to be added to log record array
        $dataDog = ['trace_id' => '', 'span_id'  => ''];

        // If DD agent, call current_context() to get the trace and span
        if (is_callable($func)) {
            $dd_context = call_user_func($func);
            $dataDog['trace_id'] = $dd_context['trace_id'] ?? '';
            $dataDog['span_id'] = $dd_context['span_id'] ?? '';
        }

        return $dataDog;
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