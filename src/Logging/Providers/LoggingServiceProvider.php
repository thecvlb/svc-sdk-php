<?php

namespace Svc\Logging\Providers;

use Svc\Auth\Providers\AuthServiceProvider;
use Svc\Logging\Domain\Exceptions\LogException;
use Svc\Logging\Infrastructure\Services\CloudWatchLoggingService;

class LoggingServiceProvider
{
    /**
     * A log service implementing /Svc/Logging/Contracts/LogService.php
     * @var CloudWatchLoggingService 
     */
    private CloudWatchLoggingService $service;
    
    /**
     * @param AuthServiceProvider $authServiceProvider
     */
    public function __construct(AuthServiceProvider $authServiceProvider)
    {
        $this->service = new CloudWatchLoggingService($authServiceProvider);
    }
    
    /**
     * @param string $message
     * @param int $level
     * @return string
     * @throws LogException
     */
    public function log(string $message, int $level = \Monolog\Logger::INFO): string
    {
        return $this->service->log($message, $level);
    }
}