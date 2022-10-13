<?php

namespace CVLB\Svc\Api\Services\Logging;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\HttpClient\Message\ResponseMediator;
use CVLB\Svc\Api\Sdk;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\MockObject\MockMethod;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class LoggingTest extends TestCase
{

    public function testPut()
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

        unset($_SERVER['SERVER_ADDR']);
        $logging = new Logging($mockSdk);
        $response = $logging->put('string', ['test'=>'data'], 123);

        $this->assertIsArray($response);
    }

    public function testPutException()
    {
        $errorMessage = 'Test Error';
        $_ENV['APP_ENV'] = 'local';
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockStream->shouldReceive('getContents')->andThrow(new \Exception($errorMessage));

        $mockResponseInterface = Mockery::mock(ResponseInterface::class);
        $mockResponseInterface->shouldReceive('getBody')
            ->andReturn($mockStream);

        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->shouldReceive('getClientId')
            ->andReturn('sdk_phpunit');

        $mockHttpMethodsClientInterface = Mockery::mock(HttpMethodsClientInterface::class);
        $mockHttpMethodsClientInterface->shouldReceive('post')
            ->andReturn($mockResponseInterface);

        $mockResponseMediator = Mockery::mock(ResponseMediator::class);
        $mockResponseMediator->shouldReceive('getContent')
            ->andReturn($mockHttpMethodsClientInterface);

        $mockSdk = Mockery::mock(Sdk::class);
        $mockSdk->shouldReceive('getHttpClient')
            ->andReturn($mockHttpMethodsClientInterface);
        $mockSdk->shouldReceive('getAuth')
            ->andReturn($mockAuthService);


        unset($_SERVER['SERVER_ADDR']);
        $logging = new Logging($mockSdk);
        $response = $logging->put('string', ['test'=>'data'], 123);

        $this->assertIsArray($response);
        $this->assertEquals(false, $response['success']);
        $this->assertEquals(500, $response['code']);
    }
}
