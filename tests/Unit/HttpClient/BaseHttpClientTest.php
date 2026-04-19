<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\HttpClient;

use Shipping\HttpClient\BaseHttpClient;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

readonly class TestHttpClient extends BaseHttpClient
{
}

class BaseHttpClientTest extends TestCase
{
    private BaseHttpClient $httpClient;
    private ClientInterface&MockObject $clientMock;
    private LoggerInterface&MockObject $loggerMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->httpClient = new TestHttpClient($this->clientMock, $this->loggerMock);
    }

    public function testGetReturnsDecodedArray(): void
    {
        $url = 'https://example.com';
        $options = ['foo' => 'bar'];

        $expectedData = ['result' => 123];
        $jsonData = json_encode($expectedData, JSON_THROW_ON_ERROR);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($jsonData);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', $url, ['query' => $options])
            ->willReturn($responseMock);

        $result = $this->httpClient->get($url, $options);
        $this->assertSame($expectedData, $result);
    }

    public function testGetHandlesExceptionAndLogsError(): void
    {
        $url = 'https://example.com';
        $exception = new RuntimeException('Network error');

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', $url, ['query' => []])
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Network error');

        $result = $this->httpClient->get($url);

        $this->assertSame([], $result);
    }

    public function testPostReturnsDecodedArray(): void
    {
        $url = 'https://example.com';
        $body = ['key' => 'value'];
        $options = ['headers' => ['X-Test' => 'abc']];
        $expectedData = ['success' => true];
        $jsonData = json_encode($expectedData, JSON_THROW_ON_ERROR);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($jsonData);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('POST', $url, array_merge(['json' => $body], $options))
            ->willReturn($responseMock);

        $result = $this->httpClient->post($url, $body, $options);

        $this->assertSame($expectedData, $result);
    }

    public function testPostHandlesExceptionAndLogsError(): void
    {
        $url = 'https://example.com';
        $body = ['key' => 'value'];
        $exception = new \RuntimeException('Server error');

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('POST', $url, ['json' => $body])
            ->willThrowException($exception);

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with('Server error');

        $result = $this->httpClient->post($url, $body);

        $this->assertSame([], $result);
    }
}