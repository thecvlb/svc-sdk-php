<?php

namespace CVLB\Svc\Api\Services\DataSegmentation;

use CVLB\Svc\Api\AuthService;
use CVLB\Svc\Api\Sdk;
use Http\Client\Common\HttpMethodsClientInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DataSegmentationTest extends TestCase
{
    public function getDse(): DataSegmentation
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
        $mockHttpMethodsClientInterface->shouldReceive('post', 'get', 'put', 'delete')
            ->andReturn($mockResponseInterface);

        $mockSdk = Mockery::mock(Sdk::class);
        $mockSdk->shouldReceive('getHttpClient')
            ->andReturn($mockHttpMethodsClientInterface);
        $mockSdk->shouldReceive('getAuth')
            ->andReturn($mockAuthService);


        return new DataSegmentation($mockSdk);
    }

    public function test__construct()
    {
        $dse = $this->getDse();

        $this->assertInstanceOf(DataSegmentation::class, $dse);
        $this->assertIsString($dse::$base_uri);
    }

    public function testGet_lists()
    {
        $dse = $this->getDse();

        $response = $dse->get_lists();
        $this->assertIsArray($response);
    }

    public function testSearch_lists()
    {
        $dse = $this->getDse();

        $response = $dse->search_lists('description', 'up');
        $this->assertIsArray($response);
    }

    public function testRefresh_lists()
    {
        $dse = $this->getDse();

        $response = $dse->refresh_lists();
        $this->assertIsArray($response);
    }

    public function testQuery()
    {
        $dse = $this->getDse();

        $response = $dse->query('SELECT patient_id FROM datalake');
        $this->assertIsArray($response);
    }

    public function testCreate_list()
    {
        $dse = $this->getDse();

        $response = $dse->create_list('1234-abcd', 'Test list', 'This is a description', true);
        $this->assertIsArray($response);
    }

    public function testView_list()
    {
        $dse = $this->getDse();

        $response = $dse->view_list('1234-abcd');
        $this->assertIsArray($response);
    }

    public function testUpdate_list()
    {
        $dse = $this->getDse();

        $response = $dse->update_list('5678-wxyz', '1234-abcd', 'Test list', 'This is a description', true);
        $this->assertIsArray($response);
    }

    public function testRefresh_list()
    {
        $dse = $this->getDse();

        $response = $dse->refresh_list('1234-abcd');
        $this->assertIsArray($response);
    }

    public function testDelete_list()
    {
        $dse = $this->getDse();

        $response = $dse->delete_list('1234-abcd');
        $this->assertIsArray($response);
    }
}
