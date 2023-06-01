<?php

namespace CVLB\Svc\Api;

use CVLB\Svc\Api\Services\DataSegmentation\DataSegmentation;
use CVLB\Svc\Api\Services\Logging\Logging;
use CVLB\Svc\Api\Services\Notify\Notify;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Psr\Http\Message\UriFactoryInterface;

class Sdk
{
    /**
     * @var ClientBuilder
     */
    private ClientBuilder $clientBuilder;

    /**
     * @var AuthService
     */
    private AuthService $auth;

    /**
     * @var string
     */
    public string $app_name;

    /**
     * @param AuthService $authService
     * @param ClientBuilder|null $clientBuilder
     * @param UriFactoryInterface|null $uriFactory
     * @throws \Exception
     */
    public function __construct(AuthService $authService, ClientBuilder $clientBuilder = null, UriFactoryInterface $uriFactory = null)
    {
        $this->app_name = $_ENV['APP_NAME'] ?? "Unknown App";
        $this->auth = $authService;
        $this->clientBuilder = $clientBuilder ?: new ClientBuilder();

        /* You could set a base uri for all endpoints here.
         * However, since each service may have a different endpoint
         * a base uri is set in each of the service's class
         *
         */
        $uriFactory = $uriFactory ?: Psr17FactoryDiscovery::findUriFactory();

        /*
         * $this->clientBuilder->addPlugin(
         *   new BaseUriPlugin($uriFactory->createUri('https://jsonplaceholder.typicode.com'))
         * );
         */

        $this->clientBuilder->addPlugin(
            new HeaderDefaultsPlugin(
                [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->getAUth()->getAccessToken()
                ]
            )
        );
    }

    /**
     * @return AuthService
     */
    public function getAUth(): AuthService
    {
        return $this->auth;
    }

    /**
     * @return HttpMethodsClientInterface
     */
    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->clientBuilder->getHttpClient();
    }

    /**
     * @return Logging
     */
    public function logging(): Logging
    {
        return new Logging($this);
    }

    /**
     * @return Notify
     */
    public function notify(): Notify
    {
        return new Notify($this);
    }

    /**
     * @return DataSegmentation
     */
    public function dse(): DataSegmentation
    {
        return new DataSegmentation($this);
    }
}