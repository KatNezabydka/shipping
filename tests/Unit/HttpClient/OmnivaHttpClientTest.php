<?php

declare(strict_types=1);

namespace App\Tests\Unit\HttpClient;

use App\DTO\Request\OmnivaFindPickupPointRequest;
use App\DTO\Request\OmnivaRegisterShippingRequest;
use App\DTO\Response\OmnivaFindPickupPointResponse;
use App\HttpClient\OmnivaHttpClient;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class OmnivaHttpClientTest extends TestCase
{
    private OmnivaHttpClient $httpClient;

    private ClientInterface&MockObject $clientMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(ClientInterface::class);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->httpClient = new OmnivaHttpClient($this->clientMock, $loggerMock);
    }

    public function testFindPickupReturnsCorrectResponse(): void
    {
        $requestDto = new OmnivaFindPickupPointRequest(
            country: 'Denmark',
            postCode: '2100'
        );

        $responseData = ['pickupPoint' => 1];
        $jsonData = json_encode($responseData, JSON_THROW_ON_ERROR);

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn($jsonData);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', 'https://omnivafake.com/pickup/find', ['query' => $requestDto->toArray()])
            ->willReturn($responseMock);

        $result = $this->httpClient->findPickup($requestDto);

        $this->assertSame(1, $result->pickupPoint);
        $this->assertTrue($result->hasPickupPoint());
    }

    public function testRegisterShippingCallsPostAndReturnsTrue(): void
    {
        $requestDto = new OmnivaRegisterShippingRequest(
            pickupPointId: 1,
            orderId: '456'
        );

        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method('getContents')->willReturn(json_encode(['ok' => true], JSON_THROW_ON_ERROR));

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getBody')->willReturn($streamMock);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('POST', 'https://omnivafake.com/register', ['json' => $requestDto->toArray()])
            ->willReturn($responseMock);

        $result = $this->httpClient->registerShipping($requestDto);

        $this->assertTrue($result);
    }
}