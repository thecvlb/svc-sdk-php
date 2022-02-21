<?php

namespace CVLB\Svc\Auth\Infrastructure\Services;

use Redis;
use CVLB\Svc\Auth\Contracts\AuthService as AuthServiceContract;

class CognitoService implements AuthServiceContract
{
    /**
     * @var array
     */
    private array $auth_endpoints = [
        'development' =>    'https://svc-lifemd-dev.auth.us-west-2.amazoncognito.com',
        'staging' =>        'https://svc-lifemd-staging.auth.us-west-2.amazoncognito.com',
        'production' =>     'https://svc-lifemd.auth.us-west-2.amazoncognito.com',
    ];

    /**
     * @var string
     */
    private string $endpoint;

    /**
     * Cognito credentials     
     * @var array{client_id: string, client_secret: string}
     */
    private array $credentials;
    
    
    /**
     * @var Redis 
     */
    private Redis $cache;

    /**
     * @var string
     */
    private string $access_token_key = 'svclifemd-access-token';

    /**
     * @param Redis $redis
     * @param array $credentials
     */
    public function __construct(Redis $redis, array $credentials)
    {
        $this->endpoint = $this->auth_endpoints[$_ENV['APP_ENV']] ?? $this->auth_endpoints['development'];
        $this->credentials = $credentials;
        $this->cache = $redis;
        $this->cache->connect($_ENV['REDIS_HOST']);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->credentials['client_id'];
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        if ($access_token = $this->cache->get($this->access_token_key))
            return $access_token;
        
        $jwt = $this->getJwt($this->endpoint . "/oauth2/token", $this->credentials['client_id'], $this->credentials['client_secret']);
        
        $this->storeAccessToken($jwt['access_token'], $jwt['expires_in']);
        
        return $jwt['access_token'];
    }

    /**
     * @param string $endpoint
     * @param string $client_id
     * @param string $client_secret
     * @return array
     */
    private function getJwt(string $endpoint, string $client_id, string $client_secret): array
    {
        $headers = self::setHeaders($client_id, $client_secret);
        $fields = self::setBody($client_id);

        return json_decode(self::makeCurlRequest($endpoint, $headers, $fields), true);
    }

    /**
     * @param string $access_token
     * @param int $expires_in
     * @return void
     */
    private function storeAccessToken(string $access_token, int $expires_in): void
    {
        $this->cache->set($this->access_token_key, $access_token, $expires_in);
    }
    
    /**
     * @param string $client_id
     * @return array
     */
    private static function setBody(string $client_id): array
    {
        $fields = [
            'client_id' => $client_id,
            'grant_type' => 'client_credentials'
        ];

        return $fields;
    }
    
    /**
     * @param string $client_id
     * @param string $client_secret
     * @return array
     */
    private static function setHeaders(string $client_id, string $client_secret): array
    {
        return [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '. base64_encode($client_id.':'.$client_secret)
        ];
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @param array $fields
     * @return string
     */
    private static function makeCurlRequest(string $endpoint, array $headers, array $fields): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }
}