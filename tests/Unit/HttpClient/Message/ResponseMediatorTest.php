<?php

namespace CVLB\Svc\Api\HttpClient\Message;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\ClientBuilder;
use CVLB\Svc\Api\Sdk;
use CVLB\Svc\Api\Services\Logging;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseMediatorTest extends TestCase
{

    public function testGetContent()
    {
        $expectedResult = 'test';
        $mockStream = Mockery::mock(StreamInterface::class);
        $mockStream->shouldReceive('getContents')->andReturn(json_encode([$expectedResult]));

        $mockResponseInterface = Mockery::mock(ResponseInterface::class);
        $mockResponseInterface->shouldReceive('getBody')
            ->andReturn($mockStream);

        $response = ResponseMediator::getContent($mockResponseInterface);

        $this->assertIsArray($response);
        $this->assertTrue(in_array($expectedResult, $response));
    }
}
