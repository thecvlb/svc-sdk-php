<?php

namespace CVLB\Svc\Api\Services\Notify;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Notify\Protocols\Chat\Chat;
use CVLB\Svc\Api\Services\Notify\Protocols\Email\Email;
use CVLB\Svc\Api\Services\Notify\Protocols\Push\Push;
use CVLB\Svc\Api\Services\Notify\Protocols\Sms\Sms;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class NotifyTest extends TestCase
{
    public function getNotify(): Notify
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


        return new Notify($mockSdk);
    }

    public function test__construct()
    {
        $notify = $this->getNotify();

        $this->assertInstanceOf(Notify::class, $notify);
        $this->assertIsString($notify::$base_uri);
    }

    public function testEmail()
    {
        $notify = $this->getNotify();
        $response = $notify->email();

        $this->assertInstanceOf(Email::class, $response);
    }

    public function testSms()
    {
        $notify = $this->getNotify();
        $response = $notify->sms();

        $this->assertInstanceOf(Sms::class, $response);
    }

    public function testChat()
    {
        $notify = $this->getNotify();
        $response = $notify->chat();

        $this->assertInstanceOf(Chat::class, $response);
    }

    public function testPush()
    {
        $notify = $this->getNotify();
        $response = $notify->push();

        $this->assertInstanceOf(Push::class, $response);
    }
}
