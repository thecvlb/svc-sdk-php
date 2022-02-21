<?php

namespace CVLB\Svc\Logging;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use CVLB\Svc\Logging\Providers\LoggingServiceProvider;
use CVLB\Svc\SvcClient;

/**
 * @method LoggingServiceProvider log (string $message, int $level = 200)
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