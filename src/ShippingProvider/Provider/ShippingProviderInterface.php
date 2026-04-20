<?php

declare(strict_types=1);

namespace Shipping\ShippingProvider\Provider;

use Shipping\Entity\Order;
use Shipping\Enum\ShippingProviderKeyEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(self::TAG)]
interface ShippingProviderInterface
{
    public const string TAG = 'strategy.shipping_provider';

    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool;

    public function registerShipment(Order $order): bool;
}
