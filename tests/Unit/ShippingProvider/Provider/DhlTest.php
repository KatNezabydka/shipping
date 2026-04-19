<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\ShippingProvider\Provider;

use Shipping\DTO\Request\DhlRegisterShippingRequest;
use Shipping\Entity\Order as OrderEntity;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\ShippingProvider\Provider\Dhl;
use Shipping\HttpClient\DhlHttpClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class DhlTest extends TestCase
{
    private DhlHttpClientInterface&MockObject $httpClientMock;
    private LoggerInterface&MockObject $loggerMock;
    private Dhl $dhlProvider;

    protected function setUp(): void
    {
        $this->httpClientMock = $this->createMock(DhlHttpClientInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->dhlProvider = new Dhl($this->httpClientMock, $this->loggerMock);
    }

    public function testSupportsReturnsTrueForDhl(): void
    {
        $this->assertTrue($this->dhlProvider->supports(ShippingProviderKeyEnum::DHL));
    }

    public function testSupportsReturnsFalseForOther(): void
    {
        $this->assertFalse($this->dhlProvider->supports(ShippingProviderKeyEnum::UPS));
    }

    public function testRegisterShipmentCallsHttpClientAndReturnsTrue(): void
    {
        $order = new OrderEntity(
            id: 1,
            street: 'Street 123',
            postCode: '12345',
            city: 'City',
            country: 'Country',
            shippingProviderKey: ShippingProviderKeyEnum::DHL
        );

        $request = DhlRegisterShippingRequest::fromOrder($order);

        $this->httpClientMock->expects($this->once())
            ->method('registerShippingRequest')
            ->with($this->callback(fn(DhlRegisterShippingRequest $r) => $r->toArray() === $request->toArray()))
            ->willReturn(true);

        $result = $this->dhlProvider->registerShipment($order);

        $this->assertTrue($result);
    }

    public function testRegisterShipmentReturnsFalseOnException(): void
    {
        $order = new OrderEntity(
            id: 2,
            street: 'Another St',
            postCode: '67890',
            city: 'OtherCity',
            country: 'OtherCountry',
            shippingProviderKey: ShippingProviderKeyEnum::DHL
        );

        $request = DhlRegisterShippingRequest::fromOrder($order);

        $this->httpClientMock->expects($this->once())
            ->method('registerShippingRequest')
            ->with($this->callback(fn(DhlRegisterShippingRequest $r) => $r->toArray() === $request->toArray()))
            ->willThrowException(new RuntimeException('Network error'));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->isInstanceOf(Throwable::class));

        $result = $this->dhlProvider->registerShipment($order);

        $this->assertFalse($result);
    }
}