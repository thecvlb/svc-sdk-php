<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Notify\Notify;
use CVLB\Svc\Api\Services\Notify\Protocols\Chat\Chat;
use CVLB\Svc\Api\Services\Notify\Protocols\Chat\SlackService;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AbstractProtocolTest extends TestCase
{
    public function getService(): SlackService
    {
        $_ENV['APP_ENV'] = 'local';
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockStream->shouldReceive('getContents')->andReturn(json_encode(['OK']));

        $mockResponseInterface = Mockery::mock(ResponseInterface::class);
        $mockResponseInterface->shouldReceive('getBody')
            ->andReturn($mockStream);

        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->shouldReceive('getClientId')
            ->andReturn('sdk_phpunit');

        $mockHttpMethodsClientInterface = Mockery::mock(HttpMethodsClientInterface::class);
        $mockHttpMethodsClientInterface->shouldReceive('post')
            ->andReturn($mockResponseInterface);

        $mockSdk = Mockery::mock(Sdk::class);
        $mockSdk->shouldReceive('getHttpClient')
            ->andReturn($mockHttpMethodsClientInterface);
        $mockSdk->shouldReceive('getAuth')
            ->andReturn($mockAuthService);

        $mockSdk->app_name = 'PHP Unit Test';

        $protocol = new Chat(new Notify($mockSdk));

        return $protocol->slack();
    }

    public function testUnsetHeader()
    {
        $service = $this->getService();
        $key = "test";
        $value = "value";

        $headers = $service->setHeader($key, $value);
        $this->assertArrayHasKey($key, $headers);

        $headers = $service->unsetHeader($key);
        $this->assertArrayNotHasKey($key, $headers);
    }

    public function testSetVerbose()
    {
        $service = $this->getService();
        $verbose = !$service->getVerbose();

        $service->setVerbose($verbose);

        $this->assertEquals($verbose, $service->getVerbose());
    }



    public function testGetVerbose()
    {
        $service = $this->getService();

        $this->assertIsBool($service->getVerbose());
    }

    public function testGetHeaders()
    {
        $service = $this->getService();

        $this->assertIsArray($service->getHeaders());
    }



    public function testGetVersion()
    {
        $service = $this->getService();

        $this->assertIsString($service->getVersion());
    }



    public function testSetUri()
    {
        $service = $this->getService();
        $uri = $service->getUri();

        $service->setUri();

        $this->assertEquals($uri, $service->getUri());
    }

    public function testGetUri()
    {
        $service = $this->getService();

        $this->assertIsString($service->getUri());
    }

    public function testSetVersion()
    {
        $service = $this->getService();
        $version = '123';

        $service->setVersion($version);

        $this->assertEquals($version, $service->getVersion());
    }

    public function testSetHeader()
    {
        $service = $this->getService();
        $key = "test";
        $value = "value";
        $headers = $service->getHeaders();

        $this->assertArrayNotHasKey($key, $headers);

        $headers = $service->setHeader($key, $value);
        $this->assertArrayHasKey($key, $headers);
    }
}
