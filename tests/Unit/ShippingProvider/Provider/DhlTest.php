<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\ShippingProvider\Provider;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Shipping\DTO\Request\DhlRegisterShippingRequest;
use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\HttpClient\DhlHttpClientInterface;
use Shipping\ShippingProvider\Provider\Dhl;
use Shipping\Tests\DataProvider\ShippingProvider\Provider\DhlDataProvider;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @internal
 *
 * @coversNothing
 */
class DhlTest extends TestCase
{
    private DhlHttpClientInterface&MockObject $httpClientMock;
    private LoggerInterface&MockObject $loggerMock;
    private Dhl $provider;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(DhlHttpClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->provider = new Dhl(
            $this->httpClientMock,
            $this->loggerMock,
            $this->createMock(SerializerInterface::class),
        );
    }

    #[DataProviderExternal(DhlDataProvider::class, 'supportsProvider')]
    public function testSupports(ShippingProviderKeyEnum $enum, bool $expected): void
    {
        $this->assertSame($expected, $this->provider->supports($enum));
    }

    #[DataProviderExternal(DhlDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShipment(int $orderId, string $street, bool $success): void
    {
        $order = new Order(
            id: $orderId,
            street: $street,
            postCode: '2100',
            city: 'Copenhagen',
            country: 'Denmark',
            shippingProviderKey: ShippingProviderKeyEnum::DHL,
        );

        if ($success) {
            $this->httpClientMock->expects($this->once())
                ->method('registerShipping')
                ->with($this->equalTo(DhlRegisterShippingRequest::fromOrder($order)))
                ->willReturn(true);

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
