<?php

declare(strict_types=1);

namespace Shipping\ShippingProvider;

use Shipping\Enum\ShippingProviderKeyEnum;
use Shipping\ShippingProvider\Provider\ShippingProviderInterface;

interface ShippingProviderStrategyInterface
{
    public function getProvider(ShippingProviderKeyEnum $shippingProviderEnum): ShippingProviderInterface;
}
