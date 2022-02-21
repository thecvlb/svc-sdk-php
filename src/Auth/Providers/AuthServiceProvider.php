<?php

namespace Svc\Auth\Providers;

use Redis;
use Svc\Auth\Infrastructure\Services\CognitoService;

class AuthServiceProvider
{
    /**
     * An auth service implementing /Svc/Auth/Contracts/AuthService.php
     * @var CognitoService 
     */
    private CognitoService $service;

    /**
     * @param Redis $redis
     * @param array $credentials
     */
    public function __construct(Redis $redis, array $credentials)
    {
        $this->service = new CognitoService($redis, $credentials);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->service->getClientId();
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->service->getAccessToken();
    }
}