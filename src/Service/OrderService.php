<?php

declare(strict_types=1);

namespace Shipping\Service;

use Shipping\Entity\Order as OrderEntity;
use Shipping\ShippingProvider\ShippingProviderStrategyInterface;

readonly class OrderService implements OrderServiceInterface
{
    public function __construct(private ShippingProviderStrategyInterface $shippingProviderStrategy)
    {
    }

    public function registerShipping(OrderEntity $order): bool
    {
        $provider = $this->shippingProviderStrategy->getProvider($order->shippingProviderKey);

        return $provider->registerShipment($order);
    }
}
