<?php

declare(strict_types=1);

namespace Shipping\Tests\Unit\Entity;

use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldHaveUpsAsDefaultShipping(): void
    {
        $order = new Order(1, 'street', '20', 'Malmoe', 'Sweden');

        $this->assertEquals(ShippingProviderKeyEnum::UPS, $order->getShippingProviderKey());
    }
}
