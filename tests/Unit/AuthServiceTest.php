<?php

namespace CVLB\Svc\Api;

use CVLB\Svc\Api\Auth\AuthToken;
use Mockery;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public $credentials = [
        'client_id' => '123456qwerty',
        'client_secret' => 'zxcvbnasdfgh'
    ];

    public function testGetClientId()
    {
        $_ENV['APP_ENV'] = 'local';
        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->shouldReceive('connect')->once();

        $authService = new AuthService($mockRedis, $this->credentials);

        // The client ID we passed to service matches what the service passes back
        $this->assertEquals($this->credentials['client_id'], $authService->getClientId());
    }

    public function testGetAccessTokenFromCache()
    {
        $_ENV['APP_ENV'] = 'local';
        $expectedValue = 'teststring';

        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->shouldReceive('connect');
        $mockRedis->shouldReceive('get')
        ->andReturn($expectedValue);

        $authService = new AuthService($mockRedis, $this->credentials);

        // AuthService fetches access token from cache
        $this->assertEquals($expectedValue, $authService->getAccessToken());
    }

    public function testGetAccessTokenFromCognito()
    {
        $_ENV['APP_ENV'] = 'local';
        $jwt = ['access_token' => 'test', 'expires_in' => 10];

        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->allows('connect');
        $mockRedis->allows('get');
        $mockRedis->allows('set');

        $mockAuthService = $this->getMockBuilder(AuthService::class)
            ->onlyMethods(['makeCurlRequest'])
            ->setConstructorArgs([$mockRedis, $this->credentials])
            ->getMock();
        $mockAuthService->expects($this->once())
            ->method('makeCurlRequest')
            ->willReturn(json_encode($jwt));

        // AuthService fetches access token from Cognito
        $this->assertEquals($jwt['access_token'], $mockAuthService->getAccessToken());
    }

    public function testGetAccessTokenInvalid()
    {
        $_ENV['APP_ENV'] = 'local';
        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->allows('connect');
        $mockRedis->allows('get');
        $mockRedis->allows('set');

        $authService = new AuthService($mockRedis, $this->credentials);

        $result = $authService->getAccessToken();

        $this->assertEmpty($result);
    }
}
