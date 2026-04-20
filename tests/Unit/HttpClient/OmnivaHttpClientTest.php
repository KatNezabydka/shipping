<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\HttpClient;

use Shipping\DTO\Request\OmnivaFindPickupPointRequest;
use Shipping\DTO\Request\OmnivaRegisterShippingRequest;
use Shipping\DTO\Response\OmnivaFindPickupPointResponse;
use Shipping\HttpClient\OmnivaHttpClient;
use Shipping\Tests\DataProvider\HttpClient\OmnivaHttpClientDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OmnivaHttpClientTest extends TestCase
{
    private OmnivaHttpClient $httpClient;
    private HttpClientInterface&MockObject $clientMock;
    private TestSerializerInterface&MockObject $serializerMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(HttpClientInterface::class);
        $this->serializerMock = $this->createMock(TestSerializerInterface::class);

        $this->httpClient = new OmnivaHttpClient(
            $this->clientMock,
            $this->createMock(LoggerInterface::class),
            $this->serializerMock,
        );
    }

    #[DataProviderExternal(OmnivaHttpClientDataProvider::class, 'findPickupProvider')]
    public function testFindPickupReturnsDeserializedResponse(
        string $country,
        string $postCode,
        int $pickupPoint,
    ): void {
        $request = new OmnivaFindPickupPointRequest(country: $country, postCode: $postCode);
        $normalized = ['country' => $country, 'post_code' => $postCode];
        $json = json_encode(['pickupPoint' => $pickupPoint], JSON_THROW_ON_ERROR);
        $expected = new OmnivaFindPickupPointResponse(pickupPoint: $pickupPoint);

        $this->serializerMock->expects($this->once())
            ->method('normalize')
            ->with($request)
            ->willReturn($normalized);

        $responseMock = $this->createMock(ResponseInterface::class);
        $responseMock->method('getContent')->willReturn($json);

        $this->clientMock->expects($this->once())
            ->method('request')
            ->with('GET', '/pickup/find', ['query' => $normalized])
            ->willReturn($responseMock);

        $this->serializerMock->expects($this->once())
            ->method('deserialize')
            ->with($json, OmnivaFindPickupPointResponse::class, 'json')
            ->willReturn($expected);

        $result = $this->httpClient->findPickup($request);

        $this->assertSame($pickupPoint, $result->pickupPoint);
        $this->assertTrue($result->hasPickupPoint());
    }

    #[DataProviderExternal(OmnivaHttpClientDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShippingCallsPostAndReturnsTrue(
        int $pickupPointId,
        string $orderId,
    ): void {
        $request = new OmnivaRegisterShippingRequest(pickupPointId: $pickupPointId, orderId: $orderId);
        $normalized = ['pickup_point_id' => $pickupPointId, 'order_id' => $orderId];

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
