<?php

namespace CVLB\Svc\Api;

use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use Mockery;
use PHPUnit\Framework\TestCase;

class ClientBuilderTest extends TestCase
{

    public function testAddPlugin()
    {
        $mockPlugin = Mockery::mock(Plugin::class);
        $clientBuilder = new ClientBuilder();

        $this->assertNull($clientBuilder->addPlugin($mockPlugin));
    }

    public function testGetHttpClient()
    {
        $clientBuilder = new ClientBuilder();

        $this->assertInstanceOf(HttpMethodsClientInterface::class, $clientBuilder->getHttpClient());
    }
}
