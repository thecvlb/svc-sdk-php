<?php

namespace CVLB\Svc\Api;

use CVLB\Svc\Api\Auth\AuthToken;
use Exception;
use Redis;

class AuthService
{
    /**
     * @var array{local: string, development: string, stage: string, production: string}
     */
    private $auth_endpoints = [
        'local' =>          'https://svc-lifemd-dev.auth.us-west-2.amazoncognito.com',
        'development' =>    'https://svc-lifemd-dev.auth.us-west-2.amazoncognito.com',
        'stage' =>          'https://svc-lifemd-stage.auth.us-west-2.amazoncognito.com',
        'production' =>     'https://svc-lifemd.auth.us-west-2.amazoncognito.com',
    ];

    /**
     * @var string
     */
    private $endpoint;

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
     * @param array{client_id: string, client_secret: string} $credentials
     * @param callable $connect
     * @throws \RedisException
     */
    public function __construct(Redis $redis, array $credentials, callable $connect = null)
    {
        $this->endpoint = $this->auth_endpoints[$_ENV['APP_ENV']] ?? $this->auth_endpoints['development'];
        $this->credentials = $credentials;
        $this->cache = $redis;
        if (!empty($connect)) {
            // allow to specify how to connect to cache
            $connect($this->cache);
        } else {
            // default connection method
            $this->cache->connect($_ENV['REDIS_HOST'] ?? 'localhost');
        }
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
     * @throws Exception
     */
    public function getAccessToken(): string
    {
        if ($access_token = $this->cache->get($this->access_token_key))
            return $access_token;

        $authToken = $this->getJwt($this->endpoint . "/oauth2/token", $this->credentials['client_id'], $this->credentials['client_secret']);

        $this->storeAccessToken($authToken->getToken(), $authToken->getExpire());

        return $authToken->getToken();
    }

    /**
     * @param string $endpoint
     * @param string $client_id
     * @param string $client_secret
     * @return AuthToken
     * @throws Exception
     */
    private function getJwt(string $endpoint, string $client_id, string $client_secret): AuthToken
    {
        $headers = $this->setHeaders($client_id, $client_secret);
        $fields = $this->setBody($client_id);

        $response = json_decode($this->makeCurlRequest($endpoint, $headers, $fields), true);

        /*
         * Error authenticating leave us nowhere to log.
         * So log to PHP's system logger
         */
        if (isset($response['error']))
            error_log($response['error']);

        if (!$response || !isset($response['access_token']))
        {
            error_log("LifeMD SVC: failed to Auth client_id: $client_id at $endpoint");

            /*
             * Setting the response auth token to empty string that expires in 1 sec
             * Sending an invalid access token will trigger log activity upon validation
             * Short expiration will let the service try again the next time it's requested
             */
            $response['access_token'] = '';
            $response['expires_in'] = 1;
        }

        return new AuthToken($response);
    }

    /**
     * @param string $access_token
     * @param int $expires_in
     * @return void
     * @throws \RedisException
     */
    private function storeAccessToken(string $access_token, int $expires_in): void
    {
        // Expire the token in Redis 10 minutes before the token actually expires
        $this->cache->set($this->access_token_key, $access_token, $expires_in-600);
    }

    /**
     * @param string $client_id
     * @return array{client_id: string, grant_type: string}
     */
    private function setBody(string $client_id): array
    {
        return [
            'client_id' => $client_id,
            'grant_type' => 'client_credentials'
        ];
    }

    /**
     * @param string $client_id
     * @param string $client_secret
     * @return array{string, string}
     */
    private function setHeaders(string $client_id, string $client_secret): array
    {
        return [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '. base64_encode($client_id.':'.$client_secret)
        ];
    }

    /**
     * @param string $endpoint
     * @param array{string, string} $headers
     * @param array{client_id: string, grant_type: string} $fields
     * @return string
     */
    public function makeCurlRequest(string $endpoint, array $headers, array $fields): string
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

        return is_bool($response) ? '' : $response;
    }
}