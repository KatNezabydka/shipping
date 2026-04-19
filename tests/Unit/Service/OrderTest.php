<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\Service;

use Shipping\Entity\Order as OrderEntity;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\Service\OrderService;
use Shipping\ShippingProvider\Provider\ShippingProviderInterface;
use Shipping\ShippingProvider\ShippingProviderStrategyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private OrderService $orderService;
    private ShippingProviderStrategyInterface&MockObject $strategyMock;

    protected function setUp(): void
    {
        $this->strategyMock = $this->createMock(ShippingProviderStrategyInterface::class);
        $this->orderService = new OrderService($this->strategyMock);
    }

    public function testRegisterShippingReturnsTrue(): void
    {
        $orderEntity = new OrderEntity(
            id: 1,
            street: '123 Main St',
            postCode: '12345',
            city: 'CityName',
            country: 'CountryName',
            shippingProviderKey: ShippingProviderKeyEnum::UPS
        );

        $providerMock = $this->createMock(ShippingProviderInterface::class);
        $providerMock->method('registerShipment')
            ->with($orderEntity)
            ->willReturn(true);

        $this->strategyMock->method('getProvider')
            ->with(ShippingProviderKeyEnum::UPS)
            ->willReturn($providerMock);

        $result = $this->orderService->registerShipping($orderEntity);

        $this->assertTrue($result);
    }

    public function testRegisterShippingReturnsFalse(): void
    {
        $orderEntity = new OrderEntity(
            id: 2,
            street: '456 Other St',
            postCode: '67890',
            city: 'OtherCity',
            country: 'OtherCountry',
            shippingProviderKey: ShippingProviderKeyEnum::DHL
        );

        $providerMock = $this->createMock(ShippingProviderInterface::class);
        $providerMock->method('registerShipment')
            ->with($orderEntity)
            ->willReturn(false);

        $this->strategyMock->method('getProvider')
            ->with(ShippingProviderKeyEnum::DHL)
            ->willReturn($providerMock);

        $result = $this->orderService->registerShipping($orderEntity);

        $this->assertFalse($result);
    }
}