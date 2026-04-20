<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\ShippingProvider\Provider;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shipping\DTO\Request\OmnivaFindPickupPointRequest;
use Shipping\DTO\Request\OmnivaRegisterShippingRequest;
use Shipping\DTO\Response\OmnivaFindPickupPointResponse;
use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\OmnivaHttpClientInterface;
use Shipping\ShippingProvider\Provider\Omniva;
use Shipping\Tests\DataProvider\ShippingProvider\Provider\OmnivaDataProvider;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class OmnivaTest extends TestCase
{
    private MockObject&OmnivaHttpClientInterface $httpClientMock;
    private LoggerInterface&MockObject $loggerMock;
    private Omniva $provider;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(OmnivaHttpClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->provider = new Omniva(
            $this->httpClientMock,
            $this->loggerMock,
            $this->createMock(SerializerInterface::class),
        );
    }

    #[DataProviderExternal(OmnivaDataProvider::class, 'supportsProvider')]
    public function testSupports(ShippingProviderKeyEnum $enum, bool $expected): void
    {
        $this->assertSame($expected, $this->provider->supports($enum));
    }

    #[DataProviderExternal(OmnivaDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShipment(
        int $orderId,
        string $country,
        int $pickupPoint,
        bool $hasPickupPoint,
        bool $expectedResult,
    ): void {
        $order = new Order(
            id: $orderId,
            street: 'Main Street 1',
            postCode: '2100',
            city: 'Copenhagen',
            country: $country,
            shippingProviderKey: ShippingProviderKeyEnum::OMNIVA,
        );

        $pickupResponse = new OmnivaFindPickupPointResponse(pickupPoint: $pickupPoint);

        $this->httpClientMock->expects($this->once())
            ->method('findPickup')
            ->with($this->equalTo(OmnivaFindPickupPointRequest::fromOrder($order)))
            ->willReturn($pickupResponse);

        if ($hasPickupPoint) {
            $this->httpClientMock->expects($this->once())
                ->method('registerShipping')
                ->with($this->equalTo(new OmnivaRegisterShippingRequest($pickupPoint, $country)))
                ->willReturn(true);
        } else {
            $this->loggerMock->expects($this->once())
                ->method('error')
                ->with('Pickup point not found for Omniva');

            $this->httpClientMock->expects($this->never())
                ->method('registerShipping');
        }

        $this->assertSame($expectedResult, $this->provider->registerShipment($order));
    }
}
