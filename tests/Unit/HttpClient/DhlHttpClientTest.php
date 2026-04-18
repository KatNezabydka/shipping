<?php

declare(strict_types=1);

namespace App\Tests\Unit\HttpClient;

use App\DTO\Request\DhlRegisterShippingRequest;
use App\HttpClient\DhlHttpClient;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DhlHttpClientTest extends TestCase
{
    private DhlHttpClient $httpClient;
    private ClientInterface&MockObject $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->httpClient = new DhlHttpClient($this->clientMock, $loggerMock);
    }

    public function testRegisterShippingRequestCallsPostWithCorrectData(): void
    {
        $orderId = 123;
        $requestDto = new DhlRegisterShippingRequest(
            orderId: $orderId,
            country: 'Denmark',
            address: 'Main Street 1',
            town: 'Copenhagen',
            zipCode: '2100'
        );

        $expectedPayload = $requestDto->toArray();

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn(json_encode(['ok' => true], JSON_THROW_ON_ERROR));

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://dhlfake.com/register',
                ['json' => $expectedPayload]
            )
            ->willReturn($responseMock);

        $result = $this->httpClient->registerShipping($requestDto);

        $this->assertFalse($result);
    }
}