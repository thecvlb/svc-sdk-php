<?php

namespace Svc\Logging;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Svc\Logging\Providers\LoggingServiceProvider;
use Svc\SvcClient;

/**
 * @method LoggingServiceProvider log (string $message, int $level = \Monolog\Logger::INFO)
 */
class LoggingClient extends SvcClient
{
    /**
     * @param array{client_id: string, client_secret: string} $credentials
     * @throws DependencyException
     * @throws NotFoundException
     * @throws Exception
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials);
        
        $this->load(LoggingServiceProvider::class);
    }
}