<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\ShippingProvider\Provider;

use Shipping\DTO\Request\UpsRegisterShippingRequest;
use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\UpsHttpClientInterface;
use Shipping\ShippingProvider\Provider\Ups;
use Shipping\Tests\DataProvider\ShippingProvider\Provider\UpsDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

class UpsTest extends TestCase
{
    private UpsHttpClientInterface&MockObject $httpClientMock;
    private LoggerInterface&MockObject $loggerMock;
    private Ups $provider;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(UpsHttpClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->provider = new Ups(
            $this->httpClientMock,
            $this->loggerMock,
            $this->createMock(SerializerInterface::class),
        );
    }

    #[DataProviderExternal(UpsDataProvider::class, 'supportsProvider')]
    public function testSupports(ShippingProviderKeyEnum $enum, bool $expected): void
    {
        $this->assertSame($expected, $this->provider->supports($enum));
    }

    #[DataProviderExternal(UpsDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShipment(int $orderId, string $street, bool $success): void
    {
        $order = new Order(
            id: $orderId,
            street: $street,
            postCode: '2100',
            city: 'Copenhagen',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::UPS,
        );

        if ($success) {
            $this->httpClientMock->expects($this->once())
                ->method('registerShipping')
                ->with($this->equalTo(UpsRegisterShippingRequest::fromOrder($order)));

            $this->assertTrue($this->provider->registerShipment($order));
        } else {
            $this->httpClientMock->expects($this->once())
                ->method('registerShipping')
                ->willThrowException(new RuntimeException('Network error'));

            $this->loggerMock->expects($this->once())
                ->method('error')
                ->with('Network error');

            $this->assertFalse($this->provider->registerShipment($order));
        }
    }
}
