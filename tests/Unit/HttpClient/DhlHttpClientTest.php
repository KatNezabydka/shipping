<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\HttpClient;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\DhlRegisterShippingRequest;
use Shipping\HttpClient\DhlHttpClient;
use Shipping\Tests\DataProvider\HttpClient\DhlHttpClientDataProvider;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class DhlHttpClientTest extends TestCase
{
    private DhlHttpClient $httpClient;
    private HttpClientInterface&MockObject $clientMock;
    private MockObject&NormalizerInterface&SerializerInterface $serializerMock;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(HttpClientInterface::class);

        /** @var MockObject&NormalizerInterface&SerializerInterface $serializer */
        $serializer = $this->createMockForIntersectionOfInterfaces([SerializerInterface::class, NormalizerInterface::class]);
        $this->serializerMock = $serializer;

        $this->httpClient = new DhlHttpClient(
            $this->clientMock,
            $this->createMock(LoggerInterface::class),
            $this->serializerMock,
        );
    }

    #[DataProviderExternal(DhlHttpClientDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShippingCallsPostAndReturnsTrue(
        int $orderId,
        string $country,
        string $address,
        string $town,
        string $zipCode,
    ): void {
        $request = new DhlRegisterShippingRequest(
            orderId: $orderId,
            country: $country,
            address: $address,
            town: $town,
            zipCode: $zipCode,
        );

        $normalized = ['order_id' => $orderId, 'country' => $country, 'address' => $address, 'town' => $town, 'zip_code' => $zipCode];

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
