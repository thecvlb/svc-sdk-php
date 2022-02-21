<?php

namespace Svc\Auth\Contracts;

interface AuthService
{
    /**
     * @return string
     */
    function getClientId(): string;
    
    /**
     * @return string
     */
    function getAccessToken(): string;
}