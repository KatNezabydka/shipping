<?php

declare(strict_types=1);

namespace App\ShippingProvider\Provider;

use App\Entity\Order;
use App\Enum\ShippingProviderKeyEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(self::TAG)]
interface ShippingProviderInterface
{
    public const string TAG = 'strategy.shipping_provider';
    public function supports(ShippingProviderKeyEnum $shippingProviderEnum): bool;

    public function registerShipment(Order $order): bool;
}
