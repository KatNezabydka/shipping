<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\HttpClient;

use Shipping\DTO\Request\UpsRegisterShippingRequest;
use Shipping\HttpClient\UpsHttpClient;
use Shipping\Tests\DataProvider\HttpClient\UpsHttpClientDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UpsHttpClientTest extends TestCase
{
    private UpsHttpClient $httpClient;
    private HttpClientInterface&MockObject $clientMock;
    private TestSerializerInterface&MockObject $serializerMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(HttpClientInterface::class);
        $this->serializerMock = $this->createMock(TestSerializerInterface::class);

        $this->httpClient = new UpsHttpClient(
            $this->clientMock,
            $this->createMock(LoggerInterface::class),
            $this->serializerMock,
        );
    }

    #[DataProviderExternal(UpsHttpClientDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShippingCallsPostAndReturnsTrue(
        int $orderId,
        string $country,
        string $street,
        string $city,
        string $postCode,
    ): void {
        $request = new UpsRegisterShippingRequest(
            orderId: $orderId,
            country: $country,
            street: $street,
            city: $city,
            postCode: $postCode,
        );

        $normalized = ['order_id' => $orderId, 'country' => $country, 'street' => $street, 'city' => $city, 'post_code' => $postCode];

        $this->serializerMock->expects($this->once())
            ->method('normalize')
            ->with($request)
            ->willReturn($normalized);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')->willReturn('{}');

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('POST', '/register', ['json' => $normalized])
            ->willReturn($responseMock);

        $this->assertTrue($this->httpClient->registerShipping($request));
    }
}
