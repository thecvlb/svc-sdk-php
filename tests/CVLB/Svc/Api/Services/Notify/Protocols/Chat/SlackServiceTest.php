<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Chat;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Notify\Notify;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SlackServiceTest extends TestCase
{
    public function getProtocol(): Chat
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

        return new Chat(new Notify($mockSdk));
    }

    public function getService(): SlackService
    {
        $protocol = $this->getProtocol();
        return $protocol->slack();
    }

    public function testGetInstance()
    {
        $protocol = $this->getProtocol();
        $service = $this->getService();

        $instance = $service::getInstance($protocol);

        $this->assertInstanceOf(SlackService::class, $instance);
    }

    public function testSetBody()
    {
        $service = $this->getService();
        $message = [["type"=>"header"]];
        $destination = ['slack_channel' => '12345qwert'];

        $body = $service->setBody($message, $destination);

        $this->assertIsArray($body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('slack_channel', $body);
        $this->assertArrayHasKey('verbose', $body);
    }

    public function testSend()
    {
        $service = $this->getService();
        $message = [["type"=>"header"]];
        $destination = ['slack_channel' => '12345qwert'];

        $response = $service->send($message, $destination);

        $this->assertIsArray($response);
    }

    public function testSendException()
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

        $mockSdk = Mockery::mock(Sdk::class);
        $mockSdk->shouldReceive('getHttpClient')
            ->andReturn($mockHttpMethodsClientInterface);
        $mockSdk->shouldReceive('getAuth')
            ->andReturn($mockAuthService);

        $mockSdk->app_name = 'PHP Unit Test';

        $protocol = new Chat(new Notify($mockSdk));

        $service = $protocol->slack();

        $message = [["type"=>"header"]];
        $destination = ['ERROR' => '12345qwert'];

        $response = $service->send($message, $destination);

        $this->assertIsArray($response);
        $this->assertEquals(false, $response['success']);
        $this->assertEquals(500, $response['code']);
    }
}
