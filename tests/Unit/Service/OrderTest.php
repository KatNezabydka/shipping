<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Order as OrderEntity;
use App\Enum\ShippingProviderKeyEnum;
use App\Service\Order;
use App\ShippingProvider\Provider\ShippingProviderInterface;
use App\ShippingProvider\ShippingProviderStrategyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    private Order $orderService;
    private ShippingProviderStrategyInterface&MockObject $strategyMock;

    protected function setUp(): void
    {
        $this->strategyMock = $this->createMock(ShippingProviderStrategyInterface::class);
        $this->orderService = new Order($this->strategyMock);
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