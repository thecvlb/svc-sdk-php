<?php

namespace CVLB\Svc\Api;

use CVLB\Svc\Api\Services\Logging;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin\BaseUriPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Message\UriFactory;

final class Sdk
{
    private ClientBuilder $clientBuilder;
    private AuthService $auth;

    public function __construct(AuthService $authService, ClientBuilder $clientBuilder = null, UriFactory $uriFactory = null)
    {
        $this->auth = $authService;
        $this->clientBuilder = $clientBuilder ?: new ClientBuilder();

        /* You could set a uri for all endpoints here.
         * However, since each service may have a different endpoint
         * the uri for each is set in the service's class
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

    public function getAUth(): AuthService
    {
        return $this->auth;
    }

    public function getHttpClient(): HttpMethodsClientInterface
    {
        return $this->clientBuilder->getHttpClient();
    }

    public function logging(): Logging
    {
        return new Logging($this);
    }
}