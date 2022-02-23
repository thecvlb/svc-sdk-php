<?php

namespace CVLB\Svc\Api\Auth;

class AuthToken
{
    /**
     * @var string
     */
    private string $token;

    /**
     * @var int
     */
    private int $expires_in;

    /**
     * @param array $token
     */
    public function __construct(array $token)
    {
        $this->setToken($token['access_token']);
        $this->setExpire($token['expires_in']);
    }

    /**
     * @param string $string
     * @return void
     */
    private function setToken(string $string)
    {
        $this->token = $string;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param int $int
     * @return void
     */
    private function setExpire(int $int)
    {
        $this->expires_in = $int;
    }

    /**
     * @return int
     */
    public function getExpire(): int
    {
        return $this->expires_in;
    }
}