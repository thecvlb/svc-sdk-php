<?php

namespace CVLB\Svc\Api;

use CVLB\Svc\Api\Services\Logging\Logging;
use CVLB\Svc\Api\Services\Notify\Notify;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class SdkTest extends TestCase
{

    public function testGetHttpClient()
    {
        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->allows('getAccessToken');

        $mockClientBuilder = Mockery::mock(ClientBuilder::class);
        $mockClientBuilder->allows('addPlugin');
        $mockClientBuilder->allows('getHttpClient');

        $sdk = new Sdk($mockAuthService, $mockClientBuilder);

        $this->assertInstanceOf(HttpMethodsClientInterface::class, $sdk->getHttpClient());
    }

    public function testLogging()
    {
        $_ENV['APP_ENV'] = 'local';
        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->allows('getAccessToken');

        $mockClientBuilder = Mockery::mock(ClientBuilder::class);
        $mockClientBuilder->allows('addPlugin');

        $sdk = new Sdk($mockAuthService, $mockClientBuilder);

        $this->assertInstanceOf(Logging::class, $sdk->logging());
    }

    public function testNotify()
    {
        $_ENV['APP_ENV'] = 'local';
        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->allows('getAccessToken');

        $mockClientBuilder = Mockery::mock(ClientBuilder::class);
        $mockClientBuilder->allows('addPlugin');

        $sdk = new Sdk($mockAuthService, $mockClientBuilder);

        $this->assertInstanceOf(Notify::class, $sdk->notify());
    }

    public function testGetAUth()
    {
        $mockAuthService = Mockery::mock(AuthService::class);
        $mockAuthService->allows('getAccessToken');

        $mockClientBuilder = Mockery::mock(ClientBuilder::class);
        $mockClientBuilder->allows('addPlugin');

        $sdk = new Sdk($mockAuthService, $mockClientBuilder);

        $this->assertInstanceOf(AuthService::class, $sdk->getAuth());
    }
}
