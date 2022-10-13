<?php

namespace CVLB\Svc\Api\Services\Notify\Protocols\Email;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Notify\Notify;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SendPulseServiceTest extends TestCase
{
    public function getProtocol(): Email
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

        return new Email(new Notify($mockSdk));
    }

    public function getService(): SendPulseService
    {
        $protocol = $this->getProtocol();
        return $protocol->sendpulse();
    }

    public function testGetInstance()
    {
        $protocol = $this->getProtocol();
        $service = $this->getService();

        $instance = $service::getInstance($protocol);

        $this->assertInstanceOf(SendPulseService::class, $instance);
    }

    public function testSetBody()
    {
        $service = $this->getService();
        $destination = [
            'from_name' => 'test from',
            'from_address' => 'test@from.com',
            'to_address' => 'test@to.com'
        ];

        $message = [
            'email_subject' => 'PHPUnit Test Email',
            'email_html_message' => 'This is <strong>HTML</strong>',
            'email_text_message' => 'This is plain text'
        ];

        $body = $service->setBody($message, $destination);

        $this->assertIsArray($body);
        $this->assertArrayHasKey('from_name', $body);
        $this->assertArrayHasKey('from_address', $body);
        $this->assertArrayHasKey('to_address', $body);
        $this->assertArrayHasKey('email_subject', $body);
        $this->assertArrayHasKey('email_base64_html_message', $body);
        $this->assertArrayHasKey('email_text_message', $body);
        $this->assertArrayHasKey('verbose', $body);
    }

    public function testSend()
    {
        $service = $this->getService();
        $destination = [
            'from_name' => 'test from',
            'from_address' => 'test@from.com',
            'to_address' => 'test@to.com'
        ];

        $message = [
            'email_subject' => 'PHPUnit Test Email',
            'email_html_message' => 'This is <strong>HTML</strong>',
            'email_text_message' => 'This is plain text'
        ];

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

        $protocol = new Email(new Notify($mockSdk));

        $service = $protocol->sendpulse();

        $destination = [
            'ERROR' => 'test from',
            'from_address' => 'test@from.com',
            'to_address' => 'test@to.com'
        ];

        $message = [
            'email_subject' => 'PHPUnit Test Email',
            'email_html_message' => 'This is <strong>HTML</strong>',
            'email_text_message' => 'This is plain text'
        ];

        $response = $service->send($message, $destination);

        $this->assertIsArray($response);
        $this->assertEquals(false, $response['success']);
        $this->assertEquals(500, $response['code']);
    }
}
