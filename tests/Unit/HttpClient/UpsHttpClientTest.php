<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\HttpClient;

use Shipping\DTO\Request\UpsRegisterShippingRequest;
use Shipping\HttpClient\UpsHttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class UpsHttpClientTest extends TestCase
{
    private UpsHttpClient $httpClient;
    private ClientInterface&MockObject $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->httpClient = new UpsHttpClient($this->clientMock, $loggerMock);
    }

    /**
     * @throws \Throwable
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function testRegisterShippingRequestCallsPostAndReturnsTrue(): void
    {
        $requestDto = new UpsRegisterShippingRequest(
            orderId: 123,
            country: 'Denmark',
            street: 'Blegdamsvej 9',
            city: 'Copenhagen',
            postCode: '2100'
        );

        $jsonData = json_encode(['ok' => true], JSON_THROW_ON_ERROR);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($jsonData);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://upsfake.com/register',
                ['json' => $requestDto->toArray()]
            )
            ->willReturn($responseMock);

        $result = $this->httpClient->registerShipping($requestDto);

        $this->assertTrue($result);
    }
}