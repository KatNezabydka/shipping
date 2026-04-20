<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\Service;

use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\Service\OrderService;
use Shipping\ShippingProvider\Provider\ShippingProviderInterface;
use Shipping\ShippingProvider\ShippingProviderStrategyInterface;
use Shipping\Tests\DataProvider\Service\OrderDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
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

    #[DataProviderExternal(OrderDataProvider::class, 'registerShippingProvider')]
    public function testRegisterShipping(
        ShippingProviderKeyEnum $provider,
        bool $expectedResult,
    ): void {
        $order = new Order(
            id: 1,
            street: 'Main Street 1',
            postCode: '2100',
            city: 'Copenhagen',
            country: 'Denmark',
            shippingProviderKey: $provider,
        );

        $providerMock = $this->createMock(ShippingProviderInterface::class);
        $providerMock->method('registerShipment')->with($order)->willReturn($expectedResult);

        $this->strategyMock->method('getProvider')
            ->with($provider)
            ->willReturn($providerMock);

        $this->assertSame($expectedResult, $this->orderService->registerShipping($order));
    }
}
