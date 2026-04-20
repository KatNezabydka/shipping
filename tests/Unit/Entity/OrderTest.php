<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\Entity;

use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\Tests\DataProvider\Entity\OrderDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testDefaultShippingProviderIsUps(): void
    {
        $order = new Order(
            id: 1,
            street: 'Main Street 1',
            postCode: '2100',
            city: 'Copenhagen',
            country: 'Denmark',
        );

        $this->assertSame(ShippingProviderKeyEnum::UPS, $order->shippingProviderKey);
    }

    #[DataProviderExternal(OrderDataProvider::class, 'orderProvider')]
    public function testPropertiesAreSetCorrectly(
        int $id,
        string $street,
        string $postCode,
        string $city,
        string $country,
        ShippingProviderKeyEnum $provider,
    ): void {
        $order = new Order(
            id: $id,
            street: $street,
            postCode: $postCode,
            city: $city,
            country: $country,
            shippingProviderKey: $provider,
        );

        $this->assertSame($id, $order->id);
        $this->assertSame($street, $order->street);
        $this->assertSame($postCode, $order->postCode);
        $this->assertSame($city, $order->city);
        $this->assertSame($country, $order->country);
        $this->assertSame($provider, $order->shippingProviderKey);
    }
}
