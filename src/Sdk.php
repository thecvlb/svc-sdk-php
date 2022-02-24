<?php

namespace CVLB\Svc\Api;

use CVLB\Svc\Api\Services\Logging;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\UriFactory;

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
     * @param AuthService $authService
     * @param ClientBuilder|null $clientBuilder
     * @param UriFactory|null $uriFactory
     * @throws \Exception
     */
    public function __construct(AuthService $authService, ClientBuilder $clientBuilder = null, UriFactory $uriFactory = null)
    {
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
}