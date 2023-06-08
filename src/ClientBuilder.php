<?php

namespace CVLB\Svc\Api;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClientFactory;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ClientBuilder
{
    /**
     * @var ClientInterface|\Http\Client\HttpClient
     */
    private ClientInterface $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private RequestFactoryInterface $requestFactoryInterface;

    /**
     * @var StreamFactoryInterface|null
     */
    private ?StreamFactoryInterface $streamFactoryInterface;

    /**
     * @var array<mixed>
     */
    private array $plugins = [];

    public function __construct() {
        $this->httpClient = HttpClientDiscovery::find();
        $this->requestFactoryInterface = Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactoryInterface = Psr17FactoryDiscovery::findStreamFactory();
    }

    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    public function getHttpClient(): HttpMethodsClientInterface
    {
        $pluginClient = (new PluginClientFactory())->createClient($this->httpClient, $this->plugins);

        return new HttpMethodsClient(
            $pluginClient,
            $this->requestFactoryInterface,
            $this->streamFactoryInterface
        );
    }
}