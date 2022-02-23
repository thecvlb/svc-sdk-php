<?php

namespace CVLB\Svc\Api\Services;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class LoggingTest extends TestCase
{

    public function testPut()
    {
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
        $mockSdk->shouldReceive('getAUth')
            ->andReturn($mockAuthService);

        unset($_SERVER['SERVER_ADDR']);
        $logging = new Logging($mockSdk);
        $response = $logging->put('string', 123);

        $this->assertIsArray($response);
    }
}
