<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order as OrderEntity;
use App\ShippingProvider\ShippingProviderStrategyInterface;

readonly class Order implements OrderInterface
{
    public function __construct(private ShippingProviderStrategyInterface $shippingProviderStrategy)
    {
    }

    public function registerShipping(OrderEntity $order): bool
    {
        $provider = $this->shippingProviderStrategy->getProvider($order->getShippingProviderKey());

        return $provider->registerShipment($order);
    }
}
