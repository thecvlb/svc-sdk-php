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
        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->shouldReceive('connect')->once();

        $authService = new AuthService($mockRedis, $this->credentials);

        $this->assertEquals($this->credentials['client_id'], $authService->getClientId());
    }

    public function testGetAccessTokenFromCache()
    {
        $expectedValue = 'teststring';

        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->shouldReceive('connect');
        $mockRedis->shouldReceive('get')
        ->andReturn($expectedValue);

        $authService = new AuthService($mockRedis, $this->credentials);

        $this->assertEquals($expectedValue, $authService->getAccessToken());
    }

    public function testGetAccessTokenFromCognito()
    {
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

        $this->assertEquals($jwt['access_token'], $mockAuthService->getAccessToken());
    }

    public function testGetAccessTokenInvalid()
    {
        $mockRedis = Mockery::mock(\Redis::class);
        $mockRedis->allows('connect');
        $mockRedis->allows('get');

        $authService = new AuthService($mockRedis, $this->credentials);

        try {
            $authService->getAccessToken();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }

        $this->assertEquals('invalid_client', $result);
    }
}
