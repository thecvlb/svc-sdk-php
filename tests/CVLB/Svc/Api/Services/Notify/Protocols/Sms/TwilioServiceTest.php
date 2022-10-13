<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Sms;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Notify\Notify;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TwilioServiceTest extends TestCase
{
    public function getProtocol(): Sms
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

        return new Sms(new Notify($mockSdk));
    }

    public function getService(): TwilioService
    {
        $protocol = $this->getProtocol();
        return $protocol->twilio();
    }

    public function testGetInstance()
    {
        $protocol = $this->getProtocol();
        $service = $this->getService();

        $instance = $service::getInstance($protocol);

        $this->assertInstanceOf(TwilioService::class, $instance);
    }

    public function testSetBody()
    {
        $service = $this->getService();
        $message = ["text"=>"A message"];
        $destination = ['from' => '+1222334444', 'to' => '+19998887777'];

        $body = $service->setBody($message, $destination);

        $this->assertIsArray($body);
        $this->assertArrayHasKey('message', $body);
        $this->assertArrayHasKey('from', $body);
        $this->assertArrayHasKey('to', $body);
        $this->assertArrayHasKey('verbose', $body);
    }

    public function testSend()
    {
        $service = $this->getService();
        $message = ["text"=>"A message"];
        $destination = ['from' => '+1222334444', 'to' => '+19998887777'];

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

        $protocol = new Sms(new Notify($mockSdk));

        $service = $protocol->twilio();

        $message = ["ERROR"=>"A message"];
        $destination = ['from' => '+1222334444', 'to' => '+19998887777'];

        $response = $service->send($message, $destination);

        $this->assertIsArray($response);
        $this->assertEquals(false, $response['success']);
        $this->assertEquals(500, $response['code']);
    }
}
